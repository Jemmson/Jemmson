<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Classes\Registration;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /**  @test */
    function should_save_access_token_data_to_table_if_the_software_type_is_quickbooks() {
        //
        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);

        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);


    }
}
