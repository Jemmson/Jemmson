<template>
    <form id="payment-form" class="w-full">
        <div class="flex flex-col">
            <div
                    style="background-color: green"
                    class="w-full banner"
                    v-if="paymentSucceeded"
            >
                <h3 class="text-center">Payment Succeeded!</h3>
            </div>
            <div
                    class="error w-full banner"
                    v-if="errors"
            >
                <h3 class="text-center">Errors!</h3>
            </div>
            <label for="card-element">
                Sign Up With A Credit or Debit Card
            </label>
            <div id="card-element" ref="card">
                <!-- a Stripe Element will be inserted here. -->
            </div>

            <!-- Used to display Element errors -->
            <div id="card-errors" role="alert"></div>
        </div>
        <div class="textfield-spacing">
            <v-text-field
                    v-model="statementDescriptor"
                    label="Payment Description"
                    counter
                    :hint="hint()"
                    :counter="22"
                    :rules="rules"
            ></v-text-field>
        </div>
        <br>
        <v-btn
                v-if="needsPayment"
                :loading="submitted"
                color="primary"
                @click="submit()">
            Pay
        </v-btn>
        <v-btn
                v-if="!needsPayment"
                :disabled="true"
                color="primary">
            Pay
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
                needsPayment: true,
                stripe: {},
                card: {},
                errors: false,
                rules: [v => v.length <= 25 || 'Max 25 characters'],
                signup: false,
                paymentSucceeded: false,
                statementDescriptor: 'Thank you for payment!',
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
            // rules() {
            //     // cannot use the special characters <, >, ', ", or *, and must not consist solely of numbers
            //     return [v => v.length <= 25 || 'Max 25 characters']
            // },
            hint() {
                return `<, >, ', ", or *, or only numbers are not allowed`;
            },
            canPay() {
                return this.statementDescriptor.length < 23
                    && document.getElementById('card-errors').innerText === ''
                // && document.getElementsByName('cardnumber')[0].value.length === 19
                // && document.getElementsByName('exp-date')[0].value.length === 7
                // && document.getElementsByName('cvc')[0].value.length === 3
                // && document.getElementsByName('postal')[0].value.length >= 5;
            },
            submit() {
                this.submitted = true;
                console.log('excludedTaskIds', this.excludedTaskIds)

                if (this.canPay()) {
                    this.errors = false;
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
                        },
                        receipt_email: Spark.state.user.email,
                        save_payment_method: true,
                        setup_future_usage: 'off_session'
                    }).then(function (result) {
                        this.submitted = false;
                        if (result.error) {
                            // Show error to your customer (e.g., insufficient funds)
                            console.log(result.error.message);
                        } else {
                            // The payment has been processed!
                            if (result.paymentIntent.status === 'succeeded') {
                                this.paymentSucceeded = true;
                                this.needsPayment = false;
                            }
                        }
                    }.bind(this));
                } else {
                    this.submitted = false;
                    this.errors = true;
                }
            },
        },

        mounted() {

            let style = {
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
                card.addEventListener('change', function (event) {
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

    .error {
        background-color: #ff5252 !important;
        border-color: #ff5252 !important;
    }

    .textfield-spacing {
        margin: 1rem 0 1rem 0;
    }

    .banner {
        margin: 0 0 1rem 0;
        border-radius: 8px;
        padding: .75rem 0 .5rem 0
    }

    .error-exists {
        background-color: red;
    }

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