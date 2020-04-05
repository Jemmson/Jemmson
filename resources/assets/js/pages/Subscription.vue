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

        <section
                v-if="show.subscriptions"
        >
            <v-card class="mb-4 mt-1">
                <div class="flex justify-content-between">
                    <v-card-title>
                        Monthly Plan
                    </v-card-title>
                    <div
                            class="green--text m-1rem"
                            v-if="selectedPlan() === 'monthly'">Current Plan</div>
                </div>
                <v-card-text>
                    50$ per month + $1 per job
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            :disabled="hasStripe()"
                            @click="selectPlan('monthly')"
                            color="primary"
                    >Select
                    </v-btn>
                </v-card-actions>
            </v-card>

            <v-card>
                <div class="flex justify-content-between">
                    <v-card-title>
                        Yearly Plan
                    </v-card-title>
                    <div
                            class="green--text m-1rem"
                            v-if="selectedPlan() === 'yearly'">Current Plan</div>
                </div>
                <v-card-text>
                    <div>540$ per year + $1 per job</div>
                    <div><small>Same as -> 45$ per month + $1 per job</small></div>
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            :disabled="hasStripe()"
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
                    confirmCancelationModal: false
                },
                currentPlan: 'Monthly Plan'
            }
        },
        methods: {

            hasStripe() {
                return Spark.state.user.stripe_id === null
            },

            selectPlan(plan){
            //    check if user is setup with stripe
            //    if not then route them to setup with stripe
            //    if they are then check if they have this plan already
            },

            selectedPlan(){
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
        }
    }
</script>

<style scoped>
    .subscription-btn-width {
        width: 49% !important;
    }
</style>