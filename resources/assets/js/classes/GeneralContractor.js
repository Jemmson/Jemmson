import Language from './Language'
import {router} from '../router.js'

export default class GeneralContractor {

    acceptSubBidForTask(jobTask, bid, disabled) {
        console.log('acceptSubBidForTask', jobTask)
        disabled.accept = true
        axios.post('/api/task/accept', {
            jobId: jobTask.job_id,
            jobTaskId: jobTask.id,
            contractorId: bid.contractor_id,
            bidId: bid.id,
            price: bid.bid_price,
        }).then((response) => {
            console.log(response.data)
            User.emitChange('bidUpdated')
            Vue.toasted.success('Accepted Bid!')
            disabled.accept = false
        }).catch((error) => {
            console.error(error)
            Vue.toasted.error('Error Trying to Accept Bid!')
            disabled.accept = false
        })
    }

    addNewTaskToBid(bid, form) {
        console.log('sendSubInviteToBidOnTask', bid, form)
        // I want the status to go from initiated to in progress when the first new task is added
        // I want each task to be added to the the tasks table
        // I want the task to associated to a job, customer, and contractor
        // I want to add the existing task to the job

        form.jobId = bid.id
        form.contractorId = Spark.state.user.id

        Format.numbers(form, 'taskPrice')
        Format.numbers(form, 'subTaskPrice')

        Spark.post('task/addTask', form)
            .then((response) => {
                console.log(response)
                // NOTICE: using Spark.post returns the exact data so response.data doesn't have anything its already data
                // show a toast notification
                form.taskName = ''
                form.taskPrice = 0
                form.subTaskPrice = 0
                form.qty = 1
                form.taskId = -1

                Bus.$emit('taskAdded', true)
                User.emitChange('bidUpdated')
                Vue.toasted.success('New Task Added!')
                // $('#add-task-modal').modal('hide')
                return true
            }).catch(error => {
            console.error(error)
            // NOTICE: lets us do addNewTaskForm.errors.has('errorName') to check if this error exists & addNewTaskForm.errors.get('errorName') to get the error message
            // usually we don't have to do this, but api routes messes this up
            // we don't have to do this for web routes we can just call addNewTaskForm.errors.has('errorName')
            // without catching the error and assigning the errors
            form.errors.errors = error.errors
            // show a toast notification
            if (error.message !== undefined && error.message !== null) {
                Vue.toasted.error(error.message)
            }
            return false
        })
    }

    adjustDate(date) {
        let d = ''
        if (date === 'today') {
            d = new Date()
            let year = d.getFullYear()
            let month = d.getMonth() + 1
            let day = d.getDate()
            return [year, month, day]
        } else {
            let dateArray = date.split('-')
            dateArray[0] = parseInt(dateArray[0])
            dateArray[1] = parseInt(dateArray[1])
            dateArray[2] = parseInt(dateArray[2])
            return dateArray
        }

        //
        // let year = d.getFullYear ();
        // let month = d.getMonth () + 1;
        // let day = d.getDate () + 1;
        //
        // if (month === 12 && day === 32) {
        //   year = year + 1;
        //   month = 1;
        //   day = 1;
        // } else if (day === 32) {
        //   month = month + 1;
        //   day = 1;
        // } else if (day === 31 && (
        //   month === 4 ||
        //   month === 6 ||
        //   month === 9 ||
        //   month === 11
        // )
        // ) {
        //   day = 1;
        //   month = month + 1;
        // } else if (
        //   day === 29 && month === 2 && (year % 4 !== 0)
        // ) {
        //   day = 1;
        //   month = month + 1;
        // } else if (
        //   day === 30 && month === 2 && (year % 4 === 0)
        // ) {
        //   day = 1;
        //   month = month + 1;
        // }
        //
        // return [year, month, day];

    }

