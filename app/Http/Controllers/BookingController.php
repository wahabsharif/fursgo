<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['petOwner', 'pets'])
            ->latest()
            ->paginate(15);

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_ids'        => 'required|array|min:1',
            'pet_ids.*'      => 'exists:pet_details,id',
            'time'           => 'required|string|max:50',
            'date'           => 'required|date',
            'service'        => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'visit_type'     => 'required|string|max:64',
            'booking_status' => 'sometimes|in:pending,confirmed,completed,cancelled',
        ]);

        $booking = Booking::create([
            'pet_owner_id'   => Auth::id(),
            'time'           => $validated['time'],
            'date'           => $validated['date'],
            'service'        => $validated['service'],
            'amount'         => $validated['amount'],
            'visit_type'     => $validated['visit_type'],
            'booking_status' => $validated['booking_status'] ?? 'pending',
        ]);

        $booking->pets()->attach($validated['pet_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'data'    => $booking->load('pets'),
        ], 201);
    }

    public function show(Booking $booking)
    {
        return response()->json($booking->load(['petOwner', 'pets']));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'pet_ids'        => 'sometimes|array|min:1',
            'pet_ids.*'      => 'exists:pet_details,id',
            'time'           => 'sometimes|string|max:50',
            'date'           => 'sometimes|date',
            'service'        => 'sometimes|string|max:255',
            'amount'         => 'sometimes|numeric|min:0',
            'visit_type'     => 'sometimes|string|max:64',
            'booking_status' => 'sometimes|in:pending,confirmed,completed,cancelled',
        ]);

        $attributes = collect($validated)->except('pet_ids')->toArray();
        if (!empty($attributes)) {
            $booking->fill($attributes);
            $booking->save();
        }

        if (isset($validated['pet_ids'])) {
            $booking->pets()->sync($validated['pet_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully.',
            'data'    => $booking->load('pets'),
        ]);
    }

    public function destroy(Booking $booking)
    {
        $booking->pets()->detach();
        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully.',
        ]);
    }

    public function accept(Booking $booking)
    {
        $booking->update(['booking_status' => 'confirmed']);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed.',
            'data'    => $booking->fresh()->load('pets'),
        ]);
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['booking_status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled.',
            'data'    => $booking->fresh()->load('pets'),
        ]);
    }
}
