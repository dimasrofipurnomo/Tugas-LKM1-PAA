<?php
 
namespace App\Http\Controllers;
 
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
 
class CustomerController extends Controller
{
    // GET /api/customers
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search  = $request->query('search');
 
        $query = Customer::query();
 
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }
 
        $customers = $query->orderBy('id')->paginate($perPage);
 
        return response()->json([
            'status' => 'success',
            'data' => $customers->items(),
            'meta' => [
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'total_pages' => $customers->lastPage(),
            ],
        ]);
    }
 
    // GET /api/customers/{id}
    public function show($id)
    {
        $customer = Customer::with('rentals')->find($id);
 
        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => "Customer dengan ID $id tidak ditemukan",
            ], 404);
        }
 
        return response()->json([
            'status' => 'success',
            'data'   => $customer,
        ]);
    }
 
    // POST /api/customers
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:150',
            'email'   => 'required|email|unique:customers,email',
            'phone'   => 'required|string|max:20',
            'address' => 'nullable|string',
        ], [
            'name.required'  => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'email.unique'   => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }
 
        $customer = Customer::create($request->only(['name', 'email', 'phone', 'address']));
 
        return response()->json([
            'status' => 'success',
            'data'   => $customer,
        ], 201);
    }
 
    // PUT /api/customers/{id}
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
 
        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => "Customer dengan ID $id tidak ditemukan",
            ], 404);
        }
 
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:150',
            'email'   => "required|email|unique:customers,email,$id",
            'phone'   => 'required|string|max:20',
            'address' => 'nullable|string',
        ], [
            'name.required'  => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'email.unique'   => 'Email sudah digunakan customer lain',
            'phone.required' => 'Nomor telepon wajib diisi',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }
 
        $customer->update($request->only(['name', 'email', 'phone', 'address']));
 
        return response()->json([
            'status' => 'success',
            'data'   => $customer->fresh(),
        ]);
    }
 
    // DELETE /api/customers/{id}
    public function destroy($id)
    {
        $customer = Customer::find($id);
 
        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => "Customer dengan ID $id tidak ditemukan",
            ], 404);
        }
 
        // Cek apakah customer masih punya rental aktif
        $activeRental = $customer->rentals()->where('status', 'aktif')->exists();
        if ($activeRental) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer masih memiliki rental aktif, tidak bisa dihapus',
            ], 409);
        }
 
        $customer->delete(); // soft delete
 
        return response()->json([
            'status' => 'success',
            'data'   => ['message' => 'Customer berhasil dihapus'],
        ]);
    }
}