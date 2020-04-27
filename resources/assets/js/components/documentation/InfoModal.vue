<template>
    <v-dialog
            width="500"
            v-model="openFeeDialog"
    >

        <div style="display: none">{{ open }}</div>

        <v-card>
            <v-card-title
                    class="w-break headline primary lighten-2"
                    primary-title
            >
                <div v-if="jobType == 'cash'">Cash Job Application Fee - $ 2.80</div>
                <div v-else>Estimated Applicable Fees - $ {{ estimatedFee }}</div>
            </v-card-title>

            <v-card-text class="mt-1rem">
                <div v-if="isCustomer">
                    <div v-if="jobType === 'cash'">
                        <p>
                            This price includes an application fee and is an estimate of $2.80.
                        </p>
                    </div>
                    <div v-else>
                        <p>
                            This price includes an application fee and is an estimate. Each charge costs 30 cents. The actual
                            price will be reflected at the time of payment.
                        </p>
                    </div>
                </div>
                <div v-else>

                    <div v-if="jobType === 'cash'">
                        <h3 class="text-center">Cash</h3>

                        <p>
                            All cash jobs are charged a one time $2.80 application fee. This fee will be charged to your
                            Stripe account. It will be charged when the customer hits Submit button
                            when they pay for the job.
                        </p>
                    </div>
                    <div v-else>
                        <h3 class="text-center">Credit Card</h3>
                        <p>Customer's Actual Price will include application fees. If you are paying with
                            credit card then Stripe will charge 2.9% the total amount plus 30 cents for each charge.
                            Since this application allows for a customer to pay for different tasks at different
                            times
                            then you may incur mutliple 30 cent charges. Jemmson charges $2.50 for every job that is
                            paid.
                            So your current estimated fees are ${{ estimatedFee }}.</p>

                        <p>For all jobs that you are the sub contractor you will not incur any extra fees.</p>

                        <p>Every Job Will Be Marked Up To Reflect These Fees</p>

                        <p>Actual fees will reflect on the Receipts after the Job has been paid by the customer</p></div>
                    </div>

            </v-card-text>

            <v-card-actions>
                <v-btn
                        primary
                        text
                        @click="close()"
                >CLOSE
                </v-btn>
            </v-card-actions>

        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        name: 'InfoModal',
        data: function () {
            return {
                openFeeDialog: false
            }
        },
        watch: {
            openFeeDialog (val) {
              if (!val) {
                  this.$emit('closeFeeDialog')
              }
            },
        },
        props: {
            estimatedFee: Number,
            isCustomer: Boolean,
            openDialog: Boolean,
            jobType: String
        },
        computed: {
            open: function () {
                this.openFeeDialog = this.openDialog
            }
        },
        methods: {
            close: function () {
                this.openFeeDialog = false
                this.$emit('closeFeeDialog')
            }
        }
    }
</script>

<style>

</style>
