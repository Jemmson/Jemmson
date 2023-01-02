<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\InitiateBidController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\JobController;


class RunBilling extends TestCase
{

    public $textCounter = 0;

    //vendor/bin/phpunit tests/Automation/RunBilling.php

//docker exec -i jemmson-mysql mysql -uroot -proot jemmson < /Users/shawnpike/Jemmson/scratch/databaseBackups/prod/12_20_2022.sql


    public function testMain()
    {
        $data = $this->getBillingData();

        foreach ($data as $customer){

            $custData = explode(',', $customer);

            $contractorId = $custData[0];
            $customerId = $custData[1];
            $automate = $custData[2];
            $first_name = $custData[3];
            $last_name = $custData[4];
            $phone = $custData[5];
            $address_line_1 = $custData[6];
            $jobName = $custData[7];
            $paymentType = $custData[8];
            $chemsIncluded = $custData[9];
            $servicePrice = floatval($custData[13]);

            if ($last_name == 'Battafarano') {
                self::verifyMainInput(
                    $contractorId,
                    $customerId,
                    $automate,
                    $first_name,
                    $last_name,
                    $phone,
                    $address_line_1,
                    $jobName,
                    $paymentType,
                    $chemsIncluded,
                    $servicePrice,
                    $custData
                );
            } else if ($last_name == 'Pike') {
                self::verifyMainInputCustomer2(
                    $contractorId,
                    $customerId,
                    $automate,
                    $first_name,
                    $last_name,
                    $phone,
                    $address_line_1,
                    $jobName,
                    $paymentType,
                    $chemsIncluded,
                    $servicePrice,
                    $custData
                );
            }


//            Only works if
//            - There is a new customer
//            -


            if ($contractorId !== 'ContractorId'
                && $paymentType !== 'Paper'
                && $customerId !== 'null'
                && $automate === 'Y'
            ) {
               // INITIATE THE BID
                $ibc = new InitiateBidController();
                $jobId = $ibc->automationSend(
                    $customerId,
                    $phone,
                    $contractorId,
                    $jobName,
                    $paymentType
                );

/*
 *  Has a new job been created?
 *  Are the job statuses set correctly?
 *  Has the job Id been returned from the method?
 * */
                self::logLine("\nJobId", $jobId);
                $this->assertDatabaseHas("jobs", ['id' => $jobId]);
                self::logLine("Job Status => app.initiated", 'app.initiated');
                $this->assertDatabaseHas("job_status", ['id' => $jobId]);


                // ADD ALL TASKS

                $addTask = new TaskController();
                // add service
                $addTask->automationAddTask(
                    'Monthly Service November',
                    $jobId,
                    $customerId,
                    $contractorId,
                    1,
                    $servicePrice,
                    $servicePrice
                );

//                $this->assertDatabaseHas('job_task', [
//                    'job_id' => $jobId,
//                    'job_id' => $customerId,
//                    'contractor_id' => $contractorId,
//                    'qty' => 1,
//                    'unit_price' => $servicePrice,
//                    'cust_final_price' => $servicePrice
//                ]);

                $this->assertDatabaseHas('tasks', [
                    'name' => 'Monthly Service November'
                ]);

                // if chems included then dont price out the basic chems
                if (floatval($chemsIncluded) == 0) {
                    // add chlorine
                    self::addTask(
                        'chlorine tab',
                        $jobId,
                        $customerId,
                        $contractorId,
                        $custData[10],
                        2.25,
                        2.25
                    );

                    // add liquid chlorine
                    self::addTask(
                        'Liquid Chlorine',
                        $jobId,
                        $customerId,
                        $contractorId,
                        $custData[11],
                        5.39,
                        5.39
                    );

                    // add acid
                    self::addTask(
                        'muriatic acid',
                        $jobId,
                        $customerId,
                        $contractorId,
                        $custData[10],
                        8.23,
                        8.23
                    );
                }

//                add any repairs
                for ($i = 15; $i < count($custData) - 2; $i = $i + 3) {
                    if (!empty($custData[$i])) {
                        // add repair
                        self::addTask(
                            $custData[$i],
                            $jobId,
                            $customerId,
                            $contractorId,
                            $custData[$i + 2],
                            $custData[$i + 1],
                            $custData[$i + 1]
                        );
//                        $custFinalPrice = $custData[$i + 1] * 100;

//                        dd($jobTaskId);

//                        $this->assertDatabaseHas('job_task', [
//                            'id' => $jobTaskId,
//                            'cust_final_price' => $custFinalPrice
//                        ]);
                    }
                }

//                dd($customerId . "\n" . $jobId . "\n");



                if ($this->textCounter < 70) {
                    $jc = new JobController();
                    $jc->automationFinishedBidNotification(
                        $customerId,
                        $jobId,
                        true,
                        true
                    );
                } else {
                    sleep(62);
                    $this->textCounter = 1;
                    $jc = new JobController();
                    $jc->automationFinishedBidNotification(
                        $customerId,
                        $jobId,
                        true,
                        true
                    );
                }

            }

        }

    }

