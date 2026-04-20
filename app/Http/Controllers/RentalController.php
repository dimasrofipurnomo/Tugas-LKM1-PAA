<?php

namespace App\Http\Controllers;

use App\Models\Iphone;
use App\Models\Rental;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RentalController extends Controller
{
    // GET /api/rentals
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $status  = $request->query('status');

        $query = Rental::with(['customer', 'iphone', 'payment']);

        if ($status) $query->where('status', $status);

        $rentals = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $rentals->items(),
            'meta'   => [
                'total'        => $rentals->total(),
                'per_page'     => $rentals->perPage(),
                'current_page' => $rentals->currentPage(),
                'total_pages'  => $rentals->lastPage(),
            ],
        ]);
    }

    // GET /api/rentals/{id}
    public function show($id)
    {
        $rental = Rental::with(['customer', 'iphone', 'payment'])->find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => "Rental dengan ID $id tidak ditemukan",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $rental,
        ]);
    }

    // POST /api/rentals
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|integer|exists:users,id',
            'iphone_id' => 'required|integer|exists:iphones,id',
            'start_date'=> 'required|date|after_or_equal:today',
            'end_date'  => 'required|date|after:start_date',
        ], [
            'user_id.required' => 'ID user wajib diisi',
            'user_id.exists'   => 'User tidak ditemukan',
            'iphone_id.required'   => 'ID iPhone wajib diisi',
            'iphone_id.exists'     => 'iPhone tidak ditemukan',
            'start_date.required'  => 'Tanggal mulai wajib diisi',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini',
            'end_date.required'    => 'Tanggal selesai wajib diisi',
            'end_date.after'       => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Validasi customer
        $customer = Customer::find($request->user_id);
        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => "User dengan ID {$request->user_id} tidak ditemukan",
            ], 404);
        }

        // Validasi iphone
        $iphone = Iphone::find($request->iphone_id);
        if (!$iphone) {
            return response()->json([
                'status'  => 'error',
                'message' => "iPhone dengan ID {$request->iphone_id} tidak ditemukan",
            ], 404);
        }

        if ($iphone->status !== 'tersedia') {
            return response()->json([
                'status'  => 'error',
                'message' => "iPhone {$iphone->model} sedang {$iphone->status}, tidak bisa disewa",
            ], 409);
        }

        // Hitung durasi dan total harga
        $startDate    = Carbon::parse($request->start_date);
        $endDate      = Carbon::parse($request->end_date);
        $durationDays = $startDate->diffInDays($endDate);
        $totalPrice   = $durationDays * $iphone->price;

        DB::beginTransaction();
        try {
            $rental = Rental::create([
                'user_id'    => $request->user_id,
                'iphone_id'  => $request->iphone_id,
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'total_price'=> $totalPrice,
                'status'     => 'aktif',
            ]);

            // Update status iPhone jadi disewa
            $iphone->update(['status' => 'disewa']);

            // Buat payment otomatis
            Payment::create([
                'rental_id' => $rental->id,
                'amount'    => $totalPrice,
                'method'    => $request->input('payment_method', 'cash'),
                'status'    => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data'   => $rental->load(['customer', 'iphone', 'payment']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat rental: ' . $e->getMessage(),
            ], 500);
        }
    }

    // PATCH /api/rentals/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $rental = Rental::with('iphone')->find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => "Rental dengan ID $id tidak ditemukan",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:aktif,selesai,dibatalkan',
        ], [
            'status.required' => 'Status wajib diisi',
            'status.in'       => 'Status tidak valid. Pilih: aktif, selesai, dibatalkan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($rental->status === 'selesai' || $rental->status === 'dibatalkan') {
            return response()->json([
                'status'  => 'error',
                'message' => "Rental dengan status '{$rental->status}' tidak bisa diubah",
            ], 409);
        }

        DB::beginTransaction();
        try {
            $rental->update(['status' => $request->status]);

            if (in_array($request->status, ['selesai', 'dibatalkan'])) {
                $rental->iphone->update(['status' => 'tersedia']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data'   => $rental->fresh(['customer', 'iphone', 'payment']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengubah status: ' . $e->getMessage(),
            ], 500);
        }
    }

    // DELETE /api/rentals/{id}
    public function destroy($id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'status'  => 'error',
                'message' => "Rental dengan ID $id tidak ditemukan",
            ], 404);
        }

        if ($rental->status === 'aktif') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Rental yang masih aktif tidak bisa dihapus',
            ], 409);
        }

        $rental->delete();

        return response()->json([
            'status' => 'success',
            'data'   => ['message' => 'Rental berhasil dihapus'],
        ]);
    }
}