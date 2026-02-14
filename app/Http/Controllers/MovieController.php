<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Favorite;

class MovieController extends Controller
{
    private function getOMDbApiKey()
    {
        return env('OMDB_API_KEY');
    }

    // Show movies list
    public function index()
    {
        // Default search - popular movies
        $response = Http::get('http://www.omdbapi.com/', [
            'apikey' => $this->getOMDbApiKey(),
            's' => 'avengers', // default search
            'type' => 'movie',
        ]);

        $data = $response->json();
        $movies = $data['Search'] ?? [];

        // Get favorite IDs
        $favoriteIds = Favorite::pluck('imdb_id')->toArray();
        $favoritesCount = count($favoriteIds);

        return view('movies.index', compact('movies', 'favoriteIds', 'favoritesCount'));
    }

    // Search movies
    public function search(Request $request)
    {
        $searchQuery = $request->input('s', 'movie'); // default search term
        $type = $request->input('type', '');
        $year = $request->input('y', '');
        $page = $request->input('page', 1);

        // Build API request
        $params = [
            'apikey' => $this->getOMDbApiKey(),
            's' => $searchQuery,
            'page' => $page,
        ];

        if ($type) {
            $params['type'] = $type;
        }

        if ($year) {
            $params['y'] = $year;
        }

        $response = Http::get('http://www.omdbapi.com/', $params);
        $data = $response->json();
        $movies = $data['Search'] ?? [];

        // Get favorite IDs
        $favoriteIds = Favorite::pluck('imdb_id')->toArray();
        $favoritesCount = count($favoriteIds);

        // If AJAX request (for infinite scroll)
        if ($request->ajax()) {
            return response()->json([
                'movies' => $movies,
                'favoriteIds' => $favoriteIds,
                'favoritesCount' => $favoritesCount,
            ]);
        }

        return view('movies.index', compact('movies', 'favoriteIds', 'favoritesCount'));
    }

    // Show movie detail
    public function show($id)
    {
        // Get movie detail from OMDb API
        $response = Http::get('http://www.omdbapi.com/', [
            'apikey' => $this->getOMDbApiKey(),
            'i' => $id,
            'plot' => 'full',
        ]);

        $movie = $response->json();

        // Check if movie is in favorites
        $isFavorite = Favorite::where('imdb_id', $id)->exists();

        return view('movies.show', compact('movie', 'isFavorite'));
    }
}