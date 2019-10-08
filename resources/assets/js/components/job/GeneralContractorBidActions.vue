<template>
    <div>

        <div ref="subTaskWarning"
             class="text-white btn-red rounded p-3 mt-2 mb-2 text-center"
             style="font-weight: 700"
             v-if="subTaskWarning">
            PLEASE CHECK TASKS. SOME TASKS HAVE SUB PRICES HIGHER THAN CONTRACTOR PRICE
        </div>

        <div class="flex flex-col">

<!--            <div class="flex flex-center">-->
<!--                <button-->
<!--                        v-if="shouldBeSignedUpForStripe()"-->
<!--                        ref="stripeButton"-->
<!--                        @click="triggerStripe()"-->
<!--                        class="mt-1rem"-->
<!--                        style="background-image: url('/img/blue-on-light.png'); width: 12rem; height: 2.15rem;"-->
<!--                ></button>-->
<!--                <button v-else-->
<!--                        class="mt-1rem"-->
<!--                        style="background-image: url('/img/powered_by_stripe.png'); background-repeat: no-repeat; width: 151px; height: 43px;"-->
<!--                >-->
<!--                </button>-->
<!--            </div>-->

            <hr>

            <button ref="submitBid"
                    class="btn btn-normal btn-lg w-full"
                    @click="checkCreditCardSetup()"
                    :disabled="checkReqs()"
            >Submit Bid
            </button>

            <!--        @click="openBidSubmissionDialog()">Submit Bid</button>-->
        </div>

        <!--        <stripe :user="getUser">-->
        <!--        </stripe>-->

    </div>

</template>

<script>

  import Stripe from '../../components/stripe/Stripe'
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
    watch: {
      submitTheBid: this.notifyCustomerOfFinishedBid
    },
    data() {
      return {
        subTaskWarning: false,
        disabled: {
          submitBid: true
        }
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
      triggerStripe() {
        console.log('trying to trigger stripe')
        Bus.$emit('needsStripe')
      },
      checkReqs() {

        // return this.shouldHaveAtLeastOneTask() && this.bid.status === 'bid.sent'

       if (this.bid && this.bid.job_tasks && this.bid.status) {
         if (this.bidHasBeenSent()) {
           return true
         }

         if (!this.bidHasBeenSent() && this.shouldHaveAtLeastOneTask()) {
           return false
         } else {
           return true
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
      shouldBeSignedUpForStripe() {
        if (this.bid && this.bid.contractor) {
          // return this.bid.contractor.stripe_id === null
          return Spark.state.user.contractor.stripe_express === null
        }
      },
      openBidSubmissionDialog() {
        return this.$emit('open-bid-submission', true)
      },

      notifyCustomerOfFinishedBid(bid, disabled) {
        disabled.submitBid = true
        // TODO: implement the code below
        // if (User.needsStripe()) {
        //     disabled.submitBid = false
        //     return false
        // }
        console.log('notifyCustomerOfFinishedBid', bid);
        axios.post('/api/task/finishedBidNotification', {
          jobId: bid.id,
          customerId: bid.customer_id
        }).then((response) => {
          console.log(response);
          disabled.submitBid = false;
          User.emitChange('bidUpdated');
          Vue.toasted.success('Bid has been submitted and notification sent!');
        }).catch((error) => {
          console.error(error);
          disabled.submitBid = false;
          Vue.toasted.error('Whoops! Something went wrong! Please try again.');
        })
      },
      checkCreditCardSetup(){
        $("#stripe-modal").modal('show')
      },
      submitBid() {
        if (this.shouldBeSignedUpForStripe()) {

        }

        // go through each job task and compare the sub price to the contractor task price
        // first check if there is a sub.
        // check if the sub price is an accepted price
        // compare the the accepted sub price to the contractor price
        // if the accepted sub price is higher then throw an error
        if (this.bid) {
          this.subTaskWarning = false
          for (let i = 0; i < this.bid.job_tasks.length; i++) {
            if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
              this.subTaskWarning = true
            }
          }
          if (!this.subTaskWarning) {
            // let gc = new GeneralContractor(Spark.state.user)
            // gc.notifyCustomerOfFinishedBid(this.bid, this.disabled)
            this.notifyCustomerOfFinishedBid(this.bid, this.disabled)

          }
        }
      }
    }
  }
</script>

<style>

</style>
