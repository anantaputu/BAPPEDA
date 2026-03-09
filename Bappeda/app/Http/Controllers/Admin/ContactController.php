<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::query()
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Contacts/Index', [
            'contacts' => $contacts->items(),
            'pagination' => [
                'links' => $contacts->linkCollection(),
                'current_page' => $contacts->currentPage(),
                'total' => $contacts->total(),
                'from' => $contacts->firstItem(),
                'to' => $contacts->lastItem(),
            ],
        ]);
    }
}
