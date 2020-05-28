<template>
    <div>
        <v-dialog
                width="500"
                scrollable
                v-model="showModal"
        >
            <!--        list of available stripe payment methods -->
            <!--        change payment method -->
            <!--        submit selected payment method -->

            <div style="display: none">{{ show }}</div>
            <div style="display: none">{{ attachedCards }}</div>

            <div
                    class="flex flex-column gray-background"
            >
                <div
                        class="error banner"
                        v-if="errors"
                >
                    <h3 class="text-center">{{ errorMessage }}</h3>
                </div>

                <v-btn
                        v-if="needsPayment"
                        color="primary"
                        outlined
                        style="background-color: white"
                        text
                        class="m-1rem"
                        @click="submit()"
                        :loading="submitted"
                >
                    Pay
                </v-btn>

                <v-btn
                        v-if="!needsPayment"
                        color="primary"
                        outlined
                        style="background-color: white"
                        text
                        class="m-1rem"
                        :disabled="true"
                >
                    Pay
                </v-btn>

                <credit-card
                        @selected-card="selectCard($event)"
                        :cards="allCards"
                        @close-modal="close()"
                        @disable-pay-buttons="disablePayButtons()"
                >

                </credit-card>

                <div class="flex flex-col">
                    <v-btn
                            color="primary"
                            class="m-1rem"
                            style="background-color: white"
                            text
                            outlined
                            @click="addNewCard()"
                    >
                        <div class="flex space-between">
                            <div class="ml-half-rem">Add A New Card</div>
                            <div class="mr-half-rem"> ></div>
                        </div>
                    </v-btn>


                    <v-btn
                            v-if="needsPayment"
                            color="primary"
                            outlined
                            style="background-color: white"
                            text
                            class="m-1rem"
                            @click="submit()"
                            :loading="submitted"
                    >
                        Pay
                    </v-btn>

                    <v-btn
                            v-if="!needsPayment"
                            color="primary"
                            outlined
                            style="background-color: white"
                            text
                            class="m-1rem"
                            :disabled="true"
                    >
                        Pay
                    </v-btn>

                </div>


                <v-btn
                        primary
                        text
                        @click="close()"
                >CLOSE
                </v-btn>

            </div>

        </v-dialog>
        <v-dialog
                width="500"
                scrollable
                v-model="newCard.showNewCardModal"
        >
            <div
                    class="flex flex-column gray-background"
            >
                <v-card
                        class="margins-1rem p-1rem"
                >

                    <div
                            style="background-color: green"
                            class="banner"
                            v-if="newCard.cardAdded"
                    >
                        <h3 class="text-center">Card Has Been Added!</h3>
                    </div>
                    <div
                            class="error banner"
                            v-if="newCard.cardError"
                    >
                        <h3 class="text-center">{{ newCard.errorAddCardMessage }}</h3>
                    </div>

                    <h5
                            class="f-size-13pt f-bold"
                    >Please enter your credit card info below</h5>
                    <hr>
                    <v-checkbox
                            v-model="newCard.useNameOnAccount"
                            label="Use name on account"
                            @change="setName()"
                    ></v-checkbox>

                    <v-text-field
                            v-model="newCard.name"
                            label="Name on card"
                    ></v-text-field>

                    <div class="flex">
                        <v-text-field
                                class="w-40"
                                label="Card number"
                                v-mask="ccmask"
                                v-model="newCard.ccnumber"
                        >
                        </v-text-field>
                        <div class="w-10"></div>
                        <v-text-field
                                class="w-10"
                                label="cvc"
                                v-mask="cvcmask"
                                v-model="newCard.cvcnumber"
                        >
                        </v-text-field>
                    </div>

                    <h5 class="f-size-13pt f-bold"
                    >Expiration date</h5>

                    <div class="flex justify-between">
                        <v-autocomplete
                                class="w-40"
                                v-model="newCard.month"
                                :items="newCard.months"
                                label="Month"
                        >
                        </v-autocomplete>
                        <div class="w-10"></div>
                        <v-autocomplete
                                class="w-40"
                                v-model="newCard.year"
                                :items="newCard.years"
                                label="Year"
                        >
                        </v-autocomplete>
                    </div>

                    <v-card-actions>
                        <v-btn
                                color="primary"
                                outlined
                                style="background-color: white"
                                text
                                class="m-1rem"
                                @click="add()"
                                :loading="newCard.loading"
                        >
                            Add Card
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                                color="red"
                                outlined
                                style="background-color: white"
                                text
                                :disabled="newCard.disableCancel"
                                class="m-1rem"
                                @click="cancelNewCard()"
                        >
                            Cancel
                        </v-btn>
                    </v-card-actions>
                </v-card>

            </div>
        </v-dialog>
    </div>
</template>

