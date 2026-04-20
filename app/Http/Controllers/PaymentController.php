<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentController extends Controller
{
    // GET /api/payments
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $status  = $request->query('status');

        $query = Payment::with(['rental.customer', 'rental.iphone']);

        if ($status) $query->where('status', $status);

        $payments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $payments->items(),
            'meta'   => [
                'total'        => $payments->total(),
                'per_page'     => $payments->perPage(),
                'current_page' => $payments->currentPage(),
                'total_pages'  => $payments->lastPage(),
            ],
        ]);
    }

    // GET /api/payments/{id}
    public function show($id)
    {
        $payment = Payment::with(['rental.customer', 'rental.iphone'])->find($id);

        if (!$payment) {
            return response()->json([
                'status'  => 'error',
                'message' => "Payment dengan ID $id tidak ditemukan",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $payment,
        ]);
    }

    // POST /api/payments
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rental_id' => 'required|integer|exists:rentals,id',
            'amount'    => 'required|numeric|min:0',
            'method'    => 'required|in:cash,transfer,qris',
            'notes'     => 'nullable|string',
        ], [
            'rental_id.required' => 'ID rental wajib diisi',
            'rental_id.exists'   => 'Rental tidak ditemukan',
            'amount.required'    => 'Jumlah pembayaran wajib diisi',
            'amount.numeric'     => 'Jumlah pembayaran harus berupa angka',
            'method.required'    => 'Metode pembayaran wajib diisi',
            'method.in'          => 'Metode tidak valid. Pilih: cash, transfer, qris',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Cek apakah rental sudah punya payment
        $existing = Payment::where('rental_id', $request->rental_id)->first();
        if ($existing) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental ini sudah memiliki data pembayaran',
            ], 409);
        }

        $payment = Payment::create([
            'rental_id' => $request->rental_id,
            'amount'    => $request->amount,
            'method'    => $request->method,
            'status'    => 'pending',
            'notes'     => $request->notes,
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $payment->load('rental'),
        ], 201);
    }

    // PATCH /api/payments/{id}/confirm
    public function confirm(Request $request, $id)
    {
        $payment = Payment::with('rental')->find($id);

        if (!$payment) {
            return response()->json([
                'status'  => 'error',
                'message' => "Payment dengan ID $id tidak ditemukan",
            ], 404);
        }

        if ($payment->status === 'lunas') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pembayaran ini sudah lunas',
            ], 409);
        }

        if ($payment->status === 'gagal') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pembayaran sudah berstatus gagal, tidak bisa dikonfirmasi',
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'method' => 'required|in:cash,transfer,qris',
        ], [
            'method.required' => 'Metode pembayaran wajib diisi',
            'method.in'       => 'Metode tidak valid. Pilih: cash, transfer, qris',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $payment->update([
            'method'  => $request->method,
            'status'  => 'lunas',
            'paid_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $payment->fresh('rental'),
        ]);
    }

    // DELETE /api/payments/{id}
    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'status'  => 'error',
                'message' => "Payment dengan ID $id tidak ditemukan",
            ], 404);
        }

        if ($payment->status === 'lunas') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pembayaran yang sudah lunas tidak bisa dihapus',
            ], 409);
        }

        $payment->delete();

        return response()->json([
            'status' => 'success',
            'data'   => ['message' => 'Data pembayaran berhasil dihapus'],
        ]);
    }
}