| Action | Job Status | Job Task Status | Bid Contractor Job Task Status |
|---|---|---|---|
|Contractor Initiates a bid | bid.initiated |||
|Contractor Adds A Task | bid.in_progress |  bid_task.initiated ||
|Contractor Invites A Sub | bid.in_progress | bid_task.initiated | bid.initiated |
|Sub submits a bid|  bid.in_progress | bid_task.bid_sent | bid_task.bid_sent |
|Contractor accepts a bid|  bid.in_progress | bid_task.accepted | bid_task.accepted |
|Contractor submits a bid|  bid.sent | bid_task.accepted | bid_task.accepted |
|Customer changes a bid|  bid.declined | bid_task.accepted | bid_task.accepted |
|Contractor submits a bid|  bid.sent | bid_task.accepted | bid_task.accepted |
|Customer approves a bid|  bid.in_progress | bid_task.approved_by_customer | bid_task.accepted |
|Sub finishes task|  job.approved | bid_task.finished_by_sub | bid_task.accepted |
|Contractor approves the task|  job.approved | bid_task.approved_by_general | bid_task.accepted |
|Customer pays for job|  bid.sent | bid_task.approved_by_general | bid_task.accepted |


| Action | Job Status | Job Task Status | Bid Contractor Job Task Status |
|---|---|---|---|
|Contractor Initiates a bid | bid.initiated |||
|Contractor Adds A Task | bid.in_progress |  bid_task.initiated ||
|Contractor Invites A Sub | bid.in_progress | bid_task.initiated | sub.initiated |
|Sub submits a bid|  bid.in_progress | bid_task.initiated | sub.bid_sent |
|Contractor accepts a bid|  bid.in_progress | bid_task.initiated | sub.accepted |
|Contractor submits a bid|  bid.sent | bid_task.sent | sub.sent |
|Customer changes a bid|  bid.declined | bid_task.declined | sub.declined |
|Contractor submits a bid|  bid.sent | bid_task.sent | sub.sent |
|Customer approves a bid|  job.approved | bid_task.approved_to_do_work | sub.approved_to_do_work |
|Sub finishes task|  job.approved | bid_task.approved_to_do_work | sub.finished |
|Contractor approves the task|  job.approved | bid_task.approved_by_general | sub.finished |
|Customer pays for job|  job.paid | bid_task.paid | sub.paid |



Need to be able to add a sub until the job has been accepted by the customer