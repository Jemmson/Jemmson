<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Job/Bid statuses
    |--------------------------------------------------------------------------
    |
    |
    */

    'job' => [
        'approved' => 'job.approved',
        'accepted' => 'job.accepted'
    ],
    'bid_task' => [
        'initiated' => 'bid_task.initiated',
        'accepted' => 'bid_task.accepted',
        'approved_by_general' => 'bid_task.approved_by_general',    
        'finished' => 'bid_task.finished',
    ],
    'bid' => [
        'initiated' => 'bid.initiated',
        'in_progress' => 'bid.in_progress'
    ]

];
