<template>
    <div>
        <div class="container" ref="jobTask">
            <div class="row">
                <!-- details -->
                <div class="col-12">

                    <div class="flex mb-1rem">
                        <button class="btn btn-md btn-normal text-uppercase flex-1"
                                @click.prevent="goBack()">
                            Back
                        </button>
                    </div>


                    <h1 class="card-title">Details</h1>
                    <card>
                        <div class="row">
                            <div class="col-12">
                                Job Task Name:
                                <!-- task name-->
                                <div class="float-right font-weight-bold">
                                    {{ jobTask ? jobTask.task.name : '' }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="flex justify-content-between">
                                    <label>Task Start Date</label>
                                    <!-- <i class="fas fa-clock icon m-r-2"></i> -->
                                    <input type="date" class="form-control form-control-sm w-50" style=""
                                           v-if="showTaskStartDate()"
                                           :value="jobTask ? prettyDate(jobTask.start_date) : 0"
                                           @blur="updateTaskStartDate($event.target.value, jobTask.id)">
                                    <!--                                <div v-else>-->
                                    <!--                                    <strong>{{ prettyDate(jobTask.start_date) }}</strong>-->
                                    <!--                                </div>-->
                                </div>
                                <span :class="{ error: hasStartDateError }"
                                      v-show="hasStartDateError">{{ startDateErrorMessage }}
                            </span>
                            </div>
                        </div>
                    </card>

                    <h1 class="card-title">Job Status</h1>
                    <card>
                        <div class="row">
                            <div class="col-12">
                                Status:
                                <div class="float-right font-weight-bold"
                                     :class="jobTask ? getLabelClass(jobTask.status) : 0">
                                    {{ jobTask ? status(jobTask.status) : ''}}
                                </div>
                            </div>
                        </div>
                        <div v-show="jobTask ? jobTask.declined_message !== '' : false">
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    Message:
                                    <div class="float-right font-weight-bold">{{jobTask ? jobTask.declined_message: ''}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </card>

                </div>

                <!-- prices -->
                <div class="col-12">
                    <h1 class="card-title mt-4">Prices</h1>
                    <card>
                        <div class="row">

                            <!-- / price/date -->
                            <div class="col-12" ref="prices">
                                <div class="row">
                                    <div class="col-12">
                                        <!--                                    v-if="parseInt(cust_final_price) > 0"-->
                                        <content-section
                                                v-if="isContractor()"
                                                label="Total Task Price:"
                                                ref="totalTaskPrice"
                                                :content="cust_final_price ? taskCustFinalPrice(cust_final_price, false) : '0'"
                                                section-classes="ph-zero"
                                                icon="fas fa-money-bill-alt icon"
                                                :warning="cust_final_price < sub_final_price"
                                                warning-message="Sub price is higher than your price"
                                                type="totalTaskPrice"></content-section>

                                        <content-section
                                                v-if="!isContractor()"
                                                label="Total Task Price:"
                                                ref="totalTaskPrice"
                                                :content="cust_final_price ? taskCustFinalPrice(cust_final_price, false) : '0'"
                                                section-classes="ph-zero"
                                                icon="fas fa-money-bill-alt icon"
                                                type="totalTaskPrice"></content-section>

                                        <!--                                    <content-section-->
                                        <!--                                            v-if="parseInt(this.cust_final_price) <= 0"-->
                                        <!--                                            label="Total Task Price:"-->
                                        <!--                                            content="Price Not Set"-->
                                        <!--                                            section-classes="ph-zero"-->
                                        <!--                                            icon="fas fa-money-bill-alt icon"-->
                                        <!--                                            type="totalTaskPrice"></content-section>-->
                                        <!--                                    v-if="isContractor() && parseInt(sub_final_price) > 0"-->

                                        <content-section
                                                v-if="isContractor()"
                                                label="Total Sub Price:"
                                                :content="sub_final_price ? taskCustFinalPrice(sub_final_price, true, true) : '0'"
                                                section-classes="ph-zero"
                                                icon="fas fa-user icon"
                                                type="totalTaskPrice"></content-section>

                                        <content-section
                                                v-if="isContractor()"
                                                label="Sub Unit Price:"
                                                :content="sub_final_price ? taskCustFinalPrice(sub_final_price, true) : '0'"
                                                section-classes="ph-zero"
                                                icon="fas fa-user icon"
                                                type="totalTaskPrice"></content-section>

                                        <!--                                    <content-section-->
                                        <!--                                            v-if="isContractor() && parseInt(sub_final_price) <= 0"-->
                                        <!--                                            label="Total Task Sub Price:"-->
                                        <!--                                            content="Price Not Set"-->
                                        <!--                                            section-classes="ph-zero"-->
                                        <!--                                            icon="fas fa-user icon"-->
                                        <!--                                            type="totalTaskPrice"></content-section>-->
                                    </div>

                                    <div class="col-12">

                                        <div class="form-group" v-if="isContractor()">
                                            <div class="flex justify-content-between mt-1rem">
                                                <label class="">Quantity:</label>
                                                <input v-if="showTaskQuantityInput()"
                                                       type="text"
                                                       ref="quantity"
                                                       class="form-control form-control-sm w-40"
                                                       :value="jobTask.qty"
                                                       @blur="updateCustomerTaskQuantity(
                                                           $event.target.value,
                                                           jobTask.id,
                                                           jobTask.qty)"
                                                >
                                                <div v-else class="mt-1">
                                                    <strong>{{ jobTask ? jobTask.qty : ''}}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div class="flex justify-content-between mt-1rem">
                                                <label class="">Quantity:</label>
                                                <strong>{{ jobTask ? jobTask.qty : ''}}</strong>
                                            </div>
                                        </div>

                                        <div class="form-group" ref="unitPrice" v-if="isContractor()">

                                            <div class="flex justify-content-between">
                                                <label class="">Unit Price:</label>
                                                <div v-if="showTaskPriceInput()" class="flex">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="text" ref="price"
                                                           class="form-control form-control-sm w-full"
                                                           :value="unit_price ? taskCustFinalPrice(unit_price) : '0'"
                                                           :class="(errors.unit_price || errors.priceMustBeANumber) ? 'box-error': ''"
                                                           @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, job.id)">
                                                </div>
                                                <div v-else class="mt-1">
                                                    <strong v-if="unit_price">{{ taskCustFinalPrice(unit_price)
                                                        }}</strong>
                                                </div>
                                            </div>
                                            <div class="errorClass" v-if="errors.unit_price">Your Contractor Task Price Must
                                                Be
                                                Higher The Sub Price
                                            </div>
                                            <div class="errorClass" v-if="errors.priceMustBeANumber">Your Input Must Be A
                                                Number
                                            </div>
                                        </div>
                                        <!-- <button v-if="isContractor()" class="btn btn-green btn-large m-t-3" v-show="
                                    jobTask.status === 'bid_task.reopened' ||
                                    jobStatus === 'bid.initiated' ||
                                    jobStatus === 'bid.in_progress' ||
                                    jobStatus === 'bid.declined'
                                ">Update
                                </button> -->
                                    </div>

                                </div>
                            </div>
                        </div>
                    </card>
                </div>

                <!-- location -->
                <div class="col-12">
                    <h1 class="card-title mt-4">Job Task Location</h1>
                    <card>
                        <div class="row">

                            <!-- / location -->
                            <div class="col-12">

                                <!--                                    <a target="_blank"-->
                                <!--                                       :href="'https://www.google.com/maps/search/?api=1&query=' + location(jobTask, job)">-->
                                <!--                                        <i class="fas fa-map-marker icon"></i>-->
                                <!--                                        {{ location(jobTask, job) }}-->
                                <!--                                    </a>-->
                                <a
                                        v-if="getAddress() !== 'Address Not Available'"
                                        target="_blank"
                                        :href="'https://www.google.com/maps/search/?api=1&query=' + getAddress()">
                                    <!--                                    <i class="fas fa-map-marker icon"></i>-->
                                    <div class="flex flex-col location" v-if="jobLocationHasBeenSet">
                                        <div>
                                            {{ getAddressLine1 }}
                                        </div>
                                        <div>
                                            {{ getCity }}, {{ getLocationState }} {{ getZip }}
                                        </div>
                                    </div>
                                </a>
                                <div v-else class="location">
                                    Job Location Has Not Been Set
                                </div>

                                <hr>

                                <div v-if="location(jobTask, job) === 'No Address Set Yet'">
                                    <i class="fas fa-map-marker icon"></i>
                                    {{ location(jobTask, job) }}
                                </div>
                                <div class="flex flex-col"
                                     v-else-if="location(jobTask, job) === 'Same as Job Location'">
                                    <button class="btn btn-normal btn-md" @click="openUpdateTaskLocation(jobTask.id)">
                                        <span class="mr-1rem">Change Task Location</span><i class="fas fa-edit"></i>
                                    </button>
                                </div>
                                <div v-else class="flex flex-col">
                                    <a target="_blank"
                                       :href="'https://www.google.com/maps/search/?api=1&query=' + location(jobTask, job)">
                                        <i class="fas fa-map-marker icon"></i>
                                        {{ location(jobTask, job) }}
                                    </a>
                                    <button class="btn btn-normal btn-md" @click="openUpdateTaskLocation(jobTask.id)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </card>
                </div>

                <!-- images -->
                <div class="col-12">
                    <h1 class="card-title mt-4">Images</h1>
                    <p>Only allowable file types are JPG, PNG, GIF or WebP files</p>
                    <card>
                        <div class="row">

                            <div class="col-12">
                                <task-images :jobTask="jobTask" type="notsub">
                                </task-images>
                            </div>
                        </div>
                    </card>
                </div>

                <!-- special instructions -->
                <div class="col-12">
                    <h1 class="card-title mt-4">Special Instructions</h1>
                    <card>
                        <div class="row">

                            <div class="col-12">
                                <div class="flex flex-col">
                                    <div class="flex flex-col mb-3" v-if="isContractor()">
                                        <message label="Notes for Subcontractor" :jobId="jobTask ? jobTask.id : -1"
                                                 :server-message="jobTask.sub_message" actor='sub'
                                                 :disable-messages="disableMessages">
                                        </message>
                                    </div>
                                    <div class="flex flex-col" v-if="isContractor()">
                                        <message label="Notes For Customer" :jobId="jobTask ? jobTask.id : -1"
                                                 :server-message="jobTask ? jobTask.customer_message : null"
                                                 actor='customer'
                                                 :disable-messages="disableMessages"></message>
                                    </div>

                                    <div class="flex flex-col" v-if="!isContractor()">
                                        <span class="label mb-2">Notes from Contractor</span>
                                        <textarea cols="0" rows="0" class="form-control" disabled
                                                  :value="getContractorNotesForCustomer"
                                                  style="color: black;"></textarea>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </card>
                </div>

                <!-- Subcontractor bids -->
                <div class="col-12" v-show="
                                           showSubsPanel &&
                                           (isGeneral() && !taskApproved && jobTask.bid_contractor_job_tasks.length > 0) &&
                                           ((!checkIfBidHasBeenAccepted(jobTask) && checkIfBidHasBeenSent(job)) ||
                                            (checkIfBidHasBeenAccepted(jobTask)) ||
                                            (!checkIfBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(job)))">
                    <h1 id="bids" class="card-title mt-4">Bids</h1>
                    <card>
                        <div class="row">
                            <!--show this box if the show button has been selected and if showSubsPanel is selected then show -->
                            <div class="col-12">
                                <div v-if="showSubsPanel" class="mt-4">
                                    <div :id="jobTask ? 'task-divider-' + jobTask.id : 0" :key="1"></div>

                                    <div :id="jobTask ? 'task-subs-' + jobTask.id : 0"
                                         v-if="isGeneral() && !taskApproved && jobTask.bid_contractor_job_tasks.length > 0"
                                         :key="3">
                                        <div class="flex flex-col">
                                            <div class="table-header">
                                                <div class="flex-1">Sub</div>
                                                <div class="flex-1">Bid</div>
                                                <div class="flex-1">Action</div>
                                            </div>
                                            <div class="flex mb-2 justify-content-between"
                                                 v-for="bid in jobTask.bid_contractor_job_tasks"
                                                 :key="jobTask ? bid.id : 0">
                                                <div class="flex-1 lookLikeALink"
                                                     @click="viewContractorInfo(bid.contractor_id)">{{
                                                    getCompanyName(bid) }}
                                                </div>
                                                <!--                                            <div class="flex-1 uppercase">{{ bid.payment_type }}</div>-->
                                                <div class="flex-1">${{ getBidPrice(bid) }}</div>
                                                <div class="flex-1">
                                                    <!-- <button v-if="showAcceptBtn(jobTask.status)" -->
                                                    <button
                                                            v-if="!checkIfBidHasBeenAccepted(jobTask, bid) && checkIfBidHasBeenSent(bid)"
                                                            @click="acceptSubBidForTask(bid, jobTask)"
                                                            class="btn btn-normal"
                                                            :disabled="disabled.accept">
                                                    <span v-if="disabled.accept">
                                                        <i class="fa fa-btn fa-spinner fa-spin"></i>
                                                    </span>
                                                        Accept
                                                    </button>
                                                    <div v-else-if="checkIfBidHasBeenAccepted(jobTask, bid)"><strong>Accepted</strong>
                                                    </div>
                                                    <div v-else-if="!checkIfAnyBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(bid)">
                                                        <strong>Pending</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </card>
                </div>

                <!-- Task Actions -->
                <div class="col-12 mb-4">
                    <h1 class="card-title mt-4"></h1>
                    <div class="flex space-between">

                        <button
                                class="btn btn-sm btn-normal w-full"
                                v-if="isGeneral()
                                       && !approvedByCustomer()"
                                ref="addASubButton"
                                @click.prevent="openSubInvite(jobTask.id)"
                        >Add A Sub

                        </button>


                        <button class="btn btn-sm btn-normal w-full" v-if="showFinishedBtn(jobTask)"
                                @click="finishedTask(jobTask)" :disabled="disabled.finished">
                            <span v-if="disabled.finished">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>Click Me When Job Is Finished
                        </button>

                        <button class="btn btn-sm btn-normal w-full"
                                v-if="showApproveBtn(jobTask)"
                                @click="approveTaskHasBeenFinished(jobTask)"
                                :disabled="disabled.approve">
                            <span v-if="disabled.approve">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>Approve
                        </button>

                        <button class="btn btn-sm btn-normal w-full" v-if="showDenyBtn(jobTask)"
                                @click="openDenyTaskForm(jobTask.id)">Deny
                        </button>
                        <button class="btn btn-sm btn-normal-red w-full"
                                v-if="showDeleteBtn(jobTask)"
                                @click="deleteTask(jobTask)"
                                :disabled="disabled.deleteTask">
                            <span v-if="disabled.deleteTask">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>Delete
                        </button>
                    </div>
                </div>
            </div>

            <sub-invite-modal v-if="isContractor()" :job-task="jobTask"
                              :job-task-task="jobTask ? jobTask.task : null"
                              :job-task-name="jobTask ? jobTask.task.name : null"
                              :id="jobTask ? jobTask.id : null">
            </sub-invite-modal>

            <deny-task-modal v-if="isContractor()"
                             :job-task="jobTask"
                             :id="jobTask ? jobTask.id : null">
            </deny-task-modal>

            <update-task-location-modal
                    v-show="isContractor()"
                    :job-task="jobTask"
                    :id="jobTask ? jobTask.id : null">
            </update-task-location-modal>

        </div>
        <feedback
                page="JobTask"
        ></feedback>
    </div>
