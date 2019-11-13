1. must be a general
2. need the job task
3. task must be assigned to the contractor that is logged in

<button class="btn btn-sm btn-normal w-full" v-if="showFinishedBtn(jobTask)"
        @click="finishedTask(jobTask)" :disabled="disabled.finished">
    <span v-if="disabled.finished">
        <i class="fa fa-btn fa-spinner fa-spin"></i>
    </span>Click Me When Job Is Finished
</button>

showFinishedBtn(jobTask) {
if (this.isContractor() &&
  this.authUser.isAssignedToMe(jobTask, this.user.id) &&
  (jobTask.status === 'bid_task.approved_by_customer'
    || jobTask.status === 'bid_task.reopened'
    || jobTask.status === 'bid_task.finished_by_sub'
    || jobTask.status === 'bid_task.denied'
  )) {
  return true
}
return false
},

finishedTask(jobTask) {
    SubContractor.finishedTask(jobTask, this.disabled)
},

isAssignedToMe(jobTask, userId) {
    return userId === jobTask.contractor_id
}