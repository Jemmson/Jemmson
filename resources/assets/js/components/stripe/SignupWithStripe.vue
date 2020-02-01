<template>
    <form id="payment-form" class="w-full">
        <div class="flex flex-col">
            <label for="card-element">
                Sign Up With A Credit or Debit Card
            </label>
            <div id="card-element" ref="card">
                <!-- a Stripe Element will be inserted here. -->
            </div>

            <!-- Used to display Element errors -->
            <div id="card-errors" role="alert"></div>
        </div>
        <br>
        <button
                @click="submit()">Sign Up
        </button>
        <div style="clear:both;"></div>
    </form>
</template>

<script>
  import { mapState } from 'vuex'

  let stripe = Stripe(`pk_test_iAX3DPtpLj5RiG3FCexe1r0Z`)
  let elements = stripe.elements()
  let card = undefined

  export default {
    data() {
      return {
        stripe: {},
        card: {},
        signup: false,
        style: {
          base: {
            color: '#32325d',
            lineHeight: '18px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4'
            }
          },
        }
      }
    },
    computed: {
      isContractor() {
        return User.isContractor()
      },
      isCustomer() {
        return User.isCustomer()
      },
      ...mapState({
        excludedTaskIds: state => state.excluded
      })
    },
    props: {
      bid: Object
    },
    methods: {
      submit() {
        stripe.createToken(card).then(function(result) {
          if (result.error) {
            self.hasCardErrors = true;
            self.$forceUpdate(); // Forcing the DOM to update so the Stripe Element can update.
            return;
          } else {
            // Send the token to your server.
            this.stripeTokenHandler(result.token)
          }
        }.bind(this))
      },
      async stripeTokenHandler(token) {
        console.log('excludedTaskIds', this.excludedTaskIds)
        const data = await axios.post('stripe/charge', {
          token: token,
          jobId: this.bid.id,
          excluded: this.excludedTaskIds
        })
        console.log(JSON.stringify(data.data))
      },
    },
    mounted() {

      let style = {
        base: {
          border: '1px solid #D8D8D8',
          borderRadius: '4px',
          color: '#000',
        },

        invalid: {
          // All of the error styles go inside of here.
        }

      }

     if (!card) {
       // Create an instance of the card Element
       card = elements.create('card', style)
       // Add an instance of the card Element into the `card-element` <div>
       card.mount("#card-element")
       // card.mount(this.$refs.card)
     }
    }
  }
</script>

<style scope>

    .StripeElement {
        box-sizing: border-box;

        height: 40px;

        padding: 10px 12px;

        border: 1px solid transparent;
        border-radius: 4px;
        background-color: white;

        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }

</style>