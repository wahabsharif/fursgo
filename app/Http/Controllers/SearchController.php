<?php

namespace App\Http\Controllers;

use App\Models\Groomer;
use App\Models\Space;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Fetch groomers – adjust query based on your needs/filters
        $groomers = Groomer::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('studio_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('sort'), function ($query) use ($request) {
                if ($request->sort === 'distance') {
                    $query->orderBy('distance');
                } elseif ($request->sort === 'lowest_price') {
                    $query->orderBy('price');
                } else {
                    $query->latest(); // recommended default
                }
            })
            ->get(); // or ->paginate(12) if you want pagination

        // Fetch spaces similarly
        $spaces = Space::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('venue_type'), function ($query) use ($request) {
                $query->where('venue_type', $request->venue_type);
            })
            ->get();

        // Return the view with both variables
        return view('search-results', compact('groomers', 'spaces'));
    }
}