    approveTaskHasBeenFinished(jobTask, disabled) {
        console.log('approveTaskHasBeenFinished', jobTask)
        disabled.approve = true
        axios.post('task/approve', jobTask)
            .then((response) => {
                console.log(response)
                // show a toast notification
                User.emitChange('bidUpdated')
                Vue.toasted.success(Language.lang().submit.approve_task.success)
                disabled.approve = false
            }).catch(error => {
            console.error(error)
            // show a toast notification
            Vue.toasted.error('Error: ' + error.message)
            disabled.approve = false
        })
    }

    checkDateIsTodayorLater(firstDate, secondDate) {
        let pickerDate = this.adjustDate(firstDate)
        let today = this.adjustDate(secondDate)
        let errorMessage = ''
        let hasDateError = false
        if (
            (pickerDate[0] < today[0]) ||
            (pickerDate[0] === today[0] && pickerDate[1] < today[1]) ||
            (pickerDate[0] === today[0] && pickerDate[1] === today[1] && pickerDate[2] < today[2])
        ) {
            errorMessage = 'Start Date cannot be before today\'s date'
            hasDateError = true
        } else {
            hasDateError = false
        }
        return [errorMessage, hasDateError]
    }

    async deleteTask(jobTask, disabled) {
        disabled.deleteTask = true
        try {
            const data = await axios.post('/api/task/delete', {
                jobTaskId: jobTask.id,
                jobId: jobTask.job_id
            })
            User.emitChange('bidUpdated')
            Vue.toasted.success('Task Deleted')
            disabled.deleteTask = false
        } catch (error) {
            error = error.response.data
            Vue.toasted.error(error.message)
            disabled.deleteTask = false
        }
    }

    // SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'pike.shawn@gmail.com' for key 'users_email_unique' (SQL: insert into `users` (`name`, `email`, `phone`, `usertype`, `password_updated`, `password`, `updated_at`, `created_at`) values (sjdsskdj, pike.shawn@gmail.com, 6024326933, customer, 0, $2y$10$3hfOxxahyXmKvc1IN71xn.//Is8H./U.KPwuunTSX9jLgvZe/FP4O, 2018-04-21 09:51:22, 2018-04-21 09:51:22))

    getBid(id) {
        axios.get('/job/' + id).then((data) => {
            console.log(JSON.stringify(data.data))
            return data.data
        }).catch(() => {
            console.log(error)
            // debugger;
            if (
                error.message === 'Not Authorized to access this resource/api' ||
                error.response.status === 403
            ) {
                this.$router.push('/bids')
            }
            // error = error.response.data;
            // Vue.toasted.error(error.message);
            Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.')
        })
    }

    goToUserAuthorizationPage() {
        router.push('userAuthorizationPage')
    }

    async initiateBid(form, disabled) {
        disabled.submit = true
        console.log(form)
        try {
            const data = await axios.post('/initiate-bid', form)
            if (data.data.tokenState !== undefined) {
                if (data.data.tokenState === 'refreshTokenHasExpired') {
                    this.goToUserAuthorizationPage()
                }
            }
            console.log(data)
            console.log(JSON.stringify(data))
            Vue.toasted.success('Bid Initiated')
            disabled.submit = false
            window.location = '/#/bids'
        } catch (error) {
            console.log(error)
            error = error.response.data
            form.errors.errors = error.errors
            Vue.toasted.error(error.message)
            console.log('Initiate bid errors')
            console.log(error)
            console.log(error.message)
            disabled.submit = false
            if (error.errors['no_free_jobs'] !== undefined) {
                window.location = '/settings#/subscription'
            }
        }
    }

    async jobCompleted(job, disabled) {
        disabled.jobCompleted = true
        try {
            const data = await axios.post('/api/job/completed', {
                id: job.id
            })
            User.emitChange('bidUpdated')
            Vue.toasted.success('Job Completed')
            disabled.jobCompleted = false
        } catch (error) {
            error = error.response.data
            Vue.toasted.error(error.message)
            disabled.jobCompleted = false
        }

    }

