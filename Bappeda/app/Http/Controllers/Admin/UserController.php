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
            'users' => User::with('role')
                ->get()
                ->map(function ($user) {
                    return [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                        'role'  => $user->role?->nama_role,
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
        // proteksi: admin tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa menghapus akun sendiri');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }


}

