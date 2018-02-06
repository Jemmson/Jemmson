export default class Customer {

    constructor() {
        this.user = Spark.state.user;
    }

    /**
     * Approve bid and all its tasks.
     * 
     * @param {Object} bidForm 
     */
    approveBid(bidForm, disabled) {
        console.log('approve');
        disabled.approve = true;
        Spark.post('/api/job/approve/' + bidForm.id, bidForm)
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
     * Deny a task that has been finished
     * 
     * @param {Ojbect} task 
     */
    async denyTask(task, disabled) {
        disabled.deny = true;
        try {
            const data = await axios.post('/task/deny', task);
            User.emitChange('bidUpdated');
            Vue.toasted.success('Task Denied & Notification Sent');
            disabled.deny = false;
        } catch (error) {
            error = error.response.data;
            Vue.toasted.error(error.message);
            disabled.deny = false;
        }
    }

    /**
     * Pay for a task
     * 
     * @param {Object} task 
     */
    async payForTask(task, disabled) {
        console.log('payForTask', task);
        disabled.pay = true;
        try {
            const data = await axios.post('/stripe/express/task/payment', task);
            User.emitChange('bidUpdated');
            Vue.toasted.success('Paid For Task');
            disabled.pay = false;
        } catch (error) {
            error = error.response.data;
            Vue.toasted.error(error.message);
            disabled.pay = false;
        }
        
    }

}