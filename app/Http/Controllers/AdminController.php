<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Daftar semua user
    public function index()
    {
        $users = User::all();
        return view('admin.index', compact('users'));
    }

    // Form edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }

    // Update user (ubah nama / role kalau ada)
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ]);


        $user = User::findOrFail($id);

        $data = [
            'name' => $request->name,
        ];

        // kalau password diisi, update juga
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.index')->with('success', 'User berhasil diperbarui.');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Biar admin nggak hapus dirinya sendiri
        if (auth()->id() == $user->id) {
            return redirect()->route('admin.index')->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.index')->with('success', 'User berhasil dihapus.');
    }
}
