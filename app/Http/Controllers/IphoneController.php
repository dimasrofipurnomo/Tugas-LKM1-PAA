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
        $kondisi   = $request->query('kondisi');
        $search    = $request->query('search');

        $query = Iphone::query();

        if ($status)  $query->where('status', $status);
        if ($kondisi) $query->where('kondisi', $kondisi);
        if ($search)  $query->where('model', 'like', "%$search%");

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
            'model'   => 'required|string|max:100',
            'storage' => 'required|in:' . implode(',', self::VALID_STORAGES),
            'color'   => 'required|string|max:50',
            'kondisi' => 'required|in:' . implode(',', self::VALID_CONDITIONS),
            'price'   => 'required|numeric|min:0',
        ], [
            'model.required'   => 'Model iPhone wajib diisi',
            'storage.required' => 'Kapasitas storage wajib diisi',
            'storage.in'       => 'Storage tidak valid. Pilih: 64GB, 128GB, 256GB, 512GB',
            'color.required'   => 'Warna wajib diisi',
            'kondisi.required' => 'Kondisi wajib diisi',
            'kondisi.in'       => 'Kondisi tidak valid. Pilih: baru, baik, cukup',
            'price.required'   => 'Harga sewa wajib diisi',
            'price.numeric'    => 'Harga harus berupa angka',
            'price.min'        => 'Harga tidak boleh negatif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $iphone = Iphone::create([
            'model'   => $request->model,
            'storage' => $request->storage,
            'color'   => $request->color,
            'kondisi' => $request->kondisi,
            'price'   => $request->price,
            'status'  => 'tersedia',
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
            'model'   => 'required|string|max:100',
            'storage' => 'required|in:' . implode(',', self::VALID_STORAGES),
            'color'   => 'required|string|max:50',
            'kondisi' => 'required|in:' . implode(',', self::VALID_CONDITIONS),
            'price'   => 'required|numeric|min:0',
            'status'  => 'required|in:' . implode(',', self::VALID_STATUSES),
        ], [
            'model.required'   => 'Model iPhone wajib diisi',
            'storage.in'       => 'Storage tidak valid. Pilih: 64GB, 128GB, 256GB, 512GB',
            'kondisi.in'       => 'Kondisi tidak valid. Pilih: baru, baik, cukup',
            'price.required'   => 'Harga sewa wajib diisi',
            'price.numeric'    => 'Harga harus berupa angka',
            'price.min'        => 'Harga tidak boleh negatif',
            'status.in'        => 'Status tidak valid. Pilih: tersedia, disewa, maintenance',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $iphone->update($request->only([
            'model', 'storage', 'color', 'kondisi', 'price', 'status',
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