    public function addTask(
        $taskName,
        $jobId,
        $customerId,
        $contractorId,
        $quantity,
        $taskPrice = 0,
        $customerPrice = 0
    )
    {
        $addTask = new TaskController();
        if (floatval($quantity) > 0) {
            return $addTask->automationAddTask(
                $taskName,
                $jobId,
                $customerId,
                $contractorId,
                floatval($quantity),
                floatval($taskPrice),
                floatval($customerPrice)
            );
        }
    }

    public function logLine($label, $data)
    {
        echo $label . " => " . $data . "\n";
    }

    public function ReadingInitiateBidController()
    {
        $ibc = new InitiateBidController();
        echo $ibc->checkAccess();
    }

    public function getBillingData()
    {
//        return file("/var/www/html/tests/Automation/test_customers.csv");
        return file("/var/www/html/tests/Automation/DecemberBilling22.csv");
    }

    public function verifyMainInput(
        $contractorId,
        $customerId,
        $automate,
        $first_name,
        $last_name,
        $phone,
        $address_line_1,
        $jobName,
        $paymentType,
        $chemsIncluded,
        $servicePrice,
        $custData
    )
    {
        $this->assertEquals('1', $custData[0]);
        $this->assertEquals('1', $contractorId);

        $this->assertEquals('2', $custData[1]);
        $this->assertEquals('2', $customerId);

        $this->assertEquals('Y', $custData[2]);
        $this->assertEquals('Y', $automate);

        $this->assertEquals('Kristen', $custData[3]);
        $this->assertEquals('Kristen', $first_name);

        $this->assertEquals('Battafarano', $custData[4]);
        $this->assertEquals('Battafarano', $last_name);

        $this->assertEquals('6023508801', $custData[5]);
        $this->assertEquals('6023508801', $phone);

        $this->assertEquals('2248 S Catarina Cir', $custData[6]);
        $this->assertEquals('2248 S Catarina Cir', $address_line_1);

        $this->assertEquals('Battafarano_2248 S Catarina Cir_November_22_796', $custData[7]);
        $this->assertEquals('Battafarano_2248 S Catarina Cir_November_22_796', $jobName);

        $this->assertEquals('creditCard', $custData[8]);
        $this->assertEquals('creditCard', $paymentType);

        $this->assertEquals('1', $custData[9]);
        $this->assertEquals('1', $chemsIncluded);

    }

    public function verifyMainInputCustomer2(
        $contractorId,
        $customerId,
        $automate,
        $first_name,
        $last_name,
        $phone,
        $address_line_1,
        $jobName,
        $paymentType,
        $chemsIncluded,
        $servicePrice,
        $custData
    )
    {
        $this->assertEquals('1', $custData[0]);
        $this->assertEquals('1', $contractorId);

        $this->assertEquals('4', $custData[1]);
        $this->assertEquals('4', $customerId);

        $this->assertEquals('Y', $custData[2]);
        $this->assertEquals('Y', $automate);

        $this->assertEquals('Shawn', $custData[3]);
        $this->assertEquals('Shawn', $first_name);

        $this->assertEquals('Pike', $custData[4]);
        $this->assertEquals('Pike', $last_name);

        $this->assertEquals('4807034902', $custData[5]);
        $this->assertEquals('4807034902', $phone);

        $this->assertEquals('14026 S 8TH ST', $custData[6]);
        $this->assertEquals('14026 S 8TH ST', $address_line_1);

        $this->assertEquals('PIKE_14026 S 8TH ST_November_22_797', $custData[7]);
        $this->assertEquals('PIKE_14026 S 8TH ST_November_22_797', $jobName);

        $this->assertEquals('creditCard', $custData[8]);
        $this->assertEquals('creditCard', $paymentType);

        $this->assertEquals('0', $custData[9]);
        $this->assertEquals('0', $chemsIncluded);

    }

}
