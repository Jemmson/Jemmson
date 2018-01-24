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
        'bid_sent' => 'bid_task.bid_sent',
        'accepted' => 'bid_task.accepted',
        'approved_by_general' => 'bid_task.approved_by_general',    
        'finished_by_sub' => 'bid_task.finished_by_sub',
        'finished_by_general' => 'bid_task.finished_by_general',
        
    ],
    'bid' => [
        'initiated' => 'bid.initiated',
        'in_progress' => 'bid.in_progress'
    ]

];
