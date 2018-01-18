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

}