<?php

namespace Tests\Feature;

use App\Classes\Quickbooks\FakeQuickbooksGateway;
use App\Classes\Quickbooks\QuickbooksGateway;
use App\Quickbook;
use Tests\TestCase;
use App\User;
use App\Contractor;
use App\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskControllerTest extends TestCase
{

    use DatabaseMigrations;


}
