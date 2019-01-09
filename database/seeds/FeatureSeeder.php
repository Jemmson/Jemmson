<?php

use Illuminate\Database\Seeder;
use App\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $data = [
            'name' => 'quickbooks',
            'on' => 1
        ];

        Feature::create($data);
    }
}
