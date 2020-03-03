<template>
    <!-- / all tasks ready for payment -->
    <div>
        <div v-if="show"><!---->
            <div class="flex flex-col">
                <div
                        style="background-color: green"
                        class="banner"
                        v-if="paymentSucceeded"
                >
                    <h3 class="text-center">Payment Succeeded!</h3>
                </div>
                <div class="flex space-between header-completed">
                    <div class="flex-1">Task</div>
                    <div class="flex-1">Qty</div>
                    <div class="flex-1">Price</div>
                    <div class="flex-1">Total</div>
                    <div class="flex-1" v-if="isContractor">Task Price (Sub Contractor)</div>
                    <div class="flex-1" v-else>Exclude</div>
                    <div class="flex-1"></div>
                </div>
                <div class="flex pl-2 mb-2" v-for="jobTask in getPayableTasks()" :key="jobTask.id">
                    <div class="flex-1 capitalize">{{ jobTask && jobTask.task ? jobTask.task.name : null}}</div>
                    <div class="pl-1 mr-1rem">{{ jobTask.qty }}</div>
                    <div class="flex-1 pl-1">$ {{ jobTask.unit_price }}</div>
                    <div class="flex-1 pl-1" v-if="isContractor">$ {{ jobTask.cust_final_price - jobTask.sub_final_price
                        }}
                    </div>
                    <div class="flex-1 pl-1" v-else>
                        <div v-if="bid ? bid.payment_type === 'cash' : false">
                            $ {{ jobTask.cust_final_price }}
                        </div>
                        <div v-else>
                            $ {{ jobTask.cust_final_price }}
                        </div>
                    </div>
                    <div class="flex-1 pl-1" v-if="isContractor">${{ jobTask.sub_final_price }}</div>
                    <div class="pl-1" v-else>
                        <input v-if="showDenyBtn(jobTask)" type="checkbox" name="exclude"
                               :id="'exclude-' + jobTask.id"
                               :ref="'exclude-' + jobTask.id"
                               @click="addJobTaskToExcludedList(jobTask)">
                    </div>

                    <div class=" pl-1" v-if="showReopenBtn(jobTask)">
                        <v-btn
                                class="w-40"
                                color="primary"
                                @click.prevent="reopenTask(jobTask)" :loading="disabled.reopen">
                            Reopen
                        </v-btn>
                    </div>
                    <div class="flex-1 pl-1" v-else>
                        <v-btn
                                class="w-40"
                                color="red"
                                text
                                v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                            Deny
                        </v-btn>

                    </div>
                </div>
                <div class="flex pl-2 mb-2" v-if="isContractor">
                    <div></div>
                    <div></div>
                    <div>Total: ${{ totalCustomerPrice }}</div>
                    <div>Total: ${{ totalSubPrice }}</div>
                    <div></div>
                </div>
                <div class="flex justify-between pl-2 mb-2 mt-4">
                    <div class="flex-1"></div>
                    <div class="flex-1">
                        <!--                    <label v-if="isCustomer">Total: ${{ (totalCustomerPrice + totalSubPrice) - -->
                        <!--                        subtractFromTotal}}</label>-->
                        <label class="w-full" v-if="isCustomer">

                            <div class="w-full" style="text-align: right"
                                 v-if="bid ? bid.payment_type === 'cash' : false">
                                Total: $ {{ totalTaskCashFee() }}
                            </div>
                            <div id="cctotal" class="w-full" style="text-align: right" v-else>
                                Total: $ {{ totalPriceForAllCompletedTasks() }}
                            </div>
                        </label>
                    </div>
                    <div class="flex-1" v-if="isContractor">
                        <label class="w-full" style="text-align: right">Total: ${{ totalCustomerPrice + totalSubPrice
                            }}</label>
                    </div>
                </div>
            </div>

            <div v-if="isCustomer" class="text-right">
                <v-btn
                        v-if="cashJobOrNotSetupWithStripe()"
                        class="w-full"
                        color="primary"
                        @click.prevent="paidCash = true" :loading="disabled.payCash">
                    Pay With Cash
                </v-btn>
                <v-btn
                        class="w-full"
                        color="primary"
                        v-if="creditCardJobAndContractorHasStripe()" @click.prevent="payAllPayableTasks()"
                        :loading="payAll"
                >
                    Pay With Credit Card
                </v-btn>
            </div>

            <transition name="slide-fade">
                <div v-show="paidCash">
                    <div class="form-group col-md-12">

                        <v-combobox
                                label="Instructions For Contractor"
                                :items="instructions"
                                v-model="cashMessage"
                        ></v-combobox>

                        <!--                    <label for="">Message</label>-->
                        <!--                    <input type="text" class="form-control"-->
                        <!--                           name="message"-->
                        <!--                           v-model="cashMessage"-->
                        <!--                           placeholder="Optional Message">-->
                    </div>
                    <div class="form-group col-md-12">
                        <v-btn
                                class="w-40"
                                color="primary"
                                @click.prevent="paidWithCash()"
                                :loading="disableCashMessageButton"
                                ref="cashMessage">
                            Submit
                        </v-btn>
                    </div>
                </div>
            </transition>

            <stripe-credit-card-modal
                    :open="showCreditCardModal"
                    @close-stripe-cc-modal="showCreditCardModal = false"
                    :client-secret="theClientSecret"
                    :payment-methods="paymentMethods"
                    @paid="setTasksToPaid()"
            >

            </stripe-credit-card-modal>

            <deny-task-modal
                    v-if="isCustomer"
                    :jobTask="jTask">
            </deny-task-modal>

            <!--        <stripe-->
            <!--                :bid="bid"-->
            <!--                :client-secret="theClientSecret"-->
            <!--                :user="getCurrentUser()"-->
            <!--        >-->
            <!--        </stripe>-->

        </div>
    </div>
