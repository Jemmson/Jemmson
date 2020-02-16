<template>
    <v-content>
        <div>

            <v-card
                    v-if="showBid(bidTask)"
                    class="mb-1rem"
            >
                <v-card-title
                        class="uppercase pb-0"
                        v-if="showBid(bidTask)"
                >
                    <v-icon
                            v-if="bidTask.payment_type === 'cash'"
                    >mdi-cash
                    </v-icon>
                    <v-icon
                            v-else
                    >mdi-credit-card
                    </v-icon>

                    <div class="mr-1rem ml-half-rem">{{ jobName(bidTask) }}</div>
                    <v-spacer></v-spacer>
                    <v-card-subtitle
                            class="ml-1rem capitalize"
                            :class="getLabelClass(bidTask)"
                    > {{ getLatestStatus() }}
                    </v-card-subtitle>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn
                            v-show="!showTheTask"
                            text
                            color="primary"
                            @click="showTheTask = !showTheTask"
                            width="40%"
                    >Show
                    </v-btn>
                    <v-btn
                            v-show="showTheTask"
                            color="primary"
                            text
                            @click="showTheTask = !showTheTask"
                            width="40%"
                    >Hide
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            text
                            color="red"
                            width="40%"
                            @click="showDeleteTaskModal(bidTask)"
                    >DELETE
                    </v-btn>
                </v-card-actions>
            </v-card>

            <div v-show="showTheTask">
                <v-container>
                    <h1 class="card-title">Task Details</h1>
                    <v-card>
                        <v-card-text>
                            <v-row
                                    class="justify-content-around mt-1rem"
                            >
                                <strong class="uppercase th-font">Start Date</strong>
                                <strong class="uppercase th-font">Quantity</strong>
                                <strong class="uppercase th-font">Price</strong>
                            </v-row>
                            <v-row
                                    class="justify-content-around mb-15"
                            >
                                <div>{{ prettyDate(bidTask) }}</div>
                                <div>{{ getTaskQuantity(bidTask) }}</div>
                                <div
                                        v-if="subHasEnteredAPrice(bidTask)"
                                        v-text="'$ ' + getBidPrice(bidTask)"
                                ></div>
                                <div v-else>Price Not Set</div>
                            </v-row>
                            <v-divider></v-divider>
                            <v-row
                                    v-if="isBidOpen(bidTask)"
                                    style="align-items:baseline"
                            >
                                <!--
                                                       v-if="bidTask.job_task.sub_sets_own_price_for_job === 1 && "-->
                                <v-spacer></v-spacer>
                                <v-text-field
                                        type="text"
                                        v-mask="getCurrencyMask()"
                                        v-model="bidPrice"
                                        class="input-margins"
                                        :id="bidTask ? 'price-' + bidTask.id : ''"
                                        label="Input Bid Price Here"
                                        @change="formatInput($event)"
                                ></v-text-field>
                                <!--                                @change="bidPrice('price-' + bidTask.id)"-->
                                <v-spacer></v-spacer>
                                <v-btn
                                        class=""
                                        color="primary"
                                        text
                                        bottom
                                        outlined
                                        @click.prevent="update(bidTask)"
                                        v-bind:id="bidTask ? bidTask.id: null" :disabled="disabled.submit">
                                        <span v-if="disabled.submit">
                                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                                        </span>
                                    Submit
                                </v-btn>
                                <v-spacer></v-spacer>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-container>

                <section class="col-12">
                    <h1 class="card-title">Job Address</h1>
                    <card>
                        <main class="row">
                            <div class="address-adjust">
                                <p v-if="getAddress(bidTask) !== 'Address Not Available'">
                                    <a target="_blank"
                                       :href="'https://www.google.com/maps/search/?api=1&query=' + getAddress(bidTask)">
                                        <i class="fas fa-map-marker icon"></i>
                                        {{ getAddress(bidTask) }}
                                    </a>
                                </p>
                                <p v-else>
                                    <i class="fas fa-map-marker icon"></i>
                                    {{ getAddress(bidTask) }}
                                </p>
                            </div>
                        </main>
                    </card>
                </section>


                <section class="col-12">
                    <h1 class="card-title">Messages</h1>
                    <card>
                        <main class="row">
                            <content-section
                                    v-if="showDeclinedMsg(bidTask)"
                                    label="Declined Reason:"
                                    :content="getDeclinedMessage(bidTask)"
                                    type="declinedReason"></content-section>
                            <content-section
                                    v-if="subHasMessage(bidTask)"
                                    label="Sub Instructions:"
                                    :content="getSubMessage(bidTask)"
                                    type="subInstructions"></content-section>
                        </main>
                    </card>
                </section>

                <section class="col-12">
                    <h1 class="card-title">Images</h1>
                    <card>
                        <main class="row">
                            <task-images class="images w-full ml-one-quarter-negative" :jobTask="getJobTask(bidTask)"
                                         type="sub">
                            </task-images>
                        </main>
                    </card>
                </section>

                <section class="col-12">
                    <h1 class="card-title">Actions</h1>
                    <card>
                        <main class="row">
                            <content-section
                                    label="Job Status:"
                                    :content="getLatestStatus()"
                                    :input-classes="getLabelClass(bidTask)"
                                    :section-classes="(isBidOpen(bidTask) || showFinishedBtn(bidTask)) ? 'border-bottom-thick-black' : ''"
                                    type="startOn"></content-section>
                            <v-btn
                                    class="w-full mt-1rem"
                                    color="primary"
                                    v-if="showFinishedBtn(bidTask)"
                                    @click="finished(bidTask)"
                                    :disabled="disabled.finished">
                                        <span v-if="disabled.finished">
                                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                                        </span>
                                Click Me When Task Is Finished
                            </v-btn>
                        </main>
                    </card>
                </section>

            </div>

            <delete-task-modal
                    @action="deleteTheTask($event)"
                    title="Do You Wish To Delete This Task?"
            >
            </delete-task-modal>

            <show-task-modal
                    :job-task="bidTask"
            >
            </show-task-modal>

        </div>
    </v-content>
