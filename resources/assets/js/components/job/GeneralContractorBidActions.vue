<template>
    <!-- /contractor bid actions -->
    <div>
        <div v-if="showPreApprovedActions" class="text-center">
            <button class="btn btn-sm btn-primary btn-contractor" @click="openModal('notifyCustomerOfFinishedBid')"
                    :disabled="bid.job_tasks.length <= 0">
                <div v-if="bid.job_tasks.length <= 0">Please add a Task before submitting bid</div>
                <div v-else>Submit Bid</div>
            </button>
            <div class="btn-group">
                <button class="btn btn-sm btn-primary btn-contractor" name="addTaskToBid" id="addTaskToBid" @click="openAddTask">
                    Add Task To Bid
                </button>
                <button class="btn btn-sm btn-primary btn-contractor" @click.prevent="cancelBid" :disabled="disabled.cancelBid">
                <span v-if="disabled.cancelBid">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                    Cancel Job
                </button>
            </div>
        </div>
        <button v-if="showJobCompletedBtn" class="btn btn-success" @click.prevent="jobCompleted"
                :disabled="disabled.jobCompleted">
            <span v-if="disabled.jobCompleted">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
            </span>
            Job Completed
        </button>
        <modal :header="modalHeader" :body="modalBody" :modalId="modalId" @modal="modalYes()" no="no" yes="yes">
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
        disabled: {
          cancelBid: false,
          jobCompleted: false
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
              ' Click yes to submit or no to cancel.',
              'notifyCustomerOfFinishedBid');
            this.modalCurrentlyOpenFor = 'notifyCustomerOfFinishedBid';
            break;
        }

        // open model after content has been updated
        $ ('#modal').modal ();
      },
      updateModal (header, body, id) {
        this.modalHeader = header;
        this.modalBody = body;
        this.modalId = id;
      },
      modalYes () {
        switch (this.modalCurrentlyOpenFor) {
          case 'notifyCustomerOfFinishedBid':
            this.$emit ('notifyCustomerOfFinishedBid');
            $ ('#modal').modal ('hide');
            break;
        }
      },
      openAddTask () {
        this.$emit ('openAddTask');
      },
      cancelBid () {
        if(confirm("Do you really wish to Cancel the Job?")) {
          Customer.cancelBid (this.bid, this.disabled);
        }
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
