<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    private function getOMDbApiKey()
    {
        return env('OMDB_API_KEY');
    }

    // Show favorites list
    public function index()
    {
        $favorites = Favorite::orderBy('created_at', 'desc')->get();
        
        // Get detailed info from OMDb API for each favorite
        $movies = [];
        foreach ($favorites as $favorite) {
            $response = Http::get('http://www.omdbapi.com/', [
                'apikey' => $this->getOMDbApiKey(),
                'i' => $favorite->imdb_id,
            ]);
            
            $movieData = $response->json();
            if (isset($movieData['Response']) && $movieData['Response'] === 'True') {
                $movies[] = $movieData;
            }
        }

        // Get favorite IDs
        $favoriteIds = Favorite::pluck('imdb_id')->toArray();
        $favoritesCount = count($favoriteIds);

        return view('favorites.index', compact('movies', 'favoriteIds', 'favoritesCount'));
    }

    // Add to favorites
    public function store(Request $request)
    {
        $request->validate([
            'imdb_id' => 'required',
            'title' => 'required',
            'year' => 'nullable',
            'poster' => 'nullable',
        ]);

        // Check if already exists
        $exists = Favorite::where('imdb_id', $request->imdb_id)->first();

        if ($exists) {
            // Remove from favorites
            $exists->delete();
            $added = false;
        } else {
            // Add to favorites
            Favorite::create([
                'imdb_id' => $request->imdb_id,
                'title' => $request->title,
                'year' => $request->year,
                'poster' => $request->poster,
            ]);
            $added = true;
        }

        $favoritesCount = Favorite::count();

        // If AJAX request
        if ($request->ajax()) {
            return response()->json([
                'added' => $added,
                'favoritesCount' => $favoritesCount,
            ]);
        }

        return back()->with('success', $added ? 'Added to favorites' : 'Removed from favorites');
    }

    // Remove from favorites
    public function destroy(Request $request, $id)
    {
        $favorite = Favorite::where('imdb_id', $id)->first();
        
        if ($favorite) {
            $favorite->delete();
            $deleted = true;
        } else {
            $deleted = false;
        }

        $favoritesCount = Favorite::count();

        // If AJAX request (dari halaman favorites)
        if ($request->ajax()) {
            return response()->json([
                'success' => $deleted,
                'favoritesCount' => $favoritesCount,
                'message' => $deleted ? 'Removed from favorites' : 'Movie not found'
            ]);
        }

        // If regular request (dari halaman detail)
        return redirect()->route('movies.show', $id)->with('success', 'Removed from favorites');
    }
}