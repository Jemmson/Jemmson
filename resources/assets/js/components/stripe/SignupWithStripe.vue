<template>
    <form method="post" id="payment-form">
        <div class="form-row">
            <label for="card-element">
                Credit or debit card
            </label>
            <br>
            <div id="card-element" style="width: 30rem">
                <!-- A Stripe Element will be inserted here. -->
            </div>

            <!-- Used to display form errors. -->
            <div id="card-errors" role="alert"></div>
        </div>

        <button>Submit Payment</button>
    </form>
</template>

<script>


  export default {
    data() {
      return {
      //   stripe: {},
      //   card: {},
      //   signup: false,
      //   style: {
      //     base: {
      //       color: '#32325d',
      //       lineHeight: '18px',
      //       fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
      //       fontSmoothing: 'antialiased',
      //       fontSize: '16px',
      //       '::placeholder': {
      //         color: '#aab7c4'
      //       }
      //     },
      //   }
      }
    },
    computed: {
      // isContractor() {
      //   return User.isContractor()
      // },
      // isCustomer() {
      //   return User.isCustomer()
      // },
    },
    methods: {
      // async getToken() {
      //
      //   let data = await this.stripe.createToken(this.card)
      //
      //   if (data.error) {
      //     // Inform the user if there was an error.
      //     var errorElement = document.getElementById('card-errors')
      //     errorElement.textContent = result.error.message
      //   } else {
      //     // Send the token to your server.
      //     this.stripeTokenHandler(result.token)
      //   }

        // // Insert the token ID into the form so it gets submitted to the server
        // var form = document.getElementById('payment-form');
        // var hiddenInput = document.createElement('input');
        // hiddenInput.setAttribute('type', 'hidden');
        // hiddenInput.setAttribute('name', 'stripeToken');
        // hiddenInput.setAttribute('value', token.id);
        // form.appendChild(hiddenInput);
        //
        // // Submit the form
        // form.submit();

        // event.preventDefault();
        // this.signup = true;
        // const {
        //   token,
        //   error
        // } = await this.stripe.createToken(this.card);

        // try {
        //   const data = await this.stripe.createToken(this.card);
        //   console.log(JSON.stringify(data));
        // } catch (e) {
        //   console.log(JSON.stringify(e));
        // }
        //
        // if (error) {
        //   // Inform the customer that there was an error
        //   const errorElement = document.getElementById('card-errors');
        //   errorElement.textContent = error.message;
        //   this.signup = false;
        // } else {
        //   try {
        //     // create stripe customer with token
        //     this.signup = false;
        //     $('#stripe-modal').modal('hide');
        //     let response = await axios.post('/stripe/customer', token);
        //     console.log('customer');
        //     let data = response.data;
        //     Bus.$emit('signedupStripe', data.id);
        //     Spark.state.user.stripe_id = data.id;
        //     Vue.toasted.success('You may now pay with stripe');
        //   } catch (error) {
        //     this.signup = false;
        //     error = error.response.data;
        //     Vue.toasted.error(error.message);
        //   }
        // }
      // },
      //
      // // Submit the form with the token ID.
      // stripeTokenHandler(token) {
      //   // Insert the token ID into the form so it gets submitted to the server
      //   var form = document.getElementById('payment-form')
      //   var hiddenInput = document.createElement('input')
      //   hiddenInput.setAttribute('type', 'hidden')
      //   hiddenInput.setAttribute('name', 'stripeToken')
      //   hiddenInput.setAttribute('value', token.id)
      //   form.appendChild(hiddenInput)
      //
      //   // Submit the form
      //   form.submit()
      // }
    },
    mounted() {
      // Create a Stripe client.
      var stripe = Stripe('pk_test_iAX3DPtpLj5RiG3FCexe1r0Z');

      // Create an instance of Elements.
      var elements = stripe.elements();

      // Custom styling can be passed to options when creating an Element.
      // (Note that this demo uses a wider set of styles than the guide below.)
      var style = {
        base: {
          color: '#32325d',
          fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
          fontSmoothing: 'antialiased',
          fontSize: '16px',
          '::placeholder': {
            color: '#aab7c4'
          }
        },
        invalid: {
          color: '#fa755a',
          iconColor: '#fa755a'
        }
      };

      // Create an instance of the card Element.
      var card = elements.create('card', {style: style});

      // Add an instance of the card Element into the `card-element` <div>.
      card.mount('#card-element');

      // Handle real-time validation errors from the card Element.
      card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
          displayError.textContent = event.error.message;
        } else {
          displayError.textContent = '';
        }
      });

      // Handle form submission.
      var form = document.getElementById('payment-form');
      form.addEventListener('submit', function(event) {
        event.preventDefault();

        stripe.createToken(card).then(function(result) {
          if (result.error) {
            // Inform the user if there was an error.
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
          } else {
            // Send the token to your server.
            stripeTokenHandler(result.token);
          }
        });
      });

      async function stripeTokenHandler (token) {
          try {
              const data = await axios.post ('stripe/charge', {
                token: token
              });
              console.log('stripeTokenHandler data', data.data)
          } catch (error) {
              console.log(error);
          }
      };

      // Submit the form with the token ID.
      // function stripeTokenHandler(token) {
        // Insert the token ID into the form so it gets submitted to the server
        // var form = document.getElementById('payment-form');
        // var hiddenInput = document.createElement('input');
        // hiddenInput.setAttribute('type', 'hidden');
        // hiddenInput.setAttribute('name', 'stripeToken');
        // hiddenInput.setAttribute('value', token.id);
        // form.appendChild(hiddenInput);
        //
        // // Submit the form
        // form.submit();
      // }
    }
  }
</script>

<style scope>
    /**
     * The CSS shown here will not be introduced in the Quickstart guide, but shows
     * how you can use CSS to style your Element's container.
     */
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