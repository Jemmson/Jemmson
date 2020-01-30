<?php

namespace Tests\Unit;

use App\Http\Controllers\StripeGatewayController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Feature\Setup;
use Tests\Feature\Traits\JobTaskTrait;
use Tests\Feature\Traits\JobTrait;
use Tests\Feature\Traits\TaskTrait;
use Tests\Feature\Traits\UserTrait;
use Tests\Feature\Traits\UtilitiesTrait;

class StripeGatewayTest extends TestCase
{
    use WithFaker;
    use UtilitiesTrait;
    use Setup;
    use UserTrait;
    use JobTaskTrait;
    use TaskTrait;
    use JobTrait;
    use RefreshDatabase;

    /**  @test */
    function that_all_parties_are_in_the_same_region() {
        //
        $customer = $this->createCustomer();
        $general = $this->createContractor();
        $sub = $this->createContractor();

        $this->createLocation($customer->id);
        $this->createLocation($general->id);
        $this->createLocation($sub->id);

        $sgc = new StripeGatewayController();

        $this->assertEquals(true, $sgc->allPartiesInSameRegion($customer, $general, $sub));
    }
}
