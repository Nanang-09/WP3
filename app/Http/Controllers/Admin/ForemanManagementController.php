<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForemanManagementController extends Controller
{
    public function index()
    {
        $foremen = User::withCount(['assignedOrders', 'orderUpdates'])
            ->where('role', User::ROLE_FOREMAN)
            ->latest()
            ->get();

        return view('admin.foremen.index', compact('foremen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_FOREMAN,
        ]);

        return redirect()
            ->route('admin.foremen.index')
            ->with('success', 'Akun mandor berhasil dibuat.');
    }
}
