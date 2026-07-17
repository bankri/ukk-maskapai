<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingReview;
use App\Models\BookingStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($booking, $validated, $request) {
            $booking = Booking::query()
                ->with(['payment', 'review'])
                ->lockForUpdate()
                ->findOrFail($booking->id);

            if (! $booking->completed_at || $booking->payment?->payment_status !== 'paid') {
                throw ValidationException::withMessages([
                    'rating' => 'Rating hanya dapat diberikan untuk perjalanan yang telah selesai dan terbayar.',
                ]);
            }

            if ($booking->review) {
                throw ValidationException::withMessages([
                    'rating' => 'Booking ini sudah memiliki rating.',
                ]);
            }

            BookingReview::create([
                'booking_id' => $booking->id,
                'user_id' => $request->user()->id,
                'rating' => $validated['rating'],
                'comment' => filled($validated['comment'] ?? null) ? trim($validated['comment']) : null,
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'changed_by' => $request->user()->id,
                'from_status' => 'completed',
                'to_status' => 'reviewed',
                'note' => 'User memberikan rating '.$validated['rating'].' dari 5.',
            ]);
        }, 3);

        return back()->with('success', 'Terima kasih. Rating perjalanan berhasil disimpan.');
    }
}
