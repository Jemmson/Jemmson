<template>
<div>
  <form @submit="submit" method="post" id="payment-form" v-if="!hasStripe">
    <br>
    <div class="form-row">
      <label for="card-element">
        Credit or debit card
      </label>
      <div id="card-element">
        <!-- a Stripe Element will be inserted here. -->
      </div>

      <!-- Used to display Element errors -->
      <div id="card-errors" role="alert"></div>
    </div>
    <br>
    <button class="btn btn-success" v-if="!hasStripe">Sign Up & Pay $0.{{amount}}</button>
  </form>
  <button class="btn btn-success" v-if="hasStripe" @click="submit">Submit Payment $0.{{amount}}</button>
</div>
</template>

<script>
export default {
    data() {
        return {
            stripe: {},
            card: {},
            stripe_id: null,
            amount: 50,
            products: {
                name: 'Moby Dick',
                description: 'I love whales',
                amount: 100000 // 100$ in cents
            },
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
        hasStripe() {
            if (this.stripe_id !== null) {
                return true;
            }
            return false;
        },
        stripeKey() {
            return Spark.stripeKey;
        },
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
            let amount = this.amount;
            // if we don't have a stripe id for this customer create one
            // then charge them when we have the id
            if (this.stripe_id === null) {
                // create stripe token
                const {
                    token,
                    error
                } = await this.stripe.createToken(this.card);

                if (error) {
                    // Inform the customer that there was an error
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = error.message;
                } else {
                    try {
                        // create stripe customer with token
                        let response = await axios.post('/stripe/customer', token);
                        console.log('customer');
                        let data = response.data;
                        this.stripe_id = data.id;
                        
                        try {
                            // charge customer
                            response = await axios.post('/stripe/customer/charge', {amount: amount});
                            console.log('customer.charge');
                            Vue.toasted.success('Payment Sent!');
                        } catch (error) {
                            error = error.response.data;
                            Vue.toasted.error(error.message);
                        }
                    } catch (error) {
                        error = error.response.data;
                        Vue.toasted.error(error.message);
                    }
                }
            } else {
                // we have a stripe id for this customer we can charge them with it
                try {
                    // charge customer
                    let response = await axios.post('/stripe/customer/charge', {amount: amount});
                    console.log('customer charge stripe id exists');
                    Vue.toasted.success('Payment Sent!');
                } catch (error) {
                    error = error.response.data;
                    Vue.toasted.error(error.message);
                }
            }
        },
    },
    mounted() {
        this.stripe_id = Spark.state.user.stripe_id;

        this.stripe = Stripe(this.stripeKey);
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