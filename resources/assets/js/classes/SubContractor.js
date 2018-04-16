import Language from "./Language";

export default class SubContractor {

    constructor() {
        this.user = Spark.state.user;
    }

    async toggleStripePaymentOption(jobTask) {
        try {
            const data = await axios.post('/api/task/togglestripe', jobTask);
            User.emitChange('bidUpdated');
            Vue.toasted.success('Stripe Payment');
        } catch (error) {
            error = error.response.data;
            form.errors.errors = error.errors;
            Vue.toasted.error(error.message);
        }
    }
    
    /**
     * reopen a task that has been approved or finished
     * 
     * @param {Object} jobTask 
     */
    reopenTask(jobTask, disabled) {
        console.log('reopenTask', jobTask);
        disabled.reopen = true;
        axios.post('/bid/tasks/reopen', jobTask)
            .then((response) => {
                console.log(response)
                // show a toast notification
                User.emitChange('bidUpdated');
                Vue.toasted.success('Reopened Task');
                disabled.reopen = false;
            }).catch((error) => {
                console.error(error);
                // show a toast notification
                Vue.toasted.error('Error: ' + error.message);
                disabled.reopen = false;
            });
    }

    finishedTask(bid, disabled) {
        console.log('finishedTask', bid);
        let id = this.user.id;
        bid.current_user_id = id;

        let general = false;
        disabled.finished = true;

        if (User.recievePaymentsWithStripe(bid)) {
            if (!User.stripeExpressConnected()) {
                console.log('No Stripe Account');
                Bus.$emit('needsStripe');
                disabled.finished = false;
                return false;
            }
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

        axios.post('/api/task/finished', bid)
            .then((response) => {
                console.log(response)
                // show a toast notification
                User.emitChange('bidUpdated');
                Vue.toasted.success(general ? Language.lang().submit.job_finished.success.general : Language.lang().submit.job_finished.success.sub);
                disabled.finished = false;
            }).catch((error) => {
                console.error(error);
                // show a toast notification
                Vue.toasted.error('Error: ' + error.message);
                disabled.finished = false;
            });
    }

}