<template>
    <v-container
            class="h-full"
    >
        <div v-show="false">{{ this.status }}</div>

        <div v-if="user === 'general'" class="h-full">
            <v-stepper
                    v-model="step"
                    vertical
                    class="h-full w-full"
            >
                <v-stepper-step
                        class="w-full"
                        :complete="step > 0" step="1"
                        @click="openDialog(1)"
                ><small>Prepare</small>
                </v-stepper-step>

                <v-stepper-step
                        class="w-full"
                        :complete="step > 1"
                        @click="openDialog(2)"
                        step="2"><small>Sent</small>
                </v-stepper-step>

                <v-stepper-step
                        class="w-full"
                        :complete="step > 2"
                        @click="openDialog(3)"
                        step="3"><small>Approved</small>
                </v-stepper-step>

                <v-stepper-step
                        class="w-full"
                        :complete="step > 3"
                        @click="openDialog(4)"
                        step="4"><small>Paid</small>
                </v-stepper-step>
            </v-stepper>
            <v-dialog
                    v-model="dialog"
                    class="dialog-margins"
            >
                <v-card>
                    <v-card-title v-if="infoStep === 1">Prepare Your Bid</v-card-title>
                    <v-card-title v-else-if="infoStep === 2">Waiting on Customer Approval</v-card-title>
                    <v-card-title v-else-if="infoStep === 3">Begin Job</v-card-title>
                    <v-card-title v-else>Job Is Complete</v-card-title>


                    <v-card-text v-if="infoStep === 1">
                        This is the initial step of the workflow. You will
                        add tasks to your bid. Each of these tasks can be
                        subbed out to other contractors.
                    </v-card-text>
                    <v-card-text v-else-if="infoStep === 2">
                        In this step you have submitted the bid to the customer.
                        You are waiting for the customer to approve the bid so that
                        you or the sub can begin the job.
                    </v-card-text>
                    <v-card-text v-else-if="infoStep === 3">
                        In this step the customer has approved the bid.
                        You are now able to begin the work.
                    </v-card-text>
                    <v-card-text v-else>
                        In this step the customer has paid for the job and the work is finished.
                    </v-card-text>
                </v-card>
            </v-dialog>
        </div>
        <div v-else-if="user === 'customer'" class="h-full">
            <v-stepper
                    v-model="step"
                    vertical
            >
                <v-stepper-step
                        :complete="step > 1" step="1"
                        @click="openDialog(1)"
                ><small>Waiting For Bid</small>
                </v-stepper-step>

                <v-stepper-step
                        :complete="step > 2"
                        @click="openDialog(2)"
                        step="2"><small>Please Approve</small>
                </v-stepper-step>

                <v-stepper-step
                        :complete="step > 3"
                        @click="openDialog(3)"
                        step="3"><small>Waiting For Completion</small>
                </v-stepper-step>

                <v-stepper-step
                        :complete="step > 4"
                        @click="openDialog(4)"
                        step="3"><small>Please Pay</small>
                </v-stepper-step>

            </v-stepper>
            <v-dialog
                    v-model="dialog"
                    class="dialog-margins"
            >
                <v-card>
                    <v-card-title v-if="infoStep === 1">Waiting For Completed Bid</v-card-title>
                    <v-card-title v-else-if="infoStep === 2">Need To Take Action On A Job</v-card-title>
                    <v-card-title v-else>Waiting On Contractor To Finish Job</v-card-title>


                    <v-card-text v-if="infoStep === 1">
                        Waiting For Completed Bid
                    </v-card-text>
                    <v-card-text v-else-if="infoStep === 2">
                        Need To Take Action On A Job
                    </v-card-text>
                    <v-card-text v-else>
                        Waiting On Contractor To Finish Job
                    </v-card-text>

                    <v-card-actions>
                        <v-btn @click="close()">
                            Close
                        </v-btn>
                    </v-card-actions>

                </v-card>
            </v-dialog>
        </div>
        <!--        <div v-else>-->
        <!--            <v-stepper-->
        <!--                    v-model="step">-->
        <!--                <v-stepper-header>-->
        <!--                    <v-stepper-step-->
        <!--                            :complete="step > 1" step="1"-->
        <!--                            @click="openDialog(1)"-->
        <!--                    >Prepare Bid-->
        <!--                    </v-stepper-step>-->
        <!--                    <v-divider></v-divider>-->

        <!--                    <v-stepper-step-->
        <!--                            :complete="step > 2"-->
        <!--                            @click="openDialog(2)"-->
        <!--                            step="2">Job Submitted-->
        <!--                    </v-stepper-step>-->
        <!--                    <v-divider></v-divider>-->

        <!--                    <v-stepper-step-->
        <!--                            :complete="step > 3"-->
        <!--                            @click="openDialog(3)"-->
        <!--                            step="3">Approved-->
        <!--                    </v-stepper-step>-->
        <!--                    <v-divider></v-divider>-->

        <!--                </v-stepper-header>-->
        <!--            </v-stepper>-->
        <!--            <v-dialog-->
        <!--                    v-model="dialog"-->
        <!--                    max-width="290"-->
        <!--            >-->
        <!--                <v-card>-->
        <!--                    <v-card-title v-if="infoStep === 1">Prepare Your Bid</v-card-title>-->
        <!--                    <v-card-title v-else-if="infoStep === 2">Waiting on Customer Approval</v-card-title>-->
        <!--                    <v-card-title v-else>Begin Your Job</v-card-title>-->


        <!--                    <v-card-text v-if="infoStep === 1">-->
        <!--                        Prepare Bid-->
        <!--                    </v-card-text>-->
        <!--                    <v-card-text v-else-if="infoStep === 2">-->
        <!--                        Waiting On Customer Approval-->
        <!--                    </v-card-text>-->
        <!--                    <v-card-text v-else>-->
        <!--                        Begin Your Job-->
        <!--                    </v-card-text>-->

        <!--                    <v-card-actions>-->
        <!--                        <v-btn @click="close()">-->
        <!--                            Close-->
        <!--                        </v-btn>-->
        <!--                    </v-card-actions>-->

        <!--                </v-card>-->
        <!--            </v-dialog>-->
        <!--        </div>-->
    </v-container>
</template>

<script>
  export default {
    name: 'JobStepper',
    data() {
      return {
        step: 1,
        infoStep: 1,
        dialog: false
      }
    },
    props: {
      status: String,
      user: String
    },
    created() {
      this.currentStep()
    },
    mounted() {
      this.currentStep()
    },
    updated() {
      this.currentStep()
    },
    methods: {
      currentStep() {
        switch (this.status) {
          case 'initiated':
            this.step = 1
            break
          case 'in_progress':
            this.step = 1
            break
          case 'sent':
            this.step = 2
            break
          case 'changed':
            this.step = 2
            break
          case 'canceled_by_customer':
            this.step = 2
            break
          case 'canceled_by_general':
            this.step = 2
            break
          case 'approved':
            this.step = 3
            break
          case 'declines_finished_task':
            this.step = 3
            break
          case 'paid':
            this.step = 4
            break
        }
      },
      openDialog(dialogStep) {
        this.infoStep = dialogStep
        this.dialog = true
      },
      close() {
        this.dialog = false
      }
    }
  }
</script>

<style scoped>
.dialog-margins {
    margin-left: .5rem;
    margin-right: .5rem;
}
</style>