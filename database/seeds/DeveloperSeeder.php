<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Developer;
use App\DeveloperAdmin;
use App\User;
use App\UnitType;
use App\Unit;
use App\DefectType;
use App\ProjectCase;
use App\ClerkOfWork;
use App\Constants\CaseStatus;
use App\Project;

class DeveloperSeeder extends Seeder
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
            $developer = Developer::create([
                'name' => $faker->name,
                'created_by' => User::find(1)->id,
            ]);

            foreach (range(1, 5) as $index) {

                $cow = User::create([
                    'name' => $faker->name,
                    'password' => Hash::make('cow'),
                    'email' => $faker->companyEmail,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'verified' => '1',
                    'change_password' => '1',
                ]);

                $cow->assignRole('cow');

                ClerkOfWork::create([
                    'user_id' => $cow->id,
                    'developer_id' => $developer->id,
                    'created_by' => 1,
                ]);


                $developerAdmin = User::create([
                    'name' => $faker->name,
                    'password' => Hash::make('developer'),
                    'email' => $faker->companyEmail,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'verified' => '1',
                    'change_password' => '1',
                ]);

                $developerAdmin->assignRole('dev-admin');

                DeveloperAdmin::create([
                    'user_id' => $developerAdmin->id,
                    'developer_id' => $developer->id,
                    'created_by' => 1,
                ]);
            }

            foreach (range(1, 2) as $index) {
                $project = Project::create([
                    'developer_id' => $developer->id,
                    'name' => $faker->name,
                    'address' => $faker->streetAddress,
                    'address2' => $faker->streetName,
                    'address3' => $faker->streetSuffix,
                    'status' => 'active',
                    'created_by' => 1,
                    ]);
                    
                foreach (range(1, 2) as $index) {
                    DefectType::create([
                        'title' => 'defect',
                        'details' =>  'defect details',
                        'is_custom' => 1,
                        'developer_id' => $developer->id,
                        'created_by' => User::find(rand(2,9))->id,

                    ]);

                    foreach (range(1, 2) as $index) {
                        $unit_type = UnitType::create([
                            'name' => $faker->name,
                            'project_id' => $project->id,
                            'created_by' => User::find(rand(2,9))->id,
                            ]);
                            
                            foreach (range(1,2) as $index) {
                                $unit = Unit::create([
                                    'project_id' => $project->id,
                                    'unit_type_id' => $unit_type->id,
                                    'unit_no' => $faker->buildingNumber,
                                    'created_by' => User::find(rand(2,9))->id,
                            ]);
                            
                            foreach (range(1,3) as $index) {
                                ProjectCase::create([
                                    'title' => $faker->name,
                                    'status' => CaseStatus::OPEN,
                                    'ref_no' => $faker->bankAccountNumber,
                                    'unit_id' => $unit->id,
                                    'project_id' => $project->id,
                                    'developer_id' => $developer->id,
                                    'created_by' => User::find(rand(2,9))->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
