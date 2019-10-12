export default class Language {
  static lang () {
    return {
      // initiated
      'bid_task.initiated': {
        sub: 'Please Bid',
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
        general: 'You Accepted a Subs Bid',
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
        sub: 'Customer Sent A Payment',
        general: 'Customer Sent A Payment',
        customer: 'Sent Payment'
      },
      'bid_task.reopened': {
        sub: 'Waiting on Contractor Approval',
        general: 'Reopened',
        customer: 'Reopened'
      },
      'bid_task.denied': {
        sub: 'Work not approved',
        general: 'Work not approved',
        customer: 'Not Approved, Waiting for Resubmit'
      },
      'bid.initiated': {
        sub: 'Waiting on General Contractor to finish job bid',
        general: 'Bid Initiated',
        customer: 'You will be notified when your estimate is ready!'
      },
      'bid.in_progress': {
        sub: 'Waiting on General Contractor to Submit Final Bid',
        general: 'Bid In Progress',
        customer: 'Waiting on Contractor to Submit Final Bid'
      },
      // general is finished with their bids and has sent
      // the bid to the customer for approval
      'bid.sent': {
        sub: 'Waiting on Customer Approval',
        general: 'Waiting on Customer Approval',
        customer: 'Please Review and Approve'
      },
      'bid.declined': {
        sub: 'Waiting on Customer Approval - sub',
        general: 'Bid Change Requested.Please Review',
        customer: 'Bid Change Requested. Waiting On Contractor'
      },
      'job.approved': {
        sub: 'In Progress',
        general: 'In Progress',
        customer: 'Approved'
      },
      'job.completed': {
        sub: 'Job Completed',
        general: 'Job Completed',
        customer: 'Job Completed'
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
      },
      // general contractor accepted the subs bid
      bid_task: {
        start_date: {
          sub: '',
          general: 'Start Date Has Been Updated',
          customer: ''
        },
        price_updated: {
          sub: '',
          general: 'Task Price Successfully Updated',
          customer: ''
        },
        quantity_updated: {
          sub: '',
          general: 'Task Quantity Successfully Updated',
          customer: ''
        },
        message_updated: {
          sub: '',
          general: 'Your Message has been Successfully Updated',
          customer: ''
        }
      },
      phone: {
        success: 'This is a valid mobile number',
        failure: 'This is not a valid mobile number',
        error: 'Please check the number. There was an unkown error'
      },
      modal: {
        reviewBidConfirmationModal: 'The bid may have changed since you last saw it, please review the bid if you have not done so.'
      },
      sub: {
        stripe_success: "Your stripe account is now active!",
      }
    };
  }

  constructor () {
  }
}