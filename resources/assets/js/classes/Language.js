export default class Language {
    constructor () {
    }

    static lang () {
        return {
            'bid_task.sent': {
                sub: 'Waiting On Contractor Approval',
                general: 'Waiting On Customer Approval' 
            },
            'bid_task.approved': {
                sub: 'Waiting On Customer Approval',
                general: 'Job Approved'
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
            }
        };
    }
}