| Action                        | Status |
|---|---|
|Contractor Initiates a bid | job -> bid.initiated |
|Contractor Adds A Task | job - bid.in_progress <br> job_task - bid_task.initiated |
|Contractor Invites A Sub | job - bid.in_progress <br> job_task - bid_task.initiated <br> bcjt -> bid.initiated |
|Sub submits a bid| job - bid.in_progress <br> job_task - bid_task.bid_sent <br> bcjt -> bid_task.bid_sent |
|Contractor accepts a bid| job - bid.in_progress <br> job_task - bid_task.accepted <br> bcjt -> bid_task.accepted |
|Contractor submits a bid| job - bid.sent <br> job_task - bid_task.accepted <br> bcjt -> bid_task.accepted |
|Customer changes a bid| job - bid.declined <br> job_task - bid_task.accepted <br> bcjt -> bid_task.accepted |
|Contractor submits a bid| job - bid.sent <br> job_task - bid_task.accepted <br> bcjt -> bid_task.accepted |
|Customer approves a bid| job - bid.in_progress <br> job_task - bid_task.approved_by_customer <br> bcjt -> bid_task.accepted |
|Sub finishes task| job - job.approved <br> job_task - bid_task.finished_by_sub <br> bcjt -> bid_task.accepted |
|Contractor approves the task| job - job.approved <br> job_task - bid_task.approved_by_general <br> bcjt -> bid_task.accepted |
|Customer pays for job| job - bid.sent <br> job_task - bid_task.approved_by_general <br> bcjt -> bid_task.accepted |



Need to be able to add a sub until the job has been accepted by the customer