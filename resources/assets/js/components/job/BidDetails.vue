<template>
    <v-container>

        <div v-if="showDeclinedMessage"
             class="ml-3 mr-3 w-100"
        >
            <h1 class="card-title mt-4">Job Change Message</h1>
            <card>
                <v-card-title
                        style="background-color: lightcoral"
                >
                    {{ bid.declined_message }}
                </v-card-title>
            </card>
        </div>

        <v-card>
            <v-card-actions
                    class="flex flex-col"
            >
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('jobStepper')"
                    >Workflow
                    </v-btn>
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('details')"
                    >Details
                    </v-btn>
                </div>
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('jobTask')"
                    >JobTasks
                    </v-btn>
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('images')"
                    >Images
                    </v-btn>
                </div>
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('location')"
                    >Location
                    </v-btn>
                    <v-btn
                            class="nav-btn-position"
                            @click="showSection('notes')"
                    >Job Notes
                    </v-btn>
                </div>
                <div class="flex flex-end">
                    <v-btn
                            v-if="canAddATask() && !isCustomer"
                            class="w-100 btn btn-sm btn-normal flex-1"
                            width="100%"
                            name="addTaskToBid"
                            id="addTaskToBid"
                            @click="$router.push('/job/add/task')"
                    >
                        Add A Task
                    </v-btn>
                </div>
            </v-card-actions>
        </v-card>

        <v-col cols="12"
               v-show="show.jobStepper"
        >
            <job-stepper
                    :status="getSelectedJob()"
                    :user="getUser()"
                    style="padding: 0;"
            ></job-stepper>
        </v-col>

        <v-col cols="12"
               v-show="show.details"
        >
            <v-card>
                <v-card-title>Details</v-card-title>

                <v-simple-table v-if="isCustomer">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th class="text-left"></th>
                            <th class="text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Job Name:</td>
                            <td class="capitalize">{{ bid.job_name }}</td>
                        </tr>
                        <tr @click="viewContractorInfo()">
                            <td>Contractor Name:</td>
                            <td class="lookLikeALink">{{ getCompanyName() }}</td>
                        </tr>
                        <tr v-if="!bidHasBeenSubmitted">
                            <td>Start Date:</td>
                            <td>Bid Not Complete</td>
                        </tr>
                        <tr v-if="bidHasBeenSubmitted">
                            <td>Start Date:</td>
                            <td>{{ agreedStartDate }}</td>
                        </tr>
                        <tr v-if="!bidHasBeenSubmitted">
                            <td>Total Bid Price:</td>
                            <td>Bid Not Complete</td>
                        </tr>
                        <tr v-if="bidHasBeenSubmitted">
                            <td>Total Bid Price:</td>
                            <td>
                                {{ bidPrice }}
                                <v-icon
                                        color="primary"
                                        @click="showFeeDialog()"
                                        class="ml-1rem">mdi-information
                                </v-icon>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>


                <info-modal
                        :open-dialog="feeDialog"
                        :is-customer="isCustomer"
                        :estimated-fee="totalEstimatedFee()"
                        :job-type="bid ? bid.payment_type : null"
                        @closeFeeDialog="feeDialog = false"
                ></info-modal>


                <v-card-text v-if="subTaskWarning && !isCustomer" class="uppercase red ml-1rem mr-1rem">bid price less
                    than the sum of
                    your subs bids
                </v-card-text>
                <v-simple-table v-if="!isCustomer">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th class="text-left"></th>
                            <th class="text-left"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Job Name:</td>
                            <td class="capitalize">{{ bid.job_name }}</td>
                        </tr>
                        <tr @click="viewCustomerInfo()">
                            <td>Customer Name:</td>
                            <td class="lookLikeALink">{{ customerName }}</td>
                        </tr>
                        <tr>
                            <td>Start Date:</td>
                            <td>{{ agreedStartDate }}</td>
                        </tr>
                        <tr>
                            <td>Total Bid Price:</td>
                            <td>
                                {{ bidPrice }}
                                <v-icon
                                        color="primary"
                                        @click="showFeeDialog()"
                                        class="ml-1rem">mdi-information
                                </v-icon>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
            </v-card>
        </v-col>

        <section ref="job_tasks" class="col-12"
                 v-if="getJobTasks() !== undefined"
                 v-show="show.jobTask"
        >
            <div v-if="!isCustomer && bid && getJobTasksLength() > 0">

                <h1 class="card-title mt-4">Job Tasks</h1>

                <v-card v-for="(item, i) in getJobTasks()"
                        :key="i"
                        class="card-positioning"
                        :class="i % 2 === 0 ? 'b-brown': 'b-blue'"
                >
                    <v-card-title
                            class="uppercase mb-0 pb-0"
                            style="font-size: 12pt"
                    >{{ jobTaskObject(item).Name }}
                        <v-spacer></v-spacer>
                        <div></div>
                        <v-card-subtitle
                                class="uppercase"
                                style="font-size: 10pt"
                        >{{ jobTaskObject(item).Status }}
                        </v-card-subtitle>
                    </v-card-title>
                    <v-card-title
                            v-if="notificationMessage(item)"
                    >
                        <v-btn
                                v-if="item.sub_statuses.length > 0"
                                text
                                class="btn-size btn-weight"
                                :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                                :to="'/job/task/' + i"
                        >{{ notificationMessage(item) }}
                        </v-btn>
                    </v-card-title>
                    <v-card-title v-if="hasTaskMessages(item)">
                        <ul>
                            <li v-for="(message, index) in item.task_messages" :key="index">
                                {{ message.message }}
                            </li>
                        </ul>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-row
                            class="justify-content-around"
                    >

                        <strong class="uppercase"
                                style="font-size: 10pt"
                        >Subs</strong>
                        <strong class="uppercase"
                                style="font-size: 10pt"
                        >Quantity</strong>
                        <strong class="uppercase"
                                style="font-size: 10pt"
                        >Price</strong>
                    </v-row>
                    <v-row
                            class="justify-content-around mb-15"
                    >
                        <div>{{ jobTaskObject(item).Subs }}</div>
                        <div>{{ jobTaskObject(item).Qty }}</div>
                        <div
                                v-if="jobTaskObject(item).Price"
                                v-text="'$ ' + jobTaskObject(item).Price"
                        ></div>
                        <div v-else>Price Not Set</div>
                    </v-row>
                    <v-divider></v-divider>
                    <v-card-actions
                            class="space-evenly"
                    >
                        <v-btn
                                class="btn-size btn-weight"
                                :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                                :to="'/job/task/' + i"
                                text
                        >Edit
                        </v-btn>
                        <v-btn
                                v-if="isGeneral() && approvedByCustomer(item)"
                                @click="openSubInvite(item.id)"
                                class="btn-size btn-weight"
                                :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                                text
                        >Add Sub
                        </v-btn>
                        <div class="flex flex-col"
                             v-if="isGeneral() && subFinishedTask(item)"
                        >
                            <div class="text-center"
                                 :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                            >Sub Has Finished</div>
                            <div class="flex space-evenly">
                                <v-btn
                                        @click="approveSubsWork(item)"
                                        class="btn-size btn-weight"
                                        :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                                        text
                                >
                                    Approve
                                </v-btn>
                                <v-btn
                                        class="btn-size btn-weight"
                                        :class="i % 2 === 0 ? 'error--text': 'error--text'"
                                        text
                                        @click="openDenyTaskForm(item)"
                                >
                                    Change Task
                                </v-btn>
                            </div>
                        </div>
                        <v-btn
                                :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                                v-if="isGeneral() && showFinishedBtn(item)"
                                @click="finishedTask(item)"
                                class="btn-size btn-weight"
                                :loading="disabled.finished"
                                text
                        >Mark Finished
                        </v-btn>
                        <div
                                :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                                v-if="isGeneral() && subHasNotFinishedTask(item)"
                        ><span
                                style="color: black"
                        >Waiting For Sub</span>
                        </div>
                    </v-card-actions>

                    <sub-invite-modal v-if="isGeneral()" :job-task="item"
                                      :job-task-task="item ? item.task : null"
                                      :job-task-name="item ? item.task.name : null"
                                      :bid-payment-type="bid ? bid.payment_type : null"
                                      :id="item ? item.id : null">
                    </sub-invite-modal>
                </v-card>
            </div>
            <div v-else-if="generalHasSentABid(bid)">
                <div id="general">General</div>
                <h1 class="card-title mt-4">Job Tasks</h1>
                <card>

                    <v-row>
                        <div>
                                <span class="">
                                    (<b ref="job_task_length_customer">{{ getJobTasksLength() }}</b>)
                                </span> Total
                        </div>

                        <v-spacer></v-spacer>

                        <v-btn
                                id="viewTasks"
                                class="w-40"
                                color="primary"
                                v-if="!taskHasChanged"
                                @click.prevent="viewTasks()"
                        >
                            View Tasks
                        </v-btn>
                    </v-row>

                    <table class="table mt-2rem">
                        <thead>
                        <tr>
                            <td>Name</td>
                            <td>Qty</td>
                            <td>Unit Price</td>
                            <td>Price</td>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="(jt, index) in getJobTasks()">
                            <tr
                                    :class="paid(jt) ? 'paid' : ''">
                                <td colspan="4"
                                    :id="'jobTaskStatus-' + index"
                                    class="uppercase text-center"
                                >
                                    {{ getLatestJobTaskStatus(jt) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="capitalize">
                                    {{ jt.task.name }}
                                </td>
                                <td>
                                    {{ jt.qty }}
                                </td>
                                <td>
                                    {{ formatPrice(jt.unit_price) }}
                                </td>
                                <td>
                                    {{ formatPrice(jt.cust_final_price) }}
                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>

                </card>
            </div>

        </section>

        <!-- images -->
        <div class="col-12"
             v-show="show.images"
        >
            <h1 class="card-title mt-4">Images</h1>
            <p>Only allowable file types are JPG, PNG, GIF or WebP files</p>
            <card>
                <div class="row">

                    <div class="col-12">
                        <task-images
                                :is-customer="isCustomer"
                                :job="bid" type="notsub">
                        </task-images>
                    </div>
                </div>
            </card>
        </div>

        <section ref="job_address" class="col-12" v-if="showAddress"
                 v-show="show.location"
        >
            <h1 class="card-title mt-4">Job Address</h1>
            <card>

                <div class="flex flex-col">
                    <div>
                        {{ bid.location.address_line_1 }}
                    </div>
                    <div>
                        {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
                    </div>
                </div>

                <hr>

                <main class="map-responsive">
                    <iframe
                            width="450"
                            height="250"
                            frameborder="0" style="border:0"
                            :src="'https://www.google.com/maps/embed/v1/search?key=AIzaSyBAQZB-zS1HVbyNe2JEk1IgNVl0Pm2xsno&q=' +
                            bid.location.address_line_1 + ' ' +
                            bid.location.city + ' ' +
                            bid.location.state + ' ' +
                            bid.location.zip
                            " allowfullscreen>
                    </iframe>
                </main>
            </card>
        </section>

        <section class="col-12"
                 v-show="show.notes"
        >
            <h1 v-if="isCustomer" class="card-title mt-4">Special Notes For The Job</h1>
            <h1 v-else class="card-title mt-4">Special Notes From Customer</h1>
            <card>
                <main class="row">
                    <section class="col-12">

                        <div style="display: none;">{{ messageFromCustomer }}</div>
                        <div style="display: none;">{{ getPaidWithCashMessage }}</div>


                        <textarea ref="message_text_area"
                                  v-model="customerNotesMessage"
                                  name="notes" id="notes" cols="30" rows="10"
                                  class="form-control"
                                  :disabled="!isCustomer"
                        >

                            </textarea>

                        <v-btn
                                v-if="isCustomer"
                                class="mt-1rem"
                                color="primary"
                                ref="update_customer_notes_button"
                                @click="updateGeneralContractorNotes"
                        >Submit
                        </v-btn>

                    </section>
                </main>
            </card>
        </section>

        <!-- / tasks -->

        <section class="col-12" v-if="(isCustomer && needsApproval()) || !isCustomer">
            <h1 class="card-title mt-4">Actions</h1>
            <card class="mb-4">
                <!-- /customer approve bid form -->
                <approve-bid v-if="isCustomer && needsApproval()" :bid="bid">
                </approve-bid>

                <v-sheet
                        class="text-center uppercase successful-submit"
                        v-if="submittedMessage"
                >You have successfully submitted a bid
                </v-sheet>

                <general-contractor-bid-actions
                        @bid-submitted="bidSubmitted()"
                        @remove-notification="removeSubmittedNotification()"
                        :bid="bid" v-if="!isCustomer">
                </general-contractor-bid-actions>
            </card>
        </section>

        <section class="col-12" v-if="customerHasCompletedTasks()">
            <h1 class="card-title mt-4">Completed Tasks</h1>
            <card>
                <main class="row w-full ml-0">
                    <completed-tasks
                            class="w-full"
                            :bid="bid"
                            :paid="checkIfPaid()"
                    >
                    </completed-tasks>
                </main>
            </card>
        </section>

        <stripe
                :bid="bid"
                :client-secret="clientSecret"
                :user="getCurrentUser()"
                @paid="paidFromSignUp()"
        >
        </stripe>

        <deny-task-modal v-if="!isCustomer"
                         :job-task="currentJobTask"
                         :id="currentJobTaskId">
        </deny-task-modal>

    </v-container>
</template>

<script>
    import {mapGetters, mapMutations, mapActions, mapState} from 'vuex'
    import ApproveBid from './ApproveBid'
    import Card from '../shared/Card'
    import CompletedTasks from './CompletedTasks'
    import ContentSection from '../shared/ContentSection'
    import GeneralContractorBidActions from './GeneralContractorBidActions'
    import Format from '../../classes/Format'
    import HorizontalTable from '../shared/HorizontalTable'
    import Info from '../shared/Info'
    import InfoModal from '../../components/documentation/InfoModal'
    import JobStepper from '../../components/shared/JobStepper'
    import Status from '../mixins/Status.js'
    import SubInviteModal from '../../components/task/SubInviteModal'
    import TaskImages from '../../components/task/UploadJobImages'
    import Utilities from '../mixins/Utilities'
    import Stripe from '../stripe/Stripe'
    import DenyTaskModal from '../../components/task/DenyTaskModal'

    export default {
        components: {
            ApproveBid,
            Card,
            ContentSection,
            CompletedTasks,
            DenyTaskModal,
            GeneralContractorBidActions,
            HorizontalTable,
            Info,
            InfoModal,
            JobStepper,
            Stripe,
            SubInviteModal,
            TaskImages
        },
        mixins: [Status, Utilities],
        props: {
            bid: Object,
            isCustomer: Boolean,
            customerName: String
        },
        created: function () {
            Bus.$on('needsStripe', (clientSecret) => {
                this.clientSecret = clientSecret
                $('#stripe-modal').modal()
            })

            // if (!this.bid) {
            //     this.bid = localStorage.getItem('getCurrentBid');
            // }

            this.getUser()
            document.body.scrollTop = 0 // For Safari
            document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
        },
        data() {
            return {
                show: {
                    jobStepper: false,
                    details: false,
                    jobTask: false,
                    location: false,
                    images: false
                },
                isPaid: false,
                clientSecret: null,
                feeDialog: false,
                el: 2,
                area: {
                    area: ''
                },
                currentJobTaskId: null,
                currentJobTask: {},
                taskHasChanged: false,
                jobTaskItem: {},
                addTaskStartDate: false,
                addTaskBidPrice: false,
                statuses: [
                    {
                        type: 'Bid Initiated',
                        description:
                            'Contractor has sent a bid but has not added a task to the job'
                    },
                    {
                        type: 'BID CHANGE REQUESTED - PLEASE REVIEW',
                        description:
                            'Customer has not approved the bid and is asking for a change to be made'
                    },
                    {
                        type: 'Bid In Progress',
                        description:
                            'Contractor has added tasks to the bid but has not yet submitted it to the customer'
                    },
                    {
                        type: 'Waiting on Customer Approval',
                        description:
                            'Contractor has submitted the finished bid and is now waiting for the customer to approve it'
                    },
                    {
                        type: 'In Progress',
                        description:
                            'The job is in progress and it is waiting for the contrator sub to finish the job'
                    },
                    {
                        type: 'Job Completed',
                        description:
                            'The Customer has paid for the job and the job is completed'
                    }
                ],
                subTaskWarning: false,
                customerNotesMessage: '',
                showPaidWithCashNotes: false,
                disableCustomerNotesButton: false,
                customerNotes: false,
                customerNotes_contractor: false,
                areaError: '',
                payWithCashMessage: '',
                successfulUpdate: '',
                locationExists: false,
                customerInfo: false,
                paymentTypeCash: false,
                paymentTypeStripe: true,
                selectedPayment: 'creditCard',
                submissionCard: false,
                cancelBidCard: false,
                disabled: {
                    cancelBid: false,
                    jobCompleted: false,
                    submitBid: false,
                    submitMessage: false,
                    finished: false
                },
                submittedMessage: false
            }
        },
        computed: {
            ...mapState({
                selectedJob: state => state.job.model,
            }),
            ...mapGetters(['getCustomerName']),

            agreedStartDate() {
                if (this.bid.agreed_start_date !== undefined && this.bid.agreed_start_date !== null) {
                    return this.dateOnly(this.bid.agreed_start_date)
                } else {
                    if (this.isCustomer) {
                        return ''
                    } else {
                        this.addTaskStartDate = true
                        return 'Add A Task'
                    }

                }
            },
            messageFromCustomer() {
                if (this.getJob() && this.bid.customer) {
                    this.customerNotesMessage = this.bid.customer.customer.notes
                    return this.bid.customer.customer.notes
                }
            },
            getPaidWithCashMessage() {
                if (this.getJob()) {
                    if (this.bid.paid_with_cash_message) {
                        this.payWithCashMessage = this.bid.paid_with_cash_message
                        this.selectedPayment = 'cash'
                        return this.bid.paid_with_cash_message
                    }
                }
            },
            bidPrice() {
                if (
                    this.bid.bid_price &&
                    (this.bid.status === 'bid.initiated' ||
                        this.bid.status === 'bid.in_progress' ||
                        this.bid.status === 'job.approved' ||
                        this.bid.status === 'bid.declined' ||
                        this.bid.status === 'bid.sent'
                    )
                ) {
                    this.addTaskBidPrice = false
                    let theBidPrice = this.bid.bid_price
                    return '$ ' + Format.decimal(theBidPrice)
                } else {
                    if (this.isCustomer) {
                        return ''
                    } else {
                        this.addTaskBidPrice = true
                        return 'Add A Task'
                    }

                }
            },
            showBidPrice() {
                if (this.isCustomer) {
                    const status = this.bid.status
                    // if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
                    if (this.generalHasSentABid(this.getJob())) {
                        return true
                    }
                    return false
                }
                return true
            },
            status() {
                return User.status(this.bid.status, this.bid, Spark.state.user)
            },
            bidHasBeenSubmitted() {
                // return this.bid.status !== 'bid.initiated' &&
                //   this.bid.status !== 'bid.in_progress'
                if (this.bid.status) {
                    return this.generalHasSentABid(this.bid)
                }
            },
            showDeclinedMessage() {
                if (this.bid && this.bid.declined_message) {
                    return (
                        !this.isCustomer &&
                        this.bid.declined_message !== null
                        && this.getJobStatus_latest(this.getJob()) === 'changed'
                        // && this.bid.status === 'bid.declined'
                    )
                }
            },

            disableSubmitBid() {
                // return this.bid.status === 'bid.sent'
                return this.generalCanSubmitABid(this.getJob())
            },

            showAddress() {
                if (this.getJob()) {
                    return (
                        this.bid.location_id !== undefined &&
                        this.bid.location_id !== null &&
                        this.bid.location !== null &&
                        !this.isCustomer
                    )
                }
            }
        },
        watch: {
            bid: function () {
                this.bid = this.bid
            }
        },
        methods: {

            showSection(section) {
                this.hideAllSections();
                if (section === 'jobStepper') {
                    this.show.jobStepper = true;
                } else if (section === 'details') {
                    this.show.details = true;
                } else if (section === 'jobTask') {
                    this.show.jobTask = true;
                } else if (section === 'images') {
                    this.show.images = true;
                } else if (section === 'location') {
                    this.show.location = true;
                } else if (section === 'notes') {
                    this.show.notes = true;
                }
            },

            hideAllSections() {
                this.show.details = false;
                this.show.jobStepper = false;
                this.show.jobTask = false;
                this.show.images = false;
                this.show.location = false;
                this.show.notes = false;
            },

            checkIfPaid() {
                return this.isPaid;
            },

            paidFromSignUp() {
                this.isPaid = true;
            },

            async getBids() {
                let url = ''
                if (User.isCustomer()) {
                    url = 'getJobsForCustomer'
                } else {
                    url = 'jobs'
                }
                await axios.get(url).then((response) => {
                    if (Array.isArray(response.data)) {
                        return response.data;
                    }
                })
            },

            openDenyTaskForm(item) {
                this.currentJobTask = item;
                this.currentJobTaskId = item.id;
                $('#deny-task-modal_' + item.id).modal('show')
            },

            subHasNotFinishedTask(item) {
                if (this.isASub(item.contractor_id, this.bid.contractor_id)) {
                    return this.getLatestJobTaskStatus1(item) === 'approved by customer'
                        || this.getLatestJobTaskStatus1(item) === 'declined subs work'
                }
            },

            isASub(subId, generalId) {
                return subId !== generalId;
            },

            getJobTasksLength() {
                let jobTasks = this.getJobTasks()
                return jobTasks.length
            },

            atleastOnetaskHasChanged() {
                const jobTasks = this.getJobTasks()
                for (let i = 0; i < jobTasks.length; i++) {
                    if (this.getLatestJobTaskStatus(jobTasks[i]) === 'changed') {
                        this.taskHasChanged = true;
                    }
                }
            },

            hasTaskMessages(jt) {
                return jt && jt.task_messages && jt.task_messages.length > 0
            },

            notificationMessage(item) {
                let initiated = false;
                let accepted = false;
                let sent_a_bid = false;
                if (item.sub_statuses) {
                    for (let i = 0; i < item.sub_statuses.length; i++) {
                        if (item.sub_statuses[i].status == 'initiated') {
                            initiated = true;
                        }
                        if (item.sub_statuses[i].status == 'accepted') {
                            accepted = true;
                        }
                        if (item.sub_statuses[i].status == 'sent_a_bid') {
                            sent_a_bid = true;
                        }
                    }
                }

                if (accepted) {
                    return "Accepted Bid"
                }

                if (sent_a_bid) {
                    return "Sent Bids"
                }

                if (initiated) {
                    return "No Sent Bids"
                }
            },

            getCurrentUser() {
                if (Spark.state) {
                    return Spark.state.user
                }
            },

            totalEstimatedFee() {
                if (this.bid) {
                    if (this.bid.payment_type === 'cash') {
                        return 2.80;
                    } else {
                        return (parseFloat(this.getBidPriceValue(this.bidPrice)) * .029) + this.getStripeFlatRateCharges() + 2.50
                    }
                }
            },

            getBidPriceValue(bidPrice) {
                const bp = bidPrice.split(' ')
                return bp[1]
            },

            getStripeFlatRateCharges() {
                return this.getNumberOfJobTasks() * .30
            },

            getNumberOfJobTasks() {
                if (this.getJob() && this.getJobTasks()) {
                    return this.bid.job_tasks.length
                }
            },

            getJobTasks() {
                if (this.getJob()) {
                    return this.bid.job_tasks
                }
            },

            getJob() {
                return this.bid
            },

            showFeeDialog() {
                this.feeDialog = true
            },

            bidSubmitted() {
                this.submittedMessage = true
            },

            removeSubmittedNotification() {
                this.submittedMessage = false
            },

            paid(jobTask) {
                let status = this.getLatestJobTaskStatus(jobTask)

                return status === 'paid'

            },

            subFinishedTask(item) {
                let status = this.getLatestSubStatus(item)
                return status === 'finished_job'
            },

            getLatestSubStatus(item) {
                if (item && item.sub_statuses && item.sub_statuses.length > 0) {
                    return item.sub_statuses[item.sub_statuses.length - 1].status
                }
            },

            approveSubsWork(jobTask) {
                GeneralContractor.approveSubsTask(jobTask)
            },

            jobIsNotFinishedAndNotApproved(item) {
                if (item) {
                    return !(item.status === 'job.approved'
                        || item.status === 'bid.paid')
                }
            },
            showFinishedBtn(jobTask) {
                let status = this.getLatestJobTaskStatus1(jobTask);
                return this.isGeneral() &&
                    this.isAssignedToMe(jobTask, Spark.state.user.id) &&
                    status === 'approved by customer'
            },
            openSubInvite(jobTaskId) {
                $('#sub-invite-modal_' + jobTaskId).modal()
            },
            finishedTask(jobTask) {
                GeneralContractor.finishedTask(jobTask, this.disabled)
            },
            isAssignedToMe(jobTask, userId) {
                return userId === jobTask.contractor_id
            },
            approvedByCustomer(task) {
                const latestStatus = this.getLatestJobTaskStatus(task)
                return latestStatus !== 'general finished work'
                    && latestStatus !== 'sub finished work'
                    && latestStatus !== 'paid'
            },


            getLatestJobTaskStatus(task) {

                let status = '';
                let taskHasChanged = false;

                if (task) {
                    if (task.job_task_statuses) {
                        status = this.formatStatus(this.getJobTaskStatus_latest(task))
                    } else {
                        status = this.formatStatus(this.getTheLatestJobTaskStatus(task.job_task_status))
                    }
                }

                const jobTasks = this.getJobTasks()
                for (let i = 0; i < jobTasks.length; i++) {
                    if (this.getLatestJobTaskStatus1(jobTasks[i]) === 'changed') {
                        taskHasChanged = true;
                    }
                }

                if (taskHasChanged && status === 'waiting for customer approval') {
                    return 'WAITING ON BID SUBMISSION'
                }

                return status
            },

            getLatestJobTaskStatus1(task) {

                if (task) {
                    if (task.job_task_statuses) {
                        status = this.formatStatus(this.getJobTaskStatus_latest(task))
                    } else {
                        status = this.formatStatus(this.getTheLatestJobTaskStatus(task.job_task_status))
                    }
                }

                return status
            },
            getSelectedJob() {
                if (this.getJob() && this.bid.job_statuses) {
                    return this.bid.job_statuses[this.bid.job_statuses.length - 1].status
                } else if (this.getJob() && this.bid.job_status) {
                    return this.bid.job_status[this.bid.job_status.length - 1].status
                }
            },



            canAddATask() {
                if (this.bid) {
                    return this.bid.status !== 'job.approved' && this.bid.status !== 'bid.sent'
                }
            },


            viewContractorInfo() {
                this.$router.push({name: 'contractor-info', params: {contractorId: this.bid.contractor.id}})
            },

            viewCustomerInfo() {
                if (this.getJob()) {
                    this.$router.push({name: 'customer-info', params: {customerId: this.bid.customer.id}})
                }
            },

            jobTaskObject(jt) {
                if (jt) {
                    return {
                        Name: jt.task ? jt.task.name : '',
                        Subs: jt.bid_contractor_job_tasks ? jt.bid_contractor_job_tasks.length : '',
                        Status: this.formatStatus(this.getJobTaskStatus_latest(jt)),
                        'Status Date': this.formatDate(this.dateOnly(this.getJobTaskCreationDate_latest(jt))),
                        Qty: jt.qty,
                        Price: jt.cust_final_price
                    }
                }
            },
            currentStep() {
                if (this.getJob()) {
                    this.step = this.getStatus(
                        this.bid.job_statuses[this.bid.job_statuses.length - 1],
                        this.bid.job_statuses[this.bid.job_statuses.length - 1],
                        this.bid.job_statuses[this.bid.job_statuses.length - 1]
                    )
                }
            },
            needsApproval() {
                // TODO: use regular status values to check these
                return this.bid.status === 'bid.sent'
            },
            formatPrice(price) {
                return '$ ' + Format.decimal(price)
            },
            customerHasCompletedTasks() {
                let taskIsFinished = false
                if (this.getJob() && this.getJobTasks() && this.getNumberOfJobTasks() > 0) {
                    for (let i = 0; i < this.getNumberOfJobTasks(); i++) {
                        let status = ''
                        if (this.bid.job_tasks[i].job_task_status
                            && this.bid.job_tasks[i].job_task_status.length > 0
                        ) {
                            status = this.bid.job_tasks[i].job_task_status[this.bid.job_tasks[i].job_task_status.length - 1].status
                        } else {
                            status = this.bid.job_tasks[i].job_task_statuses[this.bid.job_tasks[i].job_task_statuses.length - 1].status
                        }
                        if (status === 'approved_subs_work' || status === 'general_finished_work') {
                            taskIsFinished = true
                        }
                    }
                }
                return this.isCustomer && taskIsFinished
            },
            getCompanyName() {
                if (this.getJob()) {
                    if (this.getJobTasks() && (this.getNumberOfJobTasks() !== 0)) {
                        if (this.bid.job_tasks[0].task && this.bid.job_tasks[0].task.contractor) {
                            return this.bid.job_tasks[0].task.contractor.company_name
                        }
                        if (this.bid.contractor.contractor) {
                            return this.bid.contractor.contractor.company_name
                        }
                    } else if (this.bid.contractor) {
                        return this.bid.contractor.contractor.company_name
                    }
                }
            },
            bidHasNoTasks() {
                if (this.getJobTasks()) {
                    return this.getNumberOfJobTasks() === 0
                }
            },
            cancelDialog() {
                this.cancelBidCard = false
                this.submissionCard = false
                this.disabled.cancelBid = false
                this.disabled.submitBid = false
            },
            getUser() {
                switch (Spark.state.user.usertype) {
                    case 'customer':
                        return 'customer'
                    case 'contractor':
                        if (Spark.state.user.id === this.bid.contractor_id) {
                            return 'general'
                        } else {
                            return 'sub'
                        }
                }
            },
            openCancelDialogCard() {
                this.cancelBidCard = true
                this.disabled.cancelBid = true
            },
            showPreApprovedActions() {
                return this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed' && this.isGeneral(this.bid.contractor_id, Spark.state.user.id)
            },
            cancelTheBid() {
                this.disabled.cancelBid = true
                this.cancelBidCard = false
                this.disabled.cancelBid = false
            },
            submitTheBid() {
                this.submissionCard = false
                this.disabled.submitBid = false
                this.notifyCustomerOfFinishedBid()
            },
            showSubmissionCard() {
                console.log('hello')
                this.cancelBidCard = false
                this.submissionCard = true
                this.disabled.submitBid = true
            },

            showCancelCard() {
                this.disabled.cancelBid = true
                this.submissionCard = false
                this.cancelBidCard = true
            },

            notifyCustomerOfFinishedBid() {

                // go through each job task and compare the sub price to the contractor task price
                // first check if there is a sub.
                // check if the sub price is an accepted price
                // compare the the accepted sub price to the contractor price
                // if the accepted sub price is higher then throw an error

                let subTaskWarning = false
                for (let i = 0; i < this.getNumberOfJobTasks(); i++) {
                    if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
                        subTaskWarning = true
                    }
                }

                if (!subTaskWarning) {
                    this.subTaskWarning = false
                    GeneralContractor.notifyCustomerOfFinishedBid(this.getJob(), this.disabled)
                } else {
                    console.log('subs price is higher than contractor price')
                    this.subTaskWarning = true
                }

            },

            isGeneral(contractor_id, user_id) {
                if (this.getJob() !== null) {
                    return contractor_id === user_id
                }
                return false
            },
            viewTasks() {
                this.$router.push('/job/tasks')
            },
            paymentMethod(paymentType) {
                if (paymentType === 'cash') {
                    this.selectedPayment = 'cash'
                    this.paymentTypeCash = true
                    this.paymentTypeStripe = false
                } else {
                    this.selectedPayment = 'stripe'
                    this.paymentTypeCash = false
                    this.paymentTypeStripe = true
                }
            },
            async submitPayWithCashMessage() {
                this.disabled.submitMessage = true
                try {
                    const data = await axios.post('/paidWithCashMessage', {
                        jobId: this.bid.id,
                        paidWithCashMessage: this.payWithCashMessage
                    })

                    if (data.data.message) {
                        this.successfulUpdate = 'true'
                        setTimeout(function () {
                            this.successfulUpdate = ''
                            this.disabled.submitMessage = false
                        }.bind(this), 2000)
                    } else {
                        this.successfulUpdate = 'false'
                        setTimeout(function () {
                            this.successfulUpdate = ''
                            this.disabled.submitMessage = false
                        }.bind(this), 2000)
                    }

                } catch (error) {
                    console.log(error)
                }
            },
            getLabelClass(status) {
                return Format.statusLabel(status)
            },
            showNotes() {
                this.customerNotes = !this.customerNotes
            },
            ...mapMutations(['setCustomerName']),
            ...mapActions(['actCustomerName']),
            updateGeneralContractorNotes() {
                Customer.updateNotesForJob(
                    this.customerNotesMessage,
                    this.bid.customer.id
                )
            },
            updateArea() {
                // Customer.updateArea (this.area.area, this.bid.id);
            },
            showArea() {
                // console.log('user type: ' + User.isContractor())
                return this.area.area !== '' && !this.isCustomer
            },
            initializePayWithCashMessageValue() {
                if (this.getJob() && this.bid.paid_with_cash_message) {
                    console.log('I am true')
                    console.log(this.bid.paid_with_cash_message)
                    this.payWithCashMessage = this.bid.paid_with_cash_message
                    this.customerNotesMessage = this.bid.customer.customer.notes
                }
                if (this.getJob() && this.bid.customer) {
                    console.log(this.bid.customer.customer.notes)
                    this.customerNotesMessage = this.bid.customer.customer.notes
                }
            }
        },
        mounted() {
            this.initializePayWithCashMessageValue()

            // if (this.bid) {
            //     this.bid = localStorage.setItem('getCurrentBid', this.bid);
            // }
        }
    }
</script>

<style lang="less" scoped>

    .nav-btn-position {
        width: 46%;
        margin-bottom: .25rem;
    }

    .b-brown {
        background-color: beige;
    }

    .b-blue {
        background-color: cornflowerblue;
    }

    .paid {
        background-color: red;
    }

    .card-positioning {
        margin-bottom: .25rem;
    }

    .status {
        padding-top: 1rem;
        padding-bottom: 1rem;
        font-family: auto;
        font-size: 20pt;
    }

    .no-notes {
        text-align: center;
        margin-right: -9rem;
    }

    .status-header {
        font-size: 1rem;
        margin-left: -1rem;
        margin-right: -2rem;
        text-align: start;
        margin-bottom: .15rem;
        padding: .25rem;
        margin-top: .15rem;
    }

    .description {
        font-size: .9rem;
        margin-left: -1rem;
        margin-right: -2rem;
        text-align: start;
        margin-bottom: .15rem;
        /*background-color: beige;*/
        padding: .25rem;
        margin-top: .15rem;
        border-radius: 5px;
    }

    /*.spacing {*/
    /*margin-bottom: 10rem;*/
    /*}*/

    .wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .w-100 {
        width: 100%;
    }

    // .btn-width {
    //     width: 15rem;
    // }
    .status {
        /*padding: 1rem;*/
        /*padding-left: 6px;*/
        /*padding-right: 6px;*/
        align-items: center;
        justify-content: space-evenly;
    }

    .btn-width {
        width: 100%;
    }

    .notes-width {
        max-width: 75%;
    }

    span {
        font-size: 15px;
    }

    .lookLikeALink {
        text-decoration: underline;
        color: #1976d2 !important;
    }

    .successful-submit {
        background-color: green;
        padding: .25rem;
        margin-bottom: .5rem;
    }

    .btn-size {
        font-size: 12pt;
    }

    .btn-weight {
        font-weight: bolder;
    }

    .spacer-grey {
        color: lightgray;
        margin-left: .45rem;
    }

    /*@media (min-width: 762px) {*/
    /*.btn-width {*/
    /*width: 27%;*/
    /*}*/
    /*}*/
</style>