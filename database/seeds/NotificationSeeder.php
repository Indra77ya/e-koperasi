<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notifications')->truncate();

        $users = User::all();
        $faker = Faker::create('id_ID');

        foreach ($users as $user) {
            $numNotifications = rand(3, 10);

            for ($i = 0; $i < $numNotifications; $i++) {
                $type = $faker->randomElement([
                    'App\Notifications\LoanApproved',
                    'App\Notifications\PaymentReceived',
                    'App\Notifications\NewMemberRegistered'
                ]);

                $data = [
                    'message' => $faker->sentence,
                    'action_url' => '/home',
                    'amount' => rand(100000, 1000000)
                ];

                DB::table('notifications')->insert([
                    'id' => Str::uuid(),
                    'type' => $type,
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $user->id,
                    'data' => json_encode($data),
                    'read_at' => (rand(0, 1) == 1) ? now() : null,
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
