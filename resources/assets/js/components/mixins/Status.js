export default {
    methods: {

        getJobTaskStatus_latest(jobTask) {
            if (jobTask && jobTask.job_task_statuses) {
                return jobTask.job_task_statuses[jobTask.job_task_statuses.length - 1].status
            }
        },

        getTheLatestJobTaskStatus(statuses) {
            return statuses[statuses.length - 1].status
        },

        getJobTaskStatusCustomer_latest(jobTask) {
            return jobTask.job_task_status[jobTask.job_task_status.length - 1].status
        },

        getJobTaskCreationDate_latest(jobTask) {
            if (jobTask && jobTask.job_task_statuses) {
                return jobTask.job_task_statuses[jobTask.job_task_statuses.length - 1].created_at
            }
        },

        getJobStatus_latest(bid) {

            if (bid.job_status) {
                return bid.job_status[bid.job_status.length - 1].status
            } else if (bid.job_statuses) {
                return bid.job_statuses[bid.job_statuses.length - 1].status
            }
        },

        getJobStatusNumber_latest(bid) {

            if (bid.job_status) {
                return bid.job_status[bid.job_status.length - 1].status_number
            } else {
                return bid.job_statuses[bid.job_statuses.length - 1].status_number
            }
        },

        getJobTaskStatusNumber_latest(jobTask) {
            if (jobTask.job_task_status) {
                return jobTask.job_task_status[jobTask.job_task_status.length - 1].status_number
            }
            if (jobTask.job_task_statuses) {
                return jobTask.job_task_statuses[jobTask.job_task_statuses.length - 1].status_number
            }
        },

        getSubStatus_latest(bidTask) {
            return bidTask.job_task.job.sub_status[bidTask.job_task.job.sub_status.length - 1].status
        },

        formatStatus(status) {
            if (status) {
                let statusArray = status.split('_')
                if (statusArray.length > 1) {
                    let str = ''
                    for (let i = 0; i < statusArray.length; i++) {
                        i === statusArray.length - 1 ? str = str + statusArray[i] : str = str + statusArray[i] + ' '
                    }
                    return str
                } else {
                    return status
                }
            }
        },

        generalHasSentABid(bid) {
            return true
            // return this.getJobStatusNumber_latest(bid) > 2
        },

        generalCanSubmitABid(bid) {
            const status = this.getJobStatus_latest(bid)
            return status === 'in_progress'
                || status === 'changed'
                || status === 'declines_finished_task'
        }

    }
}