import Language from "./Language";

export default class SubContractor {

    constructor() {
        this.user = Spark.state.user;
    }
    
    /**
     * reopen a task that has been approved or finished
     * 
     * @param {Object} task 
     */
    reopenTask(task, disabled) {
        console.log('reopenTask', task);
        disabled.reopen = true;
        axios.post('/bid/tasks/reopen', task)
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

    finishedTask(task, disabled) {
        console.log('finishedTask', task);
        let id = this.user.id;
        task.current_user_id = id;

        let general = false;
        disabled.finished = true;

        if (User.payWithStripe()) {
            if (!User.isSignedUpWithStripe()) {
                console.log('No Stripe Account');
                Bus.$emit('needsStripe');
                disabled.finished = false;
                return false;
            }
        }
        // did the general contractor finish this task?
        if (id === task.job_task.contractor_id && id === task.contractor_id)
            general = true;

        axios.post('/api/task/finished', task)
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