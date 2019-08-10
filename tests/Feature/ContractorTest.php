<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Contractor;
use App\User;
use App\QuickbooksContractor;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ContractorTest extends TestCase
{

    use Setup;
    use RefreshDatabase;

    /**  @test */
    function sustract_one_free_job_from_a_contractor_if_the_contractor_has_a_postive_number_of_free_jobs_left()
    {

        // add a contractor to the site
        $contractor = factory(Contractor::class)->create();

        // check the number of jobs the contractor has in the site
        $this->assertEquals($contractor->numberOfJobsLeft(), 5);

        // initiate a bid and then check that there is one less free job in the database
        $contractor->subtractFreeJob();

        $this->assertEquals(4, $contractor->numberOfJobsLeft());

    }

    /**  @test */
    function do_not_sustract_one_free_job_from_a_contractor_if_the_contractor_does_not_have_a_postive_number_of_free_jobs_left()
    {

        // add a contractor to the site
        $contractor = factory(Contractor::class)->create([
            'free_jobs' => 0
        ]);

        // check the number of jobs the contractor has in the site
        $this->assertEquals(0, $contractor->numberOfJobsLeft());

        // initiate a bid and then check that there is one less free job in the database
        $contractor->subtractFreeJob();

        $this->assertEquals(0, $contractor->numberOfJobsLeft());

    }

    /**  @test */
    function throw_error_if_contractor_tries_to_initiate_a_bid_but_is_not_subscribed_and_has_no_free_jobs()
    {

        $user = factory(User::class)->create([
            'current_billing_plan' => null
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0
        ]);

        $this->assertEquals(false, $contractor->canCreateNewJob());
    }

    /**  @test */
    function a_contractor_can_create_a_new_job_if_he_is_not_subscribed_but_has_free_jobs_left()
    {

        $user = factory(User::class)->create([
            'current_billing_plan' => null
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 1
        ]);

        $this->assertEquals(true, $contractor->canCreateNewJob());
    }

    /**  @test */
    function a_contractor_can_create_a_new_job_if_he_is_subscribed_but_has_no_free_jobs_left()
    {

        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0
        ]);

        $this->assertEquals(true, $contractor->canCreateNewJob());
    }

    /**  @test */
    function if_contractor_uses_accounting_software_then_it_should_be_recorded_in_the_contractors_table()
    {
        //
        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);
        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);

        $this->assertDatabaseHas('contractors', [
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);
    }

    /**  @test */
    function must_be_able_to_add_what_quickbooks_as_the_kind_of_software_the_contractor_is_using()
    {
        //

        $user = factory(User::class)->create([
            'current_billing_plan' => 'basic_monthly'
        ]);

        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 0,
            'accounting_software' => 'quickBooks'
        ]);

        $softwareName = 'quickbooks';

        $contractor->usesAccountingSoftware($softwareName);

        $this->assertDatabaseHas('contractors', [
            'accounting_software' => 'quickBooks',
            'id' => $contractor->id
        ]);
    }

    /**  @test */
    function contractorShouldBeAbleToDetermineWhatKindOfAccountingSoftwareTheyAreUsing()
    {
        //

        $user = factory(User::class)->create();

        $contractor = factory(Contractor::class)->create([
            'user_id' => $user->id,
            'free_jobs' => 100,
            'accounting_software' => 'quickBooks'
        ]);

        $softwareName = 'quickBooks';

        $contractor->usesAccountingSoftware($softwareName);

        $this->assertEquals('quickBooks', $contractor->checkAccountingSoftware());

    }


    /**  @test */
    function make_sure_a_correctly_formatted_array_of_subs_is_returned_in_getAllQuickbookCompaniesAndFormattedSubs()
    {
        //

        $this->withExceptionHandling();

        $user = factory(User::class)->create();

        factory(QuickbooksContractor::class)->create([
            "quickbooks_id" => 7,
            "contractor_id" => $user->id,
            "sub_contractor_id" => 7,
            "company_name" => "Freeman Sporting Goods",
            "given_name" => "Kirby",
            "middle_name" => "NULL",
            "family_name" => "Freeman",
            "fully_qualified_name" => "Freeman Sporting Goods",
            "primary_phone" => "6505550987",
            "primary_email_addr" => null,
            "created_at" => "2019-05-05 01:17:28",
            "updated_at" => "2019-05-05 01:17:28"
        ]);

        factory(QuickbooksContractor::class)->create([
            "quickbooks_id" => 8,
            "contractor_id" => 1,
            "sub_contractor_id" => 8,
            "company_name" => "Freeman Sporting Goods",
            "given_name" => "Sasha",
            "middle_name" => "NULL",
            "family_name" => "Tillou",
            "fully_qualified_name" => "Freeman Sporting Goods =>0969 Ocean View Road",
            "primary_phone" => "4155559933",
            "primary_email_addr" => "stillou@freeman.com",
            "created_at" => "2019-05-05 01:17:28",
            "updated_at" => "2019-05-05 01:17:28"
        ]);

        factory(QuickbooksContractor::class)->create([
            "quickbooks_id" => 9,
            "contractor_id" => 1,
            "sub_contractor_id" => 9,
            "company_name" => "Freeman Sporting Goods",
            "given_name" => "Amelia",
            "middle_name" => "NULL",
            "family_name" => "NULL",
            "fully_qualified_name" => "Freeman Sporting Goods =>55 Twin Lane",
            "primary_phone" => "6505550987",
            "primary_email_addr" => "amelia@freeman.com",
            "created_at" => "2019-05-05 01:17:28",
            "updated_at" => "2019-05-05 01:17:28"
        ]);

        $finalArray = [
            [
                'name' => "Kirby Freeman",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "6505550987",
                'email' => null,
            ],
            [
                'name' => "Sasha Tillou",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "4155559933",
                'email' => "stillou@freeman.com",
            ],
            [
                'name' => "Amelia",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "6505550987",
                'email' => "amelia@freeman.com",
            ],
            [
                'name' => "Jeff Freeman",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "6505550987",
                'email' => '',
            ]
        ];

        $formattedSubs = [
            [
                'name' => "Jeff Freeman",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "6505550987",
                'email' => '',
            ]
        ];

        $c = new Contractor();

        $subs = $c->getAllQuickbookCompaniesAndFormattedSubs('free', $formattedSubs);

//        echo json_encode($finalArray);
//        echo "\n";
//        echo json_encode($subs);

        $this->assertEquals($finalArray, $subs);

    }

    /**  @test */
    function make_sure_sub_that_exists_in_both_quickbooksContractor_table_and_users_table_are_not_added_twice_to_the_drop_down()
    {
        //


        $this->withExceptionHandling();

        $user = factory(User::class)->create();

        factory(QuickbooksContractor::class)->create([
            "quickbooks_id" => 7,
            "contractor_id" => $user->id,
            "sub_contractor_id" => 7,
            "company_name" => "Freeman Sporting Goods",
            "given_name" => "Kirby",
            "middle_name" => "NULL",
            "family_name" => "Freeman",
            "fully_qualified_name" => "Freeman Sporting Goods",
            "primary_phone" => "6505550987",
            "primary_email_addr" => null,
            "created_at" => "2019-05-05 01:17:28",
            "updated_at" => "2019-05-05 01:17:28"
        ]);

        factory(QuickbooksContractor::class)->create([
            "quickbooks_id" => 8,
            "contractor_id" => 1,
            "sub_contractor_id" => 8,
            "company_name" => "Freeman Sporting Goods",
            "given_name" => "Sasha",
            "middle_name" => "NULL",
            "family_name" => "Tillou",
            "fully_qualified_name" => "Freeman Sporting Goods =>0969 Ocean View Road",
            "primary_phone" => "4155559933",
            "primary_email_addr" => "stillou@freeman.com",
            "created_at" => "2019-05-05 01:17:28",
            "updated_at" => "2019-05-05 01:17:28"
        ]);

        factory(QuickbooksContractor::class)->create([
            "quickbooks_id" => 9,
            "contractor_id" => 1,
            "sub_contractor_id" => 9,
            "company_name" => "Freeman Sporting Goods",
            "given_name" => "Amelia",
            "middle_name" => "NULL",
            "family_name" => "NULL",
            "fully_qualified_name" => "Freeman Sporting Goods =>55 Twin Lane",
            "primary_phone" => "6505550987",
            "primary_email_addr" => "amelia@freeman.com",
            "created_at" => "2019-05-05 01:17:28",
            "updated_at" => "2019-05-05 01:17:28"
        ]);

        $finalArray = [
            [
                'name' => "Kirby Freeman",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "6505550987",
                'email' => null,
            ],
            [
                'name' => "Sasha Tillou",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "4155559933",
                'email' => "stillou@freeman.com",
            ],
            [
                'name' => "Amelia",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "6505550987",
                'email' => "amelia@freeman.com",
            ]
        ];

        $formattedSubs = [
            [
                'name' => "Sasha Tillou",
                'contractor' => [
                    'company_name' => "Freeman Sporting Goods"
                ],
                'phone' => "4155559933",
                'email' => "stillou@freeman.com",
            ]
        ];

        $c = new Contractor();

        $subs = $c->getAllQuickbookCompaniesAndFormattedSubs('free', $formattedSubs);

        echo json_encode($finalArray);
        echo "\n";
        echo json_encode($subs);

        $this->assertEquals($finalArray, $subs);

    }

    /**  @test */
    function return_all_contractors_in_quickbook_contractors_table_that_prioritize_companies_with_the_contractors_id()
    {
        $this->withExceptionHandling();

        // 
        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 7,
            'contractor_id' => 1,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 8,
            'contractor_id' => 2,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 9,
            'contractor_id' => 3,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        $subsArray = [
            [
                'id' => '',
                'contractor_id' => 1,
                'quickbooks_id' => 7,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Goods',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ]
        ];

        $c = new Contractor();

        $allCompanies = $c->getAllCompaniesInQuickBookContractorsByCompanyName('Freeman');

//        var_dump($c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

        $this->assertEquals($subsArray, $c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

    }


    /**  @test */
    function return_2_contractor_enititys_in_quickbook_contractors_table_that_prioritize_companies_with_the_contractors_id()
    {
        $this->withExceptionHandling();

        //
        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 7,
            'contractor_id' => 1,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 8,
            'contractor_id' => 1,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 9,
            'contractor_id' => 3,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        $subsArray = [
            [
                'id' => '',
                'contractor_id' => 1,
                'quickbooks_id' => 7,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Goods',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ],
            [
                'id' => '',
                'contractor_id' => 1,
                'quickbooks_id' => 8,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Goods',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ]
        ];

        $c = new Contractor();

        $allCompanies = $c->getAllCompaniesInQuickBookContractorsByCompanyName('Freeman');

//        var_dump($c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

        $this->assertEquals($subsArray, $c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

    }

    /**  @test */
    function return_first_contractor_enitity_in_quickbook_contractors_table_if_contractors_id_does_not_exist_for_subs()
    {
        $this->withExceptionHandling();

        //
        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 7,
            'contractor_id' => 2,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 8,
            'contractor_id' => 4,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 9,
            'contractor_id' => 3,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        $subsArray = [
            [
                'id' => '',
                'contractor_id' => 3,
                'quickbooks_id' => 9,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Goods',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ]
        ];

        $c = new Contractor();

        $allCompanies = $c->getAllCompaniesInQuickBookContractorsByCompanyName('Freeman');

//        var_dump($c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

        $this->assertEquals($subsArray, $c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

    }

    /**  @test */
    function return_2_contractors_with_different_company_names_but_one_has_the_contractors_id_and_the_other_one_does_not()
    {
        $this->withExceptionHandling();

        //
        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 7,
            'contractor_id' => 1,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 8,
            'contractor_id' => 4,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 9,
            'contractor_id' => 3,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 8,
            'contractor_id' => 2,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Stores',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 9,
            'contractor_id' => 2,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Stores',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        $subsArray = [
            [
                'id' => '',
                'contractor_id' => 1,
                'quickbooks_id' => 7,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Goods',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ],
            [
                'id' => '',
                'contractor_id' => 2,
                'quickbooks_id' => 9,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Stores',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ]
        ];

        $c = new Contractor();

        $allCompanies = $c->getAllCompaniesInQuickBookContractorsByCompanyName('Freeman');

//        var_dump($c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

        $this->assertEquals($subsArray, $c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

    }

// pu --filter return_all_contractors_in_quickbook_contractors_table_that_prioritize_companies_with_the_contractors_id tests/Feature/ContractorTest.php;
// pu --filter return_2_contractor_enititys_in_quickbook_contractors_table_that_prioritize_companies_with_the_contractors_id tests/Feature/ContractorTest.php;
// pu --filter return_first_contractor_enitity_in_quickbook_contractors_table_if_contractors_id_does_not_exist_for_subs tests/Feature/ContractorTest.php;
// pu --filter return_2_contractors_with_different_company_names_but_one_has_the_contractors_id_and_the_other_one_does_not tests/Feature/ContractorTest.php;
// pu --filter return_3_contractors_with_different_company_names_but_one_has_the_contractors_id_and_the_other_two_do_not tests/Feature/ContractorTest.php;


    /**  @test */
    function return_3_contractors_with_different_company_names_but_one_has_the_contractors_id_and_the_other_two_do_not()
    {
        $this->withExceptionHandling();

        //
        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 7,
            'contractor_id' => 1,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 8,
            'contractor_id' => 4,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 9,
            'contractor_id' => 3,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Goods',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 8,
            'contractor_id' => 4,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Places',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        factory(QuickbooksContractor::class)->create([
            'quickbooks_id' => 9,
            'contractor_id' => 2,
            'sub_contractor_id' => 7,
            'company_name' => 'Freeman Sporting Stores',
            'given_name' => 'sasha',
            'family_name' => 'touli',
            'primary_phone' => '6505550987',
            'primary_email_addr' => 'kirb@kirb.net',
        ]);

        $subsArray = [
            [
                'id' => '',
                'contractor_id' => 1,
                'quickbooks_id' => 7,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Goods',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ],
            [
                'id' => '',
                'contractor_id' => 4,
                'quickbooks_id' => 8,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Places',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ],
            [
                'id' => '',
                'contractor_id' => 2,
                'quickbooks_id' => 9,
                'name' => 'sasha touli',
                'given_name' => 'sasha',
                'family_name' => 'touli',
                'last_name' => '',
                'first_name' => '',
                'contractor' =>
                    [
                        'company_name' => 'Freeman Sporting Stores',
                    ],
                'phone' => '6505550987',
                'email' => 'kirb@kirb.net',
            ]
        ];

        $c = new Contractor();

        $allCompanies = $c->getAllCompaniesInQuickBookContractorsByCompanyName('Freeman');

        // var_dump($c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

        $this->assertEquals($subsArray, $c->filterCompaniesByMakingThemUniqueAndPrioritizingThoseWithGeneralContractorId($allCompanies, 1));

    }


    /**  @test */
    function contractor_must_be_able_to_create_a_quickbooks_estimate()
    {
        //

//        $this->assertEquals(true, $contractor->quickBooksEstimateCreated());
    }

    /**  @test */
    function check_if_contractor_has_a_quickooks_customer_if_not_then_add_it_to_quickBooks()
    {
        //

//        $this->assertEquals(, $contractor->firstOrCreateQuickBooksCustomer($customer));
    }

    /**  @test */
    function check_that_a_contractor_does_not_have_a_the_same_phone_number_as_another_contractor()
    {
        // 

    }

    /**  @test */
    function shouldReturnNonDuplicatedContractors()
    {
        //

        $user = $this->createAUser('contractor', 1, 1, [], [
            'company_name' => 'Albertsons'
        ]);

        echo $user->contractor()->get()->first()->company_name;

        $response = $this->actingAs($user)->
            json('GET', '/search/' . $user->contractor()->get()->first()->company_name);

        $response->assertJson([
            'hello' => "world"
        ]);

//        $this->assertEquals(1, count($response->json()));
    }

}

