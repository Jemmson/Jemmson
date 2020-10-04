<template>
  <v-container
      class="h-full"
  >
    <div v-show="false">{{ this.status }}</div>

    <v-card v-if="user === 'general'" class="h-full">
      <v-card-title class="w-break">Current Step In Workflow</v-card-title>
      <v-stepper
          v-model="step"
          vertical
          class="h-full w-full"
      >
        <v-stepper-step
            class="w-full"
            :complete="step > 1" step="1"
            @click="openDialog(1)"
        ><span>Prepare</span>
        </v-stepper-step>

        <v-stepper-step
            class="w-full"
            :complete="step > 2"
            @click="openDialog(2)"
            step="2"><span>Sent</span>
        </v-stepper-step>

        <v-stepper-step
            class="w-full"
            :complete="step > 3"
            @click="openDialog(3)"
            step="3"><span>Approved</span>
        </v-stepper-step>

        <v-stepper-step
            class="w-full"
            :complete="step > 5"
            @click="openDialog(5)"
            step="4"><span>Paid</span>
        </v-stepper-step>
      </v-stepper>
      <v-dialog
          v-model="dialog"
          class="dialog-margins"
      >
        <v-card>
          <v-card-title class="w-break" v-if="infoStep === 1">Prepare Your Bid</v-card-title>
          <v-card-title class="w-break" v-else-if="infoStep === 2">Waiting on Customer Approval</v-card-title>
          <v-card-title class="w-break" v-else-if="infoStep === 3">Begin Job</v-card-title>
          <v-card-title class="w-break" v-else>Job Is Complete</v-card-title>
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
    </v-card>
    <div v-else-if="user === 'customer'" class="h-full">
      <v-stepper
          v-model="step"
          vertical
      >
        <v-stepper-step
            :complete="step > 1" step="1"
            @click="openDialog(1)"
        ><span>Waiting For Bid</span>
        </v-stepper-step>

        <v-stepper-step
            :complete="step > 2"
            @click="openDialog(2)"
            step="2"><span>Bid Has Been Submitted - Please Approve</span>
        </v-stepper-step>

        <v-stepper-step
            :complete="step > 3"
            @click="openDialog(3)"
            step="3"><span>Waiting For Completion</span>
        </v-stepper-step>

        <v-stepper-step
            :complete="step > 4"
            @click="openDialog(4)"
            step="4"><span>Please Pay</span>
        </v-stepper-step>

        <v-stepper-step
            :complete="step > 5"
            @click="openDialog(5)"
            step="5"><span>Job Paid</span>
        </v-stepper-step>

      </v-stepper>
      <v-dialog
          v-model="dialog"
          class="dialog-margins"
      >
        <v-card>
          <v-card-title class="w-break" v-if="infoStep === 1">Waiting For Completed Bid</v-card-title>
          <v-card-title class="w-break" v-else-if="infoStep === 2">Need To Take Action On A Job</v-card-title>
          <v-card-title class="w-break" v-else-if="infoStep === 3">Waiting For Subs TO Finish Work</v-card-title>
          <v-card-title class="w-break" v-else-if="infoStep === 4">Time For Payment</v-card-title>
          <v-card-title class="w-break" v-else>Job Has Been Paid</v-card-title>


          <v-card-text v-if="infoStep === 1">
            Your contractor is putting your bid together. Once the bid is submitted then you will
            receive a text message to log back into the job and approve the bid.
          </v-card-text>
          <v-card-text v-else-if="infoStep === 2">
            The bid has now been submitted. You can now take action on the bid by approving, changing,
            or deleting the bid altogether.
          </v-card-text>
          <v-card-text v-else-if="infoStep === 3">
            Now that you have approved the job the contractors must finish the work. Once the contractors
             have finished the work you will receive a text message to submit payment.
          </v-card-text>
          <v-card-text v-else-if="infoStep === 4">
            Now that the work is complete. You can submit payment for the work that was performed.
            Each job is either cash job or a credit card job. You can securely submit credit cards through this
            application using the Stripe Platform. There are no fees for you if this is credit card job. If this
            is a cash job then you will be charged a 1 dollar application fee.
          </v-card-text>
          <v-card-text v-else>
            The job has now been completed. You will now be able to review your receipts by selecting the receipts
            icon at the bottom of the screen.
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
          this.step = 1
          break
        case 'canceled_by_customer':
          this.step = 1
          break
        case 'canceled_by_general':
          this.step = 1
          break
        case 'approved':
          this.step = 3
          break
        case 'declines_finished_task':
          this.step = 3
          break
        case 'please_pay':
          this.step = 4
          break
        case 'paid':
          this.step = 6
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