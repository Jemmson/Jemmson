export default class Language {
    constructor () {
    }

    static lang () {
        return {
            // statuses 
            'bid_task.initiated': {
                sub: 'Please Bid On This Task',
                general: 'Initiated',
                customer: 'Initiated' 
            },
            'bid_task.accepted': {
                sub: 'Bid Accepted, Waiting On Customer Approval',
                general: 'Approved, Waiting On Customer Approval',
                customer: 'Task Finished Waiting On Approval & Pay'
            },
            'bid_task.approved_by_general': {
                sub: 'Waiting On Customer Approval & Payment',
                general: 'Approved, Waiting On Customer Approval',
                customer: 'Needs Approval & Pay'
            },
            'bid_task.finished': {
                sub: 'Waiting on Contractor Approval',
                general: 'Waiting on Customer Approval',
                customer: 'Waiting on Approval'
            },
            'bid.sent': {
                sub: 'Waiting on Customer Approval',
                general: 'Waiting on Customer Approval',
                customer: 'Waiting on Approval'
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