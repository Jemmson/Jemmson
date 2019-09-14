<template>
    <!-- Modal -->
    <div class="modal" id="stripe-modal" tabindex="-1" role="dialog" aria-labelledby="stripe-modal"
         aria-hidden="false" style="z-index: 1;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button btn-normal" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ header }}</h4>
                </div>
                <div class="modal-body">
                    <div v-if="showStripeExpress">
                       Use Stripe to handle all of your credit card payments. It is secure and safe.
                        You will be redirected to Stripe and you just have to follow the prompts and
                        you will be quickly setup.
                    </div>
                    <div v-if="isCustomer && notSignedUp">
                        Before You can pay with stripe you will need to complete the form below.
                    </div>
                </div>
                <div class="modal-footer">
                    <span>
                        <div v-if="isContractor">
                            <connect-with-stripe v-if="showStripeExpress">
                            </connect-with-stripe>
                        </div>
                        <div v-if="isCustomer && notSignedUp">
                            <signup-with-stripe>
                            </signup-with-stripe>
                        </div>
                    </span>
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
    methods: {},
    mounted() {
      axios.get('/user/current')
        .then(response => {
          this.$store.commit('setUser', response.data)
          window.User.user = response.data
        })
    }
  }
</script>