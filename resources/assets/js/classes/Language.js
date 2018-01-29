export default class Language {
    constructor () {
    }

    static lang () {
        return {
            // initiated
            'bid_task.initiated': {
                sub: 'Please Bid On This Task',
                general: 'Initiated',
                customer: 'Initiated' 
            },
            // sub sent a bid to the general contractor
            'bid_task.bid_sent': {
                sub: 'Waiting on Contractor To Accept Bid',
                general: 'Waiting On Bid Approval',
                customer: 'Pending'
            },
            // general contractor accepted the subs bid
            'bid_task.accepted': {
                sub: 'Bid Accepted, Waiting On Customer Approval',
                general: 'Bid Accepted',
                customer: 'Pending'
            },
            // sub saying this task is finished
            'bid_task.finished_by_sub': {
                sub: 'Waiting on Contractor Approval',
                general: 'Waiting On Approval',
                customer: 'Pending'
            },
            // approving a job finished by a sub is finished
            'bid_task.approved_by_general': {
                sub: 'Waiting On Customer Approval & Payment',
                general: 'Approved, Waiting On Customer Approval',
                customer: 'Needs Approval & Pay'
            },
            // general saying this task is finished
            'bid_task.finished_by_general': {
                sub: '',
                general: 'Waiting On Customer Approval & Payment',
                customer: 'Needs Approval & Pay'
            },
            'bid_task.approved_by_customer': {
                sub: 'Start Job',
                general: 'Start Job',
                customer: 'Approved Task'
            },
            'bid_task.customer_sent_payment': {
                sub: 'Customer Has Sent A Payment',
                general: 'Customer Has Sent A Payment',
                customer: 'Sent Payment'
            },
            'bid_task.reopened': {
                sub: 'Waiting on Contractor Approval',
                general: 'Reopened',
                customer: 'Reopened'
            },
            'bid.initiated': {
                sub: 'Waiting on General Contractor to finish job bid',
                general: 'Bid Initiated',
                customer: 'Bid Initiated'
            },
            'bid.in_progress': {
                sub: 'Waiting on General Contractor to Submit Final Bid',
                general: 'Bid In Progress',
                customer: 'Waiting on Contractor to Submit Final Bid'
            },
            // general is finished with their bids and has sent 
            // the bid to the customer for approval
            'bid.sent': {
                sub: 'Waiting on Customer Approval - sub',
                general: 'Waiting on Customer Approval - general',
                customer: 'Waiting on Approval'
            },
            'job.approved': {
                sub: 'Start Job',
                general: 'Start Job',
                customer: 'Approved'
            },
            // feedback messages for toasts
            'submit': {
                'job_finished': {
                    'success': {
                        sub: 'Job Finished! The contractor has been notified.',
                        general: 'Job Finished! The customer has been notified.'
                    }
                },
                'approve_task': {
                    success: 'The task has been approved! Sub and Customer have been notified.',
                }
            }
        };
    }
}