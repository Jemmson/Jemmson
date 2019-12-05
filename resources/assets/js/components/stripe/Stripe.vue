<template>
    <!-- Modal -->
    <div class="modal h-100 modal-background-gray" id="stripe-modal" tabindex="-1" role="dialog"
         aria-labelledby="stripe-modal"
         aria-hidden="false" style="z-index: 1;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex flex-col">
                        <button type="button btn-normal" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                        </button>
                        <div class="title" v-if="showStripeExpress">Would you like to be able to receive credit card
                            payments from your customer?
                        </div>
                    </div>
                    <!--                    <h4 class="modal-title">{{ header }}</h4>-->
                </div>
                <div class="modal-body">
                    <div v-if="showStripeExpress">
                        Just Sign up with Stripe. It is a safe and secure way to receive all of your credit card
                        transactions.
                        You will be redirected to Stripe for a quick one-time setup. <br>
                        (Stripe charges 2% for each credit cared payment made through its service)
                    </div>
                    <div v-if="isCustomer && notSignedUp">
                        Before You can pay with stripe you will need to complete the form below.
                    </div>
                </div>
                <div v-if="isContractor && showStripeExpress"
                     class="modal-footer"
                     style="flex-direction: column; justify-content: space-between; padding-left: 0rem; padding-right: 0rem;">
                    <div class="flex space-between">
                        <button class="btn btn-normal-red btn-md w-38 mr-1rem" @click="notAtThisTime()">Not At This
                            Time
                        </button>
                        <connect-with-stripe class="ml-1rem">
                        </connect-with-stripe>
                    </div>
                    <div>
                        <label class="label" for="doNotShowAgain">Do Not Show This Dialog Again</label>
                        <input type="checkbox" id="doNotShowAgain" :value="dontShowAgain"
                               @click="dontShowAgain = !dontShowAgain">
                    </div>
                </div>
                <div class="modal-footer" v-else-if="isCustomer && notSignedUp">
                    <signup-with-stripe>
                    </signup-with-stripe>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

  import SignupWithStripe from './SignupWithStripe'
  import ConnectWithStripe from './ConnectWithStripe'

  export default {
    props: {
      user: Object
    },
    data() {
      return {
        dontShowAgain: false
      }
    },
    components: {
      SignupWithStripe,
      ConnectWithStripe
    },
    computed: {
      header() {
        return 'Stripe'
      },
      notSignedUp() {
        return !User.hasStripeId()
      },
      isContractor() {
        return User.isContractor()
      },
      isCustomer() {
        return User.isCustomer()
      },
      showStripeExpress() {
        if (User.contractor === null)
          return false
        if (Spark.state.user.contractor) {
          return Spark.state.user.contractor.stripe_express === null
        }

      }
    },
    methods: {
      notAtThisTime() {
        this.$emit('sendBid', true);
        if (this.dontShowAgain) {
          this.dontShowModalAgain()
        } else {
          this.exit()
        }
      },
      exit() {
        $('#stripe-modal').modal('hide')
      },
      async dontShowModalAgain() {
        try {
          const data = await axios.get('/stripe/hideModal')
          this.exit()
        } catch (error) {
          console.log('error')
        }
      }
    },
    mounted() {
      axios.get('/user/current')
        .then(response => {
          this.$store.commit('setUser', response.data)
          window.User.user = response.data
        })
    }
  }
</script>

<style scoped>
    .title {
        font-size: 14pt;
        font-weight: bold;
        margin-top: 9px;
    }

    .label {
        margin-top: 20px;
        margin-right: 11px;
    }

    .w-38 {
        width: 38%;
    }
</style>