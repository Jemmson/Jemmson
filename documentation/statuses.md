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
|Customer pays for job|  job.completed | bid_task.customer_sent_payment | bid_task.accepted |


|Step| Action | Job Status | Job Task Status | Bid Contractor Job Task Status | Value |
|---|---|---|---|---|---|
|1 - |Contractor Initiates a bid | bid.initiated | {null} | {null} | 1 |
|2|Contractor Adds A Task | bid.in_progress |  bid_task.initiated | {null} | 3 |
|3 - |Contractor Invites A Sub | bid.in_progress | bid_task.initiated | sub.initiated | 4 |
|4 - |Sub submits a bid|  bid.in_progress | bid_task.initiated | sub.sent_a_bid | 5 |
|5 - |Contractor accepts a bid|  bid.in_progress | bid_task.initiated | sub.accepted / denied | 6 / 7 |
|6|Contractor submits a bid|  bid.sent | bid_task.waiting_for_customer_approval | sub.waiting_for_customer_approval | 10 |
|7 - |Customer changes a bid|  bid.changed | bid_task.waiting_for_customer_approval | sub.waiting_for_customer_approval | 11 |
|8|Customer cancels a bid|  bid.canceled_by_customer | bid_task.canceled_by_customer | sub.canceled_by_customer | 16 |
|9|General cancels a bid|  bid.canceled_by_general | bid_task.canceled_by_general | sub.canceled_by_general | 19 |
|10|Sub cancels a bid_task|  bid.{unaffected} | bid_task.{unaffected} | sub.canceled_bid_task | 9
|11|Contractor submits a bid|  bid.sent | bid_task.waiting_for_customer_approval | sub.waiting_for_customer_approval | 11 |
|12 - |Customer approves a bid|  job.approved | bid_task.approved_by_customer | sub.approved_by_customer | 23 |
|13 - |Contractor finishes task|  job.approved | bid_task.general_finished_work | {null} | 14 |
|14 - |Sub finishes task|  job.approved | bid_task.sub_finished_work | sub.finished_job | 24 |
|15 - |General declines the task|  job.approved | bid_task.approved_by_customer | sub.finished_job_denied_by_contractor | 25 |
|16 - |General approves the task|  job.approved | bid_task.approved_by_customer | sub.finished_job_approved_by_contractor | 27 |
|17 - |Customer declines the task|  job.approved | bid_task.customer_changes_finished_task | sub.customer_changes_finished_task | 29 |
|18|Customer has not paid for finished job | job.approved | bid_task.approved_by_customer | sub.waiting_for_customer_payment | 28 |
|19 - |Customer pays for job|  job.paid | bid_task.paid | sub.paid | 34 |


Need to be able to add a sub until the job has been accepted by the customer