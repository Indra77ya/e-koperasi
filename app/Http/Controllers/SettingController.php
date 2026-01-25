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
        // Define allowed keys and validation rules (Security Enhancement)
        $rules = [
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:1000',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:100',
            'default_interest_rate' => 'nullable|numeric|min:0',
            'default_admin_fee' => 'nullable|numeric|min:0',
            'default_penalty' => 'nullable|numeric|min:0',
            'loan_limit' => 'nullable|numeric|min:0',
            'col_dpk_days' => 'nullable|integer|min:0',
            'col_kl_days' => 'nullable|integer|min:0',
            'col_diragukan_days' => 'nullable|integer|min:0',
            'col_macet_days' => 'nullable|integer|min:0',
            'savings_interest_rate' => 'nullable|numeric|min:0',
            'coa_cash' => 'nullable|exists:chart_of_accounts,code',
            'coa_interest_expense' => 'nullable|exists:chart_of_accounts,code',
            'coa_savings' => 'nullable|exists:chart_of_accounts,code',
            'coa_receivable' => 'nullable|exists:chart_of_accounts,code',
            'coa_revenue_interest' => 'nullable|exists:chart_of_accounts,code',
            'coa_revenue_admin' => 'nullable|exists:chart_of_accounts,code',
            'coa_revenue_penalty' => 'nullable|exists:chart_of_accounts,code',
            'notification_due_date_threshold' => 'nullable|integer|min:0',
            'app_version' => 'nullable|string|max:20',
            'app_update_notes' => 'nullable|string|max:1000',
        ];

        $data = $request->validate($rules);

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

    public function systemUpdate(Request $request)
    {
        // Increase memory limit and execution time for update process
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        try {
            // Execute git pull
            $output = [];
            $returnVar = 0;
            exec('git pull origin master 2>&1', $output, $returnVar);

            if ($returnVar !== 0) {
                // If master fails, try main (common branch naming difference)
                $output2 = [];
                $returnVar2 = 0;
                exec('git pull origin main 2>&1', $output2, $returnVar2);

                if ($returnVar2 !== 0) {
                    return redirect()->back()->with('error', 'Gagal melakukan update (Git Pull Failed). Output: ' . implode("\n", $output));
                } else {
                    $output = $output2;
                }
            }

            // Optional: Run migrations if git pull successful
            // Artisan::call('migrate', ['--force' => true]);

            // Combine output to string to search
            $outputString = implode("\n", $output);

            if (stripos($outputString, 'Already up to date') !== false) {
                return redirect()->back()->with('success', 'Anda sudah berada di versi terbaru.');
            }

            // Retrieve new version
            $newVersion = null;
            $tagOutput = [];
            $tagReturn = 0;
            exec('git describe --tags 2>&1', $tagOutput, $tagReturn);
            if ($tagReturn === 0 && !empty($tagOutput)) {
                $newVersion = trim($tagOutput[0]);
            } else {
                $hashOutput = [];
                $hashReturn = 0;
                exec('git rev-parse --short HEAD 2>&1', $hashOutput, $hashReturn);
                if ($hashReturn === 0 && !empty($hashOutput)) {
                    $newVersion = trim($hashOutput[0]);
                }
            }

            if ($newVersion) {
                // Update database setting
                Setting::set('app_version', $newVersion);
                $message = "Update berhasil. Versi sistem saat ini: " . $newVersion;
            } else {
                $message = 'Sistem berhasil diperbarui. Log: ' . implode(" ", $output);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat update: ' . $e->getMessage());
        }
    }
}