</template>

<script>

  import SubInviteModal from '../components/task/SubInviteModal'
  import DenyTaskModal from '../components/task/DenyTaskModal'
  import UpdateTaskLocationModal from '../components/task/UpdateTaskLocationModal'
  import Message from '../components/job/Message.vue'
  import TaskImages from '../components/task/UploadTaskImages'
  import Format from '../classes/Format'
  import Card from '../components/shared/Card'
  import ContentSection from '../components/shared/ContentSection'
  import User from '../classes/User'
  import Feedback from '../components/shared/Feedback'

  import { mapState } from 'vuex'

  export default {
    components: {
      SubInviteModal,
      DenyTaskModal,
      Feedback,
      Card,
      Message,
      ContentSection,
      TaskImages,
      UpdateTaskLocationModal
    },
    data() {
      return {
        authUser: {},
        user: {},
        jobTask: null,
        currentJobTask: {},
        showDetails: false,
        start_date: '',
        start_when_accepted: true,
        startDateErrorMessage: '',
        hasStartDateError: false,
        startDateChanged: false,
        startDateError: false,
        message: '',
        sendSubMessage: true,
        sendCustomerMessage: true,
        customerMessage: '',
        cust_final_price: -1,
        unit_price: -1,
        sub_final_price: -1,
        errors: {
          unit_price: false,
          priceMustBeANumber: false
        },
        disabled: {
          showDenyForm: false,
          pay: false,
          finished: false,
          approve: false,
          reopen: false,
          deny: false,
          deleteTask: false,
          payCash: false,
          accept: false,
        }
      }
    },
    computed: {
      ...mapState({
        job: state => state.job.model,
        jobStatus: state => state.job.model.status
      }),
      jobLocationHasBeenSet() {
        if (this.jobTask && this.jobTask.location) {
          return true
        } else {
          return false
        }
      },
      getCustomerNotes() {
        if (this.jobTask) {
          if (this.jobTask.job.customer) {
            return this.jobTask.job.customer.customer.notes
          }
          return this.jobTask.customer.customer.notes
        }
      },
      getContractorNotesForCustomer() {
        if (this.jobTask) {
          return this.jobTask.customer_message
        }
      },
      getAddressLine1() {
        console.log('jobTask', this.jobTask)
        if (this.jobTask && this.jobTask.location) {
          return this.jobTask.location.address_line_1
        }
        return ''
      },
      getCity() {
        if (this.jobTask && this.jobTask.location) {
          return this.jobTask.location.city
        }
        return ''
      },
      getLocationState() {
        if (this.jobTask && this.jobTask.location) {
          return this.jobTask.location.state
        }
        return ''
      },
      getZip() {
        if (this.jobTask && this.jobTask.location) {
          return this.jobTask.location.zip
        }
        return ''
      },
      taskApproved() {
        return this.jobTask.status === 'bid_task.approved_by_customer'
      },
      isCustomer() {
        return this.authUser.isCustomer
      },
      // isContractor() {
      //   return this.user.isContractor()
      // },
      showSubsPanel() {
        return this.isContractor() && this.jobStatus !== 'job.approved' && this.jobStatus !== 'job.completed'
      },
      showSendSubInvite() {
        if (this.jobStatus === 'bid.initiated' || this.jobStatus === 'bid.in_progress') {
          return true
        }
        return false
      },
      generalTotalTaskPrice() {
        let total = 0
        for (const jobTask of this.job.job_tasks) {
          if (jobTask !== null) {
            total += cust_final_price
          }
        }
        return total
      },
      subTotalTaskPrices() {
        let total = 0
        for (const jobTask of this.job.job_tasks) {
          total += this.subTaskPrice(jobTask)
        }
        return total
      },
      disableMessages() {
        return this.jobStatus === 'job.completed'
      }
    },
    methods: {

      viewContractorInfo(id) {
        this.$router.push({name: 'contractor-info', params: {contractorId: id}})
      },

      approvedByCustomer() {
        return this.jobTask.status === 'bid_task.approved_by_customer' || this.jobTask.status === 'bid_task.finished_by_general'
      },
      getBidPrice(bid) {
        if (bid) {
          return bid.bid_price / 100
        }
      },
      goBack() {
        this.$router.go(-1)
      },
      getCompanyName(bid) {
        if (bid.contractor.contractor) {
          return bid.contractor.contractor.company_name
        } else {
          return bid.contractor.first_name + ' ' + bid.contractor.last_name
        }
      },
      checkIfAnyBidHasBeenAccepted(jobTask) {
        if (jobTask.bid_contractor_job_tasks.length > 0) {
          for (let i = 0; i < jobTask.bid_contractor_job_tasks.length; i++) {
            if (jobTask.bid_contractor_job_tasks[i].accepted === 1) {
              return true
            }
          }
        } else {
          return false
        }
      },
      checkIfBidHasBeenSent(bid) {
        return bid.updated_at !== null && bid.status === 'bid_task.bid_sent'
      },
      showSubMessage(msg) {
        return (msg !== null &&
          msg !== '' &&
          msg !== ' ' &&
          this.isContractor()) ||
          (msg != null &&
            msg !== '' &&
            msg !== ' ' &&
            this.isGeneral())
      },
      showCustomerMessage(msg) {
        return (msg != null &&
          msg !== '' &&
          msg !== ' ' &&
          this.isCustomer) ||
          (msg != null &&
            msg !== '' &&
            msg !== ' ' &&
            this.isGeneral())
      },
      showPanelActions(status) {
        if (status !== 'bid_task.customer_sent_payment') {
          return true
        }
        return false
      },
      showAcceptBtn(status) {
        return status === 'bid_task.bid_sent'
      },
      openTaskBids(id) {
        if ($('#task-options-' + id).hasClass('hidden') || this.isCustomer) {
          $('#task-divider-' + id).toggleClass('hidden')
        }
        $('#task-subs-' + id).toggleClass('hidden')
      },
      openTaskActions(id) {
        if ($('#task-subs-' + id).hasClass('hidden') || this.isCustomer) {
          $('#task-divider-' + id).toggleClass('hidden')
        }
        $('#task-options-' + id).toggleClass('hidden')
      },
      preview(jobTask, subId) {
        Bus.$emit('previewSubForTask', [
          jobTask.job_id,
          jobTask.id,
          subId
        ])
      },
      acceptSubBidForTask(bid, jobTask) {
        GeneralContractor.acceptSubBidForTask(jobTask, bid, this.disabled)
      },
      showStripeToggle(jobTask) {
        return this.authUser.isAssignedToMe(jobTask, this.user.id) && (this.jobStatus === 'bid.initiated' || this.jobStatus ===
          'bid.in_progress')
      },
      updateMessage(jobTaskId, currentMessage, actor) {
        let message = document.getElementById('message-' + actor + '-' + jobTaskId)
        message = message.value
        if (message !== currentMessage) {
          GeneralContractor.updateMessage(message, jobTaskId, actor)
        }
        if (actor === 'sub') {
          this.sendSubMessage = false
          setTimeout(function() {
            this.sendSubMessage = true
          }.bind(this), 2000)
        } else {
          this.sendCustomerMessage = false
          setTimeout(function() {
            this.sendCustomerMessage = true
          }.bind(this), 2000)
        }
      },
      userIsAGeneralContractor() {
        return Spark.state.user.usertype === 'contractor' && Spark.state.user.id === this.jobTask.job.contractor_id
      },
      userIsACustomer() {
        return Spark.state.user.usertype === 'customer'
      },
      jobHasSubs() {
        return this.jobTask.bid_contractor_job_tasks.length > 0
      },
      subHasFinishedTheJob() {
        return this.jobTask.status === 'bid_task.finished_by_sub'
      },
      jobHasNotBeenSubmittedToTheCustomer() {
        return this.jobTask.status !== 'bid_task.approved_by_general'
      },
      jobHasBeenApprovedByTheGeneral() {
        return this.jobTask.status === 'bid_task.approved_by_general'
      },
      generalHasFinishedTheJob() {
        return this.jobTask.status === 'bid_task.approved_by_general'
      },
      showDenyBtn(jobTask) {
        if (jobTask) {

          if (this.userIsAGeneralContractor()) {
            if (this.jobHasSubs() && this.subHasFinishedTheJob() && this.jobHasNotBeenSubmittedToTheCustomer()) {
              return true
            } else {
              return false
            }
          } else if (this.userIsACustomer()) {
            if (this.jobHasBeenApprovedByTheGeneral) {
              return true
            } else {
              return false
            }
          } else {
            return false
          }

          // should not see this if I am a general and the task has no subs

          // I should see this if I am a general and I have subs

          // I should see this if I am a customer and the task has been finished and approved by the general

          // const status = jobTask.status
          // if (this.isCustomer) {
          //   return (status === 'bid_task.finished_by_general' || status === 'bid_task.approved_by_general')
          // }
          // return (status === 'bid_task.finished_by_sub' || status === 'bid_task.reopened')
          // return (status === 'bid_task.finished_by_sub' || this.jobStatus === 'bid.declined');
        }
      },
      showFinishedBtn(jobTask) {
        if (this.isContractor() &&
          this.authUser.isAssignedToMe(jobTask, this.user.id) &&
          (jobTask.status === 'bid_task.approved_by_customer'
            || jobTask.status === 'bid_task.reopened'
            || jobTask.status === 'bid_task.finished_by_sub'
            || jobTask.status === 'bid_task.denied'
          )) {
          return true
        }
        return false
      },
      showApproveBtn(jobTask) {
        if (this.isGeneral() &&
          !this.authUser.isAssignedToMe(jobTask, this.user.id) &&
          (jobTask.status === 'bid_task.finished_by_sub' || jobTask.status === 'bid_task.reopened')
        ) {
          return true
        }
        return false
      },
      showDeleteBtn(jobTask) {
        if (jobTask) {
          const status = jobTask.status
          if (this.isGeneral() && (status === 'bid_task.initiated' || status === 'bid_task.bid_sent' || this.jobStatus ===
            'bid.declined')) {
            return true
          }
          return false
        }
      },
      // reopenTask(jobTask) {
      //   SubContractor.reopenTask(jobTask, this.disabled);
      // },
      deleteTask(jobTask) {
        GeneralContractor.deleteTask(jobTask, this.disabled)
      },
      /**
       * customer task price
       */
      subTaskPrice(jobTask) {
        if (jobTask === null) {
          return 0
        }
        if (jobTask.bid_id === null) {
          return 0
        } else {
          return this.authUser.findTaskBid(jobTask.bid_id, jobTask.bid_contractor_job_tasks)[0].bid_price
        }
      },
      toggleStripePaymentOption(jobTask) {
        jobTask.checked = $('#toggle-stripe-' + jobTask.id).is(':checked')
        SubContractor.toggleStripePaymentOption(jobTask)
      },
      // payForTask(jobTask) {
      //   Customer.payForTask(jobTask, this.disabled);
      // },
      // paidWithCashTask(jobTask) {
      //   Customer.paidWithCashTask(jobTask, this.disabled);
      // },
      openTaskPanel(index) {
        this.$emit('openTaskPanel', index)
      },
      finishedTask(jobTask) {
        SubContractor.finishedTask(jobTask, this.disabled)
      },
      approveTaskHasBeenFinished(jobTask) {
        GeneralContractor.approveTaskHasBeenFinished(jobTask, this.disabled)
      },
      checkIfBidHasBeenAccepted(jobTask, bid) {
        // debugger;
        if (bid) {
          return bid.accepted === 1
        }
        // try {
        //   if (bid.accepted === 1) {
        //     console.log(JSON.stringify(bid));
        //     return true
        //   } else {
        //     console.log(JSON.stringify(bid));
        //     return false
        //   }
        // } catch (e) {
        //   debugger;
        //   console.log(e)
        // }

        // if (jobTask.bid_contractor_job_tasks.length > 0) {
        //   for (let i = 0; i < jobTask.bid_contractor_job_tasks.length; i++) {
        //     if (jobTask.bid_contractor_job_tasks[i].accepted === 1) {
        //       return true
        //     }
        //   }
        // } else {
        //   return false
        // }
      },
      isGeneral() {
        if (this.jobTask && this.jobTask.task) {
          return this.jobTask.task.contractor_id === this.user.id
        }
        return false
      },
      prettyDate(date) {
        if (date == null)
          return ''
        // return the date and ignore the time
        date = date.split(' ')
        return date[0]
      },
      showTaskStartDate() {
        // return this.isGeneral() && (
        //   this.jobStatus === 'bid.in_progress' ||
        //   this.jobStatus === 'bid.initiated' ||
        //   this.jobStatus === 'bid.declined')

        return true

      },
      updateTaskStartDate(date, jobTaskId) {

        // debugger

        let dateArray = GeneralContractor.checkDateIsTodayorLater(date, this.job.created_at)
        this.startDateErrorMessage = dateArray[0]
        this.hasStartDateError = dateArray[1]

        if (!this.hasStartDateError) {
          GeneralContractor.updateTaskStartDate(date, jobTaskId)
        } else {
          this.startDateErrorMessage = 'Task Date Cannot Be Before Bid Creation Date'
        }
      },
      openUpdateTaskLocation(jobTaskId) {
        $('#update-task-location-modal_' + jobTaskId).modal()
      },
      openDenyTaskForm(jobTaskId) {
        $('#deny-task-modal_' + jobTaskId).modal()
      },
      openSubInvite(jobTaskId) {
        // debugger;
        // this.currentJobTask = jobTask;
        $('#sub-invite-modal_' + jobTaskId).modal()
      },
      location(jobTask, bid) {
        if (this.job && jobTask && jobTask.location) {
          // debugger;
          const task_location = jobTask.location_id
          const job_location = this.job.location_id
          if (task_location === null && job_location === null) {
            return 'No Address Set Yet'
          } else if (job_location === job_location) {
            return 'Same as Job Location'
          } else if (task_location !== null) {
            return jobTask.location.address_line_1
          } else if (job_location) {
            return bid.location.address_line_1
          }
        }
      },
      getAddress() {
        if (this.jobTask && this.jobTask.location) {
          console.log(JSON.stringify(this.jobTask.location))
          if (this.jobTask.location !== null) {
            return this.jobTask.location.address_line_1 + ' ' +
              this.jobTask.location.address_line_2 + ' ' +
              this.jobTask.location.city + ' ' +
              this.jobTask.location.state + ' ' +
              this.jobTask.location.zip
          } else {
            return 'Address Not Available'
          }
        }

        // return bidTask.job_task.location.address_line_1+" "+

        // <a target="_blank" href="https://www.google.com/maps/search/?api=1&amp;query=3140 Talon Track Apt. 800  McCulloughton Utah 42620-5408">
        // let location_id = 0;
        // if (bidTask.job_task.location_id !== null) {
        //   location_id = bidTask.job_task.location_id;
        // } else {
        //   location_id = bidTask.job_task.job.location_id;
        // }
        // Customer.getAddress(location_id, this.location)
        // return this.location.location
      },
      updateCustomerTaskPrice(price, jobTaskId, bidId) {

        if (isNaN(price)) {
          price = this.removeDollarSigns(price)
        }

        if (this.unit_price !== parseFloat(price)) {

          this.errors.priceMustBeANumber = price !== '' && isNaN(price)
          this.errors.unit_price = this.sub_final_price > price

          this.unit_price = price
          this.cust_final_price = price * this.jobTask.qty

          if ((this.sub_final_price <= price && !this.errors.priceMustBeANumber)) {
            GeneralContractor.updateCustomerPrice(price, jobTaskId, bidId)
          }
        } else {
          this.errors.priceMustBeANumber = price !== '' && isNaN(price)
          this.errors.unit_price = this.sub_final_price > price
        }
      },
      isContractor() {
        return this.user.usertype === 'contractor'
      },
      showTheJobTaskDetails(value) {
        if (value === 'show') {
          this.showDetails = true
        } else {
          this.showDetails = false
        }
      },
      status(status) {
        return this.authUser.status(status, this.job)
      },
      getLabelClass(status) {
        return Format.statusLabel(status)
      },
      showTaskPriceInput() {
        return this.isGeneral()
        // return this.isGeneral() &&
        //   (this.jobStatus === 'bid.in_progress' ||
        //     this.jobStatus === 'bid.initiated' ||
        //     this.jobStatus === 'bid.declined')
      },
      showTaskQuantityInput() {
        return this.isGeneral() && (this.jobTask.status === 'bid_task.initiated')
      },
      updateCustomerTaskQuantity(quantity, taskId, currentQuantityValue) {

        quantity = Number(quantity)

        this.jobTask.qty = quantity

        if (quantity != currentQuantityValue) {
          GeneralContractor.updateCustomerTaskQuantity(quantity, taskId)

          let totalPrice = this.unit_price * quantity

          this.cust_final_price = totalPrice.toFixed(2)
        }

      },
      removeDollarSigns(price) {
        return price.replace(/[$]+/g, '')
      },
      taskCustFinalPrice(price, sub, total = false) {

        if (this.jobTask) {
          if (typeof price === 'string') {
            price = this.removeDollarSigns(price)
          }

          price = parseFloat(price)

          let priceCheck = this.unit_price * this.jobTask.qty

          if (!this.isCustomer) {
            if (!sub && (this.cust_final_price !== priceCheck)) {
              this.cust_final_price = this.unit_price * this.jobTask.qty

              if (this.job[0]) {
                GeneralContractor.updateCustomerPrice(price, this.jobTask.id, this.job[0].id)
              } else if (this.job) {
                GeneralContractor.updateCustomerPrice(price, this.jobTask.id, this.job.id)
              }

            } else if (sub && total) {
              price = price * this.jobTask.qty
            }
          }

          if (price === 0) {
            return '$0.00'
          }

          if (price) {
            let priceString = price.toString()
            if (priceString.indexOf('.') === -1) {
              price = '$' + price + '.00'
            } else {
              price = '$' + price
            }
            return price
          }
        }

      },
      async getTask() {
        try {
          const data = await axios.get('/getJobTaskForGeneral/' + this.jobTask.id + '/' + this.user.id)
          this.jobTask = data.data[0]

          if (this.isContractor()) {
            this.sub_final_price = this.jobTask.sub_final_price
          }

        } catch (error) {
          console.log(error.message)
        }
      },
      checkThatThereIsAContractorPrice() {

        if (
          parseInt(this.cust_final_price) <= 0 &&
          this.jobTask.qty > 0 &&
          this.unit_price > 0
        ) {
          this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.job.id)
          return false
        }

        return true
      }
      // showReopenBtn(jobTask) {
      //   if (this.isContractor && (jobTask.status === 'bid_task.finished_by_general' || jobTask.status ===
      //       'bid_task.approved_by_general')) {
      //     return true;
      //   }
      //   return false;
      // },
      // showPayCashForTaskBtn(jobTask) {
      //   return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status === 'bid_task.approved_by_general') &&
      //     this.user.isCustomer();
      // },
      // showPayForTaskBtn(jobTask) {
      //   return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status === 'bid_task.approved_by_general') &&
      //     this.user.isCustomer() && jobTask.stripe;
      // },
    },
    mounted() {

      Bus.$on('bidUpdated', () => {
        this.getTask()
      })

      if (this.$store.state.job.model.job_tasks) {
        this.jobTask = this.$store.state.job.model.job_tasks[this.$route.params.index]
      } else if (this.$store.state.job.model[0].job_tasks) {
        this.jobTask = this.$store.state.job.model[0].job_tasks[this.$route.params.index]
      }

      if (this.jobTask) {
        this.cust_final_price = this.jobTask.cust_final_price
        this.sub_final_price = this.jobTask.sub_final_price
        this.unit_price = this.jobTask.unit_price

        if (
          parseInt(this.cust_final_price) <= 0 &&
          this.jobTask.qty > 0 &&
          this.unit_price > 0
        ) {

          if (this.$store.state.job.model[0].id > 0) {
            this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.$store.state.job.model[0].id)
          } else if (this.$store.state.job.id !== '') {
            this.updateCustomerTaskPrice(this.unit_price, this.jobTask.id, this.$store.state.job.id)
          }

        }
      }
      this.user = Spark.state.user

    },
    created() {
      document.body.scrollTop = 0 // For Safari
      document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
      this.authUser = new User()
    }
  }
</script>

<style scoped>

    .location {
        align-items: center;
        font-weight: bold;
        font-size: 16pt;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
    }

    .flex-col {
        flex-direction: column;
    }

    .flex {
        display: flex;
        justify-content: space-between;
    }

    .bid-task-wrapper {
        display: grid;
        grid-template-columns: 55% 1fr;
        /*margin-top: 1rem;*/
        /*padding: .75rem;*/
        padding: .15rem .05rem .15rem .15rem;
        /*grid-row-gap: 1rem;*/
        grid-row-gap: .5rem;
    }

    .box {
        border: white thin solid;
        border-radius: 5px;
        margin: .1rem;
        padding: .75rem;
    }

    .btn-bid-task {
        background-color: #1c3d5a;
    }

    .bid-task-border {
        border-bottom: grey thin solid;
    }

    .messageHeader {
        font-size: 12pt;
        font-weight: bold;
        font-family: Roboto, serif;
        text-align: center;
    }

    .lookLikeALink {
        text-decoration: underline;
        color: #1976d2 !important;
    }

</style>
