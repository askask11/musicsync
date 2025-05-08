<?php

namespace App\Http\Controllers;

use App\Models\Sheet;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        // Get the user's favorite sheets with their associated tracks
        $favorites = $user->favorites()->get();
        return view('favourites.index', [
            'favorites' => $favorites,
        ]);
    }

    public function toggleFavourite(Request $request)
    {
        $request->validate([
            'sheet_id' => 'required|exists:sheets,id',
        ]);

        $sheet = Sheet::findOrFail($request->input('sheet_id'));
        $user = auth()->user();

        $isFav = $user->favorites()->where('sheet_id', $sheet->id)->exists();

        if ($isFav) {
            $user->favorites()->detach($sheet->id);
            $status = 'removed from';
        } else {
            $user->favorites()->attach($sheet->id);
            $status = 'added to';
        }

        //redirect to the previous page with success message
        return redirect()->back()->with('success', "Sheet {$status} favorites successfully!");
    }
}
