<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(FeatureSeeder::class);
//        $this->call(JobSeeder::class);
//        $this->call(TaskSeeder::class);
//        $this->call(JobTaskSeeder::class);
//        $this->call(ContractorJobTask::class);
//        $this->call(BidContractorJobTaskSeeder::class);
        
    }
}
