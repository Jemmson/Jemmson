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
                    >Subscription Plan
                    </v-btn>
                    <v-btn
                            class="nav-btn-position subscription-btn-width"
                            @click="openCancelConfirmationDialog()"
                    >Cancel Subscription
                    </v-btn>
                </div>
            </v-card-actions>
        </v-card>

        <section
                v-if="show.subscriptions"
        >
            Current Plan {{ currentPlan }}
            <v-card>
                <v-card-title>
                    Monthly Plan
                </v-card-title>
                <v-card-text>
                    50$ per month + $1 per job
                </v-card-text>
                <v-card-actions>
                    <v-btn
                            color="primary"
                    >Select
                    </v-btn>
                </v-card-actions>
            </v-card>

            <v-card>
                <v-card-title>
                    Yearly Plan
                </v-card-title>
                <v-card-text>
                    45$ per month + $1 per job
                </v-card-text>
                <v-card-actions>
                    <v-btn
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