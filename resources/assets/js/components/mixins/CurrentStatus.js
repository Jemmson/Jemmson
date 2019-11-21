export default {
  methods: {
    sayHello() {
      console.log('+++++++++++++++++++++++HELLO++++++++++++++++++++')
    },

    getStatus(job, jt, sub) {

      let status = ''

      if (sub.status === 'canceled_bid_task') {
        status = ',,canceled_bid_task'
      } else if (job.status === 'approved' && jt.status === 'general_finished_work') {
        status = 'approved,general_finished_work,'
      } else {
        status = job.status +','+ jt.status +','+ sub.status
      }

      switch (status) {
        case 'initiated,,':
          return {
            step: 1,
            name: 'Contractor Initiates a bid',
            description: 'Bid has been initiated and there have been no tasks add to the job yet'
          }
        case 'in_progress,initiated,':
          return {
            step: 2,
            name: 'Contractor Adds A Task',
            description: ''
          }
        case 'in_progress,initiated,initiated':
          return {
            step: 3,
            name: 'Contractor Invites A Sub',
            description: ''
          }
        case 'in_progress,initiated,sent_a_bid':
          return {
            step: 4,
            name: 'Sub submits a bid',
            description: ''
          }
        case 'in_progress,initiated,accepted':
          return {
            step: 5,
            name: 'Contractor accepts a bid',
            description: ''
          }
        case 'in_progress,initiated,denied':
          return {
            step: 6,
            name: 'Contractor denies a bid',
            description: ''
          }
        case 'sent,waiting_for_customer_approval,waiting_for_customer_approval':
          return {
            step: 7,
            name: 'Contractor submits a bid',
            description: ''
          }
        case 'changed,waiting_for_customer_approval,waiting_for_customer_approval':
          return {
            step: 8,
            name: 'Customer changes a bid',
            description: ''
          }
        case 'canceled_by_customer,canceled_by_customer,canceled_by_customer':
          return {
            step: 9,
            name: 'Customer cancels a bid',
            description: ''
          }
        case 'canceled_by_general,canceled_by_general,canceled_by_general':
          return {
            step: 10,
            name: 'General cancels a bid',
            description: ''
          }
        case ',,canceled_bid_task':
          return {
            step: 11,
            name: 'Sub cancels a bid_task',
            description: ''
          }
        case 'approved,approved_by_customer,approved_by_customer':
          return {
            step: 12,
            name: 'Customer approves a bid',
            description: ''
          }
        case 'approved,general_finished_work,':
          return {
            step: 13,
            name: 'Contractor finishes task',
            description: ''
          }
        case 'approved,approved_by_customer,finished_job':
          return {
            step: 14,
            name: 'Sub finishes task',
            description: ''
          }
        case 'approved,approved_by_customer,finished_job_denied_by_contractor':
          return {
            step: 15,
            name: 'General declines the task',
            description: ''
          }
        case 'approved,approved_by_customer,finished_job_approved_by_contractor':
          return {
            step: 16,
            name: 'General approves the task',
            description: ''
          }
        case 'declines_finished_task,customer_changes_finished_task,customer_changes_finished_task':
          return {
            step: 17,
            name: 'Customer declines the task',
            description: ''
          }
        case 'approved,approved_by_customer,waiting_for_customer_payment':
          return {
            step: 18,
            name: 'Customer has not paid for finished job',
            description: ''
          }
        case 'paid,paid,paid':
          return {
            step: 19,
            name: 'Customer pays for job',
            description: ''
          }
        default:
          break
      }
    }

  }
}