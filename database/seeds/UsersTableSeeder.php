<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Default User (from README)
        $petugas = User::where('email', 'ekoperasi@gmail.com')->first();
        if (!$petugas) {
            User::create([
                'name' => 'Petugas E-Koperasi',
                'email' => 'ekoperasi@gmail.com',
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'role' => 'petugas',
            ]);
        } else {
            $petugas->update(['role' => 'petugas']);
        }

        // 2. Administrator User
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        } else {
            $admin->update(['role' => 'admin']);
        }

        // 3. Random Staff Users
        factory(User::class, 4)->create();
    }
}
