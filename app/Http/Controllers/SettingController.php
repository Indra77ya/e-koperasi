<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Fetch all settings as key-value pair
        $settings = Setting::all()->pluck('value', 'key');

        // Fetch COAs for dropdowns
        $coas = ChartOfAccount::orderBy('code')->get();

        return view('settings.index', compact('settings', 'coas'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'company_logo']);

        // Handle File Upload
        if ($request->hasFile('company_logo')) {
            $request->validate([
                'company_logo' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            $file = $request->file('company_logo');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            // Store in storage/app/public/uploads
            $path = $file->storeAs('uploads', $fileName, 'public');

            // Save the path that can be used with asset()
            // e.g., 'storage/uploads/logo_123.png'
            Setting::set('company_logo', 'storage/' . $path);
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
