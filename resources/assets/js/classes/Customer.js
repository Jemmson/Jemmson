export default class Customer {

  /**
   * Approve bid and all its tasks.
   *
   * @param {Object} bidForm
   */
  approveBid(bidForm, disabled) {
    console.log('approve');
    disabled.approve = true;
    Spark.post('/job/approve/' + bidForm.id, bidForm)
      .then((response) => {
        console.log(response);
        User.emitChange('bidUpdated');
        Vue.toasted.success('Job Approved');
        disabled.approve = false;
      }).catch((error) => {
        console.log(error);
        bidForm.errors.errors = bidForm.errors.errors.errors;
        Vue.toasted.error('Whoops! Something went wrong! Please try again.');
        disabled.approve = false;
      });
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
    disabled.cancelBid = true;
    try {
      const data = await axios.post('/api/job/cancel', bid);
      Bus.$emit('bidUpdated', ['closeBid']);
      Vue.toasted.success('Bid Canceled');
      disabled.cancelBid = false;
      location.href = "/#/bids";
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error(error.message);
      disabled.cancelBid = false;
    }
  }

  /**
   * Decline a job bid a contractor has submitted
   *
   * @param {Object} bid
   * @param {Object} disabled
   */
  async declineBid(bid, disabled) {
    disabled.declineBid = true;
    try {
      const data = await axios.post('/bid/job/decline', bid);
      User.emitChange('bidUpdated');
      Vue.toasted.success('Bid Declined & Notification Sent');
      disabled.declineBid = false;
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error(error.message);
      disabled.declineBid = false;
    }
  }

  /**
   * Deny a task that has been finished
   *
   * @param {Object} jobTask
   */
  async denyTask(denyForm, disabled) {
    disabled.deny = true;
    try {
      const data = await axios.post('/task/deny', {job_task_id: denyForm.job_task_id, user_id: denyForm.user_id, message: denyForm.message});
      User.emitChange('bidUpdated');
      Vue.toasted.success('Task Denied & Notification Sent');
      disabled.deny = false;
      disabled.showDenyForm = false;
      $('#deny-task-modal').modal('hide');
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error(error.message);
      disabled.deny = false;
    }
  }

  getArea(jobId, ajax_response) {
    console.log("jobId: " + jobId)
    console.log("ajax_response: " + ajax_response)
    // debugger
    axios.post('/api/job/getArea', {
      job_id: jobId
    }).then((response) => {
      console.log(response)
      console.log(response.data)
      ajax_response.area = response.data
    }).catch((error) => {
      console.log(error);
    })
  }

  getAddress(locationId, ajax_response) {
    console.log("locationId: " + locationId)
    console.log("ajax_response: " + ajax_response)
    // debugger
    axios.post('/api/customer/getAddress', {
      locationId: locationId
    }).then((response) => {
      console.log(response)
      console.log(response.data)
      ajax_response.location = response.data
    }).catch((error) => {
      console.log(error);
    })
  }

  /**
   * 
   * @param {object} job 
   */
  async paidWithCash(job) {

  }

  /**
   * Pay for a all payable tasks
   *
   * @param {Object} job
   */
  async payAllPayableTasks(job, disabled) {
    console.log('payAllPayableTasks', job);
    disabled.payAll = true;

    if (User.payWithStripe()) {
      if (!User.isSignedUpWithStripe()) {
        console.log('No Stripe Account');
        Bus.$emit('needsStripe');
        disabled.payAll = false;
        return false;
      }
    }

    try {
      const data = await axios.post('/stripe/customer/pay/tasks', job);
      User.emitChange('bidUpdated');
      Vue.toasted.success('Paid For All Payable Tasks');
      disabled.payAll = false;
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error(error.message);
      disabled.payAll = false;
    }

  }

  /**
   * Pay for a task
   *
   * @param {Object} jobTask
   */
  async paidWithCashTask(jobTask, disabled) {
    console.log('paidWithCashTask', jobTask);
    disabled.payCash = true;

    try {
      const data = await axios.post('/api/stripe/task/cash', jobTask);
      User.emitChange('bidUpdated');
      Vue.toasted.success('Paid For Task');
      disabled.payCash = false;
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error(error.message);
      disabled.payCash = false;
    }

  }

  /**
   * Pay for a task
   *
   * @param {Object} task
   */
  async payForTask(jobTask, disabled) {
    console.log('payForTask', jobTask);
    disabled.pay = true;

    if (User.payWithStripe()) {
      if (!User.isSignedUpWithStripe()) {
        console.log('No Stripe Account');
        Bus.$emit('needsStripe');
        disabled.pay = false;
        return false;
      }
    }

    try {
      const data = await axios.post('/stripe/express/task/payment', jobTask);
      User.emitChange('bidUpdated');
      Vue.toasted.success('Paid For Task');
      disabled.pay = false;
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error(error.message);
      disabled.pay = false;
    }

  }

  updateArea(area, jobId) {

    axios.post('/api/job/updateArea', {
      job_id: jobId,
      area: area
    }).then((response) => {
      Vue.toasted.success('Area Updated');
    }).catch((error) => {
      console.log(error);
      Vue.toasted.error('Area was not able to be updated');
    })
  }

  constructor() {
    this.user = Spark.state.user;
  }

}