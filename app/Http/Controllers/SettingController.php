<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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

    public function backup()
    {
        // PHP-based backup fallback since mysqldump might not be available
        $tables = DB::select('SHOW TABLES');

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="backup_ekoperasi_' . date('Y-m-d_H-i-s') . '.sql"',
        ];

        return response()->stream(function () use ($tables) {
            echo "-- E-Koperasi Database Backup\n";
            echo "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $tableObj) {
                $table = array_values((array)$tableObj)[0];

                echo "-- Table structure for table `$table`\n";
                echo "DROP TABLE IF EXISTS `$table`;\n";

                $createRow = DB::select("SHOW CREATE TABLE `$table`")[0];
                // Handle property capitalization diffs
                $createSql = $createRow->{'Create Table'} ?? $createRow->{'CREATE TABLE'};
                echo $createSql . ";\n\n";

                echo "-- Dumping data for table `$table`\n";

                // Use cursor to reduce memory usage for large tables
                $rows = DB::table($table)->cursor();

                $buffer = [];
                $limit = 100; // Bulk insert size

                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if ($value === null) return 'NULL';
                        return "'" . addslashes($value) . "'";
                    }, (array)$row);

                    $buffer[] = "(" . implode(", ", $values) . ")";

                    if (count($buffer) >= $limit) {
                         echo "INSERT INTO `$table` VALUES " . implode(", ", $buffer) . ";\n";
                         $buffer = [];
                    }
                }
                // Flush remaining buffer
                if (count($buffer) > 0) {
                     echo "INSERT INTO `$table` VALUES " . implode(", ", $buffer) . ";\n";
                }
                echo "\n";
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        }, 200, $headers);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required'
        ]);

        if (!$request->hasFile('backup_file')) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $file = $request->file('backup_file');

        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'sql') {
             return redirect()->back()->with('error', 'Format file harus .sql');
        }

        ini_set('memory_limit', '512M');
        set_time_limit(600);

        $path = $file->getRealPath();
        $content = file_get_contents($path);

        DB::disableQueryLog();
        DB::beginTransaction();
        try {
             DB::unprepared("SET FOREIGN_KEY_CHECKS=0;");

             // Robust SQL Parser to handle multi-statements
             // This iterates over the content character by character to safely split by ';'
             // while respecting single quotes to avoid splitting SQL strings containing semicolons.
             $len = strlen($content);
             $start = 0;
             $inQuote = false;

             for ($i = 0; $i < $len; $i++) {
                 $char = $content[$i];

                 // Toggle quote state (handling escaped quotes)
                 if ($char === "'" && ($i === 0 || $content[$i-1] !== '\\')) {
                     $inQuote = !$inQuote;
                 }

                 if ($char === ';' && !$inQuote) {
                     $query = substr($content, $start, $i - $start);
                     if (trim($query) !== '') {
                         DB::unprepared($query);
                     }
                     $start = $i + 1;
                 }
             }

             // Execute any remaining query
             if ($start < $len) {
                 $query = substr($content, $start);
                 if (trim($query) !== '') {
                     DB::unprepared($query);
                 }
             }

             DB::unprepared("SET FOREIGN_KEY_CHECKS=1;");

             DB::commit();
             return redirect()->back()->with('success', 'Database berhasil direstore.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal restore database: ' . $e->getMessage());
        }
    }
}
