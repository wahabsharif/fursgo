<?php

namespace App\Http\Controllers;

use App\Models\GroomerSpacerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GroomerSpacerProfileController extends Controller
{
    /**
     * Store or update groomer spacer profile for the authenticated user
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'nullable|string|min:8',
                'user_type' => 'nullable|string|max:255',
                'account_type' => 'nullable|string|max:255',
                'select_location_type' => 'nullable|array',
                'business_details' => 'nullable|array',
                'freelance_details' => 'nullable|array',
                'business_basics' => 'nullable|array',
                'groomer_business_profile' => 'nullable|array',
                'legal_policy_agreements' => 'boolean',
                'information_accuracy_confirmed' => 'nullable|boolean',
            ]);

            // No boolean conversion needed for user_type and account_type as they are now strings
            $validated['legal_policy_agreements'] = $request->has('legal_policy_agreements') || $request->input('legal_policy_agreements') === 'true' || $request->input('legal_policy_agreements') === '1';

            // Hash password if provided
            if (! empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $profile = GroomerSpacerProfile::updateOrCreate(
                ['user_id' => Auth::id()],
                array_merge($validated, ['user_id' => Auth::id()])
            );

            // Store profile ID in session
            session(['groomer_spacer_profile_id' => $profile->id]);

            // Return JSON for AJAX requests, redirect for traditional form submissions
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Groomer spacer profile saved successfully!',
                    'data' => $profile,
                ]);
            }

            return redirect()->back()->with('success', 'Groomer spacer profile saved successfully!');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to save groomer spacer profile: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get groomer spacer profile for the authenticated user
     */
    public function show()
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $profile = GroomerSpacerProfile::where('user_id', Auth::id())->first();

        if (! $profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    /**
     * Get groomer spacer profile for the authenticated user (for use in controllers/views)
     */
    public static function getProfileForUser()
    {
        if (! Auth::check()) {
            return null;
        }

        return GroomerSpacerProfile::where('user_id', Auth::id())->first();
    }

    /**
     * Update groomer spacer profile for the authenticated user
     */
    public function update(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Delete groomer spacer profile for the authenticated user
     */
    public function destroy()
    {
        GroomerSpacerProfile::where('user_id', Auth::id())->delete();
        session()->forget(['groomer_spacer_profile_id']);

        return redirect()->back()->with('success', 'Groomer spacer profile deleted successfully!');
    }
}
