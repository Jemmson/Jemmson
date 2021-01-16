<template>
  <!-- / all tasks ready for payment -->
  <div>
      <div class="flex flex-col">
        <div
            class="banner"
            :class="setPaymentColor()"
            v-if="paymentSucceeded"
        >
          <h3 class="text-center" ref="fullPayment" v-if="allTasksCompleted()">Payment Succeeded!</h3>
          <h3 class="text-center" ref="partialPayment"
              v-else-if="someTasksArePaidFor()">Partial Payment Completed!</h3>
        </div>


        <v-simple-table v-if="!isContractor">
          <template v-slot:default>
            <thead>
            <tr>
              <th>Task</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="item in customerTasks" :key="item.name">
              <td>{{ item.task }}</td>
              <td>{{ item.qty }}</td>
              <td>${{ item.unit_price.toFixed(2) }}</td>
              <td>${{ item.total.toFixed(2) }}</td>
            </tr>
            <tr v-if="creditCardJob()">
              <td><strong>Application Fee:</strong></td>
              <td></td>
              <td></td>
              <td><strong>$2.50</strong></td>
            </tr>
            <tr>
              <td><strong>Totals:</strong></td>
              <td></td>
              <td></td>
              <td><strong>${{ customerTasksTotal.totalPrice.toFixed(2) }}</strong></td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </div>

      <div v-if="isCustomer && !allTasksArePaidFor()" ref="showPaymentButton" class="text-right">
        <v-btn
            v-if="cashJobOrNotSetupWithStripe()"
            class="w-full"
            color="primary"
            @click.prevent="paidCash = true" :loading="disabled.payCash">
          Pay With Cash
        </v-btn>
        <v-btn
            ref='payWithCreditCard'
            class="w-full"
            color="primary"
            v-if="creditCardJob()"
            @click.prevent="payAllPayableTasks()"
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

      <v-dialog
          v-model="changeTask.show"
          ref="denyDialog"
          fullscreen
      >

        <v-card>
          <v-card-title class="w-break">
            Request Change For <span class="capitalize">{{ this.changeTask.taskName }}</span>
          </v-card-title>

          <v-card-text>

            <v-textarea
                outlined
                label="Reason for Change"
                v-model="changeTask.message"
            ></v-textarea>

          </v-card-text>

          <v-card-actions
              class="justify-content-between"
          >
            <v-btn
                width="50%"
                color="red"
                text
                @click="changeTask.show = false"
            >
              Cancel
            </v-btn>
            <v-btn
                :disabled="changeTask.disabled"
                width="50%"
                color="blue"
                text
                @click="requestChange()"
            >
              Submit
            </v-btn>
          </v-card-actions>

        </v-card>

      </v-dialog>


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
</template>

<script>

import Card from '../shared/Card'
import DenyTaskModal from '../task/DenyTaskModal'
import Stripe from '../stripe/Stripe'
import StripeCreditCardModal from '../stripe/StripeCreditCardModal'
import {mapActions} from 'vuex'
import Status from "../mixins/Status";
import StripeMixin from "../mixins/StripeMixin";

export default {
  props: {
    bid: Object,
    paid: Boolean
  },
  components: {
    Card,
    DenyTaskModal,
    StripeCreditCardModal,
    Stripe
  },

  watch: {
    paid: function (val) {
      if (val) {
        this.setTasksToPaid()
      }
    }
  },
  mixins: [Status, StripeMixin],
  data() {
    return {
      customerTasks: [],
      customerTasksTotal: {
        ccFees: 0,
        totalPrice: 0
      },
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
      changeTask: {
        user_id: Spark.state.user.id,
        disabled: false,
        show: false,
        taskName: null,
        job_task_id: null,
        message: null
      },
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
  mounted() {
    this.$nextTick(this.populateTasks())
  },
  methods: {

    populateTasks(){
      let total = 0;
      let tasks = this.getPayableTasks()
      for (let i = 0; i < tasks.length; i++) {
        total = total + tasks[i].unit_price * tasks[i].qty;
        this.customerTasks.push(
            {
              task: tasks[i].task.name,
              qty: tasks[i].qty,
              unit_price: tasks[i].unit_price,
              total: tasks[i].unit_price * tasks[i].qty
            }
        )
      }
      if (this.creditCardJob()) {
        this.customerTasksTotal.totalPrice = total + 2.50;
      } else {
        this.customerTasksTotal.totalPrice = total;
      }

    },

    allTasksArePaidFor() {
      // return true
      const tasks = this.getAllPayableTasks(this.jobTasks)

      if (tasks !== undefined) {

        let paid = 0;

        for (let i = 0; i < tasks.length; i++) {
          if (this.getLatestStatus(tasks[i].job_task_status) === 'paid') {
            paid = paid + 1;
          }
        }

        return paid === tasks.length
      }

    },

    requestChange() {
      this.changeTask.disabled = true;
      Customer.denyTask(this.changeTask, this.changeTask.disabled);
      this.changeTask.disabled = false;
      this.changeTask.show = false;
    },

    setPaymentColor() {
      if (this.allTasksCompleted()) {
        return 'green-background'
      }
      return 'yellow-background'
    },

    allTasksCompleted() {
      let jtFinished = true
      for (let i = 0; i < this.bid.job_tasks.length; i++) {
        let status = this.getLatestStatus(this.bid.job_tasks[i].job_task_status)
        if (status !== 'paid') {
          jtFinished = false
        }
      }
      return jtFinished
    },

    someTasksArePaidFor() {
      let jtFinished = false
      for (let i = 0; i < this.bid.job_tasks.length; i++) {
        let status = this.getLatestStatus(this.bid.job_tasks[i].job_task_status)
        if (status !== 'paid') {
          jtFinished = true
        }
      }
      return jtFinished
    },

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

      // if (this.atLeastOneTaskIsPaid()) {
      //     return Math.round((price + (parseFloat(price) * .029)
      //         + .30
      //         + Number.EPSILON) * 100) / 100;
      // } else {
      //     return Math.round((price + (parseFloat(price) * .029)
      //         + .30
      //         + this.jemmsonFee() + Number.EPSILON) * 100) / 100;
      // }

      return price


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
              || this.getLatestStatus(jobTasks[i].job_task_status) === 'paid'
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
      return this.cashJob() || this.needsStripeForCreditCardPayments()
    },

    creditCardJobAndContractorHasStripe() {
      return this.creditCardJob() && this.needsStripeForCreditCardPayments()
    },

    creditCardJob() {
      return this.bid.payment_type === 'creditCard'
    },

    cashJob() {
      return this.bid.payment_type === 'cash'
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
      this.changeTask.show = true;
      this.changeTask.job_task_id = jobTask.id;
      this.changeTask.taskName = jobTask.task.name;
      // this.jTask = jobTask
      // $('#deny-task-modal_' + jobTask.id).modal()
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
      } else {
        this.selectWhichCreditCardToUse()
      }

      this.excludedActions(this.excluded)

      this.payAll = false;
      // Customer.payAllPayableTasks(this.bid.id, this.excluded, this.disabled)
    },

    async selectWhichCreditCardToUse() {
      const {data} =
          await axios.get('stripe/customer/getPaymentMethods/' + Spark.state.user.customer_stripe_id)
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

.green-background {
  background-color: green;
  padding: 1rem;
  border-radius: 10px;
}

.yellow-background {
  background-color: yellow;
  padding: 1rem;
  border-radius: 10px;
}

.header-completed {
  font-size: 14pt;
  background-color: #80808054;
  border-radius: 4px;
  padding-left: 11px;
  margin-bottom: 10px;
  font-weight: bold;
}
</style>