<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BidListController extends Controller
{
  //
  public function contractorIndex()
  {
      // get bids related to contractor from modal
      $bids = [
                ['id' => 0, 'jobName' => 'Restaurant Work - Joe', 'status' => 'pending', 'price' => 32.00, 'tasksCompleted' => 4,  'totalTasks'  => 10, 'completionDate' => '12/13/2018'],
                ['id' => 23, 'jobName' => 'Restroom Work - Marina', 'status' => 'completed', 'price' => 432.34, 'tasksCompleted' => 7,  'totalTasks'  => 7, 'completionDate' => '08/22/2018'],
                ['id' => 457, 'jobName' => 'Paveway Work - Salina', 'status' => 'initiated', 'price' => 345.99, 'tasksCompleted' => 1,  'totalTasks'  => 2, 'completionDate' => '05/06/2048'],
                ['id' => 53, 'jobName' => 'Pool - Jon', 'status' => 'pending', 'price' => 1234.00, 'tasksCompleted' => 0,  'totalTasks'  => 5, 'completionDate' => '02/19/2018'],
                ['id' => 453, 'jobName' => 'Roof Work - Jose', 'status' => 'completed', 'price' => 542.34, 'tasksCompleted' => 1,  'totalTasks'  => 12, 'completionDate' => '05/23/2017'],
                ['id' => 65, 'jobName' => 'Restaurant #2 Work - Joe', 'status' => 'pending', 'price' => 547.00, 'tasksCompleted' => 3,  'totalTasks'  => 6, 'completionDate' => '09/16/2028'],
                ['id' => 5467, 'jobName' => 'Backyard Work - Sarah', 'status' => 'initiated', 'price' => 346.99, 'tasksCompleted' => 0,  'totalTasks'  => 1, 'completionDate' => '12/01/2019']
              ];
      return view('/bid-list')->with(['bids' => $bids]);

  }

  public function customerIndex()
  {
    // get bids related to customer from modal
    $bids = [
              ['id' => 465, 'jobName' => 'Livingroom Work - John', 'status' => 'pending', 'price' => 5363.00, 'tasksCompleted' => 3,  'totalTasks'  => 3, 'completionDate' => '08/22/2018'],
              ['id' => 3457, 'jobName' => 'Restroom Work - John', 'status' => 'completed', 'price' => 5432.99, 'tasksCompleted' => 0,  'totalTasks'  => 10, 'completionDate' => '08/22/2018'],
              ['id' => 656, 'jobName' => 'Paveway Work - John', 'status' => 'initiated', 'price' => 4532.00, 'tasksCompleted' => 1,  'totalTasks'  => 6, 'completionDate' => '08/22/2018'],
            ];
    return view('/customers/bidlist')->with(['bids' => $bids]);
  }
}
