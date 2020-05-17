<template>
    <v-container>

        <h2 class="text-center uppercase black--text" style="margin-bottom: 2rem;">Job Task Page</h2>

        <div class="col-12">
            <v-btn
                    class="w-full mb-1rem"
                    text
                    @click.prevent="goBack()"
            >
                Back
            </v-btn>
        </div>

        <v-card>
            <v-card-actions
                    class="flex"
            >
                <div class="flex justify-content-around w-full">
                    <div class="flex flex-col">
                        <v-icon
                                :color="show.details ? 'success': ''"
                                class="nav-btn-position"
                                @click="showSection('details')"
                        >mdi-details
                        </v-icon>
                        <div class="nav-icon-label" :class="show.details ? 'nav-icon-label-selected': ''">
                            Details
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <v-icon
                                :color="show.prices ? 'success': ''"
                                class="nav-btn-position"
                                @click="showSection('prices')"
                        >mdi-currency-usd
                        </v-icon>
                        <div class="nav-icon-label" :class="show.prices ? 'nav-icon-label-selected': ''">
                            Prices
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <v-icon
                                :color="show.images ? 'success': ''"
                                ref="jobTaskNavImage"
                                class="nav-btn-position"
                                @click="showSection('images')"
                        >mdi-image-edit<span
                                v-if="getImagesLength() > 0"
                        > ({{ getImagesLength() }})</span>

                        </v-icon>
                        <div class="nav-icon-label" :class="show.images ? 'nav-icon-label-selected': ''">
                            Photos
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <v-icon
                                :color="show.location ? 'success': ''"
                                class="nav-btn-position"
                                @click="showSection('location')"
                        >mdi-google-maps
                        </v-icon>
                        <div class="nav-icon-label" :class="show.location ? 'nav-icon-label-selected': ''">
                            Location
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <v-icon
                                :color="show.messages ? 'success': ''"
                                class="nav-btn-position"
                                @click="showSection('specialInstructions')"
                        >mdi-message
                        </v-icon>
                        <div class="nav-icon-label" :class="show.messages ? 'nav-icon-label-selected': ''">
                            Messages
                        </div>
                    </div>

                    <div class="flex flex-col"
                         v-if="showSubsPanel()"
                    >
                        <v-icon
                                :color="show.subs ? 'success': ''"
                                ref="subsNavButton"
                                class="nav-btn-position"
                                @click="showSection('subPanel')"
                        >mdi-face<span> ({{ getSubsLength() }})</span>
                        </v-icon>
                        <div class="nav-icon-label" :class="show.subs ? 'nav-icon-label-selected': ''">
                            Subs
                        </div>
                    </div>



                </div>
            </v-card-actions>
        </v-card>

        <section
                v-if="show.details"
                class="col-12"
        >
            <v-card>
                <v-card-title>Details</v-card-title>
                <v-card-text>
                    <div class="flex justify-content-between">
                        <div>Job Task Name:</div>
                        <div
                                class="capitalize"
                                id="taskName"
                        >{{ jobTask ? jobTask.task.name : ''}}
                        </div>
                    </div>
                    <div class="flex justify-content-between">
                        <div>Task Status</div>
                        <div class="float-right font-weight-bold capitalize"
                             :class="jobTask ? getLabelClass(jobTask.status) : 0">
                            {{ getLatestJobStatus(jobTask) }}
                        </div>
                    </div>
                    <div v-show="jobTask ? jobTask.declined_message !== '' : false">
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                Message Center:
                                <div class="float-right font-weight-bold">
                                    {{ getMessage(jobTask) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </v-card-text>
            </v-card>

        </section>

        <!-- prices -->
        <section class="col-12"
                 v-show="show.prices"
        >


            <v-dialog
                    ref="infoDialogPrices"
                    v-model="infoDialog.prices"
                    width="500"
            >
                <v-card>
                    <information-card
                            :title="informationDialogs.prices.title"
                            :sections="informationDialogs.prices.sections"
                    ></information-card>

                    <v-card-actions>
                        <v-btn
                                id="cancel"
                                ref="cancel"
                                label="cancel"
                                @click="infoDialog.prices = false"
                                color="error"
                                text>
                            Cancel
                        </v-btn>
                    </v-card-actions>
                </v-card>

            </v-dialog>


            <v-card>
                <div class="flex justify-content-between">
                    <v-card-title>Prices</v-card-title>
                    <v-icon
                            ref="priceInfo"
                            color="primary"
                            @click="infoDialog.prices = true"
                            class="mr-1rem">mdi-information
                    </v-icon>
                </div>

                <v-card-text>

                    <v-card-title
                            class="error--text"
                            style="font-size: 12pt;"
                            v-if="isContractor() && cust_final_price < sub_final_price"
                    >* The Bid You Accepted Is Higher Than Your Bid Price
                    </v-card-title>

                    <v-list dense>
                        <v-list-item>
                            <v-list-item-content>Total Task Price:</v-list-item-content>
                            <v-list-item-content class="align-end">
                                <div class="flex">
                                    <v-icon
                                            small
                                    >mdi-currency-usd
                                    </v-icon>
                                    <div class="flex-fill">{{ cust_final_price }}</div>
                                </div>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                                v-if="isContractor()"
                        >
                            <v-list-item-content>Total Sub Price:</v-list-item-content>
                            <v-list-item-content class="align-end">
                                <div class="flex">
                                    <v-icon
                                            small
                                    >mdi-currency-usd
                                    </v-icon>
                                    <div class="flex-fill">{{ sub_final_price }}</div>
                                </div>
                            </v-list-item-content>
                        </v-list-item>

                        <!--                        <v-list-item>-->
                        <!--                            <v-list-item-content>Sub Unit Price:</v-list-item-content>-->
                        <!--                            <v-list-item-content class="align-end">-->
                        <!--                                <div class="flex">-->
                        <!--                                    <v-icon-->
                        <!--                                            small-->
                        <!--                                    >mdi-currency-usd-->
                        <!--                                    </v-icon>-->
                        <!--                                    <div class="flex-fill">{{ sub_unit_price }}</div>-->
                        <!--                                </div>-->
                        <!--                            </v-list-item-content>-->
                        <!--                        </v-list-item>-->

                        <v-list-item style="height: 1rem">
                            <v-list-item-content>Quantity:</v-list-item-content>
                            <v-list-item-content class="align-end">
                                <v-text-field
                                        v-if="showTaskQuantityInput()"
                                        id="subUnitPrice"
                                        :value="jobTask ? jobTask.qty : ''"
                                        @blur="updateCustomerTaskQuantity(
                                            jobTask, $event.target.value)">
                                </v-text-field>
                                <div class="flex" v-else>
                                    <div class="flex-fill">{{ jobTask ? jobTask.qty : '' }}</div>
                                </div>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item
                                v-if="isContractor()"
                                style="height: 1rem">
                            <v-list-item-content>Unit Price:</v-list-item-content>
                            <v-list-item-content class="align-end">
                                <v-text-field
                                        v-if="!jobIsApproved()"
                                        id="totalTaskPrice"
                                        v-model="unit_price"
                                        prepend-icon="mdi-currency-usd"
                                        :value="unit_price ? taskCustFinalPrice(unit_price) : '0'"
                                        :class="(errors.unit_price || errors.priceMustBeANumber) ? 'box-error': ''"
                                        @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, job.id)"
                                >
                                </v-text-field>
                                <div v-else>{{ unit_price ? taskCustFinalPrice(unit_price) : 'Price Not Set' }}</div>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>

                </v-card-text>


                <v-card-text v-if="isContractor()">

                    <v-text-field
                            id="totalTaskPrice"
                            label="Total Task Price:"
                            v-model="cust_final_price"
                            prepend-icon="mdi-currency-usd"
                    >
                    </v-text-field>

                </v-card-text>

            </v-card>
        </section>

        <section class="col-12" v-if="getAddressLine1 !== '' && show.location">
            <v-card>
                <v-card-title>Job Task Location</v-card-title>
                <v-card-text v-if="getAddress() !== 'Address Not Available'">
                    <v-list dense>
                        <v-list-item>
                            <v-list-item-content>Address:</v-list-item-content>
                            <v-list-item-content class="align-end">
                                <div>{{ getAddressLine1 }}</div>
                                <div class="flex">
                                    <div>{{ getCity }},</div>
                                    <div
                                            style="margin-left: .2rem;"
                                    >{{ getLocationState }}
                                    </div>
                                    <div
                                            style="margin-left: .2rem;"
                                    >{{ getZip }}
                                    </div>
                                </div>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                    <hr>
                    <main class="map-responsive">
                        <iframe
                                width="450"
                                height="250"
                                frameborder="0" style="border:0"
                                :src="'https://www.google.com/maps/embed/v1/search?key=AIzaSyBAQZB-zS1HVbyNe2JEk1IgNVl0Pm2xsno&q=' +
                                                        getAddressLine1 + ' ' +
                                                        getCity + ' ' +
                                                        getLocationState + ' ' +
                                                        getZip
                                                        " allowfullscreen>
                        </iframe>
                    </main>
                </v-card-text>

                <v-card-text v-else>
                    <v-card-subtitle>Job Location Has Not Been Set</v-card-subtitle>
                </v-card-text>

                <v-card-actions>
                    <v-btn
                            v-if="jobIsNotComplete()"
                            text
                            color="primary"
                            class="w-full"
                            @click="openUpdateTaskLocation(jobTask.id)"
                    >
                        <v-icon
                                class="mr-1rem"
                        >mdi-home-edit
                        </v-icon>
                        Change Task Location
                    </v-btn>
                </v-card-actions>

            </v-card>
        </section>

        <section class="col-12" v-if="show.images">
            <v-card>
                <v-card-title>Images</v-card-title>
                <v-card-text>
                    <task-images :jobTask="jobTask" type="notsub">
                    </task-images>
                </v-card-text>
            </v-card>
        </section>

        <section
                v-if="show.specialInstructions"
        >
            <v-card>
                <v-card-title>Special Instructions</v-card-title>
                <v-card-text v-if="isContractor()">
                    <message label="Notes for Subcontractor" :jobId="jobTask ? jobTask.id : -1"
                             :server-message="jobTask && jobTask.sub_message ? jobTask.sub_message : ''"
                             actor='sub'
                             :disable-messages="disableMessages">
                    </message>

                    <message
                            class="mt-1rem"
                            label="Notes For Customer" :jobId="jobTask ? jobTask.id : -1"
                            :server-message="jobTask ? jobTask.customer_message : null"
                            actor='customer'
                            :disable-messages="disableMessages"></message>

                </v-card-text>
                <v-card-text v-if="!isContractor()">
                    <v-textarea
                            auto-grow
                            label="Notes From Contractor"
                            :value="getContractorNotesForCustomer"
                            :disabled="true"
                    ></v-textarea>
                </v-card-text>
            </v-card>
        </section>

        <section
                class="col-12"
                id="subs"
                ref="subPanelSection"
                v-if="showSubsPanel() && show.subPanel"
        >
            <v-card
                    :id="jobTask ? 'task-subs-' + jobTask.id : 0"
                    v-if="isGeneral() && jobHasSubs()"
            >

                <v-card-title>Bids</v-card-title>
                <v-card-subtitle
                        class="error--text"
                        v-if="atleastOneSubHasAHigherBidPrice(jobTask.bid_contractor_job_tasks)"
                >* Subs Bid Is Higher Than Your Bid Price
                </v-card-subtitle>
                <v-card-text>
                    <div class="flex justify-content-between">
                        <strong class="uppercase">Bid Price</strong>
                        <strong class="uppercase">Action</strong>
                    </div>
                    <v-divider></v-divider>

                    <div class="mb-2 flex flex-col"
                         v-for="bid in jobTask.bid_contractor_job_tasks"
                         :key="jobTask ? bid.id : 0">

                        <v-btn
                                color="primary"
                                text
                                @click="viewContractorInfo(bid.contractor_id)">{{
                            getCompanyName(bid) }}
                        </v-btn>
                        <div class="flex justify-content-between">
                            <div>
                                <div v-if="subPriceHigherThanBidPrice(bid)"
                                     class="error--text flex-1"
                                >$ {{ getBidPrice(bid) }} *
                                </div>
                                <div
                                        v-else
                                        class="flex-1"
                                >$ {{ getBidPrice(bid) }}
                                </div>
                            </div>
                            <div>
                                <!-- <button v-if="showAcceptBtn(jobTask.status)" -->


                                <v-btn
                                        v-if="!checkIfBidHasBeenAccepted(jobTask, bid)
                                                                    && checkIfBidHasBeenSent(bid)"
                                        @click="acceptSubBidForTask(bid, jobTask)"
                                        color="primary"
                                        text
                                        :loading="disabled.accept"
                                >
                                    Accept
                                </v-btn>


                                <div
                                        v-else-if="checkIfBidHasBeenAccepted(jobTask, bid)">
                                    <strong>Accepted</strong>
                                </div>
                                <div
                                        v-else-if="!checkIfAnyBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(bid)">
                                    <strong>Pending</strong></div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </v-card-text>
            </v-card>
        </section>

        <section>
            <v-card>
                <v-card-title>Actions</v-card-title>
                <v-card-subtitle
                        v-if="subHasNotFinishedTask(jobTask)"
                >Waiting For Sub
                </v-card-subtitle>
                <v-card-actions v-if="isGeneral()">
                    <v-btn
                            v-if="jobIsNotComplete()"
                            class="w-full mb-half-rem"
                            text
                            color="primary"
                            ref="addASubButton"
                            @click.prevent="openSubInvite(jobTask.id)"
                    >
                        Add A Sub
                    </v-btn>
                    <v-btn
                            class="w-full mb-half-rem"
                            text
                            color="primary"
                            v-if="jobTaskIsFinished(jobTask) && showFinishedBtn(jobTask)"
                            @click="finishedTask(jobTask)" :loading="disabled.finished"
                    >Click Me When Job Is Finished
                    </v-btn>
                    <v-btn
                            v-if="contractorCanApproveSubsTask(jobTask)"
                            class="w-full mb-half-rem"
                            text
                            color="primary"
                            @click="approveTaskHasBeenFinished(jobTask)"
                            :loading="disabled.approve"
                    >
                        Approve
                    </v-btn>
                    <v-btn
                            v-if="contractorWantsToChangeBid()"
                            class="w-full mb-half-rem"
                            text
                            color="warning"
                            @click="openDenyTaskForm(jobTask.id)"
                    >
                        Change Task
                    </v-btn>
                    <v-btn
                            v-if="contractorWantsToDeleteTheTask(jobTask)"
                            class="w-full mb-half-rem"
                            text
                            color="red"
                            ref="generalCanDeleteTask"
                            @click="deleteTask(jobTask)"
                            :loading="disabled.deleteTask"
                    >
                        Delete
                    </v-btn>
                </v-card-actions>
                <v-card-actions>
                    <v-btn
                            v-if="customerWantsToDeleteTheTask(jobTask)"
                            class="w-full mb-half-rem error--text"
                            text
                            ref="deleteTask"
                            @click="deleteTheTask = true"
                    >
                        Delete The Task
                    </v-btn>
                </v-card-actions>
            </v-card>
        </section>

        <v-dialog
                v-model="deleteTheTask"
                width="500px"
        >
            <v-card>
                <v-card-title style="word-break: break-word">Do You Really Want To Delete This Task?</v-card-title>
                <v-card-actions>
                    <v-btn
                            id="cancelTheTask"
                            ref="cancelTheTask"
                            @click="deleteTheTask = false"
                            class="error--text"
                            text>Cancel
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            :loading="disabled.deleteTask"
                            id="deleteTheTask"
                            ref="deleteTheTask"
                            @click="deleteTask(jobTask)"
                            color="primary"
                            text>Delete
                    </v-btn>
                </v-card-actions>
            </v-card>

        </v-dialog>

        <v-dialog
                v-model="dialog"
                width="500"
        >
            <v-card>
                <v-card-title
                        class="headline grey lighten-2"
                        primary-title
                >
                    Describe Change Here?
                </v-card-title>

                <v-card-text style="margin-top: 1.25rem">
                    <v-textarea
                            outlined
                            name="contractorMessage"
                            label="Message To Contractor"
                            v-model="changeMessage"
                    ></v-textarea>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="primary"
                            text
                            @click="sendMessageToContractor()"
                    >
                        Submit
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <sub-invite-modal v-if="isContractor()" :job-task="jobTask"
                          :job-task-task="jobTask ? jobTask.task : null"
                          :job-task-name="jobTask ? jobTask.task.name : null"
                          :bid-payment-type="jobTask && jobTask.job ? jobTask.job.payment_type : null"
                          :id="jobTask ? jobTask.id : null">
        </sub-invite-modal>

        <deny-task-modal v-if="isContractor()"
                         :job-task="jobTask"
                         :id="jobTask ? jobTask.id : null">
        </deny-task-modal>

        <update-task-location-modal
                v-show="isContractor()"
                :job-task="jobTask"
                :id="jobTask ? jobTask.id : null">
        </update-task-location-modal>

    </v-container>
