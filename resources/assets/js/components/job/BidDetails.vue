<template>
  <v-container>

    <div v-if="showDeclinedMessage"
         class="ml-3 mr-3 w-100"
    >
      <h1 class="card-title mt-4">Job Change Message</h1>
      <card>
        <v-card-title class="w-break"
                      style="background-color: lightcoral"
        >
          {{ bid.declined_message }}
        </v-card-title>
      </card>
    </div>

    <v-card>
      <v-card-actions
          class="flex flex-col"
      >
        <div class="flex justify-content-around w-full">
          <div class="flex flex-col nav-icon-spacing">
            <v-icon
                class="nav-btn-position"
                :color="show.jobStepper ? 'success': ''"
                @click="showSection('jobStepper')"
            >mdi-page-next
            </v-icon>
            <div class="nav-icon-label" :class="show.jobStepper ? 'nav-icon-label-selected': ''">
              Step
            </div>
          </div>
          <div class="flex flex-col nav-icon-spacing">
            <v-icon
                :color="show.details ? 'success': ''"
                class="nav-btn-position"
                @click="showSection('details')"

            >mdi-details
            </v-icon>
            <div class="nav-icon-label" :class="show.details ? 'nav-icon-label-selected': ''">
              Details
            </div>
          </div>
          <div class="flex flex-col nav-icon-spacing">
            <v-icon
                ref="jobTaskNavButton"
                :color="show.jobTask ? 'success': ''"
                class="nav-btn-position"
                :class="!isCustomer && jobTasksNotifications() ? 'red--text' : null"
                @click="showSection('jobTask')">mdi-briefcase<span
                v-if="getJobTasksLength() > 0">
                                    ({{ getJobTasksLength() }})
                            </span>
            </v-icon>
            <div class="nav-icon-label" :class="show.jobTask ? 'nav-icon-label-selected': ''">
              Tasks
            </div>
          </div>
          <div class="flex flex-col nav-icon-spacing" v-if="!isCustomer">
            <v-icon
                :color="show.location ? 'success': ''"
                class="nav-btn-position"
                @click="showSection('location')"
            >mdi-google-maps
            </v-icon>
            <div class="nav-icon-label" :class="show.location ? 'nav-icon-label-selected': ''">
              Location
            </div>
          </div>
          <div class="flex flex-col nav-icon-spacing">
            <v-icon
                class="nav-btn-position"
                :color="show.notes ? 'success': ''"
                @click="showSection('notes')"
            >mdi-note-text-outline
            </v-icon>
            <div class="nav-icon-label" :class="show.notes ? 'nav-icon-label-selected': ''">
              Notes
            </div>
          </div>
          <div class="flex flex-col nav-icon-spacing">
            <v-icon
                ref="imagesNavButton"
                :color="show.images ? 'success': ''"
                class="nav-btn-position"
                @click="showSection('images')"
            >mdi-image<span
                v-if="getNumberOfImages() > 0"
            >({{ getNumberOfImages() }})</span>
            </v-icon>
            <div class="nav-icon-label" :class="show.images ? 'nav-icon-label-selected': ''">
              Photos
            </div>
          </div>
          <div class="flex flex-col nav-icon-spacing"
               v-if="canAddATask() && !isCustomer"
          >
            <v-icon
                ref="job-add-task-icon"
                class="nav-btn-position"
                :class="canAddATask() && !isCustomer ? 'red--text' : null"
                name="addTaskToBid"
                id="addTaskToBid"
                @click="$router.push('/job/add/task')"
            >
              mdi-plus-thick
            </v-icon>
            <div class="nav-icon-label">
              Add Task
            </div>
          </div>


        </div>
      </v-card-actions>
    </v-card>

    <section class="mt-1rem"
             v-show="show.jobStepper"
    >
      <job-stepper
          :status="getSelectedJob()"
          :user="getUser()"
          style="padding: 0;"
      ></job-stepper>
    </section>

    <section class="mt-1rem"
             v-show="show.details"
    >
      <v-card>
        <v-card-title>Details</v-card-title>

        <v-card-subtitle
            v-if="
                        isCustomer
                        && (
                            getJobStatus_latest(bid) === 'initiated'
                            || getJobStatus_latest(bid) === 'in_progress'
                        )"
            ref="details-subtitle"
            class="w-break"
        >PLEASE WAIT UNTIL YOUR CONTRACTOR SUBMITS BID
        </v-card-subtitle>

        <v-simple-table v-if="isCustomer">
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-left"></th>
              <th class="text-left"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>Job Name:</td>
              <td class="capitalize">{{ bid ? bid.job_name : '' }}</td>
            </tr>
            <tr @click="viewContractorInfo()">
              <td>Contractor Name:</td>
              <td class="lookLikeALink">{{ getCompanyName() }}</td>
            </tr>
            <!--                        <tr v-if="!bidHasBeenSubmitted">-->
            <!--                            <td>Start Date:</td>-->
            <!--                            <td>Bid Not Complete</td>-->
            <!--                        </tr>-->
            <!--                        <tr v-if="bidHasBeenSubmitted">-->
            <!--                            <td>Start Date:</td>-->
            <!--                            <td>{{ agreedStartDate }}</td>-->
            <!--                        </tr>-->
            <tr v-if="!bidHasBeenSubmitted">
              <td>Total Bid Price:</td>
              <td>Bid Not Complete</td>
            </tr>
            <tr v-if="bidHasBeenSubmitted">
              <td>Total Bid Price:</td>
              <td>
                {{ bidPrice }}
                <!--                <v-icon-->
                <!--                    color="primary"-->
                <!--                    @click="showFeeDialog()"-->
                <!--                    class="ml-1rem">mdi-information-->
                <!--                </v-icon>-->
              </td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>


        <!--        <info-modal-->
        <!--            :open-dialog="feeDialog"-->
        <!--            :is-customer="isCustomer"-->
        <!--            :estimated-fee="totalEstimatedFee()"-->
        <!--            :job-type="bid ? bid.payment_type : null"-->
        <!--            @closeFeeDialog="feeDialog = false"-->
        <!--        ></info-modal>-->


        <v-card-text v-if="subTaskWarning && !isCustomer" class="uppercase red ml-1rem mr-1rem">bid price less
          than the sum of
          your subs bids
        </v-card-text>
        <v-simple-table v-if="!isCustomer">
          <template v-slot:default>
            <thead>
            <tr>
              <th class="text-left"></th>
              <th class="text-left"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>Job Name:</td>
              <td class="capitalize">{{ bid ? bid.job_name : '' }}</td>
            </tr>
            <tr @click="viewCustomerInfo()">
              <td>Customer Name:</td>
              <td class="lookLikeALink">{{ customerName }}</td>
            </tr>
            <!--                        <tr>-->
            <!--                            <td>Start Date:</td>-->
            <!--                            <td>{{ agreedStartDate }}</td>-->
            <!--                        </tr>-->
            <tr>
              <td>Total Bid Price:</td>
              <td>
                {{ bidPrice }}
                <!--                <v-icon-->
                <!--                    color="primary"-->
                <!--                    @click="showFeeDialog()"-->
                <!--                    class="ml-1rem">mdi-information-->
                <!--                </v-icon>-->
              </td>
            </tr>
            </tbody>
          </template>
        </v-simple-table>
      </v-card>
    </section>

    <section ref="job_tasks" class="mt-1rem"
             v-show="show.jobTask"
    >
      <div v-if="getJobTasksLength() > 0">
        <div v-if="!isCustomer && bid && getJobTasksLength() > 0">


          <v-card>
            <v-card-title>Job Tasks</v-card-title>

            <v-simple-table>
              <template>
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Price</th>
                  <th>View</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(item, i) in getJobTasks()" :key="i">
                  <td>{{ jobTaskObject(item).Name }}</td>
                  <td
                      v-if="jobTaskObject(item).Price"
                      v-text="'$ ' + jobTaskObject(item).Price"
                  ></td>
                  <td>                <v-btn
                      class="btn-size btn-weight"
                      :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                      :to="'/job/task/' + item.id"
                      text
                  >Edit
                  </v-btn></td>
                </tr>
                </tbody>
              </template>
            </v-simple-table>



            <v-card
                v-for="(item, i) in getJobTasks()"
                :key="i"
                class="card-positioning"
                :class="i % 2 === 0 ? 'b-brown': 'b-blue'"
            >
              <v-card-text>

                <v-list-item-group
                >
                  <v-list-item-content>
                    <v-list-item-title style="
                                                            font-size: 1.25rem;
                                                            font-weight: bolder;
                                                            margin-left: 1rem;"
                                       class="uppercase"
                    >
                      {{ jobTaskObject(item).Name }}
                    </v-list-item-title>
                  </v-list-item-content>
                  <hr>
                  <v-list-item-content
                      style="margin-left: 1rem; margin-right: 1rem; margin-top: -1rem"
                  >
                    <v-list-item-title class="uppercase flex justify-content-between">
                      <div style="font-size: .875rem;">Task Status:</div>
                      <div style="font-size: .875rem;">{{ jobTaskObject(item).Status }}</div>
                    </v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-content
                      v-if="notificationMessage(item)"
                      style="margin-top: -1rem"
                      :class="!hasTaskMessages(item) ? 'mb-1rem-negative' : ''"
                  >
                    <v-list-item-title class="uppercase flex justify-content-between align-center">
                      <div
                          style="font-size: .875rem; margin-left: 1rem;"
                      >Sub Status:
                      </div>
                      <div>
                        <v-btn
                            style="font-size: .875rem;"
                            v-if="item.sub_statuses.length > 0"
                            text
                            :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                            :to="'/job/task/' + i"
                        >{{ notificationMessage(item) }}
                        </v-btn>
                      </div>
                    </v-list-item-title>
                  </v-list-item-content>

                  <v-list-item-content
                      class="w-break" v-if="hasTaskMessages(item)"
                      style="margin-top: -1rem"
                  >
                    <v-list-item-title class="uppercase flex justify-content-between align-center"
                                       v-for="(message, index) in item.task_messages" :key="index"
                    >
                      <div
                          style="font-size: .875rem; margin-left: 1rem;"
                      >{{ message.message }}
                      </div>
                    </v-list-item-title>
                  </v-list-item-content>
                  <hr>

                  <v-list-item-content
                      style="margin-left: 1rem; margin-right: 1rem; margin-top: -1rem;"
                      class="mb-1rem-negative"
                  >
                    <v-list-item-title class="uppercase flex justify-content-between">
                      <div class="flex">
                        <div style="font-size: .875rem; margin-right: 6px; font-weight: bold;">
                          Subs:
                        </div>
                        <div style="font-size: .875rem;">{{ jobTaskObject(item).Subs }}</div>
                      </div>
                      <div class="flex">
                        <div style="font-size: .875rem; margin-right: 6px; font-weight: bold;">
                          Quantity:
                        </div>
                        <div style="font-size: .875rem;">{{ jobTaskObject(item).Qty }}</div>
                      </div>
                      <div class="flex">
                        <div style="font-size: .875rem; margin-right: 6px; font-weight: bold;">
                          Price:
                        </div>
                        <div style="font-size: .875rem;"
                             v-if="jobTaskObject(item).Price"
                             v-text="'$ ' + jobTaskObject(item).Price"
                        ></div>
                        <div v-else>Price Not Set</div>
                      </div>
                    </v-list-item-title>
                  </v-list-item-content>

                  <hr>
                </v-list-item-group>
              </v-card-text>
              <v-card-actions
                  class="space-evenly"
                  style="margin-top: -2rem"
              >
                <v-btn
                    class="btn-size btn-weight"
                    :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                    :to="'/job/task/' + item.id"
                    text
                >Edit
                </v-btn>
                <v-btn
                    v-if="isGeneral() && approvedByCustomer(item)"
                    @click="openSubInvite(item.id)"
                    class="btn-size btn-weight"
                    :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                    text
                >Add Sub
                </v-btn>
                <div class="flex flex-col"
                     v-if="isGeneral() && subFinishedTask(item)"
                >
                  <div class="text-center"
                       :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                  >Sub Has Finished
                  </div>
                  <div class="flex space-evenly">
                    <v-btn
                        @click="approveSubsTask(item)"
                        class="btn-size btn-weight"
                        :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                        text
                    >
                      Approve
                    </v-btn>
                    <v-btn
                        class="btn-size btn-weight"
                        :class="i % 2 === 0 ? 'error--text': 'error--text'"
                        text
                        @click="openDenyTaskForm(item)"
                    >
                      Change Task
                    </v-btn>
                  </div>
                </div>
                <v-btn
                    :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                    v-if="isGeneral() && showFinishedBtn(item)"
                    @click="finishedTask(item)"
                    class="btn-size btn-weight"
                    :loading="disabled.finished"
                    text
                >Mark Finished
                </v-btn>
                <div
                    :class="i % 2 === 0 ? 'primary--text': 'white--text'"
                    v-if="isGeneral() && subHasNotFinishedTask(item)"
                ><span
                    style="color: black"
                >Waiting For Sub</span>
                </div>
              </v-card-actions>
              <sub-invite-modal v-if="isGeneral()" :job-task="item"
                                :job-task-task="item ? item.task : null"
                                :job-task-name="item ? item.task.name : null"
                                :bid-payment-type="bid ? bid.payment_type : null"
                                :id="item ? item.id : null">
              </sub-invite-modal>
              <hr>
            </v-card>

          </v-card>


        </div>
        <div v-else-if="generalHasSentABid(bid)">

          <v-card>
            <v-card-title>Job Tasks</v-card-title>
            <card>

              <v-row>
                <div>
                                <span class="">
                                    (<b ref="job_task_length_customer">{{ getJobTasksLength() }}</b>)
                                </span> Total
                </div>

                <v-spacer></v-spacer>

                <pre v-show="false">{{ atleastOnetaskHasChanged() }}</pre>

                <v-btn
                    id="viewTasks"
                    class="w-40"
                    color="primary"
                    text
                    @click.prevent="viewTasks()"
                >
                  View Tasks
                </v-btn>
              </v-row>

              <table class="table mt-2rem">
                <thead>
                <tr>
                  <td>Name</td>
                  <td>Qty</td>
                  <td>Unit Price</td>
                  <td>Price</td>
                </tr>
                </thead>
                <tbody>
                <template v-for="(jt, index) in getJobTasks()">
                  <tr
                      :class="paid(jt) ? 'paid' : ''">
                    <td colspan="4"
                        :ref="'jobTaskStatus-' + index"
                        class="uppercase text-center"
                    >
                      {{ getLatestJobTaskStatus(jt) }}
                    </td>
                  </tr>
                  <tr>
                    <td class="uppercase">
                      {{ jt.task.name }}
                    </td>
                    <td>
                      {{ jt.qty }}
                    </td>
                    <td>
                      {{ formatPrice(jt.unit_price) }}
                    </td>
                    <td>
                      {{ formatPrice(jt.cust_final_price) }}
                    </td>
                  </tr>
                </template>
                </tbody>
              </table>

            </card>
          </v-card>

        </div>
      </div>

      <div v-else>

        <v-card class="mb-1rem">
          <v-card-title class="w-break">There are no current tasks</v-card-title>
          <v-card-actions>
            <v-btn
                ref="job-add-task"
                color="primary"
                v-if="canAddATask() && !isCustomer"
                class="nav-btn-position"
                name="addTaskToBid"
                text
                @click="$router.push('/job/add/task')"
            >
              Add A Job Task
            </v-btn>
          </v-card-actions>
        </v-card>

      </div>

    </section>

    <!-- images -->
    <section class="mt-1rem"
             v-show="show.images"
    >
      <!--            <p>Only allowable file types are JPG, PNG, GIF or WebP files</p>-->
      <v-card>

        <v-card-title>Images</v-card-title>

        <v-card-text>
          <task-images
              :is-customer="isCustomer"
              :job="bid" type="notsub">
          </task-images>
        </v-card-text>

      </v-card>
    </section>

    <v-card v-show="changeLocation" class="mt-2">
      <v-btn
          text
          color="red"
          @click="changeLocation = false">Cancel
      </v-btn>
      <v-card-title>Enter New Job Location</v-card-title>
      <v-card-title class="error--text" v-if="locationError">{{ locationErrorMessage }}</v-card-title>
      <v-card-text>

        <v-text-field
            id="route"
            :class="{'has-error': form.errors.has('address_line_1')}"
            v-model="form.address_line_1"
            label=""
            required
            :rules="[addressLine1CantBeBlank()]"
        >
        </v-text-field>
        <span ref="addressLine1Error" class="help-block"
              v-show="form.errors.has('address_line_1')">{{ form.errors.get('address_line_1') }}</span>

        <v-text-field
            id="addressLine2"
            v-model="form.address_line_2"
            label="Address Line 2"
        >
        </v-text-field>

        <v-text-field
            id="administrative_area_level_1"
            :class="{'has-error': form.errors.has('city')}"
            v-model="form.city"
            required
            :rules="[cityCantBeBlank()]"
            label="City *"
        >
        </v-text-field>
        <span ref="cityError" class="help-block"
              v-show="form.errors.has('city')">{{ form.errors.get('city') }}</span>

        <v-text-field
            id="locality"
            :class="{'has-error': form.errors.has('state')}"
            v-model="form.state"
            :rules="[stateCantBeBlank()]"
            required
            label="State *"
        >
        </v-text-field>
        <span ref="stateError" class="help-block" v-show="form.errors.has('state')">{{
            form.errors.get('state')
          }}</span>

        <v-text-field
            id="postal_code"
            :class="{'has-error': form.errors.has('zip')}"
            :rules="[this.zipMustHaveAtleast5characters(), zipCantBeBlank()]"
            v-model="form.zip"
            required
            v-mask="'#####-####'"
            label="Zip Code *"
        >
        </v-text-field>
        <span ref="zipError" class="help-block"
              v-show="form.errors.has('zip')">{{ form.errors.get('zip') }}</span>

      </v-card-text>
      <v-card-actions>
        <v-btn
            text
            :loading="updateLocationLoading"
            color="primary"
            @click="updateLocation()"
        >Update
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
            text
            color="red"
            @click="changeLocation = false"
        >Cancel
        </v-btn>
      </v-card-actions>
    </v-card>

    <section ref="job_address" class="mt-1rem"
             v-show="show.location"
    >
      <div v-if="showAddress">
        <card>

          <div class="flex justify-content-between align-baseline">
            <v-card-title>Job Location</v-card-title>
            <v-btn
                v-if="stateIsAz()"
                ref="assessor"
                class="mr-1rem"
                text
                color="primary"
                @click="routeToAssessorPage(bid)"
            >
              Assessor
            </v-btn>
          </div>

          <v-card-actions>
            <v-btn
                color="primary"
                text
                @click="openLocationDialog()"
            >Change Job Location
            </v-btn>
          </v-card-actions>

          <div class="flex flex-col">
            <div>
              {{ bid.location.address_line_1 }}
            </div>
            <div>
              {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
            </div>
          </div>

          <hr>

          <main class="map-responsive">
            <iframe
                width="450"
                height="250"
                frameborder="0" style="border:0"
                :src="'https://www.google.com/maps/embed/v1/search?key=AIzaSyBAQZB-zS1HVbyNe2JEk1IgNVl0Pm2xsno&q=' +
                            bid.location.address_line_1 + ' ' +
                            bid.location.city + ' ' +
                            bid.location.state + ' ' +
                            bid.location.zip
                            " allowfullscreen>
            </iframe>
          </main>

        </card>
      </div>
      <div v-else>
        <v-card>
          <v-card-title class="w-break">Customer Has Not Set The Job Location Yet</v-card-title>
        </v-card>
      </div>
    </section>

    <section class="mt-1rem"
             v-show="show.notes"
    >
      <v-card v-if="isCustomer">
        <v-card-title class="card-title mt-4">Notes</v-card-title>
        <v-card-text>
          <div style="display: none;">{{ messageFromCustomer }}</div>
          <div style="display: none;">{{ getPaidWithCashMessage }}</div>
          <v-textarea v-model="customerNotesMessage"
                      label="Your Notes For Your Contractor"
                      outlined
                      id="message">
          </v-textarea>
        </v-card-text>

        <v-btn
            :loading="submittedMessage"
            v-if="isCustomer"
            class="mt-1rem"
            text
            color="primary"
            ref="update_customer_notes_button"
            @click="updateGeneralContractorNotes"
        >Submit
        </v-btn>

      </v-card>

      <v-card v-if="!isCustomer">
        <v-card-title class="card-title mt-4">Notes</v-card-title>
        <v-card-text>
          <div class="capitalize" style="color: black;">{{ messageFromCustomer }}</div>
          <div style="display: none; color: black;">{{ getPaidWithCashMessage }}</div>
        </v-card-text>
      </v-card>
      <!--            <card>-->
      <!--                <v-card-title v-else class="card-title mt-4">Contractor Notes For You</v-card-title>-->
      <!--                <v-card-text>-->
      <!--                    <v-textarea cols="0" rows="0" class="form-control"-->
      <!--                                v-model="customerNotesMessage"-->
      <!--                                id="message">-->
      <!--                    </v-textarea>-->
      <!--                </v-card-text>-->
      <!--            </card>-->
    </section>

    <!-- / tasks -->

    <section class="mt-4"
             v-if="(isCustomer && needsApproval()) || !isCustomer"
    >
      <card class="mb-4" v-if="getJobTasksLength() > 0">
        <v-card-title>Actions</v-card-title>
        <!-- /customer approve bid form -->
        <approve-bid
            v-if="isCustomer && needsApproval()"
            :bid="bid"
        >
        </approve-bid>

        <v-sheet
            class="text-center uppercase successful-submit"
            v-if="submittedMessage"
        >
          You have successfully submitted a bid
        </v-sheet>

        <general-contractor-bid-actions
            ref="generalBidContractorActions"
            :disable-button="noJobTasks()"
            @bid-submitted="bidSubmitted()"
            @remove-notification="removeSubmittedNotification()"
            :bid="bid"
            v-if="!isCustomer">
        </general-contractor-bid-actions>
      </card>
    </section>

    <section class="mt-4" style="margin-left: -18px; margin-right: -18px;" v-if="customerHasCompletedTasks()">

      <v-card>
        <v-card-title>
          <!--            <v-card-title class="v-card__title">-->
          Completed Tasks
        </v-card-title>
        <v-card-text style="padding-left: 0px; padding-right: 0px;">
          <!--            <v-card-text class="v-card__title">-->
          <main class="row w-full ml-0">
            <completed-tasks
                class="w-full"
                :bid="bid"
                :paid="checkIfPaid()"
            >
            </completed-tasks>
          </main>
        </v-card-text>
      </v-card>
    </section>

    <stripe
        :bid="bid"
        :client-secret="clientSecret"
        :user="getCurrentUser()"
        @paid="paidFromSignUp()"
    >
    </stripe>
    <v-dialog
        v-model="denyDialog"
        width="500"
    >

      <v-card>
        <v-card-title class="w-break error--text" v-show="denyForm.error">{{ denyForm.error }}</v-card-title>
        <v-card-title class="w-break justify-content-between">
          <div>Deny Sub's Finished Task</div>
          <div>{{ currentJobTask.task === undefined ? '' : currentJobTask.task.name.toUpperCase() }}</div>
        </v-card-title>
        <v-card-text>
          <v-textarea
              outlined
              v-model="denyForm.message"
              :auto-grow="true"
              :clearable="true"
          ></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-btn class="w-full"
                 color="primary"
                 text
                 @click.prevent="denyTask"
                 :loading="disabled.deny"
                 ref="denyTaskBtn">
            Deny Approval
          </v-btn>
        </v-card-actions>
      </v-card>

    </v-dialog>

    <!--        <deny-task-modal v-if="!isCustomer"-->
    <!--                         :job-task="currentJobTask"-->
    <!--                         :id="currentJobTaskId">-->
    <!--        </deny-task-modal>-->

  </v-container>
</template>

<script>
import {mapGetters, mapMutations, mapActions, mapState} from 'vuex'
import ApproveBid from './ApproveBid'
import Card from '../shared/Card'
import CompletedTasks from './CompletedTasks'
import ContentSection from '../shared/ContentSection'
import GeneralContractorBidActions from './GeneralContractorBidActions'
import Format from '../../classes/Format'
import HorizontalTable from '../shared/HorizontalTable'
import Info from '../shared/Info'
import InfoModal from '../../components/documentation/InfoModal'
import JobStepper from '../../components/shared/JobStepper'
import Status from '../mixins/Status.js'
import SubInviteModal from '../../components/task/SubInviteModal'
import TaskImages from '../../components/task/UploadJobImages'
import Utilities from '../mixins/Utilities'
import Stripe from '../stripe/Stripe'
import DenyTaskModal from '../../components/task/DenyTaskModal'

export default {
  components: {
    ApproveBid,
    Card,
    ContentSection,
    CompletedTasks,
    DenyTaskModal,
    GeneralContractorBidActions,
    HorizontalTable,
    Info,
    InfoModal,
    JobStepper,
    Stripe,
    SubInviteModal,
    TaskImages
  },
  mixins: [Status, Utilities],
  props: {
    bid: Object,
    isCustomer: Boolean,
    customerName: String
  },
  created: function () {
    Bus.$on('needsStripe', (clientSecret) => {
      this.clientSecret = clientSecret
      $('#stripe-modal').modal('show')
    })
    this.getUser()
    document.body.scrollTop = 0 // For Safari
    document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
  },
  data() {
    return {
      updateLocationLoading: false,
      locationError: false,
      locationErrorMessage: null,
      denyForm: {
        job_task_id: 0,
        message: '',
        error: ''
      },
      form: new SparkForm({
        address_line_1: '',
        address_line_2: '',
        city: '',
        state: '',
        zip: ''
      }),
      denyDialog: false,
      show: {
        jobStepper: false,
        details: true,
        jobTask: false,
        location: false,
        notes: false,
        images: false
      },
      isPaid: false,
      clientSecret: null,
      feeDialog: false,
      el: 2,
      area: {
        area: ''
      },
      currentJobTaskId: null,
      currentJobTask: {},
      taskHasChanged: false,
      jobTaskItem: {},
      addTaskStartDate: false,
      addTaskBidPrice: false,
      statuses: [
        {
          type: 'Bid Initiated',
          description:
              'Contractor has sent a bid but has not added a task to the job'
        },
        {
          type: 'BID CHANGE REQUESTED - PLEASE REVIEW',
          description:
              'Customer has not approved the bid and is asking for a change to be made'
        },
        {
          type: 'Bid In Progress',
          description:
              'Contractor has added tasks to the bid but has not yet submitted it to the customer'
        },
        {
          type: 'Waiting on Customer Approval',
          description:
              'Contractor has submitted the finished bid and is now waiting for the customer to approve it'
        },
        {
          type: 'In Progress',
          description:
              'The job is in progress and it is waiting for the contrator sub to finish the job'
        },
        {
          type: 'Job Completed',
          description:
              'The Customer has paid for the job and the job is completed'
        }
      ],
      subTaskWarning: false,
      customerNotesMessage: '',
      showPaidWithCashNotes: false,
      disableCustomerNotesButton: false,
      customerNotes: false,
      customerNotes_contractor: false,
      areaError: '',
      changeLocation: false,
      payWithCashMessage: '',
      successfulUpdate: '',
      locationExists: false,
      customerInfo: false,
      paymentTypeCash: false,
      paymentTypeStripe: true,
      selectedPayment: 'creditCard',
      submissionCard: false,
      cancelBidCard: false,
      disabled: {
        deny: false,
        user_id: null,
        job_task_id: null,
        cancelBid: false,
        jobCompleted: false,
        submitBid: false,
        submitMessage: false,
        finished: false
      },
      submittedMessage: false
    }
  },
  computed: {
    ...mapState({
      selectedJob: state => state.job.model,
    }),
    ...mapGetters(['getCustomerName']),

    agreedStartDate() {
      if (this.getJob() && this.bid.agreed_start_date !== undefined && this.bid.agreed_start_date !== null) {
        return this.dateOnly(this.bid.agreed_start_date)
      } else {
        if (this.isCustomer) {
          return ''
        } else {
          this.addTaskStartDate = true
          return 'Add A Task'
        }

      }
    },
    messageFromCustomer() {
      if (this.getJob() && this.bid.customer) {
        this.customerNotesMessage = this.bid.customer.customer.notes
        return this.bid.customer.customer.notes
      }
    },
    getPaidWithCashMessage() {
      if (this.getJob()) {
        if (this.bid.paid_with_cash_message) {
          this.payWithCashMessage = this.bid.paid_with_cash_message
          this.selectedPayment = 'cash'
          return this.bid.paid_with_cash_message
        }
      }
    },
    bidPrice() {
      if (
          this.getJob()
          && this.bid.bid_price
          && (this.bid.status === 'bid.initiated'
              || this.bid.status === 'bid.in_progress'
              || this.bid.status === 'job.approved'
              || this.bid.status === 'bid.declined'
              || this.bid.status === 'bid.sent'
          )
      ) {
        this.addTaskBidPrice = false
        let theBidPrice = this.bid.bid_price
        return '$ ' + Format.decimal(theBidPrice)
      } else {
        if (this.isCustomer) {
          return ''
        } else {
          this.addTaskBidPrice = true
          return 'Add A Task'
        }

      }
    },
    showBidPrice() {
      if (this.isCustomer) {
        const status = this.bid.status
        // if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
        if (this.generalHasSentABid(this.getJob())) {
          return true
        }
        return false
      }
      return true
    },
    status() {
      return User.status(this.bid.status, this.bid, Spark.state.user)
    },
    bidHasBeenSubmitted() {
      // return this.bid.status !== 'bid.initiated' &&
      //   this.bid.status !== 'bid.in_progress'
      if (this.bid.status) {
        return this.generalHasSentABid(this.bid)
      }
    },
    showDeclinedMessage() {
      if (this.bid && this.bid.declined_message) {
        return (
            !this.isCustomer &&
            this.bid.declined_message !== null
            && this.getJobStatus_latest(this.getJob()) === 'changed'
        )
      }
    },

    disableSubmitBid() {
      // return this.bid.status === 'bid.sent'
      return this.generalCanSubmitABid(this.getJob())
    },

    showAddress() {
      if (this.getJob()) {
        return (
            this.bid.location_id !== undefined &&
            this.bid.location_id !== null &&
            this.bid.location !== null &&
            !this.isCustomer
        )
      }
    }
  },
  watch: {
    bid: function () {
      this.bid = this.bid
    }
  },
  methods: {

    stateIsAz() {
      let state = 'AZ';
      let addressState = this.bid.location.state;
      return addressState.toLowerCase() === state.toLowerCase();
    },

    addressLine1CantBeBlank() {
      return !!this.form.address_line_1 || 'Address line 1 is required'
    },

    cityCantBeBlank() {
      return !!this.form.address_line_1 || 'City is required'
    },

    stateCantBeBlank() {
      return !!this.form.state || 'State is required'
    },

    zipCantBeBlank() {
      return !!this.form.zip || 'Zip is required'
    },


    async updateLocation() {

      this.updateLocationLoading = true;

      const {data} = await axios.post('/location/update', {
        'jobId': this.bid.id,
        'userId': this.bid.customer.id,
        'address_line_1': this.form.address_line_1,
        'address_line_2': this.form.address_line_2,
        'city': this.form.city,
        'state': this.form.state,
        'zip': this.form.zip
      })

      if (data.error) {
        this.locationError = true
        this.locationErrorMessage = data.error
      } else {
        this.locationError = false
        Bus.$emit('bidUpdated');
        this.changeLocation = false;
      }

      this.updateLocationLoading = false;
    },

    zipMustHaveAtleast5characters() {
      return this.form.zip.length > 4 || 'Zip Code Must Be At Least 5 Characters'
    },

    initAutocomplete() {
      User.initAutocomplete('route')
    },

    updateFormLocation(location) {
      this.form.address_line_1 = location.route
      this.form.city = location.locality
      this.form.state = location.administrative_area_level_1
      this.form.zip = location.postal_code
    },

    openLocationDialog() {
      this.changeLocation = !this.changeLocation
    },

    routeToAssessorPage(bid) {

      let location = '';

      if (bid.location.address_line_2) {
        location = bid.location.address_line_1
            + ' ' + bid.location.address_line_2
            + ' ' + bid.location.city
            + ' ' + bid.location.state
            + ' ' + bid.location.zip
      } else {
        location = bid.location.address_line_1
            + ' ' + bid.location.city
            + ' ' + bid.location.state
            + ' ' + bid.location.zip
      }

      location = this.urlEncodeLocation(location);

      this.$router.push({
        path: '/assessor/' + location
      });
    },

    getJobTaskId() {
      if (this.jobTask) {
        return this.jobTask.id
      }
    },

    urlEncodeLocation(location) {
      var locationArray = location.split(' ')
      let result = '';
      for (let i = 0; i < locationArray.length; i++) {
        if (i !== locationArray.length - 1) {
          result = result + locationArray[i] + '+';
        } else {
          result = result + locationArray[i];
        }
      }
      return result;
    },

    async denyTask() {
      if (this.denyForm.message !== '') {
        try {
          await axios.post('/task/deny', {
            job_task_id: this.currentJobTaskId,
            user_id: Spark.state.user.id,
            message: this.denyForm.message
          })
          User.emitChange('bidUpdated')
          Vue.toasted.success('Task Denied & Notification Sent')
          this.denyDialog = false;
          this.denyForm.error = '';
        } catch (error) {
          error = error.response.data
          Vue.toasted.error(error.message)
          this.denyForm.error = error.message;
          this.denyDialog = false;
        }
      } else {
        this.denyForm.error = 'The Message Cannot Be Blank'
      }
    },

    jobTasksNotifications() {

      if (this.atleastOneJobTaskExists()) {
        return false
      } else {
        return true
      }

    },

    atleastOneJobTaskExists() {
      if (this.bid && this.bid.job_tasks) {
        return this.bid.job_tasks.length > 0;
      }
    },

    subHasInitiatedABid() {
      if (this.bid && this.bid.job_tasks) {
        for (let i = 0; i < this.bid.job_tasks.length; i++) {
          if (this.bid.job_tasks[i].sub_statuses.length > 0) {
            return this.getLatestSubStatus(this.bid.job_tasks[i]) === 'initiated';
          }
        }
      }
    },

    getNumberOfImages() {
      if (this.getJob() && this.bid.images) {
        return this.bid.images.length
      }
    },

    noJobTasks() {
      return this.bid && this.bid.job_tasks && this.bid.job_tasks.length === 0
    },

    showSection(section) {
      this.hideAllSections();
      if (section === 'jobStepper') {
        this.show.jobStepper = true;
      } else if (section === 'details') {
        this.show.details = true;
      } else if (section === 'jobTask') {
        this.show.jobTask = true;
      } else if (section === 'images') {
        this.show.images = true;
      } else if (section === 'location') {
        this.show.location = true;
      } else if (section === 'notes') {
        this.show.notes = true;
      }
    },

    hideAllSections() {
      this.show.details = false;
      this.show.jobStepper = false;
      this.show.jobTask = false;
      this.show.images = false;
      this.show.location = false;
      this.show.notes = false;
    },

    checkIfPaid() {
      return this.isPaid;
    },

    paidFromSignUp() {
      this.isPaid = true;
    },

    async getBids() {
      let url = ''
      if (this.isCustomer) {
        url = 'getJobsForCustomer'
      } else {
        url = 'jobs'
      }
      await axios.get(url).then((response) => {
        if (Array.isArray(response.data)) {
          return response.data;
        }
      })
    },

    openDenyTaskForm(item) {
      this.currentJobTask = item;
      this.currentJobTaskId = item.id;

      this.denyDialog = true;

      // $('#deny-task-modal_' + item.id).modal('show')
    },

    subHasNotFinishedTask(item) {
      if (this.isASub(item.contractor_id, this.bid.contractor_id)) {
        return this.getLatestJobTaskStatus1(item) === 'approved by customer'
            || this.getLatestJobTaskStatus1(item) === 'declined subs work'
      }
    },

    isASub(subId, generalId) {
      return subId !== generalId;
    },

    getJobTasksLength() {
      let jobTasks = this.getJobTasks()
      if (jobTasks) {
        return jobTasks.length
      }
    },

    atleastOnetaskHasChanged() {
      const jobTasks = this.getJobTasks()
      for (let i = 0; i < jobTasks.length; i++) {
        if (this.getLatestJobTaskStatus(jobTasks[i]) === 'changed') {
          this.taskHasChanged = true;
        }
      }
    },

    hasTaskMessages(jt) {
      return jt && jt.task_messages && jt.task_messages.length > 0
    },

    notificationMessage(item) {
      // let initiated = false;
      // let accepted = false;
      // let sent_a_bid = false;


      if (item.sub_statuses.length > 0) {

        let latestStatus = item.sub_statuses[item.sub_statuses.length - 1].status;

        if (latestStatus === 'initiated') {
          return "Waiting For Bids"
        }

        if (latestStatus === 'sent_a_bid') {
          return "Received A Bid"
        }

        if (latestStatus === 'accepted') {
          return "Accepted A Bid"
        }

        // for (let i = 0; i < item.sub_statuses.length; i++) {
        //     if (item.sub_statuses[i].status == 'initiated') {
        //         initiated = true;
        //     }
        //     if (item.sub_statuses[i].status == 'accepted') {
        //         accepted = true;
        //     }
        //     if (item.sub_statuses[i].status == 'sent_a_bid') {
        //         sent_a_bid = true;
        //     }
        // }
      }

    },

    getCurrentUser() {
      if (Spark.state) {
        return Spark.state.user
      }
    },

    totalEstimatedFee() {
      if (this.bid) {
        if (this.bid.payment_type === 'cash') {
          return 2.80;
        } else {
          return (parseFloat(this.getBidPriceValue(this.bidPrice)) * .029) + this.getStripeFlatRateCharges() + 2.50
        }
      }
    },

    getBidPriceValue(bidPrice) {
      const bp = bidPrice.split(' ')
      return bp[1]
    },

    // TODO: I dont not understand this method. dont know how this would even work?!!!
    getStripeFlatRateCharges() {
      return this.getNumberOfJobTasks() * .30
    },

    getNumberOfJobTasks() {
      if (this.getJob() && this.getJobTasks()) {
        return this.bid.job_tasks.length
      }
    },

    getJobTasks() {
      if (this.getJob()) {
        return this.bid.job_tasks
      }
    },

    getJob() {
      return this.bid
    },

    showFeeDialog() {
      this.feeDialog = true
    },

    bidSubmitted() {
      this.submittedMessage = true
    },

    removeSubmittedNotification() {
      this.submittedMessage = false
    },

    paid(jobTask) {
      let status = this.getLatestJobTaskStatus(jobTask)

      return status === 'paid'

    },

    subFinishedTask(item) {
      let status = this.getLatestSubStatus(item)
      return status === 'finished_job'
    },

    getLatestSubStatus(item) {
      if (item && item.sub_statuses && item.sub_statuses.length > 0) {
        return item.sub_statuses[item.sub_statuses.length - 1].status
      }
    },

    async approveSubsTask(jobTask) {
      try {
        await axios.post('task/approve', {
          id: jobTask.id
        });
        Vue.toasted.success('Task Has Been Approved and Customer Has Been Notified')
        Bus.$emit('bidUpdated');
      } catch (error) {
        console.log('error');
      }
    },


    jobIsNotFinishedAndNotApproved(item) {
      if (item) {
        return !(item.status === 'job.approved'
            || item.status === 'bid.paid')
      }
    },
    showFinishedBtn(jobTask) {
      let status = this.getLatestJobTaskStatus1(jobTask);
      return this.isGeneral() &&
          this.isAssignedToMe(jobTask, Spark.state.user.id) &&
          (
              status === 'approved by customer'
              || status === 'customer changes finished task'
          )
    },
    openSubInvite(jobTaskId) {
      $('#sub-invite-modal_' + jobTaskId).modal()
    },
    finishedTask(jobTask) {
      GeneralContractor.finishedTask(jobTask, this.disabled)
    },
    isAssignedToMe(jobTask, userId) {
      return userId === jobTask.contractor_id
    },
    approvedByCustomer(task) {
      const latestStatus = this.getLatestJobTaskStatus(task)
      return latestStatus !== 'general finished work'
          && latestStatus !== 'sub finished work'
          && latestStatus !== 'approved subs work'
          && latestStatus !== 'paid'
    },


    getLatestJobTaskStatus(task) {

      let status = '';
      let taskHasChanged = false;

      if (task) {
        if (task.job_task_statuses) {
          status = this.formatStatus(this.getJobTaskStatus_latest(task))
        } else {
          status = this.formatStatus(this.getTheLatestJobTaskStatus(task.job_task_status))
        }
      }

      const jobTasks = this.getJobTasks()
      for (let i = 0; i < jobTasks.length; i++) {
        if (this.getLatestJobTaskStatus1(jobTasks[i]) === 'changed') {
          taskHasChanged = true;
        }
      }

      if (taskHasChanged && status === 'waiting for customer approval') {
        return 'WAITING ON BID SUBMISSION'
      }

      return status
    },

    getLatestJobTaskStatus1(task) {

      let status = ''

      if (task) {
        if (task.job_task_statuses) {
          status = this.formatStatus(this.getJobTaskStatus_latest(task))
        } else {
          status = this.formatStatus(this.getTheLatestJobTaskStatus(task.job_task_status))
        }
      }

      return status
    },

    getSelectedJob() {


      let jobStatus;

      if (this.getJob() && this.bid.job_statuses) {
        jobStatus = this.bid.job_statuses[this.bid.job_statuses.length - 1].status
      } else if (this.getJob() && this.bid.job_status) {
        jobStatus = this.bid.job_status[this.bid.job_status.length - 1].status
      }

      if (jobStatus === 'approved') {
        let taskStatusFinished = false;
        let jobTaskStatus;

        let jobTasks = this.bid.job_tasks;

        for (let i = 0; i < jobTasks.length; i++) {
          let statuses = jobTasks[i].job_task_status;
          if (statuses) {
            jobTaskStatus = statuses[statuses.length - 1].status;
          } else {
            statuses = jobTasks[i].job_task_statuses;
            jobTaskStatus = statuses[statuses.length - 1].status;
          }
          if (
              jobTaskStatus === 'general_finished_work'
              || jobTaskStatus === 'sub_finished_work'
              || jobTaskStatus === 'approved_subs_work'
          ) {
            taskStatusFinished = true;
          }
        }
        if (taskStatusFinished) {
          return 'please_pay'
        } else {
          return jobStatus
        }
      } else {
        return jobStatus
      }

    },

    canAddATask() {

      if (this.bid && this.bid.job_tasks && this.bid.job_tasks.length === 0) {
        return true
      }

      if (this.bid && this.bid.job_statuses) {
        const latestJobStatus = this.bid.job_statuses[this.bid.job_statuses.length - 1].status;
        return latestJobStatus !== 'approved'
            && latestJobStatus !== 'canceled_by_customer'
            && latestJobStatus !== 'canceled_by_general'
            && latestJobStatus !== 'declines_finished_task'
            && latestJobStatus !== 'paid';
      }
    },


    viewContractorInfo() {
      this.$router.push({name: 'contractor-info', params: {contractorId: this.bid.contractor.id}})
    },

    viewCustomerInfo() {
      if (this.getJob()) {
        this.$router.push({name: 'customer-info', params: {customerId: this.bid.customer.id}})
      }
    },

    jobTaskObject(jt) {
      if (jt) {
        return {
          Name: jt.task ? jt.task.name : '',
          Subs: jt.bid_contractor_job_tasks ? jt.bid_contractor_job_tasks.length : '',
          Status: this.formatStatus(this.getJobTaskStatus_latest(jt)),
          'Status Date': this.formatDate(this.dateOnly(this.getJobTaskCreationDate_latest(jt))),
          Qty: jt.qty,
          Price: jt.cust_final_price
        }
      }
    },
    currentStep() {
      if (this.getJob()) {
        this.step = this.getStatus(
            this.bid.job_statuses[this.bid.job_statuses.length - 1],
            this.bid.job_statuses[this.bid.job_statuses.length - 1],
            this.bid.job_statuses[this.bid.job_statuses.length - 1]
        )
      }
    },
    needsApproval() {
      // TODO: use regular status values to check these
      return this.bid.status === 'bid.sent'
    },
    formatPrice(price) {
      return '$ ' + Format.decimal(price)
    },
    customerHasCompletedTasks() {
      let taskIsFinished = false
      if (this.getJob() && this.getJobTasks() && this.getNumberOfJobTasks() > 0) {
        for (let i = 0; i < this.getNumberOfJobTasks(); i++) {
          let status = ''
          if (this.bid.job_tasks[i].job_task_status
              && this.bid.job_tasks[i].job_task_status.length > 0
          ) {
            status = this.bid.job_tasks[i].job_task_status[this.bid.job_tasks[i].job_task_status.length - 1].status
          } else {
            status = this.bid.job_tasks[i].job_task_statuses[this.bid.job_tasks[i].job_task_statuses.length - 1].status
          }
          if (status === 'approved_subs_work'
              || status === 'general_finished_work'
              || status === 'customer_changes_finished_task') {
            taskIsFinished = true
          }
        }
      }
      return this.isCustomer && taskIsFinished
    },
    getCompanyName() {
      if (this.getJob()) {
        if (this.getJobTasks() && (this.getNumberOfJobTasks() !== 0)) {
          if (this.bid.job_tasks[0].task && this.bid.job_tasks[0].task.contractor) {
            return this.bid.job_tasks[0].task.contractor.company_name
          }
          if (this.bid.contractor.contractor) {
            return this.bid.contractor.contractor.company_name
          }
        } else if (this.bid.contractor) {
          return this.bid.contractor.contractor.company_name
        }
      }
    },
    bidHasNoTasks() {
      if (this.getJobTasks()) {
        return this.getNumberOfJobTasks() === 0
      }
    },
    cancelDialog() {
      this.cancelBidCard = false
      this.submissionCard = false
      this.disabled.cancelBid = false
      this.disabled.submitBid = false
    },
    getUser() {
      switch (Spark.state.user.usertype) {
        case 'customer':
          return 'customer'
        case 'contractor':
          if (Spark.state.user.id === this.bid.contractor_id) {
            return 'general'
          } else {
            return 'sub'
          }
      }
    },
    openCancelDialogCard() {
      this.cancelBidCard = true
      this.disabled.cancelBid = true
    },
    showPreApprovedActions() {
      return this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed' && this.isGeneral(this.bid.contractor_id, Spark.state.user.id)
    },
    cancelTheBid() {
      this.disabled.cancelBid = true
      this.cancelBidCard = false
      this.disabled.cancelBid = false
    },
    submitTheBid() {
      this.submissionCard = false
      this.disabled.submitBid = false
      this.notifyCustomerOfFinishedBid()
    },
    showSubmissionCard() {
      console.log('hello')
      this.cancelBidCard = false
      this.submissionCard = true
      this.disabled.submitBid = true
    },

    showCancelCard() {
      this.disabled.cancelBid = true
      this.submissionCard = false
      this.cancelBidCard = true
    },

    notifyCustomerOfFinishedBid() {

      // go through each job task and compare the sub price to the contractor task price
      // first check if there is a sub.
      // check if the sub price is an accepted price
      // compare the the accepted sub price to the contractor price
      // if the accepted sub price is higher then throw an error

      let subTaskWarning = false
      for (let i = 0; i < this.getNumberOfJobTasks(); i++) {
        if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
          subTaskWarning = true
        }
      }

      if (!subTaskWarning) {
        this.subTaskWarning = false
        GeneralContractor.notifyCustomerOfFinishedBid(this.getJob(), this.disabled)
      } else {
        console.log('subs price is higher than contractor price')
        this.subTaskWarning = true
      }

    },

    isGeneral(contractor_id, user_id) {
      if (this.getJob() !== null) {
        return contractor_id === user_id
      }
      return false
    },
    viewTasks() {
      this.$router.push('/job/tasks')
    },
    paymentMethod(paymentType) {
      if (paymentType === 'cash') {
        this.selectedPayment = 'cash'
        this.paymentTypeCash = true
        this.paymentTypeStripe = false
      } else {
        this.selectedPayment = 'stripe'
        this.paymentTypeCash = false
        this.paymentTypeStripe = true
      }
    },
    async submitPayWithCashMessage() {
      this.disabled.submitMessage = true
      try {
        const data = await axios.post('/paidWithCashMessage', {
          jobId: this.bid.id,
          paidWithCashMessage: this.payWithCashMessage
        })

        if (data.data.message) {
          this.successfulUpdate = 'true'
          setTimeout(function () {
            this.successfulUpdate = ''
            this.disabled.submitMessage = false
          }.bind(this), 2000)
        } else {
          this.successfulUpdate = 'false'
          setTimeout(function () {
            this.successfulUpdate = ''
            this.disabled.submitMessage = false
          }.bind(this), 2000)
        }

      } catch (error) {
        console.log(error)
      }
    },
    getLabelClass(status) {
      return Format.statusLabel(status)
    },
    showNotes() {
      this.customerNotes = !this.customerNotes
    },
    ...mapMutations(['setCustomerName']),
    ...mapActions(['actCustomerName']),

    async updateGeneralContractorNotes() {
      try {
        this.submittedMessage = true
        await axios.post('/customer/updateCustomerNotes',
            {
              customerNotesMessage: this.customerNotesMessage,
              customer_id: this.bid.customer.id
            }
        )
        User.emitChange('bidUpdated')
        Vue.toasted.success('Customer Note Has Been Updated')
        this.submittedMessage = false
      } catch (error) {
        // error = error.response.data
        this.submittedMessage = false
        Vue.toasted.error(error)
      }
    },
    showArea() {
      return this.area.area !== '' && !this.isCustomer
    },
    initializePayWithCashMessageValue() {
      if (this.getJob() && this.bid.paid_with_cash_message) {
        console.log('I am true')
        console.log(this.bid.paid_with_cash_message)
        this.payWithCashMessage = this.bid.paid_with_cash_message
        this.customerNotesMessage = this.bid.customer.customer.notes
      }
      if (this.getJob() && this.bid.customer) {
        console.log(this.bid.customer.customer.notes)
        this.customerNotesMessage = this.bid.customer.customer.notes
      }
    }
  },
  mounted() {

    Bus.$on('updateFormLocation', (payload) => {
      this.updateFormLocation(payload)
    })

    this.initializePayWithCashMessageValue()
    this.initAutocomplete()
  }
}
</script>

