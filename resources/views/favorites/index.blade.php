@extends('layouts.app')

@section('title', __('messages.my_favorites'))

@section('extra-css')
    <style>
        /* Override header title color */
        .header h1 {
            color: #ffffff !important;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid hsla(0, 0%, 100%, 1.00);
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            /* ← PUTIH */
            position: relative;
            transition: color 0.3s;
            text-decoration: none;
        }

        .tab.active {
            color: #ff9800;
            /* ← ORANGE */
        }

        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ff9800;
            /* ← ORANGE */
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
            z-index: 10;
        }

        .favorite-btn:hover {
            transform: scale(1.1);
            background: white;
        }

        .favorite-btn.active {
            color: #ff4081;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            color: #666;
            font-size: 18px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .empty-state-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
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
            <a href="{{ route('movies.index') }}" class="tab">
                {{ __('messages.all_movies') }}
            </a>
            <a href="{{ route('favorites.index') }}" class="tab active">
                {{ __('messages.my_favorites') }} ({{ $favoritesCount ?? 0 }})
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="success-message" style="margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Movie Grid or Empty State -->
        @if(isset($movies) && count($movies) > 0)
            <div class="movie-grid" id="movieGrid">
                @foreach($movies as $movie)
                    <div class="movie-card" data-movie-id="{{ $movie['imdbID'] }}">
                        <!-- Remove from Favorite Button -->
                        <form method="POST" action="{{ route('favorites.destroy', $movie['imdbID']) }}" class="favorite-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="favorite-btn active" title="{{ __('messages.remove_from_favorites') }}">
                                ❤️
                            </button>
                        </form>

                        <!-- Movie Poster (Lazy Load) -->
                        <a href="{{ route('movies.show', $movie['imdbID']) }}">
                            <img class="movie-poster"
                                data-src="{{ $movie['Poster'] != 'N/A' ? $movie['Poster'] : 'https://via.placeholder.com/300x450?text=No+Poster' }}"
                                alt="{{ $movie['Title'] }}">
                        </a>

                        <!-- Movie Info -->
                        <a href="{{ route('movies.show', $movie['imdbID']) }}" style="text-decoration: none;">
                            <div class="movie-info">
                                <div class="movie-title">{{ $movie['Title'] }}</div>
                                <div class="movie-year">{{ $movie['Year'] ?? 'N/A' }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">🎬</div>
                <h3>{{ __('messages.no_favorites_yet') }}</h3>
                <p>{{ __('messages.start_adding_favorites') }}</p>
                <a href="{{ route('movies.index') }}" class="btn btn-primary">
                    {{ __('messages.browse_movies') }}
                </a>
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
        // HANDLE FAVORITE FORM AJAX (Remove)
        // ========================================
        document.addEventListener('submit', function (e) {
            if (e.target.classList.contains('favorite-form')) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form);
                const movieCard = form.closest('.movie-card');
                const movieId = movieCard.dataset.movieId;

                // Add fade out animation
                movieCard.style.transition = 'opacity 0.3s, transform 0.3s';
                movieCard.style.opacity = '0';
                movieCard.style.transform = 'scale(0.9)';

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Remove card after animation
                        setTimeout(() => {
                            movieCard.remove();

                            // Update favorites count in tab
                            const currentCount = parseInt(document.querySelector('.tab.active').textContent.match(/\d+/)[0]);
                            const newCount = currentCount - 1;
                            document.querySelector('.tab.active').innerHTML =
                                document.querySelector('.tab.active').innerHTML.replace(/\(\d+\)/, `(${newCount})`);

                            // Show empty state if no more favorites
                            const movieGrid = document.getElementById('movieGrid');
                            if (movieGrid && movieGrid.children.length === 0) {
                                location.reload(); // Reload to show empty state
                            }
                        }, 300);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert animation on error
                        movieCard.style.opacity = '1';
                        movieCard.style.transform = 'scale(1)';
                        alert('Failed to remove from favorites. Please try again.');
                    });
            }
        });

        // ========================================
        // INITIALIZE ON PAGE LOAD
        // ========================================
        document.addEventListener('DOMContentLoaded', function () {
            initLazyLoad();
        });
    </script>
@endsection