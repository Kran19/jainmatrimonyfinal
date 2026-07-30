<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class BulkWhatsAppController extends Controller
{
    /**
     * Display bulk WhatsApp message form.
     */
    public function index()
    {
        $users = User::whereNotNull('mobile')
            ->where('mobile', '!=', '')
            ->orderBy('full_name', 'asc')
            ->select('id', 'full_name', 'mobile')
            ->get();

        return view('admin.cms.bulk-whatsapp', compact('users'));
    }
}
