import Language from "./Language";

export default class TaskUtil {

    constructor() {
        this.user = Spark.state.user;
    }

    previewSubForTask(bids, bidId, taskId, subId) {
        const bid = this.getBid(bidId, bids)[0];
        if (bid === undefined || bid.tasks === undefined) {
            return false;
        }
        let task = this.getTask(taskId, bid.tasks)[0];
        if (task === undefined) {
            return false;
        }
        task.job_task.bid_id = subId;
        return bids;
    }

    getBid(id, bids) {
        return bids.filter((bid) => {
            return bid.id === id;
        });
    }

    getTask(id, tasks) {
        return tasks.filter((task) => {
            return task.id === id;
        });
    }
}