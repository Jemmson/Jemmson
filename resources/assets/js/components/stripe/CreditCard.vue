<template>
    <div>
        <div
                v-for="(card, key) in cards"
                :key="key"
        >
            <v-card
                    :class="selection(key) ? 'selectedFormat' : 'unSelectedFormat'"
                    class="mr-1rem ml-1rem"
                    outlined
            >
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
                                <div class="f-bold mr-1rem capitalize">{{ card.card.brand }} Card</div>
                                <div>****{{ card.card.last4 }}</div>
                            </div>
                            <div>{{ getName(card) }}</div>
                            <div>Expires {{ card.card.exp_month }}/{{ card.card.exp_year }}</div>
                        </div>
                    </div>
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
                selectedCard: {}
            }
        },
        props: {
            cards: Array,
            name: String
        },
        computed: {},
        methods: {
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