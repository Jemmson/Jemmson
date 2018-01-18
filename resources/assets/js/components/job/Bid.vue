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
                    <bid-tasks :bid="bid" @openTaskPanel="openTaskPanel">
                    </bid-tasks>

                    <!-- /customer approve bid form -->
                    <form role="form" v-if="isCustomer && !bidApproved">
                        <div class="form-group col-md-6">
                            <label for="area">City</label>
                            <input type="text" class="form-control" id="area" v-model="bidForm.area">
                        </div>
                        <div class="form-group col-md-6" :class="{'has-error': bidForm.errors.has('agreed_start_date')}">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" v-model="bidForm.agreed_start_date">
                            <span class="help-block" v-show="bidForm.errors.has('agreed_start_date')">
                                {{ bidForm.errors.get('agreed_start_date') }}
                            </span>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-success" @click.prevent="approve">Approve</button>
                        </div>
                    </form>

                    <!-- /buttons  -->
                    <general-contractor-bid-actions :show="isGeneralContractor" :bid="bid" @notifyCustomerOfFinishedBid="notifyCustomerOfFinishedBid"
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
            <bid-add-task :show="showAddTaskPanel" :bid="bid">
            </bid-add-task>
        </transition>
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
                task: {},
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
            }
        },
        computed: {
            isCustomer() {
                return this.user.usertype === 'customer';
            },
            isGeneralContractor() {
                // General contractor is the one who created the bid
                return this.bid.contractor_id === this.user.id;
            },
        },
        methods: {
            notifyCustomerOfFinishedBid() {
                GeneralContractor.notifyCustomerOfFinishedBid(this.bid);
            },
            approve() {
                Customer.approveBid(this.bidForm);
            },
            openTaskPanel(e) {
                this.task = e;
                this.showTaskPanel = this.showTaskPanel ? false : true;
            },
            openAddTask() {
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
            this.bidApproved = (this.bid.status === 'Approved' || this.bid.status ===
                'Waiting on Contractor to Submit Final Bid');
            this.user = Spark.state.user;
        }
    }
</script>