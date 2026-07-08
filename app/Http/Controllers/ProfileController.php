<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'current_password.required_with' => 'Password saat ini wajib diisi jika ingin mengganti password baru.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];

        if ($request->filled('new_password')) {
            // Check current password if user has a password set
            if ($user->password && !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
