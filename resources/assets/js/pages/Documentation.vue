<template>
    <div>

        <app-bar>

            <template
                    slot="hamburger"
            >
                <v-app-bar-nav-icon
                        @click.stop="drawer = !drawer"
                >
                </v-app-bar-nav-icon>
            </template>

        </app-bar>
        <v-flex style="margin-top: 6rem;">
            <v-navigation-drawer
                    permanent
                    v-model="drawer"
                    height="100vh"
                    width="200px"
            >
                <v-list
                        nav
                        dense
                >
                    <v-list-item link
                                 @click="showMainWorkflow()"
                    >
                        <v-list-item-title>Main Workflow</v-list-item-title>
                    </v-list-item>
                    <v-list-item link
                                 @click="showGeneralWorkflow()"
                    >
                        <v-list-item-title>General Workflow</v-list-item-title>
                    </v-list-item>
                    <v-list-item link
                                 @click="showSubWorkflow()"
                    >
                        <v-list-item-title>Subcontractor Contractor Workflow</v-list-item-title>
                    </v-list-item>
                    <v-list-item link
                                 @click="showCustomerWorkflow()"
                    >
                        <v-list-item-title>Customer Workflow</v-list-item-title>
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
        <!--        <v-card style="height: 100vh">-->
        <!--            <v-row style="height: 100vh">-->
        <!--                <v-col cols="3" style="height: 100vh">-->
        <!--                    <v-navigation-drawer-->
        <!--                            permanent-->
        <!--                            style="margin-top: 6rem; height: 100vh"-->
        <!--                            v-if="$vuetify.breakpoint.xs"-->
        <!--                    >-->
        <!--                        <v-list-->
        <!--                                nav-->
        <!--                                dense-->
        <!--                        >-->
        <!--                            <v-list-item link>-->

        <!--                                v-list-->

        <!--                                <v-list-item-title>Main Workflow</v-list-item-title>-->
        <!--                            </v-list-item>-->
        <!--                            <v-list-item link>-->
        <!--                                <v-list-item-title>General Contractor Workflow</v-list-item-title>-->
        <!--                            </v-list-item>-->
        <!--                            <v-list-item link>-->
        <!--                                <v-list-item-title>SubContractor Workflow</v-list-item-title>-->
        <!--                            </v-list-item>-->
        <!--                        </v-list>-->
        <!--                    </v-navigation-drawer>-->
        <!--                    <v-navigation-drawer-->
        <!--                            v-if="$vuetify.breakpoint.xs"-->
        <!--                            permanent-->
        <!--                            style="margin-top: 6rem; height: 100vh"-->
        <!--                    >-->
        <!--                        <v-list-->
        <!--                                nav-->
        <!--                                dense-->
        <!--                        >-->
        <!--                            <v-list-item link>-->
        <!--                                <v-list-item-title>Main Workflow</v-list-item-title>-->
        <!--                            </v-list-item>-->
        <!--                            <v-list-item link>-->
        <!--                                <v-list-item-title>General Contractor Workflow</v-list-item-title>-->
        <!--                            </v-list-item>-->
        <!--                            <v-list-item link>-->
        <!--                                <v-list-item-title>SubContractor Workflow</v-list-item-title>-->
        <!--                            </v-list-item>-->
        <!--                        </v-list>-->
        <!--                    </v-navigation-drawer>-->
        <!--                </v-col>-->
        <!--                <v-col cols="9" style="height: 100vh">-->
        <!--                    <v-card-title-->
        <!--                            style="margin-top: 6rem"-->
        <!--                            class="display-1 text-center"-->
        <!--                    >Main Workflow-->
        <!--                    </v-card-title>-->
        <!--                    <v-timeline-->
        <!--                    >-->
        <!--                        <v-timeline-item-->
        <!--                        >-->
        <!--                            <v-btn-->
        <!--                                    text-->
        <!--                                    @click="showInitiateBidDialog()"-->
        <!--                            >-->
        <!--                                Contractor Initiates A Bid-->
        <!--                            </v-btn>-->
        <!--                        </v-timeline-item>-->
        <!--                        <v-timeline-item class="text-right">Customer Receives A Text</v-timeline-item>-->
        <!--                        <v-timeline-item>Contractor Creates Bid And Subs Out Work</v-timeline-item>-->
        <!--                        <v-timeline-item class="text-right">Contractor Submits Bid To Customer</v-timeline-item>-->
        <!--                        <v-timeline-item>Customer Approves Bid</v-timeline-item>-->
        <!--                        <v-timeline-item class="text-right">Contractor or Sub Finishes Job</v-timeline-item>-->
        <!--                        <v-timeline-item>Customer Approves and Pays</v-timeline-item>-->
        <!--                    </v-timeline>-->
        <!--                </v-col>-->
        <!--            </v-row>-->
        <!--        </v-card>-->

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
        dotcolor:'#95ca97',
        initiateBid: false,
        drawer: true,
        mainWorkflow: true,
        generalWorkflow: false,
        subWorkflow: false,
        customerWorkflow: false,
        steps:
          {
            main: [
              {id: 1, step: 'Contractor Initiates A Bid'},
              {id: 2, step: 'Customer Receives A Text'},
              {id: 3, step: 'Contractor Creates Bid And Subs Out Work'},
              {id: 4, step: 'Contractor Submits Bid To Customer'},
              {id: 5, step: 'Customer Approves Bid'},
              {id: 6, step: 'Contractor or Sub Finishes Job'},
              {id: 7, step: 'Customer Approves and Pays'},
            ],
            general: [
              {id: 1, step: 'Contractor Initiates A Bid'},
              {id: 2, step: 'Customer Receives A Initiated Bid'},
              {id: 3, step: 'Contractor Adds Tasks And Invites Subs For Task'},
              {id: 4, step: 'General waits for subs to send their bids'},
              {id: 5, step: 'General Approves Bids from SubContractors'},
              {id: 6, step: 'General Submits the bid to the customer'},
              {id: 7, step: 'Customer Approves The bid'},
              {id: 8, step: 'general finishes the work to be done'},
              {id: 9, step: 'general submits the finished job to the customer'},
              {id: 10, step: 'Customer Approves and Pays for the job'}
            ],
            sub: [
              {id: 1, step: 'Sub Receives Bid From General Contractor'},
              {id: 2, step: 'Sub Submits Bid To General Contractor'},
              {id: 3, step: 'General Contractor Approves The Bid'},
              {id: 4, step: 'General Submits Bid To Sub'},
              {id: 5, step: 'Customer Approves the Bid'},
              {id: 6, step: 'Sub Finishes the Work'},
              {id: 7, step: 'General Approves the Subs Work'},
              {id: 8, step: 'General Submits Approves Work to the Customer'},
              {id: 9, step: 'Customer Pays for Job'}
            ],
            customer: [
              {id: 1, step: 'Customer a text for an initiated bid from General Contractor'},
              {id: 2, step: 'Customer receives a text for a completed Bid'},
              {id: 3, step: 'Customer Approves the bid'},
              {id: 4, step: '>General Finishes the work'},
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
      showMainWorkflow() {
        this.hideWorkflows()
        this.mainWorkflow = true
      },
      showGeneralWorkflow() {
        this.hideWorkflows()
        this.generalWorkflow = true
      },
      showSubWorkflow() {
        this.hideWorkflows()
        this.subWorkflow = true
      },
      showCustomerWorkflow() {
        this.hideWorkflows()
        this.customerWorkflow = true
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