<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class ReferensiController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.referensi.index', compact('settings'));
    }
}