    notifyCustomerOfFinishedBid(bid, disabled) {
        disabled.submitBid = true
        // TODO: implement the code below
        // if (User.needsStripe()) {
        //     disabled.submitBid = false
        //     return false
        // }
        console.log('notifyCustomerOfFinishedBid', bid);
        axios.post('/api/task/finishedBidNotification', {
            jobId: bid.id,
            customerId: bid.customer_id
        }).then((response) => {
            console.log(response);
            disabled.submitBid = false;
            User.emitChange('bidUpdated');
            Vue.toasted.success('Bid has been submitted and notification sent!');
        }).catch((error) => {
            console.error(error);
            disabled.submitBid = false;
            Vue.toasted.error('Whoops! Something went wrong! Please try again.');
        })
    }

    sendSubInviteToBidOnTask(jobTask, form, disabled, jobTaskId) {
        disabled.invite = true;
        // debugger;
        form.jobTaskId = jobTaskId;

        form.phone = form.phone.replace(/[^0-9]/g, '');

        Spark.post('/task/notify', form)
            .then((response) => {
                console.log(response)
                User.emitChange('bidUpdated');
                Vue.toasted.success('Invite Sent!');
                disabled.invite = false;
                form.counter++;
                form.name = '';
                form.email = '';
                form.phone = '';
            }).catch((error) => {
            console.error(error);
            form.errors.errors = error.errors;
            Vue.toasted.error('Error: ' + error.message);
            disabled.invite = false
        })
    }

    updateCustomerPrice(price, jobTaskId, jobId) {
        console.log(price)
        if (price === '') {
            price = 0
        }
        console.log(price)
        axios.post('/api/task/updateCustomerPrice', {
            jobId: jobId,
            jobTaskId: jobTaskId,
            price: price
        }).then((response) => {
            User.emitChange('bidUpdated')
            Vue.toasted.success(Language.lang().bid_task.price_updated.general)
            return true;
            // console.log(response.data)
            // this.updateAllTasksDataWithCustomerPrice(response.data.price, response.data.taskId)
        }).catch(error => {
            console.error(error)
            // show a toast notification
            Vue.toasted.error('Error: ' + error.message)
            return false
        })
    }

    async updateCustomerTaskQuantity(quantity, taskId) {
        try {
            const data = await axios.post('/api/task/updateTaskQuantity', {
                quantity: quantity,
                taskId: taskId
            })
            User.emitChange('bidUpdated')
            Vue.toasted.success(Language.lang().bid_task.quantity_updated.general)
        } catch (error) {
            Vue.toasted.error('Error: ' + error.message)
        }
    }

    async updateMessage(message, jobTaskId, actor) {
        try {
            const data = await axios.post('/api/task/updateMessage/', {
                message: message,
                jobTaskId: jobTaskId,
                actor: actor
            })
            User.emitChange('bidUpdated')
            Vue.toasted.success(Language.lang().bid_task.message_updated.general)
        } catch (error) {
            Vue.toasted.error('Error: ' + error.message)
        }
    }

    async updateTaskStartDate(date, jobTaskId) {
        try {
            const data = await axios.post('/api/task/updateTaskStartDate', {
                date: date,
                jobTaskId: jobTaskId,
            })
            User.emitChange('bidUpdated')
            Vue.toasted.success(Language.lang().bid_task.start_date.general)
        } catch (error) {
            Vue.toasted.error('Error: ' + error.message)
        }
    }

    constructor(user) {
        this.user = user
    }

    // async updateJobStartDate (date, jobId) {
    //   try {
    //     const data = await axios.post ('/api/task/updateJobStartDate', {
    //       date: date,
    //       jobId: jobId,
    //     });
    //     User.emitChange ('bidUpdated');
    //     Vue.toasted.success (Language.lang ().bid_task.start_date.general);
    //   } catch (error) {
    //     Vue.toasted.error ('Error: ' + error.message);
    //   }
    // }
}

export var __useDefault = true