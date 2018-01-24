import Language from "./Language";

export default class SubContractor {

    constructor() {
        this.user = Spark.state.user;
    }

    finishedTask(task) {
        console.log('finishedTask', task);
        let id = Spark.state.user.id;
        task.current_user_id = id;
        
        let general = false;
        // did the general contractor finish this task?
        if (id === task.job_task.contractor_id && id === task.contractor_id)
            general = true;
            
        axios.post('/api/task/finished', task)
            .then((response) => {
                console.log(response)
                // show a toast notification
                Vue.toasted.success(general ? Language.lang().submit.job_finished.success.general : Language.lang().submit.job_finished.success.sub);
            }).catch((error) => {
                console.error(error);
                // show a toast notification
                Vue.toasted.error('Error: ' + error.message);
            });
    }

}