<template>
    <!-- /contractor bid actions -->
    <div class="flex ">
        <div class="flex text-white btn-red rounded-lg p-3" v-show="subTaskWarning">PLEASE CHECK TASKS. SOME TASKS HAVE
            SUB PRICES HIGHER THAN CONTRACTOR PRICE
        </div>
        <div v-if="showPreApprovedActions" class="flex w-full justify-between">
            <button class="btn btn-red" @click.prevent="openModal('confirmJobCancellation')"
                    :disabled="disabled.cancelBid" ref="cancelBtn">
          <span v-if="disabled.cancelBid">
            <i class="fa fa-btn fa-spinner fa-spin"></i>
          </span>
                Cancel Job
            </button>
            <button class="btn btn-blue" name="addTaskToBid" id="addTaskToBid" @click="openAddTask"
                    v-if="bid.job_tasks.length > 0 || bid.job_tasks.length <= 0">
                Add A Task
            </button>
            <button class="btn btn-green" v-if="bid.job_tasks.length > 0"
                    @click="openModal('notifyCustomerOfFinishedBid')"
                    :disabled="(bid.job_tasks.length <= 0 || disabled.submitBid) || disableSubmitBid"
                    ref="submitBidBtn">
          <span v-if="disabled.submitBid">
            <i class="fa fa-btn fa-spinner fa-spin"></i>
          </span>
                <span>Submit Bid</span>
            </button>
        </div>
        <modal :header="modalHeader" :body="modalBody" :modalId="modalId" @modal="modalYes()" :yes="mYes" :no="mNo">
        </modal>
    </div>
</template>

<script>

  import Modal from '../shared/Modal'

  export default {
    components: {
      Modal
    },
    props: {
      bid: Object,
    },
    data() {
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
        },
        subTaskWarning: false
      }
    },
    computed: {
      disableSubmitBid() {
        return this.bid.status === 'bid.sent'
      },
      showJobCompletedBtn() {
        return this.bid.status === 'job.approved'
      },
      showPreApprovedActions() {
        return this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed' && User.isGeneral(this.bid)
      }
    },
    methods: {
      openModal(forBtn) {
        // update model header and body
        switch (forBtn) {
          case 'notifyCustomerOfFinishedBid':
            this.updateModal('Bid Finished', 'You are about to submit this job bid to the customer,' +
              'you will not be able to edit this bid after its been approved by the customer.' +
              'Please also make sure to check whether you have accepted the subcontractors you wanted for each task.' +
              ' Click yes to submit or no to cancel.',
              'notifyCustomerOfFinishedBid', 'yes', 'no')
            this.modalCurrentlyOpenFor = 'notifyCustomerOfFinishedBid'
            break
          case 'confirmJobCancellation':
            this.updateModal('Confirm Cancellation', 'You are about to cancel this job,' +
              ' Click delete job to cancel and delete the job or back to cancel this action.',
              'confirmJobCancellation', 'Delete Job', 'back')
            this.modalCurrentlyOpenFor = 'confirmJobCancellation'
            break
        }

        // open model after content has been updated
        $('#modal').modal()
      },
      updateModal(header, body, id, yes, no) {
        this.modalHeader = header
        this.modalBody = body
        this.modalId = id
        this.mYes = yes
        this.mNo = no
      },
      modalYes() {
        switch (this.modalCurrentlyOpenFor) {
          case 'notifyCustomerOfFinishedBid':
            this.notifyCustomerOfFinishedBid()
            $('#modal').modal('hide')
            break
          case 'confirmJobCancellation':
            this.cancelBid()
            $('#modal').modal('hide')
            break
        }
      },
      openAddTask() {
        this.$emit('openAddTask')
      },
      notifyCustomerOfFinishedBid() {

        // go through each job task and compare the sub price to the contractor task price
        // first check if there is a sub.
        // check if the sub price is an accepted price
        // compare the the accepted sub price to the contractor price
        // if the accepted sub price is higher then throw an error

        this.subTaskWarning = false
        for (let i = 0; i < this.bid.job_tasks.length; i++) {
          if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
            this.subTaskWarning = true
          }
        }

        if (!this.subTaskWarning) {
          GeneralContractor.notifyCustomerOfFinishedBid(this.bid, this.disabled)
        }

      },
      cancelBid() {
        Customer.cancelBid(this.bid, this.disabled)
      }
    },
    mounted: function() {
      this.userType = Spark.state.user.usertype
    }
  }
</script>

<style lang="less" scoped>
    .btn-contractor {
        margin: 1rem;
    }

    .btn {
        padding-right: .5rem;
        padding-left: .5rem;

    }
</style>
