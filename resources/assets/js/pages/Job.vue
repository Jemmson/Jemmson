<template>


    <div class="container">

        <h2 class="text-center uppercase black--text" style="margin-bottom: 2rem;">Job Details Page</h2>

        <div v-show="false">{{ getJobStatusValue() }}</div>

        <v-card v-if="bid.paid_with_cash_message">
            <v-card-title
                    class="title-paid-format">
                {{ bid.paid_with_cash_message }}
            </v-card-title>
        </v-card>

        <v-card>
            <v-card-title class="flex justify-content-around">{{ bid.job_name }}
                <div>
                    <v-icon
                            v-if="bid.payment_type === 'cash'"
                    >mdi-cash
                    </v-icon>
                    <v-icon
                            v-else-if="bid.payment_type === 'creditCard'"
                    >mdi-credit-card
                    </v-icon>
                </div>
            </v-card-title>
            <hr class="mb-0 mt-0">
            <v-card-text>
                <h3
                        class="mr-1rem ml-half-rem text-center"
                >Job Status:
                </h3>
                <div>
                    <div v-if="userIsACustomerAndJobHasNotBeenSent(bid)"
                         style="padding-bottom: .1rem"
                    >
                        <h5
                                ref="jobNotSubmittedMessage"
                                class="text-center uppercase w-break">Please wait until your contractor submits bid</h5>
                    </div>
                    <div v-else>
                        <v-sheet class="capitalize"
                                 ref="jobStatus"
                                 color="status-bar lighten-2">{{ formatStatus(jobStatus.status) }} on {{
                            dateOnly(jobStatus.created_at) }}
                        </v-sheet>
                    </div>
                </div>
            </v-card-text>
        </v-card>

        <!-- /show all bid information -->
        <bid-details :customerName="customerName" :bid="bid" :isCustomer="isCustomer">
        </bid-details>

        <!--            stripe testing delete after -->
        <stripe :user='user'>
        </stripe>
        <feedback
                page="job"
        ></feedback>
    </div>

</template>

