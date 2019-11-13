1. Need to be a contractor
2. Need the JobTask
3. Need the JobTask task


<button class="btn btn-sm btn-normal w-full"
        v-if="isGeneral()
               && !approvedByCustomer()"
        ref="addASubButton"
        @click.prevent="openSubInvite(jobTask.id)"
>Add A Sub

</button>

<sub-invite-modal v-if="isContractor()" :job-task="jobTask"
                  :job-task-task="jobTask ? jobTask.task : null"
                  :job-task-name="jobTask ? jobTask.task.name : null"
                  :id="jobTask ? jobTask.id : null">
</sub-invite-modal>

import SubInviteModal from '../components/task/SubInviteModal'
 
components: {
    SubInviteModal
}
        
openSubInvite(jobTaskId) {
    // debugger;
    // this.currentJobTask = jobTask;
    $('#sub-invite-modal_' + jobTaskId).modal()
},

approvedByCustomer() {
    return this.jobTask.status === 'bid_task.approved_by_customer' || this.jobTask.status === 'bid_task.finished_by_general'
},