</template>

<script>

    import Card from '../shared/Card'
    import DenyTaskModal from '../task/DenyTaskModal'
    import Stripe from '../stripe/Stripe'
    import StripeCreditCardModal from '../stripe/StripeCreditCardModal'
    import {mapActions} from 'vuex'

    export default {
        props: {
            bid: Object
        },
        components: {
            Card,
            DenyTaskModal,
            StripeCreditCardModal,
            Stripe
        },
        data() {
            return {
                paymentMethods: null,
                paymentSucceeded: false,
                showCreditCardModal: false,
                instructions: [
                    'Pay In Person',
                    'Left Under The Mat',
                    'Check Is In The Mail'
                ],
                jTask: {},
                payAll: false,
                excluded: {},
                showPaidInCash: false,
                subtractFromTotal: 0,
                cashMessage: 'Pay In Person',
                paidCash: false,
                theClientSecret: null,
                disableCashMessageButton: false,
                disabled: {
                    payAll: false,
                    reopen: false,
                    payCash: false
                }
            }
        },

        computed: {
            totalCustomerPrice() {
                let total = 0
                if (this.getPayableTasks() !== null) {
                    for (const task of this.getPayableTasks()) {
                        total += (task.cust_final_price - task.sub_final_price)
                    }
                }
                return total
            },
            totalSubPrice() {
                let total = 0
                if (this.getPayableTasks() !== null) {
                    for (const task of this.getPayableTasks()) {
                        total += task.sub_final_price
                    }
                }
                return total
            },
            jobTasks() {
                return this.bid.job_tasks
            },
            showPayWithStripeBtn() {
                if (this.getPayableTasks().length > 0) {
                    return User.stripePaymentRequested(this.getPayableTasks())
                }
                return false
            },
            show() {
                return this.getPayableTasks().length > 0
            },
            isContractor() {
                return Spark.state.user.usertype === 'contractor'
            },
            isCustomer() {
                return Spark.state.user.usertype === 'customer'
            },
            payableTasks() {
                return this.getAllPayableTasks(this.jobTasks)
            },
        },
        methods: {
            ...mapActions(['excludedActions']),

            totalPriceForAllCompletedTasks() {
                let total = 0
                let payableT = this.getPayableTasks()
                for (let i = 0; i < payableT.length; i++) {
                    total = total + payableT[i].cust_final_price
                }

                if (total - this.subtractFromTotal === 0) {
                    return 0;
                } else {
                    return this.totalTaskFee(total - this.subtractFromTotal);
                }

            },

            setTasksToPaid() {

                let ex = Object.keys(this.excluded);
                this.paymentSucceeded = true;

                let nextId = 0;

                for (let i = 0; i < this.jobTasks.length; i++) {
                    let excludedId = false;
                    for (let j = 0; j < ex.length; j++) {
                        if (this.jobTasks[i].id === parseInt(ex[j])) {
                            excludedId = true;
                        }
                    }
                    if (!excludedId) {
                        let date = moment();
                        this.jobTasks[i].job_task_status[
                            this.jobTasks[i].job_task_status.length] = {
                            job_task_id: this.jobTasks[i].id,
                            status: 'paid',
                            status_number: 12,
                            sent_on: null,
                            deleted_at: null,
                            created_at: date._d,
                            updated_at: date._d,
                        }
                        nextId -= 1;
                        this.jobTasks[i].id = nextId;
                    }
                }
                this.showCreditCardModal = false;
            },

            totalTaskFee(price) {

                if (this.atLeastOneTaskIsPaid()) {
                    return Math.round((price + (parseFloat(price) * .029)
                        + .30
                        + Number.EPSILON) * 100) / 100;
                } else {
                    return Math.round((price + (parseFloat(price) * .029)
                        + .30
                        + this.jemmsonFee() + Number.EPSILON) * 100) / 100;
                }


            },

            totalTaskCashFee() {

                if (this.atLeastOneTaskIsPaid()) {
                    return this.totalPriceForAllCompletedTasksCash()
                } else {
                    return this.totalPriceForAllCompletedTasksCash() + 2.80
                }

            },

            jemmsonFee() {
                return 2.50
            },

            totalPriceForAllCompletedTasksCash() {
                let total = 0
                let payableT = this.getPayableTasks()
                for (let i = 0; i < payableT.length; i++) {
                    total = total + payableT[i].cust_final_price
                }
                return total
            },

            getCurrentUser() {
                if (Spark.state) {
                    return Spark.state.user
                }
            },

            getPayableTasks() {
                return this.getAllPayableTasks(this.jobTasks)
            },

            getAllPayableTasks(jobTasks) {
                if (jobTasks !== undefined) {

                    let payableTasks = [];

                    for (let i = 0; i < jobTasks.length; i++) {
                        if (
                            this.getLatestStatus(jobTasks[i].job_task_status) === 'approved_subs_work'
                            || this.getLatestStatus(jobTasks[i].job_task_status) === 'general_finished_work'
                        ) {
                            payableTasks.push(jobTasks[i])
                        }
                    }
                    return payableTasks;
                }
            },

            atLeastOneTaskIsPaid() {
                for (let i = 0; i < this.bid.job_tasks.length; i++) {
                    if (this.getLatestStatus(this.bid.job_tasks[0].job_task_status) === 'paid') {
                        return true
                    }
                }
                return false
            },

            getLatestStatus(jobTaskStatus) {
                if (jobTaskStatus) {
                    return jobTaskStatus[jobTaskStatus.length - 1].status
                }
            },

            cashJobOrNotSetupWithStripe() {
                return this.cashJob() || this.needsStripe()
            },

            creditCardJobAndContractorHasStripe() {
                return this.creditCardJob() && this.hasStripe()
            },

            needsStripe() {
                if (this.bid) {
                    return this.bid.contractor.contractor.stripe_id === false
                }
            },

            hasStripe() {
                if (this.bid) {
                    return this.bid.contractor.contractor.stripe_id !== false
                }
            },

            creditCardJob() {
                return this.bid.payment_type === 'creditCard'
            },

            cashJob() {
                return this.bid.payment_type === 'cash'
            },

            contractorIsSetupWithStripe() {
                return this.bid.contractor.stripe_id
            },
            showDenyBtn(jobTask) {
                const status = this.getLatestStatus(jobTask.job_task_status)
                if (this.isCustomer) {
                    return (status === 'approved_subs_work' || status === 'general_finished_work')
                }
            },
            showReopenBtn(jobTask) {
                if (this.isContractor && (jobTask.status === 'bid_task.finished_by_general' || jobTask.status ===
                    'bid_task.approved_by_general')) {
                    return true
                }
                return false
            },
            showPayCashForTaskBtn(jobTask) {
                return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status ===
                    'bid_task.approved_by_general') &&
                    User.isCustomer()
            },
            addJobTaskToExcludedList(jobTask) {
                if (document.getElementById('exclude-' + jobTask.id).checked) {
                    this.excluded[jobTask.id] = true;
                    this.subtractFromTotal += jobTask.cust_final_price;
                } else {
                    this.excluded[jobTask.id] = false;
                    this.subtractFromTotal -= jobTask.cust_final_price;
                }
            },
            openDenyTaskForm(jobTask) {
                this.jTask = jobTask
                $('#deny-task-modal_' + jobTask.id).modal()
            },

            paidWithCash() {
                // show message box to ask how contractor can pick up the cash
                // in person is an option
                // dropdown menu as well

                // this.paidCash = !this.paidCash;
                Customer.payAllPayableTasksWithCash(
                    this.bid.id, this.excluded, this.disabled, this.cashMessage)
            },
            reopenTask(jobTask) {
                SubContractor.reopenTask(jobTask, this.disabled)
            },
            async payAllPayableTasks() {
                // payAllPayableTasks() {

                this.payAll = true;

                const clientSecretData = await axios.post('stripe/getClientSecret', {
                    jobId: this.bid.id,
                    excluded: this.excluded
                });

                this.theClientSecret = clientSecretData.data;

                if (!User.isSignedUpWithStripe()) {
                    // $('#stripe-modal').modal()
                    Bus.$emit('needsStripe', clientSecretData.data);
                    this.excludedActions(this.excluded)
                } else {
                    this.selectWhichCreditCardToUse()
                }

                this.payAll = false;
                // Customer.payAllPayableTasks(this.bid.id, this.excluded, this.disabled)
            },

            async selectWhichCreditCardToUse() {

                const {data} = await axios.get('stripe/customer/getPaymentMethods/' + Spark.state.user.stripe_id)

                if (data.error) {
                    console.log(data.error);
                } else {
                    this.paymentMethods = data.data;
                    this.showCreditCardModal = true;
                }
            }
        }
    }
</script>

<style scoped>
    .header-completed {
        font-size: 14pt;
        background-color: #80808054;
        border-radius: 4px;
        padding-left: 11px;
        margin-bottom: 10px;
        font-weight: bold;
    }
</style>