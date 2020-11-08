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
|2 - |Contractor Adds A Task | bid.in_progress |  bid_task.initiated | {null} | 3 |
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
|15 - |General declines the task|  job.approved | bid_task.declined_subs_work | sub.finished_job_denied_by_contractor | 25 |
|16 - |General approves the task|  job.approved | bid_task.approved_subs_work | sub.finished_job_approved_by_contractor | 27 |
|17 - |Customer declines the task|  job.approved | bid_task.customer_changes_finished_task | sub.customer_changes_finished_task | 29 |
|18|Customer has not paid for finished job | job.approved | bid_task.approved_by_customer | sub.waiting_for_customer_payment | 28 |
|19 - |Customer pays for job|  job.paid | bid_task.paid | sub.paid | 34 |

| Action | General | Sub | Customer | General | Sub | Customer | Good |
|---|---|---|---|---|---|---|---|
| Contractor Initiates a bid | - | - | Gets A Text | Bid [ Delete ] Task [ Create, Delete, Change ] | - | Bid [ Delete ] | √ |
| Contractor Adds A Task | - | - | - | Bid [ Delete ] Task [ Create, Delete, Change ] | - | Bid [ Delete ] | √ |
| Contractor Invites A Sub | - | Gets A Text | - | Bid [ Delete ] Task [ Create, Delete, Change ] | Task [ Submit, Delete, Change ] | Bid [ Delete ] | √ |
| Sub submits a bid | Gets A Text | - | - | Bid [ Delete ] Task [ Create, Delete, Accept ] | Task [ Submit, Delete, Change ] | Bid [ Delete ] | √ |
| Contractor accepts a subs bid | - | Gets A Text | - | Bid [ Delete ] Task [ Create, Delete, Accept, Change ] | Task [ Submit, Delete, Change ]  | Bid [ Delete ] | √ |
| Contractor submits a bid | - | Gets A Text | Gets A Text | Bid [ Delete ] Task [ Create, Delete, Accept ] | Task [ Submit, Delete ]  | Bid [ Delete ] | √ |
| Customer changes a bid | Gets A Text | Gets A Text | - | Bid [ Delete ] Task [ Create, Delete, Accept ]  | Task [ Submit, Delete ]  | Bid [ Delete ] |
| Customer cancels a bid | Gets A Text | Gets A Text | - | - | - | - | √ |
| General cancels a bid | - | Gets A Text | Gets A Text | - | - | - | √ |

| Sub cancels a bid_task and task has been approved | Gets A Text | - | - | Bid [ Delete ] Task [ Create, Delete, Accept ]  | - | Bid [ Delete ] |

    if job has been approved but the task was deleted by the sub then 
        general will be notified to find a new sub or be notified to do the work
        customer will show the generals name for the task and not the sub
    if job not approved
        general 
        


| Customer approves a bid | Gets A Text | Gets A Text | - | Bid [ Delete ]  | Bid [ Delete ] | Bid [ Delete ] Task [ Delete ] |
| Customer approves a task | Gets A Text | Gets A Text | - | Bid [ Delete ] Task [ Delete ] | Task [ Delete ] | Bid [ Delete ] Task [ Delete ] |
| Customer deletes a task after approval | Gets A Text | Gets A Text | - | - | - | - |

| Contractor submits a task after approval | - | Gets A Text | Gets A Text | - | Task [ Delete ] | Bid [ Delete ] Task [ Approve, Delete ] |

| Sub deletes a task after approval | - | Gets A Text | - | - | Task [ Delete ] | Bid [ Delete ] Task [ Delete ] |
| Contractor gets a new sub after an approval | - | Gets A Text | - | - | Task [ Delete ] | Bid [ Delete ] Task [ Delete ] |
| Contractor finishes task | - | - | Gets A Text | - | - | - |
| Sub finishes task | Gets A Text | - | - | - | - | - |
| General declines the subs task | - | Gets A Text | - | - | - | - |
| General approves the subs task | - | Gets A Text | Gets A Text | - | - | - |
| Customer declines the task | Gets A Text | Gets A Text | - | - | - | - |
| Customer pays for job | Gets A Text | Gets A Text | - | - | - | - |




Need to be able to add a sub until the job has been accepted by the customer