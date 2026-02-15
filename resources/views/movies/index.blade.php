@extends('layouts.app')

@section('title', __('messages.all_movies'))

@section('extra-css')
    <style>
        .header h1 {
            color: #ffffff !important;
        }

        /* Search Bar */
        .search-section {
            background: #212121;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-form input,
        .search-form select {
            background: #333333;
            color: #ffffff;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-form input {
            flex: 1;
            min-width: 200px;
        }

        .search-form select {
            min-width: 150px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            position: relative;
            transition: color 0.3s;
            text-decoration: none;
        }

        .tab.active {
            color: #ff9800;
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ff9800;
        }

        /* Movie Grid */
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .movie-card {
            background: #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            position: relative;
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .movie-poster {
            width: 100%;
            height: 350px;
            object-fit: cover;
            background: #f0f0f0;
        }

        /* Lazy Loading Skeleton */
        .movie-poster[data-src] {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .movie-poster.loaded {
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .movie-info {
            padding: 15px;
            background: #1a1a1a;
        }

        .movie-title {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 8px;
        }

        .movie-year {
            color: #b0b0b0;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s;
        }

        .favorite-btn:hover {
            transform: scale(1.1);
            background: white;
        }

        .favorite-btn.active {
            color: #ff4081;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 40px;
            color: white;
            font-size: 18px;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            color: #666;
            font-size: 18px;
        }

        /* Infinite Scroll Trigger */
        #infiniteScrollTrigger {
            height: 100px;
            margin: 20px 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .movie-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
@endsection

@section('content')
    <!-- Header -->
    <div class="header">
        <h1>{{ __('messages.app_title') }}</h1>
        <div class="header-actions">
            <div class="lang-switch">
                <a href="{{ url()->current() }}?lang=en"
                    class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ url()->current() }}?lang=id"
                    class="lang-btn {{ app()->getLocale() == 'id' ? 'active' : '' }}">ID</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">{{ __('messages.logout') }}</button>
            </form>
        </div>
    </div>

    <div class="container">
        <!-- Tabs -->
        <div class="tabs">
            <a href="{{ route('movies.index') }}" class="tab active">
                {{ __('messages.all_movies') }}
            </a>
            <a href="{{ route('favorites.index') }}" class="tab">
                {{ __('messages.my_favorites') }} ({{ $favoritesCount ?? 0 }})
            </a>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="{{ route('movies.search') }}" class="search-form" id="searchForm">
                <input type="text" name="s" id="searchQuery" placeholder="{{ __('messages.search_placeholder') }}"
                    value="{{ request('s') }}">

                <select name="type" id="searchType">
                    <option value="">{{ __('messages.type') }}</option>
                    <option value="movie" {{ request('type') == 'movie' ? 'selected' : '' }}>Movie</option>
                    <option value="series" {{ request('type') == 'series' ? 'selected' : '' }}>Series</option>
                </select>

                <input type="number" name="y" id="searchYear" placeholder="{{ __('messages.year') }}"
                    value="{{ request('y') }}">

                <button type="submit" class="btn btn-primary">{{ __('messages.search') }}</button>
            </form>
        </div>

        <!-- Movie Grid -->
        @if(isset($movies) && count($movies) > 0)
            <div class="movie-grid" id="movieGrid">
                @foreach($movies as $movie)
                    <div class="movie-card">
                        <!-- Favorite Button -->
                        <form method="POST" action="{{ route('favorites.store') }}" class="favorite-form">
                            @csrf
                            <input type="hidden" name="imdb_id" value="{{ $movie['imdbID'] }}">
                            <input type="hidden" name="title" value="{{ $movie['Title'] }}">
                            <input type="hidden" name="year" value="{{ $movie['Year'] }}">
                            <input type="hidden" name="poster" value="{{ $movie['Poster'] }}">
                            <button type="submit"
                                class="favorite-btn {{ in_array($movie['imdbID'], $favoriteIds ?? []) ? 'active' : '' }}">
                                {{ in_array($movie['imdbID'], $favoriteIds ?? []) ? '❤️' : '🤍' }}
                            </button>
                        </form>

                        <!-- Movie Poster -->
                        <a href="{{ route('movies.show', $movie['imdbID']) }}">
                            <img class="movie-poster"
                                data-src="{{ $movie['Poster'] != 'N/A' ? $movie['Poster'] : 'https://via.placeholder.com/300x450?text=No+Poster' }}"
                                alt="{{ $movie['Title'] }}">
                        </a>

                        <!-- Movie Info -->
                        <a href="{{ route('movies.show', $movie['imdbID']) }}" style="text-decoration: none;">
                            <div class="movie-info">
                                <div class="movie-title">{{ $movie['Title'] }}</div>
                                <div class="movie-year">{{ $movie['Year'] }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Infinite Scroll Trigger -->
            <div id="infiniteScrollTrigger"></div>

            <!-- Loading Indicator -->
            <div id="loading" class="loading" style="display: none;">
                <div class="spinner"></div>
                {{ __('messages.loading') }}
            </div>
        @else
            <div class="no-results">
                {{ __('messages.no_movies_found') }}
            </div>
        @endif
    </div>
@endsection

@section('extra-js')
    <script>
        // ========================================
        // LAZY LOAD IMAGES
        // ========================================
        function initLazyLoad() {
            const lazyImages = document.querySelectorAll('img.movie-poster[data-src]');

            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('loaded');
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px' 
            });

            lazyImages.forEach(img => imageObserver.observe(img));
        }

        // ========================================
        // INFINITE SCROLL
        // ========================================
        let page = 2;
        let loading = false;
        let hasMore = true;

        function initInfiniteScroll() {
            const trigger = document.getElementById('infiniteScrollTrigger');
            if (!trigger) return;

            const scrollObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !loading && hasMore) {
                        loadMoreMovies();
                    }
                });
            }, {
                rootMargin: '200px' 
            });

            scrollObserver.observe(trigger);
        }

        function loadMoreMovies() {
            loading = true;
            document.getElementById('loading').style.display = 'block';

            // Get current search params
            const searchQuery = document.getElementById('searchQuery').value || 'movie';
            const searchType = document.getElementById('searchType').value;
            const searchYear = document.getElementById('searchYear').value;

            // Build URL with pagination
            let url = '{{ route("movies.search") }}?page=' + page;
            url += '&s=' + encodeURIComponent(searchQuery);
            if (searchType) url += '&type=' + searchType;
            if (searchYear) url += '&y=' + searchYear;

            // Force HTTPS untuk production
            url = url.replace('http://', 'https://');

            // Fetch more movies
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.movies && data.movies.length > 0) {
                        const movieGrid = document.getElementById('movieGrid');

                        data.movies.forEach(movie => {
                            const isFavorite = (data.favoriteIds || []).includes(movie.imdbID);
                            const movieCard = createMovieCard(movie, isFavorite);
                            movieGrid.insertAdjacentHTML('beforeend', movieCard);
                        });

                        // Re-init lazy loading for new images
                        initLazyLoad();

                        page++;

                        // OMDb API returns max 10 results per page
                        // If less than 10, no more results
                        if (data.movies.length < 10) {
                            hasMore = false;
                            document.getElementById('infiniteScrollTrigger').style.display = 'none';
                        }
                    } else {
                        hasMore = false;
                        document.getElementById('infiniteScrollTrigger').style.display = 'none';
                    }

                    document.getElementById('loading').style.display = 'none';
                    loading = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loading').style.display = 'none';
                    loading = false;
                });
        }

        function createMovieCard(movie, isFavorite) {
            const poster = movie.Poster != 'N/A' ? movie.Poster : 'https://via.placeholder.com/300x450?text=No+Poster';
            const heartIcon = isFavorite ? '❤️' : '🤍';
            const activeClass = isFavorite ? 'active' : '';

            return `
                            <div class="movie-card">
                                <form method="POST" action="{{ route('favorites.store') }}" class="favorite-form">
                                    @csrf
                                    <input type="hidden" name="imdb_id" value="${movie.imdbID}">
                                    <input type="hidden" name="title" value="${movie.Title}">
                                    <input type="hidden" name="year" value="${movie.Year}">
                                    <input type="hidden" name="poster" value="${movie.Poster}">
                                    <button type="submit" class="favorite-btn ${activeClass}">
                                        ${heartIcon}
                                    </button>
                                </form>
                                <a href="/movies/${movie.imdbID}">
                                    <img class="movie-poster" 
                                         data-src="${poster}"
                                         alt="${movie.Title}">
                                </a>
                                <a href="/movies/${movie.imdbID}" style="text-decoration: none;">
                                    <div class="movie-info">
                                        <div class="movie-title">${movie.Title}</div>
                                        <div class="movie-year">${movie.Year}</div>
                                    </div>
                                </a>
                            </div>
                        `;
        }

        // ========================================
        // HANDLE FAVORITE FORM AJAX
        // ========================================
        document.addEventListener('submit', function (e) {
            if (e.target.classList.contains('favorite-form')) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);

                // Force HTTPS untuk production
                const url = form.action.replace('http://', 'https://');

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        const btn = form.querySelector('.favorite-btn');
                        if (data.added) {
                            btn.classList.add('active');
                            btn.textContent = '❤️';
                        } else {
                            btn.classList.remove('active');
                            btn.textContent = '🤍';
                        }

                        const favTab = document.querySelector('.tabs a[href="{{ route('favorites.index') }}"]');
                        if (favTab && data.favoritesCount !== undefined) {
                            favTab.innerHTML = favTab.innerHTML.replace(/\(\d+\)/, `(${data.favoritesCount})`);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        // ========================================
        // INITIALIZE ON PAGE LOAD
        // ========================================
        document.addEventListener('DOMContentLoaded', function () {
            initLazyLoad();
            initInfiniteScroll();
        });
    </script>
@endsection