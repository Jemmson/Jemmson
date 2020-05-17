<template>
    <v-container>
        <v-card>
            <v-card-title class="w-break">Manage Subscription</v-card-title>
            <v-card-actions
                    class="flex"
            >
                <v-spacer></v-spacer>
                <v-icon
                        class="nav-btn-position"
                        color="success"
                        @click="showSubscription()"
                >mdi-floor-plan
                </v-icon>
                <div class="nav-icon-label nav-icon-label-selected">
                    Subscription
                </div>
                <v-spacer></v-spacer>
                <v-icon
                        class="nav-btn-position"
                        color="red"
                        @click="openCancelConfirmationDialog()"
                >mdi-cancel
                </v-icon>
                <div class="nav-icon-label" style="color: red">
                    Cancel Subscription
                </div>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>

        <v-card v-show="show.cardProcessingErrors" class="mt-1rem">
            <v-card-title
                    class="w-break error--text">Errors
            </v-card-title>
            <v-card-text class="error--text">{{ cardProcessingErrorMessage }}</v-card-text>
        </v-card>

        <v-card v-show="show.cardProcessingSuccess" class="mt-1rem">
            <v-card-title
                    style="color: mediumseagreen">Success
            </v-card-title>
            <v-card-text style="color: mediumseagreen">{{ cardProcessingSuccessMessage }}</v-card-text>
        </v-card>

        <v-card v-show="show.creditCardDialog" class="mt-1rem">
            <v-card-title class="w-break">
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
                           text
                           :data-secret="paymentIntent.clientSecret">
                        Update Payment Method
                    </v-btn>

                    <v-btn
                            @click="cancelAddingPaymentMethod()"
                            color="error"
                            text
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
                v-show="show.subscriptions"
        >
            <v-card class="mb-4 mt-1"
                    :disabled="disablePlans"
            >
                <div class="flex justify-content-between">
                    <v-card-title class="w-break">
                        Monthly Plan
                    </v-card-title>
                    <div
                            class="green--text m-1rem"
                            v-show="getSelectedPlan() === 'Monthly Plan'">Current Plan
                    </div>
                </div>
                <v-card-text>
                    50$ per month + $1 per job
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            :loading="loadingPlan"
                            @click="selectPlan('Monthly Plan')"
                            color="primary"
                    >Select
                    </v-btn>
                </v-card-actions>
            </v-card>

            <v-card
                    :disabled="disablePlans"
            >
                <div class="flex justify-content-between">
                    <v-card-title class="w-break">
                        Yearly Plan
                    </v-card-title>
                    <div
                            class="green--text m-1rem"
                            v-show="getSelectedPlan() === 'Yearly Plan'">Current Plan
                    </div>
                </div>
                <v-card-text>
                    <div>540$ per year + $1 per job</div>
                    <div><small>Same as -> 45$ per month + $1 per job</small></div>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            @click="selectPlan('Yearly Plan')"
                            color="primary"
                    >Select
                    </v-btn>
                </v-card-actions>
            </v-card>
        </section>

        <v-dialog
                v-model="show.confirmCancelationModal"
                width="500"
        >
            <v-card>
                <v-card-title class="w-break uppercase">Do You wish To Really Cancel?</v-card-title>
                <v-card-actions>
                    <v-btn
                            text
                            color="error"
                            @click="closeCancelation()"
                    >Cancel
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            :loading="loadingCancelButton"
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

    let stripe = Stripe(Spark.stripeKey);
    let elements = stripe.elements();
    let card = undefined;

    export default {
        name: "Subscription",
        data() {
            return {
                show: {
                    subscriptions: false,
                    confirmCancelationModal: false,
                    createCreditCard: true,
                    creditCardDialog: false,
                    cardProcessingErrors: false,
                    cardProcessingSuccess: false,
                },
                loadingCancelButton: false,
                cardProcessingErrorMessage: null,
                cardProcessingSuccessMessage: null,
                theSelectedPlan: null,
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
                return Spark.state.user.customer_stripe_id === null
            },

            selectPlan(plan) {
                //    check if user is a stripe customer and open a dialog if they are not
                if (Spark.state.user.subscriptions.length === 0) {
                    this.theSelectedPlan = plan;
                    this.loadingPlan = true;
                    this.disablePlans = true;
                    this.getPaymentIntent();
                } else {
                    if (plan !== this.currentPlan) {
                        this.changePlan(plan)
                    } else {
                        this.show.cardProcessingErrors = true;
                        this.show.cardProcessingSuccess = false;
                        this.cardProcessingErrorMessage = 'You are currently enrolled in this plan';
                    }
                }

            },

            async changePlan(newPlan) {
                this.submitted = true;
                this.show.cardProcessingErrors = false;
                this.show.cardProcessingSuccess = false;
                this.disablePlans = true;

                const {data} = await axios.post('subscription/changePlan', {
                    selectedPlan: newPlan
                });

                if (data.error) {
                    console.log('subscription Payment Error', data.error);
                    this.show.cardProcessingErrors = true;
                    this.cardProcessingErrorMessage = data.message;
                    this.disablePlans = false;
                } else {
                    console.log('subscription successful', data.success);
                    this.show.cardProcessingSuccess = true;
                    this.cardProcessingSuccessMessage = data.success;
                    this.currentPlan = data.currentPlan;
                    this.disablePlans = false;
                    Bus.$emit('updateUser');
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
                this.show.cardProcessingErrors = false;
                this.show.cardProcessingSuccess = false;

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
                    this.show.cardProcessingErrors = true;
                    this.cardProcessingErrorMessage = error.message;
                    this.disablePlans = false;
                    console.log('stripe error', error)
                } else {
                    // The card has been verified successfully...
                    this.sendPaymentMethodToServer(setupIntent.payment_method)
                }

                this.show.creditCardDialog = false
                this.submitted = false;

            },

            async sendPaymentMethodToServer(paymentMethod) {
                const {data} = await axios.post('subscriptions/setPaymentMethod', {
                    paymentMethod,
                    selectedPlan: this.theSelectedPlan
                });

                if (data.error) {
                    console.log('subscription Payment Error', data.error);
                    this.show.cardProcessingErrors = true;
                    this.cardProcessingErrorMessage = data.message;
                    this.disablePlans = false;
                } else {
                    console.log('subscription successful', data.success);
                    this.show.cardProcessingSuccess = true;
                    this.cardProcessingSuccessMessage = data.success;
                    this.currentPlan = data.currentPlan;
                    this.disablePlans = false;
                    Bus.$emit('updateUser');
                }

                this.disablePlans = false;
            },

            closeCreditCardWindow() {
                this.show.creditCardDialog = false;
            },

            openCreditCardWindow() {
                this.show.creditCardDialog = true;
            },

            getSelectedPlan() {
                if (this.currentPlan === 'canceled') {
                    return null;
                } else if (this.currentPlan === null && Spark.state.user.plan !== null) {
                    this.currentPlan = Spark.state.user.plan;
                    return Spark.state.user.plan;
                } else if (this.currentPlan !== null) {
                    return this.currentPlan;
                } else {
                    return null;
                }
            },

            closeCancelation() {
                this.show.confirmCancelationModal = false;
            },

            confirmCancelation() {
                this.cancelSubscription();
            },

            async cancelSubscription() {
                this.loadingCancelButton = true;
                const {data} = await axios.post('subscription/cancelPlan');

                if (data.error) {
                    console.log('cancelation error', data.error);
                    this.show.confirmCancelationModal = false;
                    this.show.cardProcessingErrors = true;
                    this.cardProcessingErrorMessage = data.message;
                    this.loadingCancelButton = false;
                } else {
                    console.log('cancelation successful', data.success);
                    this.show.cardProcessingSuccess = true;
                    this.cardProcessingSuccessMessage = data.success;
                    this.show.confirmCancelationModal = false;
                    Bus.$emit('updateUser');
                    this.currentPlan = 'canceled';
                    this.loadingCancelButton = false;
                }
            },

            openCancelConfirmationDialog() {

                if (this.currentPlan === null && Spark.state.user.plan === null) {
                    this.show.cardProcessingErrors = true;
                    this.cardProcessingErrorMessage = 'You are not currently enrolled in a plan. You cannot cancel at this time.';
                } else {
                    this.show.confirmCancelationModal = true;
                }
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

            this.currentPlan = Spark.state.user.plan;
        }
    }
</script>

<style scoped>
    .subscription-btn-width {
        width: 49% !important;
    }
</style>