<template>
  <form @submit="submit" method="post" id="payment-form">
    <br>
    <div class="form-row">
      <label for="card-element">
        Sign Up With A Credit or Debit Card
      </label>
      <div id="card-element">
        <!-- a Stripe Element will be inserted here. -->
      </div>

      <!-- Used to display Element errors -->
      <div id="card-errors" role="alert"></div>
    </div>
    <br>
    <button class="btn btn-success" style="float: right;" :disabled="signup">
        <span v-if="signup">
            <i class="fa fa-btn fa-spinner fa-spin"></i>
        </span>
        Sign Up
    </button>
    <div style="clear:both;"></div>
  </form>
</template>

<script>
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
            return User.isContractor();
        },
        isCustomer() {
            return User.isCustomer();
        },
    },
    methods: {
        async submit(event) {
            event.preventDefault();
            this.signup = true;
            const {
                token,
                error
            } = await this.stripe.createToken(this.card);

            if (error) {
                // Inform the customer that there was an error
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = error.message;
                this.signup = false;
            } else {
                try {
                    // create stripe customer with token
                    this.signup = false;
                    $('#stripe-modal').modal('hide');  
                    let response = await axios.post('/stripe/customer', token);
                    console.log('customer');
                    let data = response.data;
                    Bus.$emit('signedupStripe', data.id);
                    Spark.state.user.stripe_id = data.id;
                    Vue.toasted.success('You may now pay with stripe');
                } catch (error) {
                    this.signup = false;
                    error = error.response.data;
                    Vue.toasted.error(error.message);
                }
            }
        },
    },
    mounted() {
        this.stripe = Stripe(Spark.stripeKey);
        const elements = this.stripe.elements();
        // Create an instance of the card Element
        this.card = elements.create('card', {
            style: this.style
        });

        // Add an instance of the card Element into the `card-element` <div>
        this.card.mount('#card-element');
        this.card.addEventListener('change', ({
            error
        }) => {
            const displayError = document.getElementById('card-errors');
            if (error) {
                displayError.textContent = error.message;
            } else {
                displayError.textContent = '';
            }
        });
    }
}
</script>