</template>

<script>

    import SubInviteModal from '../components/task/SubInviteModal'
    import DenyTaskModal from '../components/task/DenyTaskModal'
    import UpdateTaskLocationModal from '../components/task/UpdateTaskLocationModal'
    import Message from '../components/job/Message.vue'
    import TaskImages from '../components/task/UploadTaskImages'
    import Format from '../classes/Format'
    import Card from '../components/shared/Card'
    import ContentSection from '../components/shared/ContentSection'
    import User from '../classes/User'
    import Feedback from '../components/shared/Feedback'
    import Status from '../components/mixins/Status'
    import Currency from '../components/mixins/Currency'
    import Utilities from '../components/mixins/Utilities'
    import InformationCard from '../components/shared/InformationCard'

    import {mapState} from 'vuex'

    export default {
        components: {
            SubInviteModal,
            DenyTaskModal,
            Feedback,
            Card,
            Message,
            ContentSection,
            TaskImages,
            InformationCard,
            UpdateTaskLocationModal
        },
        mixins: [
            Status, Utilities, Currency
        ],
        data() {
            return {
                deleteTheTask: false,
                validate: false,
                informationDialogs: {
                    prices: {
                        title: 'How Job Task Prices Work',
                        sections: [
                            {
                                title: 'Total Task Price',
                                description: `The total task price is the final price for the task. This price will be the price that you
                charge as a General Contractor or it is the price of the accepted price of the sub. You should
                charge more than the price of the sub and an error will be thrown if it is not.`
                            }
                        ]
                    }
                },
                showDialog: false,
                infoDialog: {
                    prices: false
                },
                show: {
                    details: false,
                    taskStatus: false,
                    prices: true,
                    images: false,
                    location: false,
                    specialInstructions: false,
                    subPanel: false,
                },
                messages: false,
                changeMessage: '',
                dialog: false,
                authUser: {},
                user: {},
                jobTask: null,
                currentJobTask: {},
                showDetails: false,
                start_date: '',
                start_when_accepted: true,
                startDateErrorMessage: '',
                hasStartDateError: false,
                startDateChanged: false,
                startDateError: false,
                sub_unit_price: null,
                message: '',
                sendSubMessage: true,
                sendCustomerMessage: true,
                customerMessage: '',
                cust_final_price: -1,
                unit_price: -1,
                sub_final_price: -1,
                errors: {
                    unit_price: false,
                    priceMustBeANumber: false
                },
                disabled: {
                    showDenyForm: false,
                    pay: false,
                    finished: false,
                    approve: false,
                    reopen: false,
                    deny: false,
                    deleteTask: false,
                    payCash: false,
                    accept: false,
                }
            }
        },
        computed: {
            ...mapState({
                job: state => state.job.model,
                jobStatus: state => state.job.model.status
            }),
            jobLocationHasBeenSet() {
                if (this.jobTask && this.jobTask.location) {
                    return true
                } else {
                    return false
                }
            },
            getCustomerNotes() {
                if (this.jobTask) {
                    if (this.jobTask.job.customer) {
                        return this.jobTask.job.customer.customer.notes
                    }
                    return this.jobTask.customer.customer.notes
                }
            },
            getContractorNotesForCustomer() {
                if (this.jobTask) {
                    return this.jobTask.customer_message
                }
            },
            getAddressLine1() {
                if (this.jobTask && this.jobTask.location) {
                    return this.jobTask.location.address_line_1
                }
                return ''
            },
            getCity() {
                if (this.jobTask && this.jobTask.location) {
                    return this.jobTask.location.city
                }
                return ''
            },
            getLocationState() {
                if (this.jobTask && this.jobTask.location) {
                    return this.jobTask.location.state
                }
                return ''
            },
            getZip() {
                if (this.jobTask && this.jobTask.location) {
                    return this.jobTask.location.zip
                }
                return ''
            },
            taskApproved() {
                return this.jobTask.status === 'bid_task.approved_by_customer'
            },
            isCustomer() {
                return this.authUser.isCustomer
            },
            // isContractor() {
            //   return this.user.isContractor()
            // },

            showSendSubInvite() {
                if (this.jobStatus === 'bid.initiated' || this.jobStatus === 'bid.in_progress') {
                    return true
                }
                return false
            },
            generalTotalTaskPrice() {
                let total = 0
                for (const jobTask of this.job.job_tasks) {
                    if (jobTask !== null) {
                        total += cust_final_price
                    }
                }
                return total
            },
            subTotalTaskPrices() {
                let total = 0
                for (const jobTask of this.job.job_tasks) {
                    total += this.subTaskPrice(jobTask)
                }
                return total
            },
            disableMessages() {
                return this.jobStatus === 'job.completed'
            }
        },
        methods: {

            atleastOneSubHasAHigherBidPrice(bids) {
                if (bids) {
                    for (let i = 0; i < bids.length; i++) {
                        if (this.subPriceHigherThanBidPrice(bids[i])) {
                            return true
                        }
                    }
                    return false
                }
            },

            validateGeneralPriceIsHigherThanSubsPrice() {

                if (this.subsBidHasBeenAccepted()) {
                    return this.generalsPriceIsHigherThanSubsPrice() || 'The Accepted Subs Bid Is Higher Than Your Bid Price';
                }
            },

            generalsPriceIsHigherThanSubsPrice() {
                return this.getSubsPrice() < this.getJobTaskPrice()
            },

            getJobTaskPrice() {
                return this.jobTask.cust_final_price;
            },

            getSubsPrice() {
                for (let i = 0; i < this.jobTask.bid_contractor_job_tasks.length; i++) {
                    if (this.jobTask.bid_contractor_job_tasks[i].contractor_id === this.jobTask.contractor_id) {
                        return this.jobTask.bid_contractor_job_tasks[i].bid_price;
                    }
                }
                return null;
            },

            subsBidHasBeenAccepted() {
                if (this.jobTask) {
                    return this.jobTask.contractor_id !== this.jobTask.job.contractor_id;
                }
            },

            getImagesLength() {
                if (this.jobTask && this.jobTask.images) {
                    return this.jobTask.images.length
                }
            },

            getSubsLength() {
                if (this.jobTask && this.jobTask.bid_contractor_job_tasks) {
                    return this.jobTask.bid_contractor_job_tasks.length
                }
            },

            showSubsPanel() {
                return this.isContractor()
                    && this.jobHasNotBeenCompleted()
                    && this.jobHasSubs()
                    && this.isGeneral()
            },

            showSection(section) {
                this.hideAllSections();
                if (section === 'details') {
                    this.show.details = true;
                } else if (section === 'taskStatus') {
                    this.show.taskStatus = true;
                } else if (section === 'prices') {
                    this.show.prices = true;
                } else if (section === 'images') {
                    this.show.images = true;
                } else if (section === 'location') {
                    this.show.location = true;
                } else if (section === 'specialInstructions') {
                    this.show.specialInstructions = true;
                } else if (section === 'subPanel') {
                    this.show.subPanel = true;
                }
            },

            hideAllSections() {
                this.show.details = false;
                this.show.taskStatus = false;
                this.show.prices = false;
                this.show.subPanel = false;
                this.show.images = false;
                this.show.location = false;
                this.show.specialInstructions = false;
            },

            subHasNotFinishedTask(item) {
                if (this.isASub(item.contractor_id, item.job.contractor_id)) {
                    return this.getLatestJobTaskStatus1(item) === 'approved by customer'
                        || this.getLatestJobTaskStatus1(item) === 'declined subs work'
                }
            },

            isASub(subId, generalId) {
                return subId !== generalId;
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

            getStartDate(date) {
                return this.dateOnly(date)
            },

            subPriceHigherThanBidPrice(bid) {
                return this.jobTask.cust_final_price < bid.bid_price
            },

            jobHasSubs() {
                if (
                    this.jobTask
                    && this.jobTask.bid_contractor_job_tasks
                ) {
                    return this.jobTask.bid_contractor_job_tasks.length > 0
                }
            },

            jobHasNotBeenCompleted() {
                return this.jobStatus !== 'job.completed'
            },

            getMessage(jobTask) {

                let status = this.getLatestJobTaskStatus(jobTask)

                if (status === 'paid') {
                    return jobTask.job.paid_with_cash_message
                } else if (jobTask && jobTask.task_messages && jobTask.task_messages.length > 0) {
                    return jobTask.task_messages[jobTask.task_messages.length - 1].message
                }

            },

            checkIfThereAreMessaages(jobTask) {
                if (jobTask && jobTask.messages) {
                    return jobTask.messages.length > 0
                }
            },

            showTaskMessages(show) {
                this.messages = show
            },

            createHyphenDate(date) {
                if (date) {
                    const dateArray = date.split('/')
                    return dateArray[2] + '-' + dateArray[0] + '-' + dateArray[1]
                }
            },

            setStartDate(jt) {
                if (jt) {
                    return this.dateOnlyHyphen(jt.start_date)
                    // return this.createHyphenDate(this.formatDate(this.prettyDate(jt.start_date)))
                } else {
                    return 0
                }
            },

            jobIsApproved() {
                return this.getLatestJobStatusNumber() === 7
            },

            jobTaskIsFinished(jobTask) {
                const status = this.getLatestJobTaskStatus(jobTask)
                return status === 'general finished work' || status === 'sub finished work'
            },

            subHasFinishedWork(jobTask) {
                if (jobTask && jobTask.job_task_statuses) {
                    const status = this.getJobTaskStatus_latest(jobTask)
                    return status === 'sub_finished_work'
                }
            },

            contractorCanApproveSubsTask(jobTask) {
                this.subHasFinishedWork(jobTask)
            },

            customerWantsToDeleteTheTask(jobTask) {
                return this.taskHasNotBeenApproved(jobTask)
            },

            contractorWantsToDeleteTheTask(jobTask) {
                return this.taskHasNotBeenApproved(jobTask)
            },

            taskHasNotBeenApproved(jobTask) {
                return this.getLatestJobTaskStatusNumber(jobTask) < 6
            },

            getLatestJobTaskStatusNumber(jobTask) {
                if (jobTask && (jobTask.job_task_statuses || jobTask.job_task_status)) {
                    return this.getJobTaskStatusNumber_latest(jobTask)
                }
            },

            getLatestJobStatusNumber() {
                if (this.job && (this.job.job_statuses || this.job.job_status)) {
                    return this.getJobStatusNumber_latest(this.job)
                }
            },

            getLatestJobTaskStatus(jobTask) {
                if (jobTask && (jobTask.job_task_statuses || jobTask.job_task_status)) {
                    return this.getJobTaskStatus_latest(jobTask)
                }
            },

            getLatestJobStatus(jobTask) {
                if (jobTask && jobTask.job && jobTask.job.job_statuses) {
                    return jobTask.job.job_statuses[jobTask.job.job_statuses.length - 1].status
                }
            },

            async sendMessageToContractor() {
                if (Spark.state.user.usertype === 'customer') {
                    try {
                        await axios.post('/jobTask/message', {
                            general_id: this.jobTask.job.contractor_id,
                            sub_id: this.jobTask.contractor_id,
                            customer_id: Spark.state.user.id,
                            job_task_id: this.jobTask.id,
                            message: this.changeMessage
                        });
                        Bus.$emit('bidUpdated')
                        this.dialog = false
                    } catch (error) {
                        console.log(error)
                    }
                } else if (Spark.state.user.usertype === 'contractor') {
                    try {
                        await axios.post('/jobTask/message', {
                            general_id: this.jobTask.general.id,
                            sub_id: this.jobTask.contractor.id,
                            customer_id: this.jobTask.customer.id,
                            job_task_id: this.jobTask.id,
                            message: this.changeMessage
                        })
                        Bus.$emit('bidUpdated')
                        this.dialog = false
                    } catch (error) {
                        console.log(error)
                    }
                }
            },

            jobHasNotBeenApproved() {
                const latestStatus = this.getJobStatus_latest(this.job)
                return latestStatus === 'in_progress' || latestStatus === 'initiated'
            },
            jobIsNotComplete() {
                const latestStatus = this.getLatestJobTaskStatus()
                return latestStatus !== 'general finished work'
                    && latestStatus !== 'sub finished work'
                    && latestStatus !== 'paid'
            },
            getLatestJobTaskStatus() {
                if (this.jobTask && this.jobTask.job_task_statuses) {
                    return this.formatStatus(this.getJobTaskStatus_latest(this.jobTask))
                }
            },
            viewContractorInfo(id) {
                this.$router.push({name: 'contractor-info', params: {contractorId: id}})
            },
            getBidPrice(bid) {
                if (bid) {
                    return this.convertNumToString(bid.bid_price)
                }
            },
            goBack() {
                this.$router.go(-1)
            },
            getCompanyName(bid) {
                if (bid.contractor.contractor) {
                    return bid.contractor.contractor.company_name
                } else {
                    return bid.contractor.first_name + ' ' + bid.contractor.last_name
                }
            },
            checkIfAnyBidHasBeenAccepted(jobTask) {
                if (this.jobHasSubs()) {
                    for (let i = 0; i < jobTask.bid_contractor_job_tasks.length; i++) {
                        if (jobTask.bid_contractor_job_tasks[i].accepted === 1) {
                            return true
                        }
                    }
                } else {
                    return false
                }
            },
            checkIfBidHasBeenSent(bid) {
                return bid.updated_at !== null && bid.status === 'bid_task.bid_sent'
            },
            showSubMessage(msg) {
                return (msg !== null &&
                    msg !== '' &&
                    msg !== ' ' &&
                    this.isContractor()) ||
                    (msg != null &&
                        msg !== '' &&
                        msg !== ' ' &&
                        this.isGeneral())
            },
            showCustomerMessage(msg) {
                return (msg != null &&
                    msg !== '' &&
                    msg !== ' ' &&
                    this.isCustomer) ||
                    (msg != null &&
                        msg !== '' &&
                        msg !== ' ' &&
                        this.isGeneral())
            },
            showPanelActions(status) {
                if (status !== 'bid_task.customer_sent_payment') {
                    return true
                }
                return false
            },
            showAcceptBtn(status) {
                return status === 'bid_task.bid_sent'
            },
            openTaskBids(id) {
                if ($('#task-options-' + id).hasClass('hidden') || this.isCustomer) {
                    $('#task-divider-' + id).toggleClass('hidden')
                }
                $('#task-subs-' + id).toggleClass('hidden')
            },
            openTaskActions(id) {
                if ($('#task-subs-' + id).hasClass('hidden') || this.isCustomer) {
                    $('#task-divider-' + id).toggleClass('hidden')
                }
                $('#task-options-' + id).toggleClass('hidden')
            },
            preview(jobTask, subId) {
                Bus.$emit('previewSubForTask', [
                    jobTask.job_id,
                    jobTask.id,
                    subId
                ])
            },
            acceptSubBidForTask(bid, jobTask) {

                let generalId = null
                if (jobTask.task.contractor) {
                    generalId = jobTask.task.contractor.id
                } else {
                    generalId = jobTask.task.contractor_id
                }
                GeneralContractor.acceptSubBidForTask(jobTask, bid, this.disabled, generalId)

            },
            showStripeToggle(jobTask) {
                return this.authUser.isAssignedToMe(jobTask, this.user.id) && (this.jobStatus === 'bid.initiated' || this.jobStatus ===
                    'bid.in_progress')
            },
            updateMessage(jobTaskId, currentMessage, actor) {
                let message = document.getElementById('message-' + actor + '-' + jobTaskId)
                message = message.value
                if (message !== currentMessage) {
                    GeneralContractor.updateMessage(message, jobTaskId, actor)
                }
                if (actor === 'sub') {
                    this.sendSubMessage = false
                    setTimeout(function () {
                        this.sendSubMessage = true
                    }.bind(this), 2000)
                } else {
                    this.sendCustomerMessage = false
                    setTimeout(function () {
                        this.sendCustomerMessage = true
                    }.bind(this), 2000)
                }
            },
            userIsAGeneralContractor() {
                return Spark.state.user.usertype === 'contractor'
                    && Spark.state.user.id === this.jobTask.job.contractor_id
            },
            userIsACustomer() {
                return Spark.state.user.usertype === 'customer'
            },
            subHasFinishedTheJob() {
                return this.jobTask.status === 'bid_task.finished_by_sub'
            },
            jobHasNotBeenSubmittedToTheCustomer() {
                return this.jobTask.status !== 'bid_task.approved_by_general'
            },
            jobHasBeenApprovedByTheGeneral() {
                return this.jobTask.status === 'bid_task.approved_by_general'
            },
            generalHasFinishedTheJob() {
                return this.jobTask.status === 'bid_task.approved_by_general'
            },
            customerWantsToChangeTheBid() {
                return !this.jobIsApproved()
            },
            contractorWantsToChangeBid(jobTask) {
                if (jobTask) {
                    return this.jobHasSubs() && this.subHasFinishedWork(jobTask)
                }
            },
            showDenyBtn(jobTask) {
                if (jobTask) {
                    if (this.userIsAGeneralContractor()) {
                        if (this.jobHasSubs() && this.subHasFinishedTheJob() && this.jobHasNotBeenSubmittedToTheCustomer()) {
                            return true
                        } else {
                            return false
                        }
                    } else if (this.userIsACustomer()) {
                        if (this.jobHasBeenApprovedByTheGeneral) {
                            return true
                        } else {
                            return false
                        }
                    } else {
                        return false
                    }
                }
            },
            showFinishedBtn(jobTask) {
                if (this.isContractor() &&
                    this.authUser.isAssignedToMe(jobTask, this.user.id) &&
                    (jobTask.status === 'bid_task.approved_by_customer'
                        || jobTask.status === 'bid_task.reopened'
                        || jobTask.status === 'bid_task.finished_by_sub'
                        || jobTask.status === 'bid_task.denied'
                    )) {
                    return true
                }
                return false
            },
            showApproveBtn(jobTask) {
                if (this.isGeneral() &&
                    !this.authUser.isAssignedToMe(jobTask, this.user.id) &&
                    (jobTask.status === 'bid_task.finished_by_sub' || jobTask.status === 'bid_task.reopened')
                ) {
                    return true
                }
                return false
            },
            showDeleteBtn(jobTask) {
                if (jobTask) {
                    const status = jobTask.status
                    if (this.isGeneral() && (
                        status === 'bid_task.initiated'
                        || status === 'bid_task.bid_sent'
                        || this.jobStatus === 'bid.declined')) {
                        return true
                    }
                    return false
                }
            },
            async deleteTask(jobTask) {
                await GeneralContractor.deleteTask(jobTask, this.disabled);
                this.deleteTheTask = false;
                this.$router.push('/bid/' + this.job.id);
            },

            /**
             * customer task price
             */
            subTaskPrice(jobTask) {
                if (jobTask === null) {
                    return 0
                }
                if (jobTask.bid_id === null) {
                    return 0
                } else {
                    return this.authUser.findTaskBid(jobTask.bid_id, jobTask.bid_contractor_job_tasks)[0].bid_price
                }
            },
            toggleStripePaymentOption(jobTask) {
                jobTask.checked = $('#toggle-stripe-' + jobTask.id).is(':checked')
                SubContractor.toggleStripePaymentOption(jobTask)
            },
            // payForTask(jobTask) {
            //   Customer.payForTask(jobTask, this.disabled);
            // },
            // paidWithCashTask(jobTask) {
            //   Customer.paidWithCashTask(jobTask, this.disabled);
            // },
            openTaskPanel(index) {
                this.$emit('openTaskPanel', index)
            },
            finishedTask(jobTask) {
                SubContractor.finishedTask(jobTask, this.disabled)
            },
            approveTaskHasBeenFinished(jobTask) {
                GeneralContractor.approveTaskHasBeenFinished(jobTask, this.disabled)
            },
            checkIfBidHasBeenAccepted(jobTask, bid) {
                // debugger;
                if (bid) {
                    return bid.accepted === 1
                }
                // try {
                //   if (bid.accepted === 1) {
                //     console.log(JSON.stringify(bid));
                //     return true
                //   } else {
                //     console.log(JSON.stringify(bid));
                //     return false
                //   }
                // } catch (e) {
                //   debugger;
                //   console.log(e)
                // }

                // if (jobTask.bid_contractor_job_tasks.length > 0) {
                //   for (let i = 0; i < jobTask.bid_contractor_job_tasks.length; i++) {
                //     if (jobTask.bid_contractor_job_tasks[i].accepted === 1) {
                //       return true
                //     }
                //   }
                // } else {
                //   return false
                // }
            },
            isGeneral() {
                if (this.jobTask && this.jobTask.task) {
                    return this.jobTask.task.contractor_id === this.user.id
                }
            },
            prettyDate(date) {
                if (date == null)
                    return ''
                // return the date and ignore the time
                date = date.split(' ')
                return date[0]
            },
            showTaskStartDate() {
                return true
            },
            updateTaskStartDate(date, jobTaskId) {
                // if (date !== '') {
                //     let dateArray = GeneralContractor.checkDateIsTodayorLater(date, this.job.created_at)
                //     this.startDateErrorMessage = dateArray[0]
                //     this.hasStartDateError = dateArray[1]

                // if (!this.hasStartDateError) {
                GeneralContractor.updateTaskStartDate(date, jobTaskId)
                // } else {
                //     this.startDateErrorMessage = 'Task Date Cannot Be Before Bid Creation Date'
                // }

                // }
            },
            openUpdateTaskLocation(jobTaskId) {
                $('#update-task-location-modal_' + jobTaskId).modal()
            },
            changeTaskDialog() {
                this.dialog = true
            },
            openDenyTaskForm(jobTaskId) {
                $('#deny-task-modal_' + jobTaskId).modal('show')
            },
            openSubInvite(jobTaskId) {
                // debugger;
                // this.currentJobTask = jobTask;
                $('#sub-invite-modal_' + jobTaskId).modal('show')
            },
            location(jobTask, bid) {
                if (this.job && jobTask && jobTask.location) {
                    // debugger;
                    const task_location = jobTask.location_id
                    const job_location = this.job.location_id
                    if (task_location === null && job_location === null) {
                        return 'No Address Set Yet'
                    } else if (job_location === job_location) {
                        return 'Same as Job Location'
                    } else if (task_location !== null) {
                        return jobTask.location.address_line_1
                    } else if (job_location) {
                        return bid.location.address_line_1
                    }
                }
            },
            getAddress() {
                if (this.jobTask && this.jobTask.location) {
                    if (this.jobTask.location !== null) {
                        return this.jobTask.location.address_line_1 + ' ' +
                            this.jobTask.location.address_line_2 + ' ' +
                            this.jobTask.location.city + ' ' +
                            this.jobTask.location.state + ' ' +
                            this.jobTask.location.zip
                    } else {
                        return 'Address Not Available'
                    }
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
            },
            updateCustomerTaskPrice(price, jobTaskId, bidId) {

                if (isNaN(price)) {
                    price = this.removeDollarSigns(price)
                }

                if (this.unit_price !== parseFloat(price)) {

                    this.errors.priceMustBeANumber = price !== '' && isNaN(price)
                    this.errors.unit_price = this.sub_final_price > price

                    this.unit_price = price
                    this.cust_final_price = price * this.jobTask.qty

                    if ((this.sub_final_price <= price && !this.errors.priceMustBeANumber)) {
                        GeneralContractor.updateCustomerPrice(price, jobTaskId, bidId)
                    }
                } else {
                    this.errors.priceMustBeANumber = price !== '' && isNaN(price)
                    this.errors.unit_price = this.sub_final_price > price
                }
            },
            isContractor() {
                return this.user.usertype === 'contractor'
            },
            showTheJobTaskDetails(value) {
                if (value === 'show') {
                    this.showDetails = true
                } else {
                    this.showDetails = false
                }
            },
            status(status) {
                return this.authUser.status(status, this.job)
            },
            getLabelClass(status) {
                return Format.statusLabel(status)
            },
            showTaskPriceInput() {
                return this.isGeneral()
                // return this.isGeneral() &&
                //   (this.jobStatus === 'bid.in_progress' ||
                //     this.jobStatus === 'bid.initiated' ||
                //     this.jobStatus === 'bid.declined')
            },
            showTaskQuantityInput() {
                return this.isGeneral() && this.jobHasNotBeenApproved();
            },
            updateCustomerTaskQuantity(jobTask, newQuantity) {

                newQuantity = Number(newQuantity)

                if (jobTask.qty != newQuantity) {
                    GeneralContractor.updateCustomerTaskQuantity(newQuantity, jobTask.id)

                    this.jobTask.qty = newQuantity

                    let totalPrice = this.unit_price * newQuantity

                    this.cust_final_price = totalPrice.toFixed(2)
                }

            },
            removeDollarSigns(price) {
                return price.replace(/[$]+/g, '')
            },
            taskCustFinalPrice(price, sub, total = false) {

                if (this.jobTask) {
                    if (typeof price === 'string') {
                        price = this.removeDollarSigns(price)
                    }

                    price = parseFloat(price)

                    let priceCheck = this.unit_price * this.jobTask.qty

                    if (!this.isCustomer) {
                        if (!sub && (this.cust_final_price !== priceCheck)) {
                            this.cust_final_price = this.unit_price * this.jobTask.qty

                            if (this.job[0]) {
                                GeneralContractor.updateCustomerPrice(price, this.jobTask.id, this.job[0].id)
                            } else if (this.job) {
                                GeneralContractor.updateCustomerPrice(price, this.jobTask.id, this.job.id)
                            }

                        } else if (sub && total) {
                            price = price * this.jobTask.qty
                        }
                    }

                    if (price === 0) {
                        return '$0.00'
                    }

                    const minimumPrice = price * .029 + .30 + 2.5;

                    if (this.jobTask) {

                    }

                    if (price) {
                        let priceString = price.toString()
                        if (priceString.indexOf('.') === -1) {
                            price = '$' + price + '.00'
                        } else {
                            price = '$' + price
                        }
                        return price
                    }
                }

            },
            async getTask() {
                try {
                    const data = await axios.get('/getJobTaskForGeneral/' + this.jobTask.id + '/' + this.user.id)
                    this.jobTask = data.data[0]

                    if (this.isContractor()) {
                        this.sub_final_price = this.jobTask.sub_final_price
                    }

                } catch (error) {
                    console.log(error.message)
                }
            },
            checkThatThereIsAContractorPrice() {

                if (
                    parseInt(this.cust_final_price) <= 0 &&
                    this.jobTask.qty > 0 &&
                    this.unit_price > 0
                ) {
                    this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.job.id)
                    return false
                }

                return true
            },
            jobStateIsAnArray() {
                return this.$store.state.job.model[0]
            },
            jobStateIsAnObject() {
                return this.$store.state.job.model
            }
        },
        mounted() {

            Bus.$on('bidUpdated', () => {
                this.getTask()
            })

            if (!this.$store.state.job.model) {
                this.$router.push('/bids');
            } else {
                if (this.$store.state.job.model.job_tasks) {
                    this.jobTask = this.$store.state.job.model.job_tasks[this.$route.params.index]
                } else if (this.$store.state.job.model[0].job_tasks) {
                    this.jobTask = this.$store.state.job.model[0].job_tasks[this.$route.params.index]
                }

                if (this.jobTask) {
                    this.cust_final_price = this.jobTask.cust_final_price
                    this.sub_final_price = this.jobTask.sub_final_price
                    this.unit_price = this.jobTask.unit_price

                    if (
                        parseInt(this.cust_final_price) <= 0 &&
                        this.jobTask.qty > 0 &&
                        this.unit_price > 0
                    ) {

                        if (this.jobStateIsAnArray()) {
                            if (this.$store.state.job.model[0].id > 0) {
                                this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.$store.state.job.model[0].id)
                            } else if (this.$store.state.job.id !== '') {
                                this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.$store.state.job.id)
                            }
                        } else if (this.jobStateIsAnObject) {
                            if (this.$store.state.job.model.id > 0) {
                                this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.$store.state.job.model.id)
                            } else if (this.$store.state.job.id !== '') {
                                this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.$store.state.job.id)
                            }
                        }


                    }
                }
                this.user = Spark.state.user
            }

        },
        created() {
            document.body.scrollTop = 0 // For Safari
            document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
            this.authUser = new User()
        }
    }
</script>

<style scoped>

    .bid-price-error {
        color: red;
    }

    .location {
        align-items: center;
        font-weight: bold;
        font-size: 16pt;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
    }

    .flex-col {
        flex-direction: column;
    }

    .flex {
        display: flex;
        justify-content: space-between;
    }

    .bid-task-wrapper {
        display: grid;
        grid-template-columns: 55% 1fr;
        /*margin-top: 1rem;*/
        /*padding: .75rem;*/
        padding: .15rem .05rem .15rem .15rem;
        /*grid-row-gap: 1rem;*/
        grid-row-gap: .5rem;
    }

    .box {
        border: white thin solid;
        border-radius: 5px;
        margin: .1rem;
        padding: .75rem;
    }

    .btn-bid-task {
        background-color: #1c3d5a;
    }

    .bid-task-border {
        border-bottom: grey thin solid;
    }

    .messageHeader {
        font-size: 12pt;
        font-weight: bold;
        font-family: Roboto, serif;
        text-align: center;
    }

    .lookLikeALink {
        text-decoration: underline;
        color: #1976d2 !important;
    }

</style>
