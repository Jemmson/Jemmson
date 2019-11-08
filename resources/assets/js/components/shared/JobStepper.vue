<template>
    <v-container>
        <v-stepper
                v-model="step">
            <v-stepper-header>
                <v-stepper-step
                        :complete="step > 1" step="1"
                        @click="openDialog(1)"
                >Prepare Bid
                </v-stepper-step>
                <v-divider></v-divider>

                <v-stepper-step
                        :complete="step > 2"
                        @click="openDialog(2)"
                        step="2">Job Submitted</v-stepper-step>
                <v-divider></v-divider>

                <v-stepper-step
                        :complete="step > 3"
                        @click="openDialog(3)"
                        step="3">Approved</v-stepper-step>
                <v-divider></v-divider>

            </v-stepper-header>
        </v-stepper>

        <v-dialog
                v-model="dialog"
                max-width="290"
                >
            <v-card>
                <v-card-title v-if="infoStep === 1">Prepare Your Bid</v-card-title>
                <v-card-title v-else-if="infoStep === 2">Waiting on Customer Approval</v-card-title>
                <v-card-title v-else>Begin Your Job</v-card-title>


                <v-card-text v-if="infoStep === 1">
                    Prepare Bid
                </v-card-text>
                <v-card-text v-else-if="infoStep === 2">
                    Waiting On Customer Approval
                </v-card-text>
                <v-card-text v-else>
                    Begin Your Job
                </v-card-text>

                <v-card-actions>
                    <v-btn @click="close()">
                        Close
                    </v-btn>
                </v-card-actions>

            </v-card>
        </v-dialog>

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
      status: String
    },
    mounted(){
      this.currentStep
    },
    computed:{
      currentStep () {
        switch (this.status) {
          case 'bid.initiated':
            this.step = 1;
            break;
          case 'bid.sent':
            this.step = 2;
            break;
          case 'job.approved':
            this.step = 3;
            break;
        }
      }
    },
    methods: {
      openDialog (dialog) {
        this.infoStep = dialog
        this.dialog = true;
      },
      close(){
        this.dialog = false;
      }
    }
  }
</script>

<style scoped>

</style>