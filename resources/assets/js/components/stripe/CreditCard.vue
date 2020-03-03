<template>
    <div>
        <pre style="display: none">{{ getCards() }}</pre>
        <div
                v-for="(card, key) in cccards"
                :key="card.id"
        >


            <div
                    class="error banner"
                    v-if="removalError"
            >
                <h3 class="text-center">{{ errorMessage }}</h3>
            </div>

            <v-card
                    :class="selection(key) ? 'selectedFormat' : 'unSelectedFormat'"
                    class="mr-1rem ml-1rem"
                    outlined
                    :loading="removingCard"
            >
                <div class="flex align-center space-between">
                    <div class="flex align-center">
                        <div class="mr-1rem mt-1rem ml-1rem">
                            <input type="radio"
                                   name="card"
                                   :value="card"
                                   v-model="selectedCard"
                                   @change="theSelectedCard()">
                        </div>
                        <div>
                            <div class="flex flex-col mt-1rem mb-1rem">
                                <div class="flex space-between">
                                    <div class="f-bold mr-1rem capitalize">{{ getCardBrand(card) }}</div>
                                    <div>****{{ card.card.last4 }}</div>
                                </div>
                                <div>{{ getName(card) }}</div>
                                <div>Expires {{ card.card.exp_month }}/{{ card.card.exp_year }}</div>
                            </div>
                        </div>
                    </div>

                    <v-btn
                            color="primary"
                            text
                            @click="showRemove(card.id)"
                    >
                        Remove
                    </v-btn>

                </div>
                <div :id="'removeCard-' + card.id" style="display: none; background-color: white">
                    <hr>
                    <v-card-subtitle>
                        <strong
                                color="white"
                        >Are you sure you want to remove this card?</strong>
                    </v-card-subtitle>
                    <v-card-actions>
                        <v-btn
                                color="primary"
                                text
                                @click="remove(card)"
                        >
                            Yes
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                                color="warning"
                                text
                                @click="hideRemove(card.id)"
                        >
                            No
                        </v-btn>
                    </v-card-actions>
                </div>

            </v-card>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'CreditCard',
        data() {
            return {
                cccards: [],
                selectedCard: {},
                removingCard: false,
                removalError: false,
                errorMessage: ''
            }
        },
        props: {
            cards: Array,
            name: String
        },
        methods: {

            getCardBrand(card) {
                if (card.card.brand === 'mastercard') {
                    return card.card.brand
                } else {
                    return card.card.brand + ' Card'
                }
            },

            getCards() {
                this.cccards = this.cards;
            },

            showRemove(id) {
                let remove = document.getElementById('removeCard-' + id);
                remove.removeAttribute('style')
                remove.style.backgroundColor = 'white';
            },
            hideRemove(id) {
                let remove = document.getElementById('removeCard-' + id);
                remove.style.display = 'none';
            },
            remove(card) {
                this.triggerLoader();
                console.log('remove card', card);
                this.removeCardFromStripe(card);
                this.$forceUpdate();
            },
            async removeCardFromStripe(card) {
                this.$emit('disable-pay-buttons');
                const {data} =
                    await axios.get('/stripe/customer/removeCard/'
                        + card.id + '/'
                        + card.customer);

                if (data.error) {
                    this.removalError = true;
                    this.errorMessage = data.error;
                } else {
                    this.$emit('close-modal');
                }

                this.untriggerLoader();
            },
            triggerLoader() {
                this.removingCard = true;
            },
            untriggerLoader() {
                this.removingCard = false;
            },
            getName(card) {
                if (card && card.billing_details.name) {
                    return card.billing_details.name
                }
                return Spark.state.user.name
            },
            selection(index) {
                if (document.getElementsByTagName('input').valueOf()[index]) {
                    return document.getElementsByTagName('input').valueOf()[index].checked
                }
            },
            theSelectedCard() {
                return this.$emit('selected-card', this.selectedCard)
            }
        },
    }
</script>

<style scoped>

    .selectedFormat {
        background-color: beige;
    }

    .unselectedFormat {
        background-color: white;
    }

</style>