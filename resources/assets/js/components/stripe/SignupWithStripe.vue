<template>
    <form id="payment-form" class="w-full">
        <div class="flex flex-col">
            <v-banner single-line
                      v-if="paymentSucceeded"
            >
                Payment Succeeded!
            </v-banner>
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
        <v-btn
                :loading="submitted"
                color="primary"
                @click="submit()">Pay
        </v-btn>
        <div style="clear:both;"></div>
    </form>
</template>

<script>
    import {mapState} from 'vuex'

    let stripe = Stripe(`pk_test_iAX3DPtpLj5RiG3FCexe1r0Z`)
    let elements = stripe.elements()
    let card = undefined

    export default {
        data() {
            return {
                submitted: false,
                stripe: {},
                card: {},
                signup: false,
                paymentSucceeded: false,
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
            bid: Object,
            clientSecret: String
        },
        methods: {
            submit() {
                this.submitted = true;
                console.log('excludedTaskIds', this.excludedTaskIds)
                stripe.confirmCardPayment(this.clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: Spark.state.user.name,
                            phone: Spark.state.user.phone,
                            email: Spark.state.user.email,
                            address: {
                                city: Spark.state.user.customer.location.city,
                                country: Spark.state.user.customer.location.country,
                                line1: Spark.state.user.customer.location.address_line_1,
                                line2: Spark.state.user.customer.location.address_line_2,
                                postal_code: Spark.state.user.customer.location.zip,
                                state: Spark.state.user.customer.location.state
                            }
                        }
                    }
                }).then(function(result) {
                    this.submitted = false;
                    if (result.error) {
                        // Show error to your customer (e.g., insufficient funds)
                        console.log(result.error.message);
                    } else {
                        // The payment has been processed!
                        if (result.paymentIntent.status === 'succeeded') {

                            let paymentSucceeded = true;

                            // axios.post('stripe/charge', {
                            //     token: result,
                            //     jobId: this.bid.id,
                            //     excluded: this.excludedTaskIds
                            // })
                            // Show a success message to your customer
                            // There's a risk of the customer closing the window before callback
                            // execution. Set up a webhook or plugin to listen for the
                            // payment_intent.succeeded event that handles any business critical
                            // post-payment actions.
                        }
                    }
                }.bind(this));
            },
        },

        mounted() {

            let  style = {
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

            if (!card) {
                // Create an instance of the card Element
                card = elements.create('card', style)
                // Add an instance of the card Element into the `card-element` <div>
                card.mount("#card-element")
                // card.mount(this.$refs.card)
                card.addEventListener('change', function(event) {
                    var displayError = document.getElementById('card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }
                });
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