<?php

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            // ASSETS
            ['code' => '1101', 'name' => 'Kas', 'type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '1102', 'name' => 'Bank', 'type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '1103', 'name' => 'Piutang Pinjaman', 'type' => 'ASSET', 'normal_balance' => 'DEBIT'],
            ['code' => '1201', 'name' => 'Aset Tetap - Peralatan', 'type' => 'ASSET', 'normal_balance' => 'DEBIT'],

            // LIABILITIES
            ['code' => '2101', 'name' => 'Simpanan Anggota', 'type' => 'LIABILITY', 'normal_balance' => 'CREDIT'],
            ['code' => '2102', 'name' => 'Utang Usaha', 'type' => 'LIABILITY', 'normal_balance' => 'CREDIT'],

            // EQUITY
            ['code' => '3101', 'name' => 'Modal Awal', 'type' => 'EQUITY', 'normal_balance' => 'CREDIT'],
            ['code' => '3102', 'name' => 'Laba Ditahan', 'type' => 'EQUITY', 'normal_balance' => 'CREDIT'],

            // REVENUE
            ['code' => '4101', 'name' => 'Pendapatan Bunga Pinjaman', 'type' => 'REVENUE', 'normal_balance' => 'CREDIT'],
            ['code' => '4102', 'name' => 'Pendapatan Admin', 'type' => 'REVENUE', 'normal_balance' => 'CREDIT'],
            ['code' => '4103', 'name' => 'Pendapatan Denda', 'type' => 'REVENUE', 'normal_balance' => 'CREDIT'],

            // EXPENSES
            ['code' => '5101', 'name' => 'Beban Gaji', 'type' => 'EXPENSE', 'normal_balance' => 'DEBIT'],
            ['code' => '5102', 'name' => 'Beban Operasional', 'type' => 'EXPENSE', 'normal_balance' => 'DEBIT'],
            ['code' => '5103', 'name' => 'Beban Bunga Simpanan', 'type' => 'EXPENSE', 'normal_balance' => 'DEBIT'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(
                ['code' => $account['code']],
                $account
            );
        }
    }
}
