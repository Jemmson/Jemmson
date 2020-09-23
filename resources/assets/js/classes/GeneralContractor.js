import Language from './Language'
import {router} from '../router.js'

export default class GeneralContractor {

    acceptSubBidForTask(jobTask, bid, disabled, generalId) {
        disabled.accept = true
        axios.post('/task/accept', {
            jobId: jobTask.job_id,
            jobTaskId: jobTask.id,
            contractorId: bid.contractor_id,
            generalId: generalId,
            bidId: bid.id,
            price: bid.bid_price
        }).then((response) => {
            User.emitChange('bidUpdated')
            Vue.toasted.success('Accepted Bid!')
            disabled.accept = false
        }).catch((error) => {
            Vue.toasted.error('Error Trying to Accept Bid!')
            disabled.accept = false
        })
    }

    addNewTaskToBid(bid, form) {
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

    async approveSubsTask(jobTask) {
        try {
            await axios.post('task/approve', {
                id: jobTask.id
            });
            Vue.toasted.success('Task Has Been Approved and Customer Has Been Notified')
            Bus.$on('bidUpdated');
        } catch (error) {
        }
    }

    adjustDate(date) {
        if (date) {
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
        }
    }

    approveTaskHasBeenFinished(jobTask, disabled) {
        disabled.approve = true
        axios.post('task/approve', jobTask)
            .then((response) => {
                // show a toast notification
                User.emitChange('bidUpdated')
                Vue.toasted.success(Language.lang().submit.approve_task.success)
                disabled.approve = false
            }).catch(error => {
            // show a toast notification
            Vue.toasted.error('Error: ' + error.message)
            disabled.approve = false
        })
    }

    finishedTask(bid, disabled) {

        let id = this.user.id;
        bid.current_user_id = id;

        let general = false;
        disabled.finished = true;

        if (bid.payment_type === 'stripe' &&
            User.needsStripe()) {
            disabled.finished = false;
            return false;
        }

        let contractor_id = 0;
        if (bid.job_task !== undefined) {
            contractor_id = bid.job_task.task.contractor_id;
        } else {
            contractor_id = bid.task.contractor_id;
        }
        // did the general contractor finish this task?
        if (id === bid.contractor_id && id === contractor_id)
            general = true;

        axios.post('/task/finished/general', bid)
            .then((response) => {
                // show a toast notification
                User.emitChange('bidUpdated');
                Vue.toasted.success(general ?
                    Language.lang().submit.job_finished.success.general :
                    Language.lang().submit.job_finished.success.sub);
                disabled.finished = false;
            }).catch((error) => {
            // show a toast notification
            Vue.toasted.error('Error: ' + error.message);
            disabled.finished = false;
        });
    }

    checkDateIsTodayorLater(firstDate, secondDate) {
        if (firstDate && secondDate) {
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
    }

    async deleteTask(jobTask, disabled) {
        disabled.deleteTask = true
        try {
            await axios.post('/api/task/delete', {
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
            return data.data
        }).catch(() => {
            if (
                error.message === 'Not Authorized to access this resource/api' ||
                error.response.status === 403
            ) {
                this.$router.push('/bids')
            }
            Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.')
        })
    }

    goToUserAuthorizationPage() {
        router.push('userAuthorizationPage')
    }

    async initiateBid(form, disabled) {
        disabled.submit = true
        try {
            const data = await axios.post('/initiate-bid', form)
            if (data.data.tokenState !== undefined) {
                if (data.data.tokenState === 'refreshTokenHasExpired') {
                    this.goToUserAuthorizationPage()
                }
            }
            Vue.toasted.success('Bid Initiated')
            disabled.submit = false
            window.location = '/#/bid/' + data.data.job.id
            localStorage.setItem('customerName', '');
            localStorage.setItem('mobile', '');
            localStorage.setItem('isMobile', 'false');
            localStorage.setItem('jobName', '');
        } catch (error) {
            localStorage.setItem('customerName', '');
            localStorage.setItem('mobile', '');
            localStorage.setItem('isMobile', 'false');
            localStorage.setItem('jobName', '');
            error = error.response.data
            form.errors.errors = error.errors
            Vue.toasted.error(error.message)
            disabled.submit = false
            if (error.errors['no_free_jobs'] !== undefined) {
                router.push('/settings')
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
        axios.post('/api/task/finishedBidNotification', {
            jobId: bid.id,
            customerId: bid.customer_id
        }).then((response) => {
            disabled.submitBid = false;
            User.emitChange('bidUpdated');
            Vue.toasted.success('Bid has been submitted and notification sent!');
        }).catch((error) => {
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
                User.emitChange('bidUpdated');
                Vue.toasted.success('Invite Sent!');
                disabled.invite = false;
                form.counter++;
                form.name = '';
                form.email = '';
                form.phone = '';
                Bus.$emit('clearAndCloseForm')
            }).catch((error) => {
            form.errors.errors = error.errors;
            Vue.toasted.error('Error: ' + error.message);
            disabled.invite = false
        })
    }

    updateCustomerPrice(price, jobTaskId, jobId) {
        if (price === '') {
            price = 0
        }
        axios.post('/api/task/updateCustomerPrice', {
            jobId: jobId,
            jobTaskId: jobTaskId,
            price: price
        }).then((response) => {
            User.emitChange('bidUpdated')
            Vue.toasted.success(Language.lang().bid_task.price_updated.general)
            return true;
        }).catch(error => {
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
            const data = await axios.post('/task/updateMessage', {
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

}

export var __useDefault = true