<script>

    import Feedback from '../components/shared/Feedback'
    import BidDetails from '../components/job/BidDetails'
    import ApproveBid from '../components/job/ApproveBid'
    import GeneralContractorBidActions from '../components/job/GeneralContractorBidActions'
    import CompletedTasks from '../components/job/CompletedTasks'
    import Stripe from '../components/stripe/Stripe'
    import Status from '../components/mixins/Status'
    import Utilities from '../components/mixins/Utilities'
    import {mapState} from 'vuex'

    // global.Bus = new Vue();

    export default {
        props: {
            user: Object,
        },
        mixins: [
            Status,
            Utilities
        ],
        components: {
            Feedback,
            BidDetails,
            ApproveBid,
            GeneralContractorBidActions,
            CompletedTasks,
            Stripe
        },
        data() {
            return {
                myText: '',
                contractor: {
                    name: 'General Contractor'
                },
                bid: {},
                jobTaskIndex: 0,
                bidForm: {
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                },
                bidApproved: false,
                showTaskPanel: false,
                showAddTaskPanel: false,
                disabled: {
                    approve: false,
                    declineBid: false
                },
                showStripe: false,
                jobStatus: {
                    status: '',
                    created_at: ''
                }
            }
        },
        watch: {
            '$route'(to, from) {
                // get the bid
                const bidId = this.$route.params.id
                this.getBid(bidId)
            }
        },
        computed: {
            ...mapState({
                userFromState: state => state.user.user,
            }),
            status() {
                return User.status(this.bid.status, this.bid)
            },
            customerName() {
                if (this.isCustomer) {
                    return this.user.name
                } else if (this.bid.customer) {
                    return this.bid.customer.name
                }
            },
            showTasks() {
                if (this.isCustomer) {
                    const status = this.bid.status
                    if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
                        return true
                    }
                    return false
                }
                return true
            },
            jobTask() {
                if (this.bid.job_tasks !== undefined) {
                    return this.bid.job_tasks[this.jobTaskIndex]
                } else {
                    return this.bid
                }
            },
            jobApproved() {
                return this.bid.status === 'job.approved'
            },
            needsApproval() {
                // TODO: use regular status values to check these
                return this.bid.status === 'bid.sent'
            },
            isCustomer() {
                return this.user.usertype === 'customer'
            },
            isGeneralContractor() {
                // General contractor is the one who created the bid
                return User.isGeneral(this.bid, this.user.id)
            },
        },
        methods: {

            userIsACustomerAndJobHasNotBeenSent(bid) {
                return this.isCustomer
                    && (this.getJobStatus_latest(bid) === 'initiated' || this.getJobStatus_latest(bid) === 'in_progress')
            },

            setMyText() {
                this.myText = 'hello'
            },
            getJobStatusValue() {
                if (this.bid && this.bid.job_statuses) {
                    this.jobStatus = this.bid.job_statuses[this.bid.job_statuses.length - 1]
                } else if (this.bid && this.bid.job_status) {
                    this.jobStatus = this.bid.job_status[this.bid.job_status.length - 1]
                }

            },
            getLabelClass(status) {
                return Format.statusLabel(status,)
            },
            reloadPage() {
                location.reload()
            },
            declineBid() {
                Customer.declineBid(this.bid, this.disabled)
            },
            approve() {
                Customer.approveBid(this.bidForm, this.disabled)
            },
            openTaskPanel(index) {
                console.log(index)

                if (this.jobTaskIndex === index && this.showTaskPanel) {
                    this.showTaskPanel = false
                } else {
                    this.showTaskPanel = true
                    this.jobTaskIndex = index
                    this.showAddTaskPanel = false
                    this.$nextTick(() => {
                        document.getElementById('task-details').scrollIntoView()
                    })
                }
            },
            openAddTask() {
                $('#add-task-modal').modal()
            },
            closeBid: function () {
                console.log('closeBid')
                this.$emit('closeBid')
            },
            async getBid(id) {
                try {
                    const {data} = await axios.get('/job/' + id)
                    if (data[0]) {
                        console.log('data', data[0])
                        this.bid = data[0]
                        this.$store.commit('setJob', data[0])
                    } else {
                        console.log('data', data)
                        this.bid = data
                        this.$store.commit('setJob', data)
                    }
                    this.$store.commit('setJob', data)
                } catch (error) {
                    if (
                        error.message === 'Not Authorized to access this resource/api' ||
                        error.response !== undefined && error.response.status === 403
                    ) {
                        this.$router.push('/bids')
                    }
                    Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.')
                }
            }
        },
        created: function () {
            document.body.scrollTop = 0 // For Safari
            document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
            // get the bid
            const bidId = this.$route.params.id
            this.getBid(bidId)

            Bus.$on('taskAdded', () => {
                this.getBid(bidId)
            })

            Bus.$on('bidUpdated', () => {
                this.getBid(bidId)
            })

            Bus.$on('needsStripe', () => {
                $('#stripe-modal').modal()
            })

            Bus.$emit('updateUser')

        },
        beforeDestroy() {
            // ensures no old listeners are left in the bus from old components
            Bus.$off('bidUpdated')
            Bus.$off('taskAdded')
        },
        mounted: function () {

            this.$store.commit('setCurrentPage', this.$router.history.current.path)
            if (!this.user.user) {
                this.user.user = this.userFromState
            }
            this.bidForm.id = this.bid.id
            this.bidForm.status = this.bid.status
            const success = this.$route.query.success
            Vue.toasted.success(success)
            const error = this.$route.query.error
            Vue.toasted.error(error)
            this.getJobStatusValue()

        },
    }
</script>

<style scoped>


    .title-paid-format {
        background-color: #d03838ba;
        width: 100%;
        text-align: center;
        margin-bottom: 1rem;
    }

    .status {
        padding-top: 1rem;
        padding-bottom: 1rem;
        font-family: auto;
        font-size: 20pt;
    }

    .status-bar {
        width: 100%;
        margin-top: 1rem;
        text-align: center;
        padding: .5rem;
        background-color: cornflowerblue;
        font-size: 18pt;
        font-weight: bolder;
    }

</style>