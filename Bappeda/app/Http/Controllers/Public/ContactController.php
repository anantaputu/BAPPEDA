<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|min:5',
        ]);

        Contact::create([
            'name' => $request->name,
            'message' => $request->message,
        ]);

        return Redirect::back()->with('success', 'Masukkan Anda berhasil terkirim!');
    }
}
