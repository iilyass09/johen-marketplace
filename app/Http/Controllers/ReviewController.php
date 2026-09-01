<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Simpan ulasan untuk satu order yang sudah sukses.
     * Satu order hanya boleh direview sekali (dibedakan per order_id).
     */
    public function store(Request $request, Order $order)
    {
        if (!in_array($order->status, ['success', 'processing'])) {
            throw ValidationException::withMessages(['order' => 'Pesanan belum berhasil diproses.']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $already = Review::where('order_id', $order->order_id)->exists();
        if ($already) {
            throw ValidationException::withMessages(['rating' => 'Ulasan untuk pesanan ini sudah pernah dikirim.']);
        }

        $review = Review::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'order_id' => $order->order_id,
            'email' => Auth::check() ? Auth::user()->email : ($order->email ?: null),
            'game' => $order->brand,
            'rating' => (int) $validated['rating'],
            'comment' => trim((string) ($validated['comment'] ?? '')) ?: null,
            'status' => 'approved',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas ulasan Anda!',
            'review' => $review,
        ]);
    }
}
