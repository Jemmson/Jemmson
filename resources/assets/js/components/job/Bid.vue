<template>
    <div class="col-md-12">
        <div class="panel panel-default">
            <!-- <div class="panel-heading">Dashboard</div> -->
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <span style="float: right;">
                            <button class="btn btn-danger btn-close" @click="closeBid">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </span>
                    </div>

                    <!-- /show all bid information -->
                    <bid-details :bid="bid">
                    </bid-details>

                    <!-- /show all tasks associated to this bid -->
                    <bid-tasks v-if="bid.tasks !== undefined" :bid="bid" @openTaskPanel="openTaskPanel">
                    </bid-tasks>

                    <!-- /customer approve bid form -->
                    <form role="form" v-if="isCustomer && needsApproval">
                        <div class="form-group col-sm-12 col-md-6" :class="{'has-error': bidForm.errors.has('agreed_start_date')}">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" v-model="bidForm.agreed_start_date">
                            <span class="help-block" v-show="bidForm.errors.has('agreed_start_date')">
                                {{ bidForm.errors.get('agreed_start_date') }}
                            </span>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-success" @click.prevent="approve" :disabled="disabled.approve">
                                <span v-if="disabled.approve">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                                </span>
                                Approve
                            </button>
                            <button class="btn btn-danger" @click.prevent="declineBid" :disabled="disabled.declineBid">
                                <span v-if="disabled.declineBid">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                                </span>
                                Decline
                            </button>
                        </div>
                    </form>

                    <!-- /buttons  -->
                    <general-contractor-bid-actions :show="isGeneralContractor && !jobApproved" :bid="bid" @notifyCustomerOfFinishedBid="notifyCustomerOfFinishedBid"
                        @openAddTask="openAddTask">
                    </general-contractor-bid-actions>
                </div>
            </div>
        </div>

        <!-- /task details and actions -->
        <transition name="slide-fade">
            <bid-task :show="showTaskPanel" :task="task">
            </bid-task>
        </transition>

        <!-- /add task to bid -->
        <transition name="slide-fade">
            <bid-add-task :show="showAddTaskPanel" :bid="bid" v-if="!jobApproved">
            </bid-add-task>
        </transition>

        <!-- / stripe testing delete after -->
        <stripe v-if="showStripe">
        </stripe>
    </div>
</template>

<script>
    export default {
        props: {
            bid: Object,
            showBid: false,
        },
        data() {
            return {
                taskIndex: 0,
                bidForm: new SparkForm({
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                }),
                user: '',
                bidApproved: false,
                showTaskPanel: false,
                showAddTaskPanel: false,
                disabled: {
                    approve: false,
                    declineBid: false
                },
                showStripe: false
            }
        },
        computed: {
            task() {
                if (this.bid.tasks !== undefined) {
                    return this.bid.tasks[this.taskIndex];
                } else {
                    return this.bid;
                }
            },
            jobApproved() {
                return this.bid.status === 'job.approved';
            },
            needsApproval() {
                // TODO: use regular status values to check these
                return this.bid.status === "bid.sent";
            },
            isCustomer() {
                return User.isCustomer();
            },
            isGeneralContractor() {
                // General contractor is the one who created the bid
                return User.isGeneral(this.bid);
            },
        },
        methods: {
            declineBid() {
                Customer.declineBid(this.bid, this.disabled);
            },
            notifyCustomerOfFinishedBid() {
                GeneralContractor.notifyCustomerOfFinishedBid(this.bid);
            },
            approve() {
                Customer.approveBid(this.bidForm, this.disabled);
            },
            openTaskPanel(index) {
                this.taskIndex = index;
                this.showAddTaskPanel = false;
                this.showTaskPanel = this.showTaskPanel ? false : true;
            },
            openAddTask() {
                this.showTaskPanel = false;
                this.showAddTaskPanel = this.showAddTaskPanel ? false : true;
            },
            closeBid: function () {
                console.log('closeBid');
                this.$emit('closeBid');
            }
        },
        mounted: function () {
            // set up init data
            this.bidForm.id = this.bid.id;
            this.bidForm.status = this.bid.status;
            this.user = Spark.state.user;
        },
        created: function () {
            Bus.$on('taskAdded', () => {
                this.showAddTaskPanel = false;
            });
            Bus.$on('needsStripe', () => {
                this.showStripe = true;                
            });
        }
    }
</script>