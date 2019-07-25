export default class TaskUtil {

    constructor() {
        this.user = Spark.state.user;
    }

    previewSubForTask(bids, bidId, jobTaskId, subId) {
        const bid = this.getBid(bidId, bids)[0];
        if (bid === undefined || bid.job_tasks === undefined) {
            return false;
        }
        let jobTask = this.getTask(jobTaskId, bid.job_tasks)[0];
        if (jobTask === undefined) {
            return false;
        }
        jobTask.bid_id = subId;
        return bids;
    }

    getBid(id, bids) {
        return bids.filter((bid) => {
            return bid.id === id;
        });
    }

    getTask(id, jobTasks) {
        return jobTasks.filter((jobTask) => {
            return jobTask.id === id;
        });
    }
}