export default class Format {

  constructor(){

  }

  static decimal (number) {
    // let num = parseInt(number) * 100;
    // let num = parseInt(number)/100;
    return number.toFixed(2);
  }

  static addDollarSign(obj, key) {
    obj[key] = '$ ' + this.numbersOnly(obj[key])
  }

  static customerLabel(status) {
    switch (status) {
      // action required: yellow
      case 'bid_task.approved_by_general':
      case 'bid_task.finished_by_general':
      case 'bid.sent':
        return 'status-yellow'
        break
      // important update: blue
      case 'job.approved':
      case 'bid.in_progress':
      case 'bid_task.approved_by_customer':
      case 'bid_task.reopened':
        return 'status-blue'
        break
      // rejected something: red
      case 'bid.declined':
      case 'bid_task.denied':
        return 'status-red'
        break
      // finished: green
      case 'job.completed':
      case 'bid_task.customer_sent_payment':
        return 'status-green'
      // don't need to do anything and not important info: grey
      case 'bid.initiated':
      case 'bid_task.initiated':
      case 'bid_task.bid_sent':
      case 'bid_task.accepted':
      case 'bid_task.finished_by_sub':
      default:
        return 'status-grey'
        break
    }
  }

  static generalContractorLabel(status) {
    switch (status) {
      // action required: yellow
      case 'bid_task.reopened':
      case 'bid_task.bid_sent':
      case 'bid_task.finished_by_sub':
      case 'bid_task.approved_by_customer':
      case 'job.approved':
        return 'status-yellow'
        break
      // important update: blue
      case 'bid_task.accepted':
      case 'bid.sent':
        return 'status-blue'
        break
      // rejected something: red
      case 'bid.declined':
      case 'bid_task.denied':
        return 'status-red'
        break
      // finished: green
      case 'job.completed':
      case 'bid_task.customer_sent_payment':
        return 'status-green'
        break
      // don't need to do anything and not important info: grey
      case 'bid.initiated':
      case 'bid.in_progress':
      case 'bid_task.initiated':
      case 'bid_task.approved_by_general':
      case 'bid_task.finished_by_general':
      default:
        return 'status-grey'
        break
    }
  }

  static jobName(name) {
    // if (name.length > 20) {
    //     return name.substring(0, 20) + '...';
    // }
    return name
  }

  static numbers(obj, key) {
    obj[key] = this.numbersOnly(obj[key])
  }

  static numbersOnly(num) {
    return num.toString().replace(/[^0-9.]/g, '')
  }

  static phone(phone) {
    return phone.replace(/[^0-9]/g, '')
      .replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3')
  }

  /**
   * Get Job or JobTask statuses for specific user types
   *
   * @param {String} status
   * @param {JobTask} jobTask
   */
  static statusLabel(status, isCustomer, isGeneral, jobTask) {

    if (typeof jobTask !== 'undefined') {
      console.log(jobTask)
      // job_task status
      if (isCustomer) {
        return this.customerLabel(status)
      } else if (isGeneral) {
        return this.generalContractorLabel(status)
      } else {
        // subcontractor labels
        return this.subContractorLabel(status)
      }

    } else {
      // is job status

      if (isCustomer) {
        return this.customerLabel(status)
      } else {
        // contractor labels
        return this.generalContractorLabel(status)
      }
    }
  }

  static subContractorLabel(status) {
    switch (status) {
      // action required: yellow
      case 'bid_task.reopened':
      case 'bid_task.approved_by_customer':
        return 'status-yellow'
        break
      // important update: blue
      case 'job.approved':
      case 'bid_task.approved_by_general':
      case 'bid_task.accepted':
        return 'status-blue'
        break
      // rejected something: red
      case 'bid.declined':
      case 'bid_task.denied':
        return 'status-red'
        break
      // finished: green
      case 'job.completed':
      case 'bid_task.customer_sent_payment':
        return 'status-green'
      // don't need to do anything and not important info: grey
      case 'bid.initiated':
      case 'bid.in_progress':
      case 'bid_task.initiated':
      case 'bid_task.bid_sent':
      case 'bid_task.finished_by_sub':
      case 'bid_task.finished_by_general':
      case 'bid.sent':
      default:
        return 'status-grey'
        break
    }
  }
}