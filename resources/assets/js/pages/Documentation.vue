<template>
    <div>

        <app-bar
                @drawer="drawer = $event"
        >
        </app-bar>
        <v-flex style="margin-top: 6rem;">
            <v-navigation-drawer
                    v-model="drawer"
                    fixed
                    clipped
                    style="margin-top: 6rem;"
            >
                <v-list
                >
                    <v-list-item link
                                 @click="workflow('main')"
                    >
                        <v-list-item-title
                                class="subtitle-1 capitalize"
                        >Main Workflow
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item link
                                 @click="workflow('general')"
                    >
                        <v-list-item-title
                                class="subtitle-1 capitalize"
                        >General Workflow
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item link
                                 @click="workflow('sub')"
                    >
                        <v-list-item-title
                                class="subtitle-1 capitalize"
                        >Subcontractor Workflow
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item link
                                 @click="workflow('customer')"
                    >
                        <v-list-item-title
                                class="subtitle-1 capitalize"
                        >Customer Workflow
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-navigation-drawer>
            <v-content>
                <v-container
                        fluid
                        fill-height
                >
                    <div
                            class="flex flex-column w-full h-full"
                            style="align-items: center;"
                    >
                        <div v-show="mainWorkflow" class="display-1 text-center header-spacing">Main Workflow</div>
                        <div v-show="generalWorkflow" class="display-1 text-center header-spacing">General Workflow
                        </div>
                        <div v-show="subWorkflow" class="display-1 text-center header-spacing">Subcontractor Workflow
                        </div>
                        <div v-show="customerWorkflow" class="display-1 text-center header-spacing">Customer Workflow
                        </div>
                        <div v-show="mainWorkflow">
                            <v-timeline>
                                <v-timeline-item
                                        :color="dotcolor"
                                        class="capitalize"
                                        v-for="step in steps.main" :key="step.id"
                                        :class="step.id % 2 === 0 ? 'text-right' : ''"
                                >{{ step.step }}
                                </v-timeline-item>
                            </v-timeline>
                        </div>

                        <div v-show="generalWorkflow">
                            <v-timeline>
                                <v-timeline-item
                                        :color="dotcolor"
                                        class="capitalize"
                                        v-for="step in steps.general" :key="step.id"
                                        :class="step.id % 2 === 0 ? 'text-right' : ''"
                                >{{ step.step }}
                                </v-timeline-item>
                            </v-timeline>
                        </div>

                        <div v-show="subWorkflow">
                            <v-timeline>
                                <v-timeline-item
                                        :color="dotcolor"
                                        class="capitalize"
                                        v-for="step in steps.sub" :key="step.id"
                                        :class="step.id % 2 === 0 ? 'text-right' : ''"
                                >{{ step.step }}
                                </v-timeline-item>
                            </v-timeline>
                        </div>

                        <div v-show="customerWorkflow">
                            <v-timeline>
                                <v-timeline-item
                                        :color="dotcolor"
                                        class="capitalize"
                                        v-for="step in steps.customer" :key="step.id"
                                        :class="step.id % 2 === 0 ? 'text-right' : ''"
                                >{{ step.step }}
                                </v-timeline-item>
                            </v-timeline>
                        </div>
                    </div>

                </v-container>
            </v-content>
        </v-flex>

        <v-dialog
                v-model="initiateBid"
        >
            <h1>Initiate Bid</h1>
        </v-dialog>

    </div>
</template>

<script>
  import AppBar from '../components/header/AppBar'

  export default {
    name: 'Documentation',
    data() {
      return {
        dotcolor: '#95ca97',
        initiateBid: false,
        drawer: false,
        mainWorkflow: true,
        generalWorkflow: false,
        subWorkflow: false,
        customerWorkflow: false,
        steps:
          {
            main: [
              {id: 1, step: 'Contractor Initiates Bid'},
              {id: 2, step: 'Customer Receives Text'},
              {id: 3, step: 'Contractor Sends Subs A bid'},
              {id: 4, step: 'Contractor Submits Bid To Customer'},
              {id: 5, step: 'Customer Approves Bid'},
              {id: 6, step: 'Contractor or Sub Finishes Job'},
              {id: 7, step: 'Customer Approves and Pays'},
            ],
            general: [
              {id: 1, step: 'Contractor Initiates A Bid'},
              {id: 2, step: 'Customer Receives A Initiated Bid'},
              {id: 3, step: 'Contractor Adds Tasks And Subs'},
              {id: 4, step: 'General waits for subs to send their bids'},
              {id: 5, step: 'General Approves Bids from SubContractors'},
              {id: 6, step: 'General Submits the bid to the customer'},
              {id: 7, step: 'Customer Approves The bid'},
              {id: 8, step: 'general finishes the work to be done'},
              {id: 9, step: 'General Submits task to Customer'},
              {id: 10, step: 'Customer Approves and Pays for the job'}
            ],
            sub: [
              {id: 1, step: 'Sub Receives Bid From General'},
              {id: 2, step: 'Sub Submits Bid To General Contractor'},
              {id: 3, step: 'General Contractor Approves The Bid'},
              {id: 4, step: 'General Submits Bid To Sub'},
              {id: 5, step: 'Customer Approves the Bid'},
              {id: 6, step: 'Sub Finishes the Work'},
              {id: 7, step: 'General Approves the Subs Work'},
              {id: 8, step: 'General Submits task to Customer'},
              {id: 9, step: 'Customer Pays for Job'}
            ],
            customer: [
              {id: 1, step: 'Customer receives initiated bid'},
              {id: 2, step: 'Customer receives completed Bid'},
              {id: 3, step: 'Customer Approves the bid'},
              {id: 4, step: 'General Finishes the work'},
              {id: 5, step: 'Customer Pays for Job'}
            ]
          },
      }
    },
    components: {
      AppBar
    },
    methods: {
      showInitiateBidDialog() {
        this.initiateBid = true
      },
      hideWorkflows() {
        this.mainWorkflow = false
        this.generalWorkflow = false
        this.subWorkflow = false
        this.customerWorkflow = false
      },
      closeDrawer() {
        this.drawer = false
      },
      workflow(workflow) {
        this.hideWorkflows()
        if (workflow === 'main') {
          this.mainWorkflow = true
          this.closeDrawer()
        } else if (workflow === 'general') {
          this.generalWorkflow = true
          this.closeDrawer()
        } else if (workflow === 'sub') {
          this.subWorkflow = true
          this.closeDrawer()
        } else if (workflow === 'customer') {
          this.customerWorkflow = true
          this.closeDrawer()
        }
      }
    }
  }
</script>

<style scoped>
    .header-spacing {
        margin-top: 2rem;
        margin-bottom: 3rem;
    }
</style>