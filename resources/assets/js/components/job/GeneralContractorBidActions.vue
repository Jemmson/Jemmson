<template>
  <div>
    <div ref="subTaskWarning"
         class="text-white bg-red rounded p-3 mt-2 mb-2 text-center w-break"
         style="font-weight: 700"
         v-if="subTaskWarning && creditCardJob()">
      Unable to submit this bid. There is a task where the sub price is higher than your price + the credit card fee.
    </div>
    <div ref="disableButton"
         class="capitalize text-white bg-red rounded p-3 mt-2 mb-2 text-center w-break"
         style="font-weight: 700"
         v-if="disableButton">
      This job does not have any tasks. Please add a task
    </div>
    <div class="flex flex-col">
      <div v-if="jobHasNotBeenSubmittedOrAChangeIsRequested()">
        <v-btn
            ref="submitBid"
            color="green"
            text
            class="btn btn-normal-green btn-lg w-full"
            @click="submitBid()"
            :disabled="subTaskWarning || disableButton"
            :loading="disabled.submitBid"
        >

          <div v-if="bidHasBeenSentBefore()">Resubmit Bid</div>
          <div v-else>Submit Bid</div>
        </v-btn>
        <div class="flex justify-around align-baseline">
          <h5>Does Job Have Customer Approval?</h5>
          <v-checkbox
              v-model="approved"
              :value="true"
              color="blue"
          >
          </v-checkbox>
        </div>
        <div class="flex justify-around align-baseline">
          <h5>Has Job Been Finished?</h5>
          <v-checkbox
              v-model="finished"
              :value="true"
              color="blue"
          >
          </v-checkbox>
        </div>
      </div>
      <div v-else-if="jobIsApproved()">
                <span class="capitalize w-break"
                >Bid Has Been Approved By The Customer. Please refer to individual tasks for Task Completion.</span>
      </div>
      <div v-if="notSignedUpModalIsHidden()"
      >
        <v-divider></v-divider>
        <v-btn
            text
            color="primary"
            @click="connectWithStripe($route.path)"
        >
          SIGN UP WITH STRIPE
        </v-btn>
      </div>
      <hr>
      <v-simple-table>
        <template v-slot:default>
          <thead>
          <tr>
            <th>Task Name</th>
            <th>General Price</th>
            <th>Sub Price</th>
            <th>Profit</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="item in tasks" :key="item.id">
            <td>{{ item.name }}</td>
            <td>${{ item.general ? item.general.toFixed(2) : '' }}
              <v-tooltip v-if="!item.ccFeeCovered && creditCardJob()" v-model="item.show" top>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon color="warning">mdi-alert-rhombus</v-icon>
                  </v-btn>
                </template>
                <span>Profit Does Not Cover Estimated Credit Card Fee of {{ item.ccFee ? item.ccFee.toFixed(2) : '' }}.
                  Minimum price should be at least {{ item.minimumPrice ? item.minimumPrice.toFixed(2) : '' }}</span>
              </v-tooltip>
            </td>
            <td>${{ item.sub ? item.sub.toFixed(2) : '' }}</td>
            <td>${{ item.profit ? item.profit.toFixed(2) : '' }}</td>
          </tr>
          <tr>
            <td><strong>Totals:</strong></td>
            <td><strong>${{ tasksTotal.general ? tasksTotal.general.toFixed(2) : '' }}</strong></td>
            <td><strong>${{ tasksTotal.sub ? tasksTotal.sub.toFixed(2) : '' }}</strong></td>
            <td><strong>${{ tasksTotal.profit ? tasksTotal.profit.toFixed(2) : '' }}</strong></td>
          </tr>
          <tr v-if="creditCardJob()">
            <td></td>
            <td></td>
            <td><strong>Total Stripe Fee:</strong></td>
            <td><strong>-${{ tasksTotal.stripeFee }}</strong></td>
          </tr>
          <tr v-if="creditCardJob()">
            <td></td>
            <td></td>
            <td><strong>Net Profit:</strong></td>
            <td><strong>${{ tasksTotal.netTotal }}</strong></td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>

      <v-card>
        <v-card-text>
          <strong>NOTE:</strong><br>
          <strong>Credit Card Job:</strong><br>
          You will be responsible for the credit card fees for the job. This means your profit between a general and a
          sub
          should be enough to cover the fee. You will be unable to submit a bid until all tasks cover the fee.
          CC fees are .30 per transaction and 2.9% per transaction total. The customer will cover
          the application fee of $2.50 per job.
          <br>
          <strong>Cash Job:</strong><br>
          If the job is a cash job then you as the General Contractor
          will be charged $1.00 at the time the customer approves
          the job. The charge will be made with the credit card that
          you have on file with the site.
        </v-card-text>
      </v-card>


      <p style="color:black; font-size: 12px;">

      </p>

    </div>
    <stripe
        :user="getUser"
        @sendBid="$event ? sendBid() : false"
    >
    </stripe>
  </div>

