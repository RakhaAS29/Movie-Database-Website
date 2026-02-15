@extends('layouts.app')

@section('title', $movie['Title'] ?? __('messages.movie_not_found'))

@section('extra-css')
    <style>
        .header h1 {
            color: #ffffff !important;
        }

        /* Movie Detail */
        .movie-detail {
            background: #1a1a1a;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        .detail-header {
            display: flex;
            gap: 30px;
            padding: 30px;
        }

        .detail-poster {
            width: 300px;
            height: 450px;
            object-fit: cover;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .detail-info {
            flex: 1;
        }

        .detail-info h2 {
            color: #ffffff;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .detail-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .meta-item {
            background: #2a2a2a;
            color: #ffffff;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
        }

        .detail-plot {
            line-height: 1.6;
            color: #e0e0e0;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .detail-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .spec-item {
            background: #2a2a2a;
            padding: 15px;
            border-radius: 8px;
        }

        .spec-label {
            font-weight: bold;
            color: #ff9800;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .spec-value {
            color: #e0e0e0;
            font-size: 14px;
        }

        .back-button {
            margin-bottom: 20px;
        }

        .favorite-detail-btn {
            margin-top: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
            }

            .detail-poster {
                width: 100%;
                height: auto;
            }

            .detail-info h2 {
                font-size: 24px;
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
        <div class="back-button">
            <a href="{{ route('movies.index') }}" class="btn btn-secondary">
                ← {{ __('messages.back_to_list') }}
            </a>
        </div>

        @if(isset($movie))
            <div class="movie-detail">
                <div class="detail-header">
                    <!-- Poster -->
                    <img class="detail-poster"
                        src="{{ $movie['Poster'] != 'N/A' ? $movie['Poster'] : 'https://via.placeholder.com/300x450?text=No+Poster' }}"
                        alt="{{ $movie['Title'] }}">

                    <!-- Movie Info -->
                    <div class="detail-info">
                        <h2>{{ $movie['Title'] }}</h2>

                        <!-- Meta Info -->
                        <div class="detail-meta">
                            <span class="meta-item">{{ $movie['Year'] ?? 'N/A' }}</span>
                            <span class="meta-item">⭐ {{ $movie['imdbRating'] ?? 'N/A' }}</span>
                            <span class="meta-item">{{ $movie['Runtime'] ?? 'N/A' }}</span>
                            @if(isset($movie['Rated']) && $movie['Rated'] != 'N/A')
                                <span class="meta-item">{{ $movie['Rated'] }}</span>
                            @endif
                        </div>

                        <!-- Plot -->
                        <p class="detail-plot">
                            {{ $movie['Plot'] ?? 'No plot available.' }}
                        </p>

                        <!-- Favorite Button -->
                        <div class="favorite-detail-btn">
                            @if($isFavorite)
                                <form method="POST" action="{{ route('favorites.destroy', $movie['imdbID']) }}"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        ❤️ {{ __('messages.remove_from_favorites') }}
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('favorites.store') }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="imdb_id" value="{{ $movie['imdbID'] }}">
                                    <input type="hidden" name="title" value="{{ $movie['Title'] }}">
                                    <input type="hidden" name="year" value="{{ $movie['Year'] }}">
                                    <input type="hidden" name="poster" value="{{ $movie['Poster'] }}">
                                    <button type="submit" class="btn btn-primary">
                                        🤍 {{ __('messages.add_to_favorites') }}
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Detailed Specs -->
                        <div class="detail-specs">
                            @if(isset($movie['Director']) && $movie['Director'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.director') }}</div>
                                    <div class="spec-value">{{ $movie['Director'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Writer']) && $movie['Writer'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.writer') }}</div>
                                    <div class="spec-value">{{ $movie['Writer'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Actors']) && $movie['Actors'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.actors') }}</div>
                                    <div class="spec-value">{{ $movie['Actors'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Genre']) && $movie['Genre'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.genre') }}</div>
                                    <div class="spec-value">{{ $movie['Genre'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Released']) && $movie['Released'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.released') }}</div>
                                    <div class="spec-value">{{ $movie['Released'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Country']) && $movie['Country'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.country') }}</div>
                                    <div class="spec-value">{{ $movie['Country'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Language']) && $movie['Language'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.language') }}</div>
                                    <div class="spec-value">{{ $movie['Language'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Awards']) && $movie['Awards'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.awards') }}</div>
                                    <div class="spec-value">{{ $movie['Awards'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['BoxOffice']) && $movie['BoxOffice'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.box_office') }}</div>
                                    <div class="spec-value">{{ $movie['BoxOffice'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Production']) && $movie['Production'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.production') }}</div>
                                    <div class="spec-value">{{ $movie['Production'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['imdbVotes']) && $movie['imdbVotes'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.imdb_votes') }}</div>
                                    <div class="spec-value">{{ $movie['imdbVotes'] }}</div>
                                </div>
                            @endif

                            @if(isset($movie['Metascore']) && $movie['Metascore'] != 'N/A')
                                <div class="spec-item">
                                    <div class="spec-label">{{ __('messages.metascore') }}</div>
                                    <div class="spec-value">{{ $movie['Metascore'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="no-results">
                {{ __('messages.movie_not_found') }}
            </div>
        @endif
    </div>
@endsection