export default class Customer {

  constructor() {
    this.user = Spark.state.user
  }

  /**
   * Approve bid and all its tasks.
   *
   * @param {Object} bidForm
   */
  approveBid(bidForm, disabled) {
    console.log('approve')
    disabled.approve = true
    Spark.post('/job/approve/' + bidForm.id, bidForm)
      .then((response) => {
        console.log(response)
        User.emitChange('bidUpdated')
        Vue.toasted.success('Job Approved')
        disabled.approve = false
      }).catch((error) => {
      console.log(error)
      bidForm.errors.errors = bidForm.errors.errors.errors
      Vue.toasted.error('Whoops! Something went wrong! Please try again.')
      disabled.approve = false
    })
  }

  /**
   * Approve individual tasks of a bid
   *
   * @param {Object} task
   */
  approveTask(task) {

  }

  /**
   * Canel a job bid a contractor has submitted
   *
   * @param {Object} bid
   * @param {Object} disabled
   */
  async cancelBid(bid, disabled) {
    console.log(bid)
    disabled.cancelBid = true
    try {
      await axios.post('/job/cancel', bid)
      Vue.toasted.success('Bid Canceled')
      disabled.cancelBid = false
      location.href = '/#/bids'
      //Bus.$emit('bidUpdated', ['closeBid']);
    } catch (error) {
      error = error.response.data
      Vue.toasted.error(error.message)
      disabled.cancelBid = false
    }
  }

  /**
   * Decline a job bid a contractor has submitted
   *
   * @param {Object} bid
   * @param {Object} disabled
   */
  async declineBid(bid, disabled) {
    disabled.declineBid = true
    try {
      await axios.post('/bid/job/decline', bid)
      User.emitChange('bidUpdated')
      Vue.toasted.success('Bid Changed & Notification Sent')
      disabled.declineBid = false
    } catch (error) {
      error = error.response.data
      Vue.toasted.error(error.message)
      disabled.declineBid = false
    }
  }

  /**
   * Deny a task that has been finished
   *
   * @param {Object} jobTask
   */
  async denyTask(denyForm, disabled) {
    if (disabled.deny) {
      disabled.deny = true
    }
    try {
      await axios.post('/task/deny', {
        job_task_id: denyForm.job_task_id,
        user_id: denyForm.user_id,
        message: denyForm.message
      })
      User.emitChange('bidUpdated')
      Vue.toasted.success('Task Denied & Notification Sent')
      if (disabled.deny) {
        disabled.deny = false
        disabled.showDenyForm = false
      }
      $('#deny-task-modal').modal('hide')
    } catch (error) {
      error = error.response.data
      Vue.toasted.error(error.message)
      if (disabled.deny) {
        disabled.deny = false
      }
    }
  }

  getAddress(locationId, ajax_response) {
    console.log('locationId: ' + locationId)
    console.log('ajax_response: ' + ajax_response)
    // debugger
    axios.post('/api/customer/getAddress', {
      locationId: locationId
    }).then((response) => {
      console.log(response)
      console.log(response.data)
      ajax_response.location = response.data
    }).catch((error) => {
      console.log(error)
    })
  }

  getArea(jobId, ajax_response) {
    console.log('jobId: ' + jobId)
    console.log('ajax_response: ' + ajax_response)
    // debugger
    axios.post('/api/job/getArea', {
      job_id: jobId
    }).then((response) => {
      console.log(response)
      console.log(response.data)
      ajax_response.area = response.data
    }).catch((error) => {
      console.log(error)
    })
  }

  /**
   *
   * @param {object} job
   */
  async paidWithCash(job) {

  }

  /**
   * Pay for a task
   *
   * @param {Object} jobTask
   */
  async paidWithCashTask(jobTask, disabled) {
    console.log('paidWithCashTask', jobTask)
    disabled.payCash = true

    try {
      await axios.post('/api/stripe/task/cash', jobTask)
      User.emitChange('bidUpdated')
      Vue.toasted.success('Paid For Task')
      disabled.payCash = false
    } catch (error) {
      error = error.response.data
      Vue.toasted.error(error.message)
      disabled.payCash = false
    }

  }

  /**
   *
   * @param {int} id
   * @param {obj} excluded
   * @param {obj} disabled
   */
  async payAllPayableTasks(id, excluded, disabled) {
    console.log('payAllPayableTasks', id)
    disabled.payAll = true

    if (!User.isSignedUpWithStripe()) {
      console.log('No Stripe Account')
      Bus.$emit('needsStripe')
      disabled.payAll = false
      return false
    }

    try {
      await axios.post('/stripe/customer/pay/tasks', {id: id, excluded: excluded})
      User.emitChange('bidUpdated')
      Vue.toasted.success('Paid For All Payable Tasks')
      disabled.payAll = false
    } catch (error) {
      error = error.response.data
      Vue.toasted.error(error.message)
      disabled.payAll = false
    }
  }

  /**
   *
   * @param {int} id
   * @param {obj} excluded
   * @param {obj} disabled
   */
  async payAllPayableTasksWithCash(id, excluded, disabled, cashMessage) {
    console.log('payAllPayableTasksWithCash', id)
    disabled.payCash = true
    try {
      await axios.post('/stripe/customer/pay/tasks/cash', {
        id: id,
        excluded: excluded,
        cashMessage: cashMessage
      })
      User.emitChange('bidUpdated')
      Vue.toasted.success('Paid For All Payable Tasks')
      disabled.payCash = false
    } catch (error) {
      error = error.response.data
      Vue.toasted.error(error.message)
      disabled.payCash = false
    }
  }

  /**
   * Pay for a task
   *
   * @param {Object} task
   */
  async payForTask(jobTask, disabled) {
    console.log('payForTask', jobTask)
    disabled.pay = true
    try {
      await axios.post('/stripe/express/task/payment', jobTask)
      User.emitChange('bidUpdated')
      Vue.toasted.success('Paid For Task')
      disabled.pay = false
    } catch (error) {
      error = error.response.data
      Vue.toasted.error(error.message)
      disabled.pay = false
    }

  }

  updateArea(area, jobId) {

    axios.post('/api/job/updateArea', {
      job_id: jobId,
      area: area
    }).then((response) => {
      Vue.toasted.success('Area Updated')
    }).catch((error) => {
      console.log(error)
      Vue.toasted.error('Area was not able to be updated')
    })
  }

  async updateNotesForJob(customerNotesMessage, customer_id) {
    try {
      await axios.post('/customer/updateCustomerNotes',
        {
          customerNotesMessage: customerNotesMessage,
          customer_id: customer_id
        }
      )
      User.emitChange('bidUpdated')
      Vue.toasted.success('Customer Note Has Been Updated')
    } catch (error) {
      // error = error.response.data
      Vue.toasted.error(error)
    }
  }

}