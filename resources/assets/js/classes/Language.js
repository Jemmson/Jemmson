export default class Language {
  static lang () {
    return {
      estimate: {
        initiated: {
          sub: null,
          general: 'Initiated',
          customer: null
        },
        in_progress: {
          bid_task: {
            initiated: {
              noSub: {
                sub: null,
                general: 'Please Finish and Submit your Bid',
                customer: 'Please Wait for Finished Bid'
              },
              sub: {
                initiated: {
                  sub: 'Please Review Task and Submit Bid',
                  general: 'Please Finish and Submit your Bid',
                  customer: null
                },
                task_sent: {
                  sub: 'Please Wait for General to Complete Bid',
                  general: 'Please Finish and Submit your Bid',
                  customer: 'Please Wait for Finished Bid'
                },
                accepted: {
                  sub: 'Congratulations! General has Accepted your Bid',
                  general: 'Please Finish and Submit your Bid',
                  customer: 'Please Wait for Finished Bid'
                },
                denied: {
                  sub: '',
                  general: 'Please Finish and Submit your Bid',
                  customer: 'Please Wait for Finished Bid'
                }
              }
            }
          }
        },
        sent: {
          bid_task: {
            waiting_for_customer_approval: {
              noSub: {
                sub: null,
                general: 'Your Bid has Been Successfully Sent',
                customer: 'Your Bid is Complete. Please Approve, Change, or Cancel'
              },
              sub: {
                waiting_for_customer_approval: {
                  sub: 'Your Task has Been Sent. Waiting on Customer Approval',
                  general: 'Your Bid has Been Successfully Sent',
                  customer: 'Your Bid is Complete. Please Approve, Change, or Cancel'
                }
              }
            }
          }
        },
        changed: {
          bid_task: {
            customer_changes_bid: {
              sub: {
                noSub: {
                  sub: null,
                  general: 'Customer Requests a Change. Please Review',
                  customer: 'Waiting on General to Review Changes'
                },
                customer_changes_bid: {
                  sub: 'Customer is Requesting a Change. Please Review',
                  general: 'Customer Requests a Change. Please Review',
                  customer: 'Waiting on General to Review Changes'
                }
              }
            }
          }
        },
        canceled_by_customer: {
          bid_task: {
            canceled_by_customer: {
              noSub: {
                sub: null,
                general: 'Customer has Canceled Job',
                customer: 'Thank You. Sorry Job did not Work Out'
              },
              sub: {
                canceled_by_customer: {
                  sub: 'Customer has Canceled Job',
                  general: 'Customer has Canceled Job',
                  customer: 'Thank You. Sorry Job did not Work Out'
                }
              }
            }
          }
        },
        canceled_by_general: {
          bid_task: {
            canceled_by_general: {
              noSub: {
                sub: null,
                general: 'General has Canceled Job',
                customer: 'General has Canceled Job'
              },
              sub: {
                canceled_by_customer: {
                  sub: 'General has Canceled Job',
                  general: 'General has Canceled Job',
                  customer: 'General has Canceled Job'
                }
              }
            }
          }
        },
      },
      job: {
        approved: {
          bid_task: {
            approved_by_customer: {
              noSub: {
                sub: null,
                general: 'Congratulations! Bid has been Approved. Please Begin Work!',
                customer: 'Thank You for Approving Work. Contractor will begin work'
              },
              sub: {
                approved_by_customer: {
                  sub: 'Congratulations! Bid has been Approved. Please Begin Work!',
                  general: 'Congratulations! Bid has been Approved. Please Begin Work!',
                  customer: 'Thank You for Approving Work. Contractor will begin work'
                },
                finished_job: {
                  sub: 'Task Completed. Please wait for General to Approve the Work',
                  general: 'Sub Has finished. Please Approve the job',
                  customer: 'Sub Has finished. General is reviewing bid'
                },
                finished_job_approved_by_contractor: {
                  sub: 'General Approved',
                  general: '',
                  customer: ''
                },
                finished_job_denied_by_contractor: {
                  sub: 'General Requests a Change on Job. Please Review',
                  general: 'Job Change Sent. Sub is Reviewing',
                  customer: 'Sub Has finished. General is reviewing bid'
                }
              }
            },
            general_finished_work: {
              sub: null,
              general: 'Please wait for customer to pay for task',
              customer: 'Task is Finished. Please Pay for task'
            }
          }
        },
        declines_finished_task: {
          bid_task: {
            customer_changes_finished_task: {
              sub: {
                customer_changes_finished_task: {
                  sub: 'Customer Requests a Change. Please Review',
                  general: 'Customer Requests a Change. Please Review',
                  customer: 'Change Sent. Contractors are reviewing'
                }
              }
            }
          }
        },
        paid: {
          bid_task: {
            paid: {
              noSub: {
                sub: null,
                general: 'Customer Has Sent Payment!',
                customer: 'Thank You For Your Payment'
              },
              sub: {
                paid: {
                  sub: 'Customer Has Sent Payment!',
                  general: 'Customer Has Sent Payment!',
                  customer: 'Thank You For Your Payment'
                }
              }
            }
          }
        }
      },
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
        customer: 'You will be notified when your estimate is ready'
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
        general: 'Bid Change Requested. Please Review',
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