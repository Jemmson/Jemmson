<template>
    <!-- /contractor bid actions -->
    <div>
        <div v-if="showPreApprovedActions" class="text-center">
            <button class="btn btn-sm btn-primary btn-contractor" @click="openModal('notifyCustomerOfFinishedBid')"
                    :disabled="bid.job_tasks.length <= 0 || disabled.submitBid">
                <span v-if="disabled.submitBid">
                  <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                <div v-if="bid.job_tasks.length <= 0">Please add a Task before submitting bid</div>
                <div v-else>Submit Bid</div>
            </button>
            <div class="btn-group">
                <button class="btn btn-sm btn-primary btn-contractor" name="addTaskToBid" id="addTaskToBid" @click="openAddTask">
                    Add Task To Bid
                </button>
                <button class="btn btn-sm btn-primary btn-contractor" @click.prevent="openModal('confirmJobCancellation')" :disabled="disabled.cancelBid">
                <span v-if="disabled.cancelBid">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                    Cancel Job
                </button>
            </div>
        </div>
        <div class="text-right">
        <button v-if="showJobCompletedBtn" class="btn btn-success" @click.prevent="jobCompleted"
                :disabled="disabled.jobCompleted">
            <span v-if="disabled.jobCompleted">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
            </span>
            Job Completed
        </button>
        </div>
        <modal :header="modalHeader" :body="modalBody" :modalId="modalId" @modal="modalYes()" :yes="mYes" :no="mNo">
        </modal>
    </div>
</template>

<script>
  export default {
    props: {
      bid: Object,
      show: false,
    },
    data () {
      return {
        userType: '',
        modalCurrentlyOpenFor: '',
        modalHeader: '',
        modalBody: '',
        modalId: '',
        mYes: 'yes',
        mNo: 'no',
        disabled: {
          cancelBid: false,
          jobCompleted: false,
          submitBid: false
        }
      }
    },
    computed: {
      showJobCompletedBtn () {
        return this.bid.status === 'job.approved';
      },
      showPreApprovedActions () {
        return this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed' && User.isGeneral (this.bid);
      }
    },
    methods: {
      openModal (forBtn) {
        // update model header and body
        switch (forBtn) {
          case 'notifyCustomerOfFinishedBid':
            this.updateModal ('Bid Finished', 'You are about to submit this job bid to the customer,' +
              'you will not be able to edit this bid after its been approved by the customer.' +
              'Please also make sure to check whether you have accepted the subcontractors you wanted for each task.' +
              ' Click yes to submit or no to cancel.',
              'notifyCustomerOfFinishedBid', 'yes', 'no');
            this.modalCurrentlyOpenFor = 'notifyCustomerOfFinishedBid';
            break;
          case 'confirmJobCancellation':
            this.updateModal ('Confirm Cancellation', 'You are about to cancel this job,' +
              ' Click delete job to cancel and delete the job or back to cancel this action.',
              'confirmJobCancellation', 'Delete Job', 'back');
            this.modalCurrentlyOpenFor = 'confirmJobCancellation';
            break;
        }

        // open model after content has been updated
        $ ('#modal').modal ();
      },
      updateModal (header, body, id, yes, no) {
        this.modalHeader = header;
        this.modalBody = body;
        this.modalId = id;
        this.mYes = yes;
        this.mNo = no;
      },
      modalYes () {
        switch (this.modalCurrentlyOpenFor) {
          case 'notifyCustomerOfFinishedBid':
            this.notifyCustomerOfFinishedBid();
            $ ('#modal').modal ('hide');
            break;
          case 'confirmJobCancellation':
            this.cancelBid();
            $ ('#modal').modal ('hide');
            break;
        }
      },
      openAddTask () {
        this.$emit ('openAddTask');
      },
      notifyCustomerOfFinishedBid () {
        GeneralContractor.notifyCustomerOfFinishedBid (this.bid, this.disabled);
      },
      cancelBid () {
        Customer.cancelBid (this.bid, this.disabled);
      },
      jobCompleted () {
        GeneralContractor.jobCompleted (this.bid, this.disabled);
      }
    },
    mounted: function () {
      this.userType = Spark.state.user.usertype;
    }
  }
</script>

<style>
    .btn-contractor {
        margin: 1rem;
    }
</style>
