<template>
    <div>

        <v-card v-show="show.cardProcessingErrors">
            <v-card-title>Errors</v-card-title>
            <v-card-subtitle>{{ cardProcessingErrorMessage }}</v-card-subtitle>
        </v-card>

        <v-card v-show="show.cardProcessingSuccess">
            <v-card-title>Success</v-card-title>
            <v-card-subtitle>{{ cardProcessingSuccessMessage }}</v-card-subtitle>
        </v-card>

        <v-card
                v-show="show.currentPaymentMethod"
                class="mt-1rem"
        >
            <v-card-title>Default Credit Card</v-card-title>

            <v-card-text>
                <div class="flex flex-col mt-1rem mb-1rem">
                    <div class="flex space-between">
                        <div class="f-bold mr-1rem capitalize">{{ getCardBrand(paymentMethod) }}</div>
                        <div>****{{ getLast4(paymentMethod) }}</div>
                    </div>
                    <div>{{ getName(paymentMethod) }}</div>
                    <div>Expires {{ getExpirationMonth(paymentMethod) }}/{{ getExpirationYear(paymentMethod) }}</div>
                </div>
            </v-card-text>

            <v-card-actions>
                <v-btn
                        color="primary"
                        text
                        @click="showUpdateCard()"
                >Edit
                </v-btn>
            </v-card-actions>
        </v-card>

        <v-card class="mt-1rem" v-show="show.editPaymentMethod">
            <v-card-title>
                Update Credit Card Info
            </v-card-title>
            <v-card-text>
                <v-text-field
                        style="margin-bottom: -1.5rem"
                        id="updateHolderName"
                        type="text"
                        v-model="cardHolderName"
                        label="Name On Card"
                />

                <input id="update-holder-name" type="text">

                <!-- Stripe Elements Placeholder -->
                <div id="update-card-element"></div>

                <!--                class="mt-4 v-btn v-btn&#45;&#45;contained theme&#45;&#45;light v-size&#45;&#45;default primary"-->

            </v-card-text>

            <v-card-actions>
                <v-btn id="update-card-button"
                       @click="submit()"
                       text
                       color="primary"
                       :loading="submitted"
                       :data-secret="paymentIntent.clientSecret">
                    Update Card
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                        color="error"
                        class="mt-4 v-btn"
                        text
                        @click="closeUpdateCard()"
                >
                    Cancel
                </v-btn>
            </v-card-actions>
        </v-card>

    </div>
</template>

<script>

    // let stripe = Stripe(Spark.stripeKey);
    // let elements = stripe.elements();
    // let card = undefined;

    export default {
        name: "ManageCreditCard",
        data() {
            return {
                show: {
                    editPaymentMethod: false,
                    currentPaymentMethod: true,
                    cardProcessingErrors: false,
                    cardProcessingSuccess: false
                },
                cardProcessingErrorMessage: '',
                cardProcessingSuccessMessage: '',
                loading: false,
                submitted: false,
                cardHolderName: null,
                paymentMethod: null,
                paymentIntent: {
                    clientSecret: null
                },
                card: {}
            }
        },
        methods: {

            closeUpdateCard() {
                this.show.editPaymentMethod = false;
                this.show.currentPaymentMethod = true;
            },

            showUpdateCard() {
                this.show.editPaymentMethod = true;
                this.show.currentPaymentMethod = false;
            },

            getCardBrand(card) {
                if (card) {
                    if (card[0].card.brand === 'mastercard') {
                        return card[0].card.brand
                    } else {
                        return card[0].card.brand + ' Card'
                    }
                }
            },

            getLast4(card) {
                if (card) {
                    if (card[0].card) {
                        return card[0].card.last4
                    }
                }
            },

            getExpirationMonth(card) {
                if (card) {
                    if (card[0].card) {
                        return card[0].card.exp_month
                    }
                }
            },

            getExpirationYear(card) {
                if (card) {
                    if (card[0].card) {
                        return card[0].card.exp_year
                    }
                }
            },

            getName(card) {
                if (card && card[0].billing_details) {
                    return card[0].billing_details.name
                }
                return Spark.state.user.name
            },

            async selectWhichCreditCardToUse() {
                const {data} =
                    await axios.get('stripe/customer/getPaymentMethods/' + Spark.state.user.stripe_id)
                if (data.error) {
                    console.log(data.error);
                } else {
                    this.paymentMethod = data.data;
                    console.log('paymentMethods', JSON.stringify(data.data))
                }
            },
            async submit() {
                this.submitted = true;
                this.show.cardProcessingErrors = false;
                this.show.cardProcessingSuccess = false;

                const {setupIntent, error} = await stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                    billing_details: {
                        name: this.cardHolderName,
                        phone: Spark.state.user.phone,
                        email: Spark.state.user.email
                    }
                });

                if (error) {
                    this.show.cardProcessingErrors = true;
                    this.cardProcessingErrorMessage = error.message;
                    console.log('stripe error', error)
                } else {
                    // The card has been verified successfully...
                    if (setupIntent) {
                        this.sendPaymentMethodToServer(setupIntent.payment_method)
                    } else {
                        console.log('payment Method has not been created');
                    }
                }

                this.show.editPaymentMethod = false
                this.show.currentPaymentMethod = true
                this.submitted = false;

            },

            async sendPaymentMethodToServer(paymentMethod) {
                const {data} = await axios.post('subscriptions/updatePaymentMethod', {
                    paymentMethod
                });

                if (data.error) {
                    console.log('Update Payment Error', data.error);
                    this.show.cardProcessingErrors = true;
                    this.cardProcessingErrorMessage = data.message;
                } else {
                    console.log('Card Update Successful', data.success);
                    this.show.cardProcessingSuccess = true;
                    this.cardProcessingSuccessMessage = data.success;
                    Bus.$emit('updateUser');
                }
            },
        },
        mounted() {
            this.selectWhichCreditCardToUse()

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
                card.mount("#update-card-element")
                // card.mount(this.$refs.card)
                card.addEventListener('change', function (event) {
                    var displayError = document.getElementById('update-card-errors');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = '';
                    }

                });

                // while(card[0] === undefined){
                //     card = document.getElementsByClassName('InputContainer');
                //     console.log('cardNum', card[0])
                // }

                // console.log('cardNum', card[0].children.cardnumber)

                // cardNum[0].children.cardnumber.value = '4242424242424242';

                // let cardNumber = document.getElementById('update-card-errors');


            }
        }
    }
</script>

<style scoped>

</style>