<?php

namespace App\Http\Controllers;

use App\Models\Iphone;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class IphoneController extends Controller
{
    private const VALID_STATUSES    = ['tersedia', 'disewa', 'maintenance'];
    private const VALID_CONDITIONS  = ['baru', 'baik', 'cukup'];
    private const VALID_STORAGES    = ['64GB', '128GB', '256GB', '512GB'];

    // GET /api/iphones
    public function index(Request $request)
    {
        $perPage   = $request->query('per_page', 10);
        $status    = $request->query('status');
        $condition = $request->query('condition');
        $search    = $request->query('search');

        $query = Iphone::query();

        if ($status)    $query->where('status', $status);
        if ($condition) $query->where('condition', $condition);
        if ($search)    $query->where('model', 'like', "%$search%");

        $iphones = $query->orderBy('model')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $iphones->items(),
            'meta'   => [
                'total'        => $iphones->total(),
                'per_page'     => $iphones->perPage(),
                'current_page' => $iphones->currentPage(),
                'total_pages'  => $iphones->lastPage(),
            ],
        ]);
    }

    // GET /api/iphones/{id}
    public function show($id)
    {
        $iphone = Iphone::find($id);

        if (!$iphone) {
            return response()->json([
                'status'  => 'error',
                'message' => "iPhone dengan ID $id tidak ditemukan",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $iphone,
        ]);
    }

    // POST /api/iphones
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model'       => 'required|string|max:100',
            'storage'     => 'required|in:' . implode(',', self::VALID_STORAGES),
            'color'       => 'required|string|max:50',
            'condition'   => 'required|in:' . implode(',', self::VALID_CONDITIONS),
            'daily_price' => 'required|numeric|min:0',
        ], [
            'model.required'       => 'Model iPhone wajib diisi',
            'storage.required'     => 'Kapasitas storage wajib diisi',
            'storage.in'           => 'Storage tidak valid. Pilih: 64GB, 128GB, 256GB, 512GB',
            'color.required'       => 'Warna wajib diisi',
            'condition.required'   => 'Kondisi wajib diisi',
            'condition.in'         => 'Kondisi tidak valid. Pilih: baru, baik, cukup',
            'daily_price.required' => 'Harga sewa per hari wajib diisi',
            'daily_price.numeric'  => 'Harga harus berupa angka',
            'daily_price.min'      => 'Harga tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $iphone = Iphone::create([
            'model'       => $request->model,
            'storage'     => $request->storage,
            'color'       => $request->color,
            'condition'   => $request->condition,
            'daily_price' => $request->daily_price,
            'status'      => 'tersedia',
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $iphone,
        ], 201);
    }

    // PUT /api/iphones/{id}
    public function update(Request $request, $id)
    {
        $iphone = Iphone::find($id);

        if (!$iphone) {
            return response()->json([
                'status'  => 'error',
                'message' => "iPhone dengan ID $id tidak ditemukan",
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'model'       => 'required|string|max:100',
            'storage'     => 'required|in:' . implode(',', self::VALID_STORAGES),
            'color'       => 'required|string|max:50',
            'condition'   => 'required|in:' . implode(',', self::VALID_CONDITIONS),
            'daily_price' => 'required|numeric|min:0',
            'status'      => 'required|in:' . implode(',', self::VALID_STATUSES),
        ], [
            'model.required'       => 'Model iPhone wajib diisi',
            'storage.in'           => 'Storage tidak valid. Pilih: 64GB, 128GB, 256GB, 512GB',
            'condition.in'         => 'Kondisi tidak valid. Pilih: baru, baik, cukup',
            'daily_price.required' => 'Harga sewa per hari wajib diisi',
            'status.in'            => 'Status tidak valid. Pilih: tersedia, disewa, maintenance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $iphone->update($request->only([
            'model', 'storage', 'color', 'condition', 'daily_price', 'status',
        ]));

        return response()->json([
            'status' => 'success',
            'data'   => $iphone->fresh(),
        ]);
    }

    // DELETE /api/iphones/{id}
    public function destroy($id)
    {
        $iphone = Iphone::find($id);

        if (!$iphone) {
            return response()->json([
                'status'  => 'error',
                'message' => "iPhone dengan ID $id tidak ditemukan",
            ], 404);
        }

        if ($iphone->status === 'disewa') {
            return response()->json([
                'status'  => 'error',
                'message' => 'iPhone sedang disewa, tidak bisa dihapus',
            ], 409);
        }

        $iphone->delete(); // soft delete

        return response()->json([
            'status' => 'success',
            'data'   => ['message' => 'iPhone berhasil dihapus'],
        ]);
    }
}