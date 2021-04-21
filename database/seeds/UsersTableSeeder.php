<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = App\User::create([
            'name' => 'Super Admin',
            'password' => Hash::make('superadmin'),
            'email' => 'superadmin@linkzzapp.com',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'verified' => '1',
            'change_password' => '1',
        ]);

        $superadmin->assignRole('super-admin');
    }
}
