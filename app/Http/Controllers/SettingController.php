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
        $data = $request->except(['_token', 'company_logo', 'front_background']);

        // Handle Company Logo Upload
        if ($request->hasFile('company_logo')) {
            $request->validate([
                'company_logo' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            $file = $request->file('company_logo');
            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $fileName, 'public');
            Setting::set('company_logo', 'storage/' . $path);
        }

        // Handle Front Background Upload
        if ($request->hasFile('front_background')) {
            $request->validate([
                'front_background' => 'image|mimes:jpeg,png,jpg|max:1024',
            ]);

            $file = $request->file('front_background');
            $fileName = 'bg_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $fileName, 'public');
            Setting::set('front_background', 'storage/' . $path);
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function removeLogo()
    {
        $path = Setting::get('company_logo');
        if ($path) {
            // Remove 'storage/' prefix for Storage facade
            $relativePath = str_replace('storage/', 'public/', $path);
            if (Storage::exists($relativePath)) {
                Storage::delete($relativePath);
            }
            Setting::set('company_logo', null);
        }
        return redirect()->back()->with('success', 'Logo berhasil dihapus.');
    }

    public function removeBackground()
    {
        $path = Setting::get('front_background');
        if ($path) {
            // Remove 'storage/' prefix for Storage facade
            $relativePath = str_replace('storage/', 'public/', $path);
            if (Storage::exists($relativePath)) {
                Storage::delete($relativePath);
            }
            Setting::set('front_background', null);
        }
        return redirect()->back()->with('success', 'Background berhasil dihapus.');
    }
}
