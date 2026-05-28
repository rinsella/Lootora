<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offerwall;

class IntegrationGuideController extends Controller
{
    public function index()
    {
        $providers = Offerwall::orderBy('name')->get();
        return view('admin.integration-guide', compact('providers'));
    }
}
