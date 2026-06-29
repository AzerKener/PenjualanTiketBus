<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $ratings = Rating::with(['user', 'jadwal.rute', 'jadwal.bus'])
            ->when($request->filled('rating'), function ($q) use ($request) {
                $q->where('rating', $request->rating);
            })
            ->latest()
            ->paginate(15);

        $averageRating = Rating::avg('rating') ?? 0;
        $totalRatings = Rating::count();

        return view('admin.rating.index', compact('ratings', 'averageRating', 'totalRatings', 'request'));
    }
}
