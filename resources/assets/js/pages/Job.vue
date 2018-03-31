<template>
    <div class="container">
        <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <!-- <div class="panel-heading">Dashboard</div> -->
                <div class="panel-body">
                    <div class="row">
                        <!-- /show all bid information -->
                        <bid-details :bid="bid">
                        </bid-details>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <!-- /customer approve bid form -->
                        <approve-bid v-if="isCustomer && needsApproval" :bid="bid">
                        </approve-bid>
                        
                        <!-- /buttons  -->
                        <general-contractor-bid-actions :bid="bid" @notifyCustomerOfFinishedBid="notifyCustomerOfFinishedBid" @openAddTask="openAddTask">
                        </general-contractor-bid-actions>
                    </div>
                </div>
            </div>
        </div>


        <!-- /show all tasks associated to this bid -->
        <bid-tasks v-if="bid.job_tasks !== undefined" :bid="bid" @openTaskPanel="openTaskPanel">
        </bid-tasks>

        <!-- /add task to bid -->
        <transition name="slide-fade">
            <bid-add-task :show="showAddTaskPanel" :bid="bid" v-if="!jobApproved">
            </bid-add-task>
        </transition>

        <!-- / stripe testing delete after -->
        <stripe :user='user'>
        </stripe>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
        },
        data() {
            return {
                bid: {},
                jobTaskIndex: 0,
                bidForm: new SparkForm({
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                }),
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
        watch: {
            '$route' (to, from) {
                // get the bid
                const bidId = this.$route.params.id;
                this.getBid(bidId);
            }
        },
        computed: {
            jobTask() {
                if (this.bid.job_tasks !== undefined) {
                    return this.bid.job_tasks[this.jobTaskIndex];
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
                console.log(index);

                if (this.jobTaskIndex === index && this.showTaskPanel) {
                    this.showTaskPanel = false;
                } else {
                    this.showTaskPanel = true;
                    this.jobTaskIndex = index;
                    this.showAddTaskPanel = false;
                    this.$nextTick(() => {
                        document.getElementById('task-details').scrollIntoView();
                    });
                }
            },
            openAddTask() {
                $('#add-task-modal').modal();
            },
            closeBid: function () {
                console.log('closeBid');
                this.$emit('closeBid');
            },
            async getBid(id) {
                try {
                    const {data} = await axios.get('/job/' + id);
                    this.bid = data;
                } catch (error) {
                    error = error.response.data;
                    form.errors.errors = error.errors;
                    Vue.toasted.error(error.message);
                }
            }
        },
        created: function () {

            // get the bid
            const bidId = this.$route.params.id;
            this.getBid(bidId);

            Bus.$on('taskAdded', () => {
                this.getBid(bidId);
            });

            Bus.$on('bidUpdated', () => {
                this.getBid(bidId);
            });

            Bus.$on('needsStripe', () => {
                $('#stripe-modal').modal();
            });
        },
        mounted: function () {
            // set up init data
            this.bidForm.id = this.bid.id;
            this.bidForm.status = this.bid.status;
        },
    }
</script>