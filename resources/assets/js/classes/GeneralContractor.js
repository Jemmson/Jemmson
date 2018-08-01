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
      User.emitChange ('bidUpdated');
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
        form.taskPrice = 0;
        form.subTaskPrice = 0;
        form.qty = 1;
        form.taskId = -1;
        
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
      if (error.message !== undefined && error.message !== null) {
        Vue.toasted.error (error.message);
      }
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
      Vue.toasted.success ('Task Deleted');
      disabled.deleteTask = false;
    } catch (error) {
      error = error.response.data;
      Vue.toasted.error (error.message);
      disabled.deleteTask = false;
    }
  }

  // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'pike.shawn@gmail.com' for key 'users_email_unique' (SQL: insert into `users` (`name`, `email`, `phone`, `usertype`, `password_updated`, `password`, `updated_at`, `created_at`) values (sjdsskdj, pike.shawn@gmail.com, 6024326933, customer, 0, $2y$10$3hfOxxahyXmKvc1IN71xn.//Is8H./U.KPwuunTSX9jLgvZe/FP4O, 2018-04-21 09:51:22, 2018-04-21 09:51:22))

  async initiateBid (form, disabled) {
    disabled.submit = true;
    console.log (form)
    try {
      const data = await axios.post ('/initiate-bid', form);
      console.log (data)
      Vue.toasted.success ('Bid Initiated');
      disabled.submit = false;
      window.location = '/#/bids';
    } catch (error) {
      console.log(error)
      error = error.response.data;
      form.errors.errors = error.errors;
      Vue.toasted.error (error.message);
      console.log ('Initiate bid errors')
      console.log (error)
      console.log (error.message)
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

  notifyCustomerOfFinishedBid (bid, disabled) {
    disabled.submitBid = true;
    if (User.needsStripe()) {
      disabled.submitBid = false;
      return false;
    }
    console.log ('notifyCustomerOfFinishedBid', bid);
    axios.post ('/api/task/finishedBidNotification', {
      jobId: bid.id,
      customerId: bid.customer_id
    }).then ((response) => {
      console.log (response);
      disabled.submitBid = false;
      User.emitChange ('bidUpdated');
      Vue.toasted.success ('Bid has been submitted and notification sent!');
    }).catch ((error) => {
      console.error (error);
      disabled.submitBid = false;
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
        form.counter++;
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

  async updateMessage (message, jobTaskId, actor) {
    try {
      const data = await axios.post ('/api/task/updateMessage', {
        message: message,
        jobTaskId: jobTaskId,
        actor: actor
      });
      User.emitChange ('bidUpdated');
      Vue.toasted.success (Language.lang ().bid_task.message_updated.general);
    } catch (error) {
      Vue.toasted.error ('Error: ' + error.message);
    }
  }

  async updateTaskStartDate (date, jobTaskId) {
    try {
      const data = await axios.post ('/api/task/updateTaskStartDate', {
        date: date,
        jobTaskId: jobTaskId,
      });
      User.emitChange ('bidUpdated');
      Vue.toasted.success (Language.lang ().bid_task.start_date.general);
    } catch (error) {
      Vue.toasted.error ('Error: ' + error.message);
    }
  }

  async updateCustomerTaskQuantity (quantity, taskId) {
    try {
      const data = await axios.post ('/api/task/updateTaskQuantity', {
        quantity: quantity,
        taskId: taskId
      });
      User.emitChange ('bidUpdated');
      Vue.toasted.success (Language.lang ().bid_task.quantity_updated.general);
    } catch (error) {
      Vue.toasted.error ('Error: ' + error.message);
    }
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