</template>

<script>

    import TaskImages from '../../components/task/UploadTaskImages'
    import ContentSection from '../shared/ContentSection'
    import DeleteTaskModal from '../../components/job/DeleteTaskModal'
    import ShowTaskModal from '../../components/job/ShowTaskModal'
    import Status from '../../components/mixins/Status'
    import Card from '../shared/Card'
    import Currency from '../../components/mixins/Currency'

    export default {
        name: 'Task',
        components: {
            TaskImages,
            ContentSection,
            ShowTaskModal,
            DeleteTaskModal,
            Card
        },
        mixins: [
            Status,
            Currency
        ],
        updated() {
            this.getStoredBidPrice
        },
        computed: {
            getStoredBidPrice() {
                // if (localStorage.getItem('bidPrice' + this.bidTask.id)) {
                //   this.bidTask.bid_price = localStorage.getItem('bidPrice' + this.bidTask.id)
                // }
            }
        },
        mounted() {
            this.bidTask ? this.paymentType = this.bidTask.payment_type : this.paymentType = null
        },
        data() {
            return {
                paymentType: 'cash',
                showTheTask: false,
                disabled: {
                    submit: false,
                    finished: false,
                    deleteTask: false
                },
                deleteTask: {
                    id: ''
                },
                jobTask: {},
                formattedBidPrice: '',
                bidPrice: ''
            }
        },
        props: {
            bidTask: Object
        },
        methods: {

            getCurrencyMask() {
                return this.currencyMask(this.bidPrice)
            },

            subHasEnteredAPrice(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return bidTask.bid_price !== 0
                    // return true
                }
            },

            getBidPrice(bidTask) {
                if (bidTask) {
                    return this.convertNumToString(this.formatInput(bidTask.bid_price)).toLocaleString()
                }
            },

            formatInput(input) {
                if (typeof input === 'string') {
                    const numLength = input.length
                    let pricef = ''
                    if (numLength < 3) {
                        pricef = '.' + input
                        this.formattedBidPrice = pricef
                    } else if (numLength > 2) {
                        let price = ''
                        for (let i = 0; i < numLength - 2; i++) {
                            price = price + input[i]
                        }
                        pricef = price + '.' + input[numLength - 2] + input[numLength - 1]
                        this.formattedBidPrice = pricef
                    }
                    return pricef
                } else if (typeof input === 'number') {
                    let bidPrice = input / 100
                    this.formattedBidPrice = bidPrice
                    return bidPrice
                }
            },

            getLatestStatus() {
                if (
                    this.bidTask
                    && this.bidTask.job_task
                    && this.bidTask.job_task.job
                    && this.bidTask.job_task.job.sub_status
                    && this.bidTask.job_task.job.sub_status.length > 0
                ) {
                    return this.formatStatus(this.getSubStatus_latest(this.bidTask))
                }
            },
            showTheTaskModal() {
                $('#show-task-modal').modal('show')
            },
            showDeleteTaskModal(bidTask) {
                if (bidTask && bidTask.job_task) {
                    let job_task = bidTask.job_task
                    this.deleteTask.id = job_task.id
                    this.jobTask = job_task
                    $('#delete-task-modal').modal('show')
                }
            },
            deleteTheTask(action) {
                if (action === 'delete') {
                    this.deleteTheActualTask(this.deleteTask.id)
                }
                $('#delete-task-modal').modal('hide')
            },
            async deleteTheActualTask(id) {
                try {
                    const data = await axios.post('/jobTask/delete/', {
                        id: id
                    })
                    this.getBid(this.job_task.job.id)
                } catch (error) {
                    console.log(error)
                }
            },

            zero() {
                let zero = 0
                return zero.toString()
            },

            update(bidTask) {
                if (bidTask && bidTask.job_task) {
                    let id = bidTask.id
                    // debugger;
                    let bid_price = $('#price-' + id).val()
                    bid_price = this.convertPriceToIntegers(bid_price)
                    let po = this.paymentType
                    this.disabled.submit = true
                    axios.post('/bidTask', {
                        id: id,
                        bid_price: bid_price,
                        paymentType: po,
                        job_task_id: bidTask.job_task.id,
                        subId: bidTask.contractor_id,
                        generalId: bidTask.job_task.job.contractor_id
                    }).then((response) => {
                        // TODO: security review
                        Vue.toasted.success('Bid Sent.')
                        User.emitChange('bidUpdated')
                        this.disabled.submit = false
                    }).catch((error) => {
                        Vue.toasted.error(error.response.data.message)
                        this.disabled.submit = false
                    })
                }
            },
            setPaymentType(value) {
                this.paymentType = value
            },
            showBid(bid) {
                // TODO: backend what should happen to the bids that wheren't accepted

                const status = this.getLatestStatus()

                return status !== 'denied'
                    && status !== 'canceled_by_customer'
                    && status !== 'canceled_by_general'
                    && status !== 'canceled_bid_task'
                    && status !== 'paid'

                // if (bid.job_task === null) {
                //   return false
                // }
                // return (
                //   this.subsBidHasBeenAccepted()
                //   && (this.jobTaskHasBeenApproved() || this.jobHasBeenCompleted() || this.jobTaskHasBeenAccepted())
                //   || (this.jobHasBeenSentToTheCustomer() || this.jobTaskHasBeenInitiated))
            },

            subsBidHasBeenAccepted() {
                if (this.bid && this.bid.job_task) {
                    return this.bid.id === this.bid.job_task.bid_id
                }
            },

            jobTaskHasBeenApproved() {
                if (this.bid && this.bid.job_task) {
                    return bid.job_task.job.status === 'job.approved'
                }
            },

            jobHasBeenCompleted() {
                if (this.bid && this.bid.job_task) {
                    return bid.job_task.job.status === 'job.completed'
                }
            },

            jobTaskHasBeenAccepted() {
                if (this.bid && this.bid.job_task) {
                    return bid.job_task.status === 'bid_task.accepted'
                }
            },

            jobHasBeenSentToTheCustomer() {
                if (this.bid && this.bid.job_task) {
                    return bid.job_task.status === 'bid_task.bid_sent'
                }
            },

            jobTaskHasBeenInitiated() {
                if (this.bid && this.bid.job_task) {
                    return bid.job_task.status === 'bid_task.initiated'
                }
            },

            getLabelClass(bidTask) {

                if (bidTask && bidTask.job_task) {

                    let status = this.getStatus(bidTask)

                    return Format.statusLabel(
                        status,
                        this.isGeneral(bidTask),
                        this.isCustomer(bidTask),
                        bidTask
                    )

                    // if (this.isUserTheGeneral(bidTask)) {
                    //   return Format.statusLabel(
                    //     bidTask.job_task.status,
                    //     this.isGeneral(bidTask),
                    //     this.isCustomer(bidTask),
                    //     bidTask
                    //   )
                    // } else {
                    //   return Format.statusLabel(bidTask.status)
                    // }
                }

            },
            isGeneral(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return Spark.state.user.id === bidTask.job_task.contractor_id
                }
            },
            isCustomer(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return Spark.state.user.usertype === 'customer'
                }
            },
            status(bid_task) {
                return User.status(bid_task.status, bid_task.job_task, false)
            },

            jobName(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return Format.jobName(bidTask.job_task.task.name)
                }
            },

            prettyDate(bidTask) {
                if (bidTask && bidTask.job_task) {
                    let date = bidTask.job_task.start_date
                    if (date == null)
                        return ''
                    // return the date and ignore the time
                    date = date.split(' ')
                    return date[0]
                }
            },

            getTaskQuantity(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return bidTask.job_task.qty.toString()
                }
            },

            isBidOpen(bid) {
                const status = this.getLatestStatus();
                if (
                    status !== 'approved_by_customer'
                    && status !== 'finished_job'
                    && status !== 'finished_job_denied_by_contractor'
                    && status !== 'customer_changes_finished_task'
                    && status !== 'finished_job_approved_by_contractor'
                    && status !== 'waiting_for_customer_payment'
                    && status !== 'paid'
                ) {
                    return true
                } else {
                    return false
                }
            },
            getAddress(bidTask) {

                if (bidTask && bidTask.job_task) {
                    if (bidTask.job_task.location !== null) {
                        return bidTask.job_task.location.address_line_1 + ' ' +
                            bidTask.job_task.location.address_line_2 + ' ' +
                            bidTask.job_task.location.city + ' ' +
                            bidTask.job_task.location.state + ' ' +
                            bidTask.job_task.location.zip
                    } else {
                        return 'Address Not Available'
                    }

                    // return bidTask.job_task.location.address_line_1+" "+

                    // <a target="_blank" href="https://www.google.com/maps/search/?api=1&amp;query=3140 Talon Track Apt. 800  McCulloughton Utah 42620-5408">
                    // let location_id = 0;
                    // if (bidTask.job_task.location_id !== null) {
                    //   location_id = bidTask.job_task.location_id;
                    // } else {
                    //   location_id = bidTask.job_task.job.location_id;
                    // }
                    // Customer.getAddress(location_id, this.location)
                    // return this.location.location
                }

            },
            showDeclinedMsg(bidTask) {
                if (bidTask && bidTask.job_task) {
                    let msg = bidTask.job_task.declined_message
                    return msg !== null && msg !== ''
                }
            },

            getDeclinedMessage(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return bidTask.job_task.declined_message
                }
            },

            subHasMessage(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return bidTask.job_task.sub_message !== null && bidTask.job_task.sub_message != ''
                }
            },

            getSubMessage(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return bidTask.job_task.sub_message
                }
            },

            getJobTask(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return bidTask.job_task
                }
            },

            getStatus(bidTask) {
                if (bidTask && bidTask.job_task) {
                    return bidTask.job_task.status
                }
            },

            showFinishedBtn(bid) {
                if (bid && bid.job_task) {
                    return bid.job_task.status === 'bid_task.approved_by_customer' || bid.job_task.status === 'bid_task.denied'
                }
            },
            finished(bid) {
                SubContractor.finishedTask(bid, this.disabled)
            },
            async getBid(id) {
                try {
                    const {
                        data
                    } = await axios.get('/job/' + id)
                    if (data[0]) {
                        this.bid = data[0]
                        this.$store.commit('setJob', data[0])
                    } else {
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
        }
    }
</script>

<style scoped>

    .th-font {
        font-size: 12pt;
    }

    .input-margins {
        /*margin-right: 11rem;*/
    }

    .list-card {
        margin-left: 0rem !important;
    }

    .blue {
        /*background-color: #1c3d5a;*/
    }

    .images {
        margin: 1rem;
    }

    .address-adjust {
        padding: .5rem;
    }

    .hr {
        border: black solid thin;
    }

    .wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-row-gap: .5rem;
        /*margin-top: 1rem;*/
        /*padding: .75rem;*/
        /*grid-row-gap: 1rem;*/
    }

    .p-1rem {
        padding: 1rem 1rem 1rem 1rem;
    }

    .form-control-input {
        /*width: 40%;*/
    }

    .text-size {
        font-size: 14pt;
    }

    .task-box {
        border: white thin solid;
        border-radius: 5px;
        margin: .1rem;
        padding: .75rem;
    }

    .task {
        width: 100%;
        padding: .25rem .25rem .25rem .25rem;
        /*border: black thin solid;*/
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #3045a2;
    }

    .box {
        border: white thin solid;
        border-radius: 5px;
        margin: 1rem .1rem 1rem .1rem;
        background-color: #3490dc52;
        padding: .75rem;
    }

    .space {
        padding-left: 2rem;
    }

    .job-status {
        width: 100%;
        text-align: center;
        color: white;
        font-size: 14pt;
        margin-right: 1.75rem;
        /*margin: 1rem 1rem 1rem 1rem;*/
        padding: .5rem 1rem .5rem 1rem;
        border-radius: 5px;
    }

</style>
