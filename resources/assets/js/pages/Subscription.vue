<template>
    <v-container>
        <v-card>
            <v-card-actions
                    class="flex flex-col"
            >
                <div class="flex justify-content-around w-full">
                    <v-btn
                            class="nav-btn-position subscription-btn-width"
                            @click="showSubscription()"
                    >Plans
                    </v-btn>
                    <v-btn
                            class="nav-btn-position subscription-btn-width"
                            @click="openCancelConfirmationDialog()"
                    >Cancel
                    </v-btn>
                </div>
            </v-card-actions>
        </v-card>


        <v-card v-show="show.creditCardDialog" class="mt-1">
            <v-card-title>
                Add A Payment Method
            </v-card-title>
            <v-card-text>
                <v-text-field
                        style="margin-bottom: -1.5rem"
                        id="cardHolderName"
                        type="text"
                        v-model="cardHolderName"
                        label="Name On Card"
                />

                <input id="card-holder-name" type="text">

                <!-- Stripe Elements Placeholder -->
                <div id="card-element"></div>

                <div class="flex justify-content-between">
                    <v-btn id="card-button"
                           @click="submit()"
                           color="primary"
                           :loading="submitted"
                           class="mt-4 v-btn"
                           :data-secret="paymentIntent.clientSecret">
                        Update Payment Method
                    </v-btn>

                    <v-btn
                            @click="cancelAddingPaymentMethod()"
                            color="error"
                            class="mt-4 v-btn"
                    >
                        Cancel
                    </v-btn>
                </div>

                <!--                class="mt-4 v-btn v-btn&#45;&#45;contained theme&#45;&#45;light v-size&#45;&#45;default primary"-->

            </v-card-text>

            <v-card-actions>
            </v-card-actions>
        </v-card>

        <section
                v-if="show.subscriptions"
        >
            <v-card class="mb-4 mt-1"
                    :disabled="disablePlans"
            >
                <div class="flex justify-content-between">
                    <v-card-title>
                        Monthly Plan
                    </v-card-title>
                    <div
                            class="green--text m-1rem"
                            v-if="selectedPlan() === 'monthly'">Current Plan
                    </div>
                </div>
                <v-card-text>
                    50$ per month + $1 per job
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            :loading="loadingPlan"
                            @click="selectPlan('monthly')"
                            color="primary"
                    >Select
                    </v-btn>
                </v-card-actions>
            </v-card>

            <v-card
                    :disabled="disablePlans"
            >
                <div class="flex justify-content-between">
                    <v-card-title>
                        Yearly Plan
                    </v-card-title>
                    <div
                            class="green--text m-1rem"
                            v-if="selectedPlan() === 'yearly'">Current Plan
                    </div>
                </div>
                <v-card-text>
                    <div>540$ per year + $1 per job</div>
                    <div><small>Same as -> 45$ per month + $1 per job</small></div>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            @click="selectPlan('yearly')"
                            color="primary"
                    >Select
                    </v-btn>
                </v-card-actions>
            </v-card>
        </section>

        <v-dialog
                v-model="show.confirmCancelationModal"
                width="440"
        >
            <v-card>
                <v-card-title>Do You wish To Really Cancel?</v-card-title>
                <v-card-actions>
                    <v-btn
                            text
                            color="error"
                            @click="closeCancelation()"
                    >Cancel
                    </v-btn>
                    <v-btn
                            text
                            color="primary"
                            @click="confirmCancelation()"
                    >Confirm
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </v-container>
</template>

<script>

    export default {
        name: "Subscription",
        data() {
            return {
                show: {
                    subscriptions: false,
                    confirmCancelationModal: false,
                    createCreditCard: true,
                    creditCardDialog: false
                },
                disablePlans: false,
                cardHolderName: null,
                paymentIntent: {
                    clientSecret: null
                },
                submitted: false,
                currentPlan: null,
                loadingPlan: false
            }
        },
        methods: {

            cancelAddingPaymentMethod() {
                this.loadingPlan = false;
                this.disablePlans = false;
                this.show.creditCardDialog = false;
            },

            hasStripe() {
                return Spark.state.user.stripe_id === null
            },

            selectPlan(plan) {
                //    check if user is a stripe customer and open a dialog if they are not
                if (Spark.state.user.subscriptions.length === 0) {
                    this.loadingPlan = true;
                    this.disablePlans = true;
                    this.getPaymentIntent();
                }

            },

            async getPaymentIntent() {
                const {data} = await axios.get('subscription/getPaymentIntent')

                if (data.error) {
                    console.log(data.error);
                } else {
                    this.show.creditCardDialog = true;
                    this.paymentIntent.clientSecret = data.client_secret;
                    console.log('success', data.data)
                }
                this.loadingPlan = false;
            },

            async submit() {
                this.submitted = true;

                const {setupIntent, error} = await stripe.confirmCardSetup(
                    this.paymentIntent.clientSecret, {
                        payment_method: {
                            card: card,
                            billing_details: {
                                name: this.cardHolderName,
                                phone: Spark.state.user.phone,
                                email: Spark.state.user.email
                            },
                            metadata: {
                                userId: Spark.state.user.id
                            }
                        }
                    }
                );

                if (error) {
                    // Display "error.message" to the user...
                } else {
                    // The card has been verified successfully...
                    this.sendPaymentMethodToServer(setupIntent.payment_method)
                }

                this.show.creditCardDialog = false

                this.submitted = false;

            },

            async sendPaymentMethodToServer(paymentMethod) {
                const {data} = await axios.post('subscriptions/setPaymentMethod', {
                    paymentMethod
                })

                if (data.error) {
                    // Display "error.message" to the user...
                } else {
                    // The card has been verified successfully...
                    this.sendPaymentIntentToServer()
                }
            },

            closeCreditCardWindow() {
                this.show.creditCardDialog = false;
            },

            openCreditCardWindow() {
                this.show.creditCardDialog = true;
            },

            selectedPlan() {
                return Spark.state.user.plan
            },

            closeCancelation() {
                this.show.confirmCancelationModal = false;
            },

            confirmCancelation() {
                this.show.confirmCancelationModal = false;
            },

            openCancelConfirmationDialog() {
                this.show.confirmCancelationModal = true;
            },

            showSubscription() {
                this.show.subscriptions = true;
            },

            hideAllSections() {
                this.show.confirmCancelationModal = false;
                this.show.subscriptions = false;
            },
        },
        mounted() {

            let stripe = Stripe(Spark.stripeKey);
            let elements = stripe.elements();
            let card = undefined;


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

<style scoped>
    .subscription-btn-width {
        width: 49% !important;
    }
</style>