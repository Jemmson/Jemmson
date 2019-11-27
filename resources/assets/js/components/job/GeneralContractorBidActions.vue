<template>
    <div>
        <div ref="subTaskWarning"
             class="text-white btn-red rounded p-3 mt-2 mb-2 text-center"
             style="font-weight: 700"
             v-if="subTaskWarning">
            PLEASE CHECK TASKS. SOME TASKS HAVE SUB PRICES HIGHER THAN CONTRACTOR PRICE
        </div>
        <div class="flex flex-col">
            <v-btn
                    v-if="jobHasNotBeenSubmittedOrAChangeIsRequested()"
                    ref="submitBid"
                    class="btn btn-normal-green btn-lg w-full"
                    @click="submitBid()"
                    :disabled="disableSubmitBid"
            >
                Submit Bid
            </v-btn>
            <div v-else>
                <span class="capitalize">Bid Has Been Approved By The Customer. Please refer to individual tasks for Task Completion.</span>
            </div>
            <div v-if="notSignedUpModalIsHidden()"
            >
                <v-divider></v-divider>
                <v-btn
                        text
                        color="primary"
                        @click="connectWithStripe()"
                >
                    SIGN UP WITH STRIPE
                </v-btn>
            </div>
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
  import Status from '../../components/mixins/Status'
  import GeneralContractor from '../../classes/GeneralContractor'

  export default {
    name: 'GeneralContractorBidActions',
    components: {
      Stripe
    },
    props: {
      submitTheBid: Boolean,
      bid: Object
    },
    mixins: [Status],
    watch: {
      submitTheBid: function() {
        this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
      }
    },
    data() {
      return {
        subTaskWarning: false,
        disabled: {
          submitBid: true
        },
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
    watch: {
      bid: function() {
        this.checkReqs()
      }
    },
    methods: {

      jobHasNotBeenSubmittedOrAChangeIsRequested() {
        if (this.bid && this.bid.job_statuses) {
          const statusNumber = this.getJobStatusNumber_latest(this.bid);
          return statusNumber < 3 || statusNumber === 4;
        }
      },

      connectWithStripe() {
        let connectLink = 'https://connect.stripe.com/express/oauth/authorize?client_id='
          + Spark.stripeClientId + '&state=' + this.$route.path
          + '&stripe_user[email]=' + Spark.state.user.email
          + '&stripe_user[country]=US'
          + '&stripe_user[phone_number]=' + Spark.state.user.phone

        window.location = connectLink
      },

      notSignedUpModalIsHidden() {
        if (this.bid && this.bid.contractor && this.bid.contractor.contractor) {
          return this.bid.contractor.stripe_id === null && this.bid.contractor.contractor.hide_stripe_modal === 1
        }
      },
      checkReqs() {
        // return this.shouldHaveAtLeastOneTask() && this.bid.status === 'bid.sent'
        if (this.bid && this.bid.job_tasks && this.bid.status) {
          if (this.bidHasBeenSent()) {
            this.disableSubmitBid = true
          }
          if (!this.bidHasBeenSent() && this.shouldHaveAtLeastOneTask()) {
            this.disableSubmitBid = false
          } else {
            this.disableSubmitBid = true
          }
        }
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
        console.log('notifyCustomerOfFinishedBid', bid)
        axios.post('/api/task/finishedBidNotification', {
          jobId: bid.id,
          customerId: bid.customer_id
        }).then((response) => {
          console.log(response)
          disabled.submitBid = false
          User.emitChange('bidUpdated')
          Vue.toasted.success('Bid has been submitted and notification sent!')
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
              if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
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
      sendBid() {
        this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
      },
      submitBid() {
        if (!this.subsPriceIsHigherThanTheGeneralsPrice()) {
          if (this.signedUpForStripe()) {
            this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
          } else {
            if (!this.suppressCreditCardModalWindow()) {
              this.showStripeModalWindow()
            } else {
              this.notifyCustomerOfFinishedBid(this.bid, this.disabled)
            }
          }
        }
      }
    },
    mounted() {
      this.checkReqs()
    }
  }
</script>

<style>

</style>
