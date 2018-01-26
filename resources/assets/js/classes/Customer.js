export default class Customer {

    constructor() {
        this.user = Spark.state.user;
    }

    /**
     * Approve bid and all its tasks.
     * 
     * @param {Object} bidForm 
     */
    approveBid(bidForm) {
        console.log('approve');
        Spark.post('/api/job/approve/' + bidForm.id, bidForm)
            .then((response) => {
                console.log(response);
                User.emitChange('bidUpdated');
                Vue.toasted.success('Job Approved');
            }).catch((error) => {
                console.log(error);
                bidForm.errors.errors = bidForm.errors.errors.errors;
                Vue.toasted.error('Whoops! Something went wrong! Please try again.');
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
     * Pay for a task
     * 
     * @param {Object} task 
     */
    payForTask(task) {
        console.log('payForTask', task);
        axios.post('/api/pay/task/', task)
            .then((response) => {
                console.log(response);
                User.emitChange('bidUpdated');
                Vue.toasted.success('Paid For Task');
            }).catch((error) => {
                console.log(error);
                Vue.toasted.error('Whoops! Something went wrong! Please try again.');
            });
        
    }

}