<style lang="less" scoped>

//.v-card__title {
//  padding: 0;
//}

//.card__title {
//  padding: 0;
//}

.b-brown {
  background-color: beige;
}

.b-blue {
  background-color: cornflowerblue;
}

.paid {
  background-color: red;
}

.card-positioning {
  margin-bottom: .25rem;
}

.status {
  padding-top: 1rem;
  padding-bottom: 1rem;
  font-family: auto;
  font-size: 20pt;
}

.no-notes {
  text-align: center;
  margin-right: -9rem;
}

.status-header {
  font-size: 1rem;
  margin-left: -1rem;
  margin-right: -2rem;
  text-align: start;
  margin-bottom: .15rem;
  padding: .25rem;
  margin-top: .15rem;
}

.description {
  font-size: .9rem;
  margin-left: -1rem;
  margin-right: -2rem;
  text-align: start;
  margin-bottom: .15rem;
  /*background-color: beige;*/
  padding: .25rem;
  margin-top: .15rem;
  border-radius: 5px;
}

/*.spacing {*/
/*margin-bottom: 10rem;*/
/*}*/

.wrapper {
  display: grid;
  grid-template-columns: 1fr 1fr;
}

.w-100 {
  width: 100%;
}

// .btn-width {
//     width: 15rem;
// }
.status {
  /*padding: 1rem;*/
  /*padding-left: 6px;*/
  /*padding-right: 6px;*/
  align-items: center;
  justify-content: space-evenly;
}

.btn-width {
  width: 100%;
}

.notes-width {
  max-width: 75%;
}

span {
  font-size: 15px;
}

.lookLikeALink {
  text-decoration: underline;
  color: #1976d2 !important;
}

.successful-submit {
  background-color: green;
  padding: .25rem;
  margin-bottom: .5rem;
}

.btn-size {
  font-size: 12pt;
}

.btn-weight {
  font-weight: bolder;
}

.spacer-grey {
  color: lightgray;
  margin-left: .45rem;
}

/*@media (min-width: 762px) {*/
/*.btn-width {*/
/*width: 27%;*/
/*}*/
/*}*/
</style>