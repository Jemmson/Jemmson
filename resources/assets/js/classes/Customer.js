export default class Customer {

    constructor() {
        this.user = Spark.state.user;
    }

    approveBid(bidForm) {
        console.log('approve');
        Spark.post('/api/job/approve/' + bidForm.id, bidForm)
            .then((response) => {
                console.log(response);
                Vue.toasted.success('Job Approved');
            }).catch((error) => {
                console.log(error);
                bidForm.errors.errors = bidForm.errors.errors.errors;
                Vue.toasted.error('Whoops! Something went wrong! Please try again.');
            });
    }

}