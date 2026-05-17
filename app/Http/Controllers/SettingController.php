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
            'periodic_admin_rate' => 'nullable|numeric|min:0',
            'periodic_admin_interval' => 'nullable|integer|min:1',
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

    public function backup()
    {
        $filename = 'backup_ekoperasi_' . date('Y-m-d_H-i-s') . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        try {
            $this->generateBackupZip($tempFile);
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        // Security check: only allow files from backups directory and with .sql or .zip extension
        if (strpos($filename, '..') !== false || (substr($filename, -4) !== '.sql' && substr($filename, -4) !== '.zip')) {
            abort(403);
        }

        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    private function generateBackupZip($zipPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Gagal membuka file ZIP untuk menulis.");
        }

        // 1. Add SQL Dump
        $sqlFile = tempnam(sys_get_temp_dir(), 'sql');
        $this->generateBackupSql($sqlFile);
        $zip->addFile($sqlFile, 'database.sql');

        // 2. Add Public Files (Images, Documents, etc.)
        $publicPath = storage_path('app/public');
        if (file_exists($publicPath)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publicPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                // Skip directories (they would be added automatically)
                if (!$file->isDir()) {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = 'files/' . substr($filePath, strlen($publicPath) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();
        unlink($sqlFile);
    }

    private function generateBackupSql($targetPath)
    {
        // PHP-based backup fallback since mysqldump might not be available
        $tables = DB::select('SHOW TABLES');
        $handle = fopen($targetPath, 'w');

        fwrite($handle, "-- E-Koperasi Database Backup\n");
        fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $tableObj) {
            $table = array_values((array)$tableObj)[0];

            fwrite($handle, "-- Table structure for table `$table`\n");
            fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");

            $createRow = DB::select("SHOW CREATE TABLE `$table`")[0];
            // Handle property capitalization diffs
            $createSql = $createRow->{'Create Table'} ?? $createRow->{'CREATE TABLE'};
            fwrite($handle, $createSql . ";\n\n");

            fwrite($handle, "-- Dumping data for table `$table`\n");

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
                     fwrite($handle, "INSERT INTO `$table` VALUES " . implode(", ", $buffer) . ";\n");
                     $buffer = [];
                }
            }
            // Flush remaining buffer
            if (count($buffer) > 0) {
                 fwrite($handle, "INSERT INTO `$table` VALUES " . implode(", ", $buffer) . ";\n");
            }
            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
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

        if ($extension !== 'sql' && $extension !== 'zip') {
             return redirect()->back()->with('error', 'Format file harus .sql atau .zip');
        }

        ini_set('memory_limit', '512M');
        set_time_limit(600);

        if ($extension === 'sql') {
            return $this->restoreSql($file->getRealPath());
        } else {
            return $this->restoreZip($file->getRealPath());
        }
    }

    private function restoreSql($path)
    {
        $content = file_get_contents($path);
        DB::disableQueryLog();
        DB::beginTransaction();
        try {
             DB::unprepared("SET FOREIGN_KEY_CHECKS=0;");

             $len = strlen($content);
             $start = 0;
             $inQuote = false;

             for ($i = 0; $i < $len; $i++) {
                 $char = $content[$i];

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

    private function restoreZip($zipPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== TRUE) {
            return redirect()->back()->with('error', 'Gagal membuka file ZIP.');
        }

        $tempDir = storage_path('app/temp_restore_' . time());
        mkdir($tempDir, 0755, true);
        $zip->extractTo($tempDir);
        $zip->close();

        $sqlFile = $tempDir . '/database.sql';
        if (!file_exists($sqlFile)) {
            $this->deleteDirectory($tempDir);
            return redirect()->back()->with('error', 'File database.sql tidak ditemukan dalam ZIP.');
        }

        // 1. Restore Database
        $restoreResult = $this->restoreSql($sqlFile);

        // Check if restoreSql was successful by checking session flash
        if (session('error')) {
            $this->deleteDirectory($tempDir);
            return $restoreResult;
        }

        // 2. Restore Files
        $filesSrc = $tempDir . '/files';
        if (file_exists($filesSrc)) {
            $publicPath = storage_path('app/public');

            // Clear existing files
            $this->deleteDirectory($publicPath, true); // keep the directory itself

            // Copy files
            $this->copyDirectory($filesSrc, $publicPath);
        }

        $this->deleteDirectory($tempDir);
        return redirect()->back()->with('success', 'Database dan file berhasil direstore.');
    }

    private function deleteDirectory($dir, $keepRoot = false)
    {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir)) return unlink($dir);

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
        }

        return $keepRoot ? true : rmdir($dir);
    }

    private function copyDirectory($src, $dst)
    {
        if (!file_exists($dst)) mkdir($dst, 0755, true);
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'confirm_reset' => 'required|string',
            'reset_options' => 'required|array',
        ]);

        if (strtoupper($request->confirm_reset) !== 'RESET') {
            return redirect()->back()->with('error', 'Konfirmasi reset tidak valid. Silakan ketik RESET.');
        }

        // 1. Auto Backup
        $filename = 'pre_reset_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $directory = storage_path('app/backups');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $backupPath = $directory . '/' . $filename;
        $this->generateBackupZip($backupPath);

        $options = $request->reset_options;

        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            if (in_array('transactions', $options)) {
                DB::table('pinjaman')->truncate();
                DB::table('pinjaman_angsuran')->truncate();
                DB::table('setoran')->truncate();
                DB::table('penarikan')->truncate();
                DB::table('riwayat_tabungan')->truncate();
                DB::table('bunga_tabungan')->truncate();
                DB::table('journal_entries')->truncate();
                DB::table('journal_items')->truncate();
                DB::table('penagihan_lapangan')->truncate();
                DB::table('penagihan_log')->truncate();
                DB::table('jaminan')->truncate();
                DB::table('notifications')->truncate();
                DB::table('nasabah_loans')->truncate();
                // Reset savings balances
                DB::table('tabungan')->update(['saldo' => 0]);
            }

            if (in_array('members', $options)) {
                DB::table('anggota')->truncate();
                DB::table('nasabahs')->truncate();
                DB::table('tabungan')->truncate();
            }

            if (in_array('coa', $options)) {
                DB::table('chart_of_accounts')->truncate();
            }

            if (in_array('users', $options)) {
                // Delete users except admins
                DB::table('users')->where('role', '!=', 'admin')->where('email', '!=', 'admin@example.com')->delete();
            }

            if (in_array('settings', $options)) {
                // Delete settings except app_version and app_update_notes
                DB::table('settings')->whereNotIn('key', ['app_version', 'app_update_notes'])->delete();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            return redirect()->back()->with([
                'success' => 'Sistem berhasil direset. File backup akan segera di download',
                'backup_to_download' => $filename
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mereset sistem: ' . $e->getMessage());
        }
    }
}
