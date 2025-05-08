<?php

namespace App\Http\Controllers;

use App\Models\Sheet;

class HomeController extends Controller
{
    public function index()
    {
        $publicSheets = Sheet::with(['images', 'user'])
            ->where('is_public', '=', 'on')
            ->latest()
            ->take(12)
            ->get();

        return view('index', ['publicSheets' => $publicSheets]);
    }
}
