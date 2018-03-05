<template>
    <!-- /contractor bid actions -->
    <div class="col-md-12">
        <div v-if="showPreApprovedActions">
            <button class="btn btn-sm btn-primary button" name="addTaskToBid" id="addTaskToBid" @click="openAddTask">
                Add Task To Bid
            </button>
            <button class="btn btn-sm btn-warning button" @click="openModal('notifyCustomerOfFinishedBid')">
                Notify Customer of Finished Bid
            </button>
            <button class="btn btn-danger" @click.prevent="cancelBid" :disabled="disabled.cancelBid">
                <span v-if="disabled.cancelBid">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Cancel Job
            </button>
        </div>
        <button v-if="showJobCompletedBtn" class="btn btn-success" @click.prevent="jobCompleted" :disabled="disabled.jobCompleted">
            <span v-if="disabled.jobCompleted">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
            </span>
            Job Completed
        </button>
        <modal :header="modalHeader" :body="modalBody" :modalId="modalId" @modal="modalYes()">
        </modal>
    </div>
</template>

<script>
export default {
    props: {
        bid: Object,
        show: false,
    },
    data() {
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
        showJobCompletedBtn() {
            return this.bid.status === 'job.approved';
        },
        showPreApprovedActions() {
            return this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed' && User.isGeneral(this.bid);
        }
    },
    methods: {
        openModal(forBtn) {
            // update model header and body
            switch (forBtn) {
                case 'notifyCustomerOfFinishedBid':
                    this.updateModal('Bid Finished', 'You are about to submit this job bid to the customer,' +
                        'you will not be able to edit this bid after its been submitted.' +
                        ' Click yes to submit or no to cancel.',
                        'notifyCustomerOfFinishedBid');
                    this.modalCurrentlyOpenFor = 'notifyCustomerOfFinishedBid';
                    break;
            }

            // open model after contect has been udpated
            $('#modal').modal();
        },
        updateModal(header, body, id) {
            this.modalHeader = header;
            this.modalBody = body;
            this.modalId = id;
        },
        modalYes() {
            switch (this.modalCurrentlyOpenFor) {
                case 'notifyCustomerOfFinishedBid':
                    this.$emit('notifyCustomerOfFinishedBid');
                    $('#modal').modal('hide');
                    break;
            }
        },
        openAddTask() {
            this.$emit('openAddTask');
        },
        cancelBid() {
            Customer.cancelBid(this.bid, this.disabled);
        },
        jobCompleted() {
            GeneralContractor.jobCompleted(this.bid, this.disabled);
        }
    },
    mounted: function () {
        this.userType = Spark.state.user.usertype;
    }
}
</script>