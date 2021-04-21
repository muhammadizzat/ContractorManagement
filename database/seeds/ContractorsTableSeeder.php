<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

use App\Contractor;
use App\User;

class ContractorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 2) as $index) {

        $contractor = User::create([
            'name' => $faker->name,
            'password' => Hash::make('cow'),
            'email' => $faker->companyEmail,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'verified' => '1',
            'change_password' => '1',
        ]);

        $contractor->assignRole('contractor');

        Contractor::create([
            'user_id' => $contractor->id,
            'contact_no' => '1-300-88-2525',
            'status' => 1,
            'address' => 'home',
            'created_at' => now(),
            'updated_at' => now(),
            'created_by'=> 1
        ]);
        }
    }
}
