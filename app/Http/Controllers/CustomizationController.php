<?php

namespace App\Http\Controllers;

use App\Models\Customization;
use Illuminate\Http\Request;

class CustomizationController extends Controller
{
    public function index()
    {
        return view('Customization.index');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'header_color' => 'nullable|string|max:255',
            'footer_color' => 'nullable|string|max:255',
            'text_color' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'header_background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'footer_background' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $Customization = Customization::firstOrCreate(
            ['id' => 1]
        );

        $Customization->update($validatedData);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $Customization->addMediaFromUpload('logo')->toMediaCollection('logo');
        }

        if ($request->hasFile('header_background')) {
            $Customization->addMediaFromUpload('header_background')->toMediaCollection('header_background');
        }

        if ($request->hasFile('footer_background')) {
            $Customization->addMediaFromUpload('footer_background')->toMediaCollection('footer_background');
        }

        return redirect()->back()
            ->with('success', 'Customization settings saved successfully.');
    }
}
