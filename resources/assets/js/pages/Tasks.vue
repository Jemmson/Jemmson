<template>
    <div class="main flex flex-col justify-between mt-1">

        <h2 class="text-center uppercase black--text" style="margin-bottom: 2rem;">Sub Jobs Page</h2>

        <div>
            <v-btn
                    @click="connectWithStripe($route.path)"
                    class="w-full mt-6"
                    color="aliceblue"
                    text
                    elevation="2"
                    single-line
                    sticky
                    style="background-color: cornflowerblue; font-size: 9pt;"
                    v-show="needsStripeForCreditCardPayments() && oneOpenBidIsACreditCardJob()"
            ><span style="color: white">Click To Accept Credit Cards</span></v-btn>
            <search-bar>
                <input type="text" class="form-control" placeholder="Search Tasks" v-model="searchTerm" @keyup="search">
            </search-bar>
            <section>
                <paginate ref="paginator"
                          :list="sTasks"
                          :per="8"
                          name="sTasks"
                          v-show="sTasks.length > 0"
                >
                    <div v-for="bidTask in paginated('sTasks')"
                         v-bind:key="bidTask.id"
                         :id="'task_' + bidTask.task_id"
                         style="z-index:2;"
                    >
                        <task
                                :bidTask="bidTask"
                                :user="current_user"
                        ></task>
                    </div>
                </paginate>
                <div class="card card-body justify-center">
                    <paginate-links :limit="2"
                                    :show-step-links="true"
                                    name="tasks"
                                    class="m-center"
                                    for="sTasks">
                    </paginate-links>
                </div>
            </section>
        </div>

        <!-- / end tasks -->
        <stripe :user='user'>
        </stripe>
        <feedback
                page="tasks"
        ></feedback>
    </div>
</template>


<script>

    import SearchBar from '../components/shared/SearchBar'
    import Feedback from '../components/shared/Feedback'
    import Stripe from '../components/stripe/Stripe'
    import Card from '../components/shared/Card'
    import Task from '../components/task/Task'
    import StripeMixin from '../components/mixins/StripeMixin'
    import Phone from '../components/mixins/Phone'
    import Status from "../components/mixins/Status";

    export default {
        name: 'Tasks',
        props: {
            user: Object
        },
        components: {
            SearchBar,
            Feedback,
            Stripe,
            Card,
            Task
        },
        data() {
            return {
                current_user: null,
                showTasks: {},
                paginate: ['sTasks'],
                address: '',
                location: {
                    location: []
                },
                localArea: '',
                area: {
                    area: ''
                },
                hello: 'world',
                tasks: [],
                sTasks: [],
                price: '',
                searchTerm: '',
            }
        },
        mixins: [Phone, StripeMixin, Status],
        methods: {

            oneOpenBidIsACreditCardJob(){
                for (let i = 0; i < this.tasks.length; i++) {
                    if (
                        this.tasks[i].job_task.job.payment_type === 'creditCard'
                        && this.tasks[i].job_task.job.sub_status[0].status === 'initiated'
                    ) {
                        return true
                    }
                }
                return false
            },

            goBack() {
                this.$router.go(-1)
            },
            showTheTask(index, action) {

                if (action === 'show') {
                    let a = window.document.getElementById('showTask' + index)
                    a.setAttribute('style', '')
                    // return true
                } else {
                    let a = window.document.getElementById('showTask' + index)
                    a.setAttribute('style', 'display:none;')
                }

                // for (let i = 0; i < this.tasks.length; i++) {
                // }
            },
            getLabelClass(status) {
                return Format.statusLabel(status)
            },
            search() {
                this.sTasks = this.tasks.filter((task) => {
                    if (this.searchTerm == '' || this.searchTerm.length <= 1) {
                        return true
                    }
                    return this.task.job_task.task.name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
                })
                if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
                    this.$refs.paginator.goToPage(1)
                }
            },
            showBid(bid) {
                // TODO: backend what should happen to the bids that wheren't accepted
                if (bid.job_task === null) {
                    return false
                }
                return (bid.id === bid.job_task.bid_id && (bid.job_task.job.status === 'job.approved' || bid.job_task.job.status === 'job.completed' || bid.job_task.status === 'bid_task.accepted')) || (bid.job_task.status ===
                    'bid_task.bid_sent' || bid.job_task.status === 'bid_task.initiated')
            },
            getArea(bidTask) {
                // console.log(bidTask)
                // debugger
                // Customer.getArea(bidTask.job_id, this.area)
                // this.localArea = this.area

                // return this.localArea.area
            },

            hasStripe() {
                return this.bid.contractor.stripe_id === null
            },

            showAddress(bidTask) {
                const status = bidTask.job_task.status
                return status !== 'bid_task.initiated' && status !== 'bid_task.bid_sent' && status !== 'bid_task.finished_by_sub'
            },
            showStripeToggle(jobTask) {
                return jobTask.contractor_id === User.getId() && (jobTask.job.status === 'bid.initiated' || jobTask.job.status === 'bid.in_progress')
            },
            toggleStripePaymentOption(jobTask) {
                SubContractor.toggleStripePaymentOption(jobTask)
            },
            getTasks() {
                if (Spark.state.user.usertype === 'contractor') {
                    console.log('getTasks');
                    axios.post('/bid/tasks').then((response) => {
                        if (response.data) {
                            this.tasks = response.data;
                            this.sTasks = this.tasks;
                        }
                    })
                }
            }
        },
        created: function () {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
            this.getTasks();
            Bus.$on('bidUpdated', (payload) => {
                this.getTasks();
            });
            Bus.$on('needsStripe', () => {
                $('#stripe-modal').modal();
            });

            window.Echo.private('');

        },
        mounted() {

            this.$store.commit('setCurrentPage', '/tasks');

            for (let j = 0; j < this.tasks.length; j++) {
                this.showTasks[j] = false;
            }

            const taskId = User.getParameterByName('taskId');
            if (taskId !== null && taskId !== '') {
                $('#task_' + taskId).addClass('info');
            }
            let success = this.$route.query.success;
            if (success !== undefined) {
                success = Language.lang().sub.stripe_success;
                Vue.toasted.success(success);
            }
            const error = this.$route.query.error;
            Vue.toasted.error(error);

            if (!this.user) {
                this.current_user = Spark.state.user
            }
        }
    }
</script>

<style scoped>

    ul {
        padding: 0px !important;
    }

    .paginate {
        height: 1rem;
    }

    .main {
        background-color: white;
        height: 200vh;
        padding: .25rem;
    }

    .search-bar {
        /*width: 100%;*/
        /*background-color: white;*/
        /*padding: .25rem .25rem 0rem .25rem;*/
        /*border: black thin solid;*/
    }

</style>