<script>

    import CreditCard from './CreditCard.vue';

    export default {
        name: 'StripeCreditCardModal',
        components: {
            CreditCard
        },
        data() {
            return {
                stripe: null,
                statementDescriptor: 'Thank you for your payment!',
                paymentSucceeded: false,
                needsPayment: true,
                errorMessage: '',
                errors: false,
                submitted: false,
                selectedCard: {},
                showModal: false,
                defaultCard: '4243',
                rules: [v => v.length <= 25 || 'Max 25 characters'],
                newCard: {
                    cardAdded: false,
                    cardError: false,
                    ccnumber: null,
                    cvcnumber: null,
                    disableCancel: false,
                    errorAddCardMessage: null,
                    loading: false,
                    month: null,
                    months: [
                        '01',
                        '02',
                        '03',
                        '04',
                        '05',
                        '06',
                        '07',
                        '08',
                        '09',
                        '10',
                        '11',
                        '12',
                    ],
                    name: null,
                    showNewCardModal: false,
                    useNameOnAccount: false,
                    year: null,
                    years: [
                        '2020',
                        '2021',
                        '2022',
                        '2023',
                        '2024',
                        '2025',
                        '2026',
                        '2027',
                        '2028',
                        '2029',
                        '2030',
                    ]
                },
                ccmask: '####-####-####-####',
                cvcmask: '###',
                allCards: []
            }
        },
        props: {
            open: Boolean,
            clientSecret: String,
            paymentMethods: Array
        },
        watch: {
            showModal(val) {
                if (!val) {
                    this.$emit('closeModal');
                }
            }
        },
        computed: {
            show: function () {
                this.showModal = this.open;
                this.needsPayment = true;
            },
            attachedCards() {
                this.allCards = this.paymentMethods;
            }
        },
        methods: {
            // rules(){
            //     // cannot use the special characters <, >, ', ", or *, and must not consist solely of numbers
            //     return [
            //         v => v.length <= 25 || 'Max 25 characters'
            //     ]
            // },
            setName() {
                this.newCard.name = Spark.state.user.name;
            },
            addNewCard() {
                this.showModal = false;
                this.newCard.showNewCardModal = true;
            },
            cancelNewCard() {
                this.closeNewCardModal();
            },

            closeNewCardModal() {
                this.showModal = true;
                this.newCard.showNewCardModal = false;
            },
            async add() {
                let form = this.getForm();
                if (form) {
                    this.newCard.loading = true;
                    this.newCard.disableCancel = true;
                    const {data} = await axios.post('/stripe/customer/newcard', {
                        form
                    });

                    if (data.error) {
                        console.log(data.error);
                        this.newCard.cardError = true;
                        this.newCard.cardAdded = false;
                        this.newCard.errorAddCardMessage = data.error;
                    } else {
                        this.allCards = data.data;
                        this.closeNewCardModal();
                        this.newCard.cardError = false;
                        this.newCard.cardAdded = true;
                    }

                    this.newCard.loading = false;
                    this.newCard.disableCancel = false;
                }
            },
            getForm() {
                if (
                    this.newCard.month
                    && this.newCard.year
                    && this.newCard.month
                    && this.newCard.name
                    && this.newCard.ccnumber
                    && this.newCard.cvcnumber
                ) {
                    let form = {};
                    form.month = this.newCard.month;
                    form.year = this.newCard.year;
                    form.ccnumber = this.newCard.ccnumber;
                    form.cvcnumber = this.newCard.cvcnumber;
                    form.name = this.newCard.name;
                    return form;
                } else {
                    return null;
                }
            },
            submit() {
                if (Object.values(this.selectedCard).length > 0) {
                    this.submitted = true;
                    this.stripe.confirmCardPayment(this.clientSecret, {
                        payment_method: this.selectedCard.id,
                        receipt_email: Spark.state.user.email
                    }).then(function (result) {
                        this.submitted = false;
                        if (result.error) {
                            // Show error to your customer (e.g., insufficient funds)
                            console.log(result.error.message);
                            this.errorMessage = result.error.message;
                            this.errors = true;
                        } else {
                            // The payment has been processed!
                            if (result.paymentIntent.status === 'succeeded') {
                                this.needsPayment = false;
                                this.showModal = false;
                                this.$emit('paid');
                            }
                        }
                    }.bind(this));
                } else {
                    this.errorMessage = "Please select a card";
                    this.submitted = false;
                    this.errors = true;
                }
            },
            disablePayButtons(){
                this.needsPayment = false
            },
            close () {
                this.showModal = false;
                this.$emit('close-stripe-cc-modal')
            },
            selectCard(card) {
                this.selectedCard = card;
            },
            transformCard(card) {
                let transformedCard = {};
                transformedCard.address_city = this.selectedCard.address_city;
                transformedCard.address_country = this.selectedCard.address_country;
                transformedCard.address_line1 = this.selectedCard.address_line1;
                transformedCard.address_line1_check = this.selectedCard.address_line1_check;
                transformedCard.address_line2 = this.selectedCard.address_line2;
                transformedCard.address_state = this.selectedCard.address_state;
                transformedCard.address_zip = this.selectedCard.address_zip;
                transformedCard.address_zip_check = this.selectedCard.address_zip_check;
                transformedCard.cvc_check = this.selectedCard.cvc_check;
                transformedCard.dynamic_last4 = this.selectedCard.dynamic_last4;
                transformedCard.exp_month = this.selectedCard.exp_month;
                transformedCard.exp_year = this.selectedCard.exp_year;
                transformedCard.metadata = this.selectedCard.metadata;
                transformedCard.tokenization_method = this.selectedCard.tokenization_method;

                return transformedCard;
            }
        },
        mounted() {
            this.stripe = Stripe(`pk_test_iAX3DPtpLj5RiG3FCexe1r0Z`);
        }
    }
</script>

<style scoped>
    .gray-background {
        background-color: lightgray
    }

    .banner {
        margin: 1rem;
        border-radius: 8px;
        padding: .75rem 0 .5rem 0;
        width: 92%
    }

    .error {
        background-color: #ff5252 !important;
        border-color: #ff5252 !important;
    }

</style>