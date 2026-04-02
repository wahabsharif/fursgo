<?php

namespace App\Http\Controllers;

use App\Models\PetDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetDetailController extends Controller
{
    /**
     * Store or update pet details for the authenticated user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|string',
            'birthday' => 'required|date',
            'pet_type' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'sex' => 'required|string|in:male,female',
            'weight' => 'required|numeric|min:0|max:999.99',
            'notes' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:500',
            'home_address_toggled' => 'boolean',
        ]);

        // Convert checkbox to boolean
        $validated['home_address_toggled'] = $request->has('home_address_toggled') || $request->input('home_address_toggled') === 'true';

        $petDetail = PetDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            array_merge($validated, ['user_id' => Auth::id()])
        );

        // Store in session for immediate display
        session(['pet_details' => $petDetail]);

        return redirect()->back()->with('success', 'Pet details saved successfully!');
    }

    /**
     * Get pet details for the authenticated user (for use in controllers/views)
     */
    public static function getPetDetailsForUser()
    {
        if (!Auth::check()) {
            return null;
        }

        // First check session for recently saved data
        if (session()->has('pet_details')) {
            return session('pet_details');
        }

        return PetDetail::where('user_id', Auth::id())->first();
    }

    /**
     * Delete pet details for the authenticated user
     */
    public function destroy()
    {
        PetDetail::where('user_id', Auth::id())->delete();
        session()->forget('pet_details');

        return redirect()->back()->with('success', 'Pet details deleted successfully!');
    }
}