</template>

<script>

import Stripe from '../../components/stripe/Stripe'
import StripeMixin from '../../components/mixins/StripeMixin'
import Status from '../../components/mixins/Status'
import GeneralContractor from '../../classes/GeneralContractor'

export default {
  name: 'GeneralContractorBidActions',
  components: {
    Stripe
  },
  props: {
    submitTheBid: Boolean,
    bid: Object,
    disableButton: Boolean
  },
  mixins: [Status, StripeMixin],
  watch: {
    submitTheBid: function () {
      this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
      this.disabled.submit = true;
      setTimeout(() => {
        this.disabled.submit = false;
      }, 5000)
    },
    bid: function () {
      this.checkReqs()
    }
  },
  data() {
    return {
      tasks: [],
      approved: false,
      finished: false,
      tasksTotal: {
        general: 0,
        sub: 0,
        profit: 0,
        stripeFee: 0,
        netTotal: 0
      },
      subTaskWarning: false,
      disabled: {
        submitBid: false
      },
      submitted: false,
      disableSubmitBid: true
    }
  },
  computed: {
    getUser() {
      if (Spark) {
        return Spark.state.user
      }
    }
  },
  methods: {
    filterTasks() {
      let general = 0;
      let sub = 0;
      let profit = 0;
      let ccFee = 0;
      let stripeFee = 0;
      let minimumPrice = 0;
      let ccFeeCovered = false;
      for (let i = 0; i < this.bid.job_tasks.length; i++) {
        general = general + this.bid.job_tasks[i].cust_final_price;
        sub = sub + this.bid.job_tasks[i].sub_final_price;
        profit = profit + this.bid.job_tasks[i].cust_final_price - this.bid.job_tasks[i].sub_final_price
        ccFee = 0;
        minimumPrice = 0;
        ccFeeCovered = false;
        stripeFee = stripeFee + ccFee;
        minimumPrice = this.bid.job_tasks[i].sub_final_price + ccFee;
        ccFeeCovered = (this.bid.job_tasks[i].cust_final_price - this.bid.job_tasks[i].sub_final_price) > ccFee;
        this.tasks.push(
            {
              id: i + 1,
              name: this.bid.job_tasks[i].task.name,
              general: this.bid.job_tasks[i].cust_final_price,
              sub: this.bid.job_tasks[i].sub_final_price,
              ccFee: ccFee,
              minimumPrice: minimumPrice,
              ccFeeCovered: ccFeeCovered,
              show: false,
              profit: this.bid.job_tasks[i].cust_final_price - this.bid.job_tasks[i].sub_final_price,
            }
        )
      }
      const totalCustomerCost = general + 2.5;
      stripeFee = (totalCustomerCost * .029) + .30;
      let jemmsonFinalAmount = totalCustomerCost - stripeFee;
      let netTotal = jemmsonFinalAmount - 2.50 - sub;

      // let netTotal = profit - stripeFee;
      this.tasksTotal.general = general;
      this.tasksTotal.sub = sub;
      this.tasksTotal.profit = profit;
      this.tasksTotal.stripeFee = stripeFee.toFixed(2);
      // this.tasksTotal.stripeFee = stripeFee.toFixed(2);
      this.tasksTotal.netTotal = netTotal.toFixed(2);
      this.subsPriceIsHigherThanTheGeneralsPrice();
    },

    creditCardJob() {
      return this.bid.payment_type === 'creditCard'
    },

    jobHasBeenSent() {
      if (this.bid && this.bid.job_statuses) {
        const statusNumber = this.getJobStatusNumber_latest(this.bid);
        return statusNumber === 3;
      }
    },

    jobIsApproved() {
      if (this.bid && this.bid.job_statuses) {
        const statusNumber = this.getJobStatusNumber_latest(this.bid);
        return statusNumber === 7;
      }
    },

    jobHasNotBeenSubmittedOrAChangeIsRequested() {
      if (this.bid) {
        if (this.bid.job_statuses) {
          const statusNumber = this.getJobStatusNumber_latest(this.bid)
          return statusNumber <= 5 || statusNumber === 8
        }
      }
    },

    notSignedUpModalIsHidden() {
      if (this.bid && this.bid.contractor && this.bid.contractor.contractor) {
        return this.bid.contractor.stripe_id === null && this.bid.contractor.contractor.hide_stripe_modal === 1
      }
    },

    checkReqs() {
      this.disableSubmitBid = !(!this.jobIsApproved() && this.shouldHaveAtLeastOneTask())
    },
    shouldHaveAtLeastOneTask() {
      if (this.bid && this.bid.job_tasks) {
        return this.bid.job_tasks.length > 0
      }
    },
    bidHasBeenSent() {
      if (this.bid && this.bid.status) {
        return this.bid.status === 'bid.sent'
      }
    },
    bidHasBeenSentBefore() {
      if (this.bid) {
        for (let i = 0; i < this.bid.job_statuses.length; i++) {
          if (this.bid.job_statuses[i].status === 'sent') {
            return true
          }
        }
        return false
      }
    },
    // shouldBeSignedUpForStripe() {
    //   if (this.bid && this.bid.contractor) {
    //     // return this.bid.contractor.stripe_id === null
    //     return Spark.state.user.contractor.stripe_express === null
    //   }
    // },
    // openBidSubmissionDialog() {
    //   return this.$emit('open-bid-submission', true)
    // },

    notifyCustomerOfFinishedBid(bid, disabled) {
      disabled.submitBid = true
      // TODO: implement the code below
      // if (User.needsStripe()) {
      //     disabled.submitBid = false
      //     return false
      // }
      this.$emit('remove-notification');

      console.log('notifyCustomerOfFinishedBid', bid)
      axios.post('/api/task/finishedBidNotification', {
        jobId: bid.id,
        customerId: bid.customer_id,
        approved: this.approved,
        finished: this.finished,
      }).then((response) => {
        console.log(response)
        disabled.submitBid = false
        User.emitChange('bidUpdated');
        Vue.toasted.success('Bid has been submitted and notification sent!')
        this.$emit('bid-submitted');
      }).catch((error) => {
        console.error(error)
        disabled.submitBid = false
        Vue.toasted.error('Whoops! Something went wrong! Please try again.')
      })
    },
    signedUpForStripe() {
      if (this.bid && this.bid.contractor) {
        // return this.bid.contractor.stripe_id === null
        return Spark.state.user.contractor.stripe_express !== null
      }
    },
    suppressCreditCardModalWindow() {
      if (this.bid && this.bid.contractor) {
        // return this.bid.contractor.stripe_id === null
        return Spark.state.user.contractor.hide_stripe_modal === 1
      }
    },
    showStripeModalWindow() {
      $('#stripe-modal').modal('show')
    },
    subsPriceIsHigherThanTheGeneralsPrice() {
      // go through each job task and compare the sub price to the contractor task price
      // first check if there is a sub.
      // check if the sub price is an accepted price
      // compare the the accepted sub price to the contractor price
      // if the accepted sub price is higher then throw an error
      if (this.bid) {
        if (this.bid.job_tasks.length) {
          this.subTaskWarning = false
          for (let i = 0; i < this.bid.job_tasks.length; i++) {
            if (this.acceptedSubPriceTooHigh(this.bid.job_tasks[i])) {
              this.subTaskWarning = true
              return true
            }
          }
        } else {
          this.checkReqs()
        }
      }
      return false
    },
    acceptedSubPriceTooHigh(task) {
      return this.subPrice(task) > (this.generalPrice(task) - this.stripeFee(this.generalPrice(task)) - .01);
    },
    stripeFee(price) {
      return price * .029 + .30;
    },
    subPrice(task) {
      return task.sub_final_price
    },
    generalPrice(task) {
      return task.cust_final_price
    },
    sendBid() {
      this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
    },
    submitBid() {
      if (!this.subsPriceIsHigherThanTheGeneralsPrice()) {
        if (this.signedUpForStripe()) {
          this.submitted = true;
          this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
          this.submitted = false;
        } else {
          if (!this.suppressCreditCardModalWindow()) {
            this.showStripeModalWindow()
          } else {
            this.submitted = true;
            this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
            this.submitted = false;
          }
        }
      }
    }
  },
  mounted() {
    this.checkReqs()
    this.filterTasks();
  }
}
</script>

<style>

.bg-red {
  background-color: red;
}

</style>
