<template>
    <v-container>

        <div class="flex flex-end">
            <v-icon
                    color="red"
                    right
                    @click="showVerificationDialog()"
            >mdi-alert-circle
            </v-icon>
        </div>

        <v-dialog
                v-model="dialog"
                fullscreen
        >

            <v-card>
                <v-card-title>Verification Details</v-card-title>
                <v-card-text>
                    <div class="flex flex-col">
                        <div class="flex justify-content-between mt-25rem mb-25rem">
                            <div class="verification-title">Current Deadline</div>
                            <div>{{ getDeadline() }}</div>
                        </div>
                        <div class="flex justify-content-between mt-25rem mb-25rem">
                            <div class="verification-title">Currently Due</div>
                            <div v-for="(val, index) in getValues('currently_due')" :key="index">
                                <div>{{ val }}</div>
                            </div>
                        </div>
                        <div class="flex justify-content-between mt-25rem mb-25rem">
                            <div class="verification-title">Disabled Reason</div>
                            <div>{{ getReason() }}</div>
                        </div>
                        <div class="flex justify-content-between mt-25rem mb-25rem">
                            <div class="verification-title">Eventually Due</div>
                            <div v-for="(val, index) in getValues('eventually_due')" :key="index">
                                <div>{{ val }}</div>
                            </div>
                        </div>
                        <div class="flex justify-content-between mt-25rem mb-25rem">
                            <div class="verification-title">Past Due</div>
                            <div v-for="(val, index) in getValues('past_due')" :key="index">
                                <div>{{ val }}</div>
                            </div>
                        </div>
                        <div class="flex justify-content-between mt-25rem mb-25rem">
                            <div class="verification-title">Pending Verification</div>
                            <div v-for="(val, index) in getValues('pending_verification')" :key="index">
                                <div>{{ val }}</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- https://stripe.com/docs/file-upload -->
                    <label for="supportingDocumentUploads"
                           class="verification-title"
                    >Please Upload Your Supporting Documents Here</label>
                    <div><i>* File Must Be Less Than 10 MB</i></div>
                    <div><i>* Must be smaller than 8000px by 8000px</i></div>
                    <input
                            ref="supportingDoc"
                            type="file"
                            id="supportingDocumentUploads"
                            @change="uploadSupportingDocument()"
                            accept="image/png, image/jpeg"
                    >
                    <v-btn
                            v-on:click="submitFile()">
                        Submit
                    </v-btn>

                </v-card-text>

                <v-card-actions>
                    <v-btn
                            text
                            color="primary"
                            @click="closeDialog()"
                    >
                        close
                    </v-btn>
                </v-card-actions>

            </v-card>
        </v-dialog>

    </v-container>
</template>

<script>
    export default {
        name: "StripeVerificationRequired",
        data() {
            return {
                dialog: false,
                file: ''
            }
        },
        props: {
            verification: Object
        },
        methods: {

            async uploadSupportingDocument() {
                this.file = this.$refs['supportingDoc'].files[0];
            },

            submitFile() {
                let formData = new FormData();
                formData.append('file', this.file);
                formData.append('accountId', this.verification.account_id);
                axios.post('/verification/supportingDocs',
                    formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(function () {
                    console.log('SUCCESS!!');
                }).catch(function () {
                    console.log('FAILURE!!');
                });

            },

            getValues(val) {

                let reason = null

                if (val === 'currently_due') {
                    reason = this.verification.currently_due;
                } else if (val === 'eventually_due') {
                    reason = this.verification.eventually_due;
                } else if (val === 'past_due') {
                    reason = this.verification.past_due;
                } else if (val === 'pending_verification') {
                    reason = this.verification.pending_verification;
                }

                let stringWithoutBrackets = reason.substr(1, reason.length - 2)
                let newArray = stringWithoutBrackets.split(',')

                let values = []

                for (let i = 0; i < newArray.length; i++) {
                    if (newArray[i] === '"individual.id_number"') {
                        values.push('ID Number')
                    }
                }

                return values

            },

            getReason() {
                if (this.verification.disabled_reason) {
                    return 'Your Requirements Are Past Due'
                }
            },

            getDeadline(deadline) {
                if (this.verification.current_deadline) {
                    return this.verification.current_deadline
                }
                return 'No Current Deadline'
            },

            showVerificationDialog() {
                this.dialog = true
            },
            closeDialog() {
                this.dialog = false
            }
        }
    }
</script>

<style scoped>

    .verification-title {
        font-weight: bold;
    }

</style>