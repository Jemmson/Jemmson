import Language from './Language';

export default class GeneralContractor {

  acceptSubBidForTask (jobTask, bid, disabled) {
    console.log ('acceptSubBidForTask', jobTask);
    disabled.accept = true;
    axios.post ('/api/task/accept', {
      jobId: jobTask.job_id,
      jobTaskId: jobTask.id,
      contractorId: bid.contractor_id,
      bidId: bid.id,
      price: bid.bid_price,
    }).then ((response) => {
      console.log (response.data)
      User.emitChange('bidUpdated');
      Vue.toasted.success ('Accepted Bid!');
      disabled.accept = false;
    }).catch ((error) => {
      console.error (error);
      Vue.toasted.error ('Error Trying to Accept Bid!');
      disabled.accept = false;
    });
  }

  addNewTaskToBid (bid, form) {
    console.log ('sendSubInviteToBidOnTask', bid, form);
    // I want the status to go from initiated to in progress when the first new task is added
    // I want each task to be added to the the tasks table
    // I want the task to associated to a job, customer, and contractor
    // I want to add the existing task to the job

    // TODO: handle tasks existing
    form.taskId = 1;
    form.taskExists = false;

    form.jobId = bid.id;
    form.contractorId = Spark.state.user.id;

    Format.numbers (form, 'taskPrice');
    Format.numbers (form, 'subTaskPrice');

    Spark.post ('/api/task/addTask', form)
      .then ((response) => {
        console.log (response)
        // NOTICE: using Spark.post returns the exact data so response.data doesn't have anything its already data
        // show a toast notification
        form.taskName = '';
        form.taskPrice = '';
        form.subTaskPrice = '';
        Bus.$emit ('taskAdded', true);
        User.emitChange ('bidUpdated');
        Vue.toasted.success ('New Task Added!');
        $ ('#add-task-modal').modal ('hide');
      }).catch (error => {
      console.error (error);
      // NOTICE: lets us do addNewTaskForm.errors.has('errorName') to check if this error exists & addNewTaskForm.errors.get('errorName') to get the error message
      // usually we don't have to do this, but api routes messes this up
      // we don't have to do this for web routes we can just call addNewTaskForm.errors.has('errorName')
      // without catching the error and assigning the errors
      form.errors.errors = error.errors;
      // show a toast notification
      Vue.toasted.error ('Whoops! Something went wrong! Please try again.');
    });
  }

  approveTaskHasBeenFinished (jobTask, disabled) {
    console.log ('approveTaskHasBeenFinished', jobTask);
    disabled.approve = true;
    axios.post ('/api/task/approve', jobTask)
      .then ((response) => {
        console.log (response);
        // show a toast notification
        User.emitChange ('bidUpdated');
        Vue.toasted.success (Language.lang ().submit.approve_task.success);
        disabled.approve = false;
      }).catch (error => {
      console.error (error);
      // show a toast notification
      Vue.toasted.error ('Error: ' + error.message);
      disabled.approve = false;
    });
  }

  async deleteTask (jobTask, disabled) {
    disabled.deleteTask = true;
    try {
      const data = await axios.post ('/api/task/delete', {
        jobTaskId: jobTask.id,
        jobId: jobTask.job_id
      });
      User.emitChange ('bidUpdated');
      Vue.toasted.success ('Task Denied & Notification Sent');
      disabled.deleteTask = false;
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error (error.message);
      disabled.deleteTask = false;
    }
  }

  async initiateBid (form, disabled) {
    disabled.submit = true;
    console.log (form)
    try {
      const data = await axios.post ('/initiate-bid', form);
      console.log(data)
      User.emitChange ('bidUpdated');
      Vue.toasted.success ('Bid Initiated');
      disabled.submit = false;
      window.location = '/#/bids';
    } catch (error) {
      error = error.response.data;
      form.errors.errors = error.errors;
      Vue.toasted.error (error.message);
      console.log ('Initiate bid errors')
      console.log (error)
      disabled.submit = false;
      if (error.errors['no_free_jobs'] !== undefined) {
        window.location = '/settings#/subscription';
      }
    }
  }

  async jobCompleted (job, disabled) {
    disabled.jobCompleted = true;
    try {
      const data = await axios.post ('/api/job/completed', {
        id: job.id
      });
      User.emitChange ('bidUpdated');
      Vue.toasted.success ('Job Completed');
      disabled.jobCompleted = false;
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error (error.message);
      disabled.jobCompleted = false;
    }

  }

  notifyCustomerOfFinishedBid (bid) {
    if (User.needsStripe (bid)) {
      return false;
    }
    console.log ('notifyCustomerOfFinishedBid', bid);
    axios.post ('/api/task/finishedBidNotification', {
      jobId: bid.id,
      customerId: bid.customer_id
    }).then ((response) => {
      console.log (response);
      User.emitChange ('bidUpdated');
      Vue.toasted.success ('Bid has been submitted and notification sent!');
    }).catch ((error) => {
      console.error (error);
      Vue.toasted.error ('Whoops! Something went wrong! Please try again.');
    });
  }

  sendSubInviteToBidOnTask (jobTask, form, disabled) {
    console.log ('sendSubInviteToBidOnTask', jobTask, form);
    disabled.invite = true;
    form.jobTaskId = jobTask.id;
    Spark.post ('/api/task/notify', form)
      .then ((response) => {
        console.log (response);
        User.emitChange ('bidUpdated');
        Vue.toasted.success ('Invite Sent!');
        disabled.invite = false;
        form.name = '';
        form.email = '';
        form.phone = '';
      }).catch ((error) => {
      console.error (error);
      form.errors.errors = error.errors;
      Vue.toasted.error ('Error: ' + error.message);
      disabled.invite = false;
    });
  }

  updateCustomerPrice (price, jobTaskId, jobId) {
    console.log (price)
    if (price === '') {
      price = 0
    }
    console.log (price)
    axios.post ('/api/task/updateCustomerPrice', {
      jobId: jobId,
      jobTaskId: jobTaskId,
      price: price
    }).then ((response) => {
      User.emitChange ('bidUpdated');
      Vue.toasted.success (Language.lang ().bid_task.price_updated.general);
      // console.log(response.data)
      // this.updateAllTasksDataWithCustomerPrice(response.data.price, response.data.taskId)
    }).catch (error => {
      console.error (error);
      // show a toast notification
      Vue.toasted.error ('Error: ' + error.message);
    });
  }

  constructor () {
    this.user = Spark.state.user;
  }
}