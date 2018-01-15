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
        };
    }
}