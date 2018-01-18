export default class GeneralContractor {

    constructor() {
        this.user = Spark.state.user;
    }

    static notifyCustomerOfFinishedBid(bid) {
        console.log('notifyCustomerOfFinishedBid', bid);
        axios.post('/api/task/finishedBidNotification', {
            jobId: bid.id,
            customerId: bid.customer_id
        }).then((response) => {
            console.log(response);
            Vue.toasted.success('Bid has been submitted and notification sent!');
        }).catch((error) => {
            console.error(error);
            Vue.toasted.error('Whoops! Something went wrong! Please try again.');
        });
    }

    static acceptSubBidForTask(task, bid) {
        console.log('acceptSubBidForTask', task);
        axios.post('/api/task/accept', {
            jobId: task.job_task.job_id,
            taskId: task.id,
            contractorId: bid.contractor_id,
            bidId: bid.id,
            price: bid.bid_price,
        }).then((response) => {
            console.log(response.data)
            Vue.toasted.success('Accepted Bid!');
        }).catch((error) => {
            console.error(error);
            Vue.toasted.error('Error Trying to Accept Bid!');
        });
    }

    static sendSubInviteToBidOnTask(task, form) {
        console.log('sendSubInviteToBidOnTask', task, form);
        form.taskId = task.id;
        form.jobId = task.job_task.job_id;
        Spark.post('/api/task/notify', form)
            .then((response) => {
                console.log(response);
                Vue.toasted.success('Invite Sent!');
            }).catch((error) => {
                console.error(error);
                form.errors.errors = error.errors;
                Vue.toasted.error('Error: ' + error.message);
            });
    }

    static addNewTaskToBid(bid, form) {
        console.log('sendSubInviteToBidOnTask', bid, form);
        // I want the status to go from initiated to in progress when the first new task is added
        // I want each task to be added to the the tasks table
        // I want the task to associated to a job, customer, and contractor
        // I want to add the existing task to the job
        
        form.taskId = 1;
        form.taskExists = false;
        
        form.jobId = bid.id;
        
        form.subTaskPrice = 0; // TODO: is this needed?
        
        form.contractorId = Spark.state.user.id;
        
        Spark.post('/api/task/addTask', form)
            .then(function (response) {
                console.log(response)
                // NOTICE: using Spark.post returns the exact data so response.data doesn't have anything its already data
                // show a toast notification
                Vue.toasted.success('New Task Added!');
            }.bind(this)).catch(error => {
                console.error(error);
                // NOTICE: lets us do addNewTaskForm.errors.has('errorName') to check if this error exists & addNewTaskForm.errors.get('errorName') to get the error message
                // usually we don't have to do this, but api routes messes this up
                // we don't have to do this for web routes we can just call addNewTaskForm.errors.has('errorName')
                // without catching the error and assigning the errors
                form.errors.errors = error.errors;
                // show a toast notification
                Vue.toasted.error('Whoops! Something went wrong! Please try again.');
            });

    }

}