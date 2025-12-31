<?php

namespace NuxtIt\RP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('rp::settings.index');
    }

    /**
     * Store settings.
     */
    public function store(Request $request)
    {
        // This is a placeholder for future settings functionality
        // You can extend this to save settings to database or config file
        
        return redirect()->route('rp.settings.index')
            ->with('success', 'Settings saved successfully.');
    }
}

