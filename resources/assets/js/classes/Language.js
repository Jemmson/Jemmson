export default class Language {
    constructor () {
    }

    static lang () {
        return {
            // statuses 
            'bid_task.initiated': {
                sub: 'Please Bid On This Task',
                general: 'Waiting on Bid',
                customer: 'Initiated' 
            },
            'bid_task.sent': {
                sub: 'Waiting On Contractor Approval',
                general: 'Waiting On Customer Approval',
                customer: 'Initiated'  
            },
            'bid_task.approved': {
                sub: 'Waiting On Contractor Approval',
                general: 'Waiting On Customer Approval',
                customer: 'Task Finished Waiting On Approval'
            },
            'bid.sent': {
                sub: 'Waiting on Customer Approval',
                general: 'Waiting on Customer Approval',
                customer: 'Waiting on Approval'
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