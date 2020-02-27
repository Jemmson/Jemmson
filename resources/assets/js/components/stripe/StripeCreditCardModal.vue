<template>
    <v-dialog
            width="500"
            v-model="showModal"
    >
        <!--        list of available stripe payment methods -->
        <!--        change payment method -->
        <!--        submit selected payment method -->

        <div style="display: none">{{ show }}</div>

        <v-banner single-line
                  v-if="paymentSucceeded"
        >
            Payment Succeeded!
        </v-banner>

        <div
                class="flex flex-column gray-background"
        >
            <v-btn
                    color="primary"
                    style="background-color: white"
                    text
                    class="m-1rem"
                    outlined
                    @click="submit()"
                    :loading="submitted"
            >
                Pay
            </v-btn>

            <credit-card
                    @selected-card="selectCard($event)"
                    :cards="allCards"
            >

            </credit-card>
<!--            <div class="textfield-spacing">-->
<!--                <v-text-field-->
<!--                        v-model="statementDescriptor"-->
<!--                        label="Payment Description"-->
<!--                        counter-->
<!--                        :counter="22"-->
<!--                        :rules="rules"-->
<!--                ></v-text-field>-->
<!--            </div>-->

            <!--            v-model="selected[i.id]"-->


            <div class="flex flex-col">
                <v-btn
                        color="primary"
                        class="m-1rem"
                        style="background-color: white"
                        text
                        outlined
                >
                    <div class="flex space-between">
                        <div class="ml-half-rem">Add A New Card</div>
                        <div class="mr-half-rem"> ></div>
                    </div>
                </v-btn>


                <v-btn
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
            </div>


            <v-btn
                    primary
                    text
                    @click="close()"
            >CLOSE
            </v-btn>

        </div>

    </v-dialog>
</template>

<script>

    import CreditCard from './CreditCard.vue';

    let stripe = Stripe(`pk_test_iAX3DPtpLj5RiG3FCexe1r0Z`)

    export default {
        name: 'StripeCreditCardModal',
        components: {
            CreditCard
        },
        data() {
            return {
                statementDescriptor: 'Thank you for your payment!',
                paymentSucceeded: false,
                submitted: false,
                selectedCard: {},
                showModal: false,
                defaultCard: '4243',
                rules: [v => v.length <= 25 || 'Max 25 characters'],
                allCards: [
                    {
                        "id": "pm_1GGW5d2eZvKYlo2C0pSNb4aQ",
                        "object": "payment_method",
                        "billing_details": {
                            "address": {
                                "city": null,
                                "country": null,
                                "line1": null,
                                "line2": null,
                                "postal_code": null,
                                "state": null
                            },
                            "email": null,
                            "name": null,
                            "phone": null
                        },
                        "card": {
                            "brand": "visa",
                            "checks": {
                                "address_line1_check": null,
                                "address_postal_code_check": null,
                                "cvc_check": null
                            },
                            "country": "US",
                            "exp_month": 8,
                            "exp_year": 2021,
                            "fingerprint": "Xt5EWLLDS7FJjR1c",
                            "funding": "credit",
                            "generated_from": null,
                            "last4": "4242",
                            "three_d_secure_usage": {
                                "supported": true
                            },
                            "wallet": null
                        },
                        "created": 1582747361,
                        "customer": null,
                        "livemode": false,
                        "metadata": {},
                        "type": "card"
                    },
                    {
                        "id": "pm_1GGW5d2eZvKYlo2C0pSNb4aQ",
                        "object": "payment_method",
                        "billing_details": {
                            "address": {
                                "city": null,
                                "country": null,
                                "line1": null,
                                "line2": null,
                                "postal_code": null,
                                "state": null
                            },
                            "email": null,
                            "name": null,
                            "phone": null
                        },
                        "card": {
                            "brand": "visa",
                            "checks": {
                                "address_line1_check": null,
                                "address_postal_code_check": null,
                                "cvc_check": null
                            },
                            "country": "US",
                            "exp_month": 8,
                            "exp_year": 2021,
                            "fingerprint": "Xt5EWLLDS7FJjR1c",
                            "funding": "credit",
                            "generated_from": null,
                            "last4": "4243",
                            "three_d_secure_usage": {
                                "supported": true
                            },
                            "wallet": null
                        },
                        "created": 1582747361,
                        "customer": null,
                        "livemode": false,
                        "metadata": {},
                        "type": "card"
                    },
                    {
                        "id": "pm_1GGW5d2eZvKYlo2C0pSNb4aQ",
                        "object": "payment_method",
                        "billing_details": {
                            "address": {
                                "city": null,
                                "country": null,
                                "line1": null,
                                "line2": null,
                                "postal_code": null,
                                "state": null
                            },
                            "email": null,
                            "name": null,
                            "phone": null
                        },
                        "card": {
                            "brand": "visa",
                            "checks": {
                                "address_line1_check": null,
                                "address_postal_code_check": null,
                                "cvc_check": null
                            },
                            "country": "US",
                            "exp_month": 8,
                            "exp_year": 2021,
                            "fingerprint": "Xt5EWLLDS7FJjR1c",
                            "funding": "credit",
                            "generated_from": null,
                            "last4": "4244",
                            "three_d_secure_usage": {
                                "supported": true
                            },
                            "wallet": null
                        },
                        "created": 1582747361,
                        "customer": null,
                        "livemode": false,
                        "metadata": {},
                        "type": "card"
                    }
                ]
            }
        },
        props: {
            open: Boolean,
            clientSecret: String
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
            }
        },
        methods: {
            // rules(){
            //     // cannot use the special characters <, >, ', ", or *, and must not consist solely of numbers
            //     return [
            //         v => v.length <= 25 || 'Max 25 characters'
            //     ]
            // },
            submit() {
                this.submitted = true;
                console.log('excludedTaskIds', this.excludedTaskIds)
                stripe.confirmCardPayment(this.clientSecret, {
                    payment_method: this.selectedCard.id,
                    receipt_email: Spark.state.user.email
                }).then(function (result) {
                    this.submitted = false;
                    if (result.error) {
                        // Show error to your customer (e.g., insufficient funds)
                        console.log(result.error.message);
                    } else {
                        // The payment has been processed!
                        if (result.paymentIntent.status === 'succeeded') {

                            this.paymentSucceeded = true;

                        }
                    }
                }.bind(this));
            },
            close: function () {
                this.showModal = false;
                this.$emit('closeModal')
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
        }
    }
</script>

<style scoped>
    .gray-background {
        background-color: lightgray
    }
</style>