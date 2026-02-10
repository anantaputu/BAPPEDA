<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;    
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return inertia('Admin/Users/Index', [
            'users' => User::with('role') // Memuat relasi role
                ->get()
                ->map(function ($user) {
                    return [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                        'role'  => $user->role, // Kirim seluruh objek role, bukan hanya ID
                        'status_aktif' => $user->status_aktif,
                        'nama_depan'   => $user->nama_depan,
                        'nama_belakang'=> $user->nama_belakang,
                        'username'     => $user->username,
                    ];
                }),
        ]);
    }

    public function create() {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_depan'    => 'required|string|max:100',
            'nama_belakang' => 'nullable|string|max:100',
            'username'      => 'required|string|unique:users,username',
            'email'         => 'required|email|unique:users,email',
            'role_id'       => 'required|exists:roles,id_role',
            'password'      => 'required|min:6',
        ]);


        User::create([
            'name'          => trim($validated['nama_depan'].' '.$validated['nama_belakang']),
            'nama_depan'    => $validated['nama_depan'],
            'nama_belakang' => $validated['nama_belakang'],
            'username'      => $validated['username'],
            'email'         => $validated['email'],
            'role_id'       => $validated['role_id'],
            'password'      => $validated['password'], // cast 'hashed' akan kerja
            'status_aktif'  => true,
        ]);

        return redirect('/admin/users')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user'  => $user->load('role'),
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Proteksi: Jika user yang diedit adalah Admin, tolak akses
        if ($user->role?->nama_role === 'Admin') {
            return back()->with('error', 'Akun Admin tidak dapat diubah melalui menu ini.');
        }

        $validated = $request->validate([
            'nama_depan'    => 'required|string',
            'nama_belakang' => 'nullable|string',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role_id'       => 'required|exists:roles,id_role',
        ]);

        $user->update([
            'name'          => trim($validated['nama_depan'].' '.$validated['nama_belakang']),
            'nama_depan'    => $validated['nama_depan'],
            'nama_belakang' => $validated['nama_belakang'],
            'email'         => $validated['email'],
            'role_id'       => $validated['role_id'],
        ]);

        return redirect('/admin/users')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Proteksi 1: Admin tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa menghapus akun sendiri');
        }

        // Proteksi 2: User dengan Role Admin tidak bisa dihapus
        if ($user->role?->nama_role === 'Admin') {
            return back()->with('error', 'Akun Admin dilindungi dan tidak dapat dihapus.');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus');
    }


}

