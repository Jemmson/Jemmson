<template>
    <div class="row">

        <v-col cols="5">
            <job-stepper
                    :status="getSelectedJob()"
                    :user="getUser()"
                    style="padding: 0;"
            ></job-stepper>
        </v-col>

        <v-col cols="7">
            <card v-if="showDeclinedMessage"
                  style="background-color: lightcoral">
                {{ bid.declined_message }}
            </card>
            <v-card>
                <v-card-title>Details</v-card-title>

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
                            <td class="capitalize">{{ bid.job_name }}</td>
                        </tr>
                        <tr @click="viewContractorInfo()">
                            <td>Contractor Name:</td>
                            <td class="lookLikeALink">{{ getCompanyName() }}</td>
                        </tr>
                        <tr v-if="!bidHasBeenSubmitted">
                            <td>Start Date:</td>
                            <td>Bid Not Complete</td>
                        </tr>
                        <tr v-if="bidHasBeenSubmitted">
                            <td>Start Date:</td>
                            <td>{{ agreedStartDate }}</td>
                        </tr>
                        <tr v-if="!bidHasBeenSubmitted">
                            <td>Total Bid Price:</td>
                            <td>Bid Not Complete</td>
                        </tr>
                        <tr v-if="bidHasBeenSubmitted">
                            <td>Total Bid Price:</td>
                            <td>{{ bidPrice }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>


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
                            <td class="capitalize">{{ bid.job_name }}</td>
                        </tr>
                        <tr @click="viewCustomerInfo()">
                            <td>Customer Name:</td>
                            <td class="lookLikeALink">{{ customerName }}</td>
                        </tr>
                        <tr>
                            <td>Start Date:</td>
                            <td>{{ agreedStartDate }}</td>
                        </tr>
                        <tr>
                            <td>Total Bid Price:</td>
                            <td>{{ bidPrice }}</td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>

                <v-btn
                        v-if="canAddATask() && !isCustomer"
                        class="w-100 btn btn-sm btn-normal flex-1"
                        width="100%"
                        name="addTaskToBid"
                        id="addTaskToBid"
                        @click="$router.push('/job/add/task')"
                >
                    Add A Task
                </v-btn>

            </v-card>


        </v-col>


        <section ref="job_tasks" class="col-12"
                 v-if="bid.job_tasks !== undefined"
        >
            <div v-if="!isCustomer && bid.job_tasks.length > 0">

                <h1 class="card-title mt-4">Job Tasks</h1>

                <v-card v-for="(item, i) in bid.job_tasks"
                        :key="i"
                        class="card-positioning"
                >
                    <v-card-title
                            class="uppercase"
                    >{{ jobTaskObject(item).Name }}
                    </v-card-title>
                    <v-card-subtitle
                            class="uppercase"
                    >{{ jobTaskObject(item).Status }}
                    </v-card-subtitle>
                    <v-divider></v-divider>
                    <v-row
                            class="justify-content-around"
                    >
                        <strong class="uppercase">Subs</strong>
                        <strong class="uppercase">Quantity</strong>
                        <strong class="uppercase">Price</strong>
                    </v-row>
                    <v-row
                            class="justify-content-around mb-15"
                    >
                        <div>{{ jobTaskObject(item).Subs }}</div>
                        <div>{{ jobTaskObject(item).Qty }}</div>
                        <div
                                v-if="jobTaskObject(item).Price"
                                v-text="'$ ' + jobTaskObject(item).Price"
                        ></div>
                        <div v-else>Price Not Set</div>
                    </v-row>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-btn
                                color="primary"
                                class="btn-size btn-weight"
                                :to="'/job/task/' + i"
                                text
                        >Edit <span class="btn-size btn-weight spacer-grey">|</span>
                        </v-btn>
                        <v-btn
                                v-if="isGeneral() && approvedByCustomer(item)"
                                @click="openSubInvite(item.id)"
                                class="btn-size btn-weight"
                                color="primary"
                                text
                        >Add Sub <span class="btn-size btn-weight spacer-grey">|</span>
                        </v-btn>
                        <v-btn
                                v-if="isGeneral() && subFinishedTask(item)"
                                @click="approveSubsWork(item)"
                                class="btn-size btn-weight"
                                color="primary"
                                text
                        >
                            Approve Subs Work <span class="btn-size btn-weight spacer-grey">|</span>
                        </v-btn>
                        <v-btn
                                color="primary"
                                v-if="isGeneral() && showFinishedBtn(item)"
                                @click="finishedTask(item)"
                                class="btn-size btn-weight"
                                :loading="disabled.finished"
                                text
                        >Mark When Finished
                        </v-btn>
                    </v-card-actions>

                    <sub-invite-modal v-if="isGeneral()" :job-task="item"
                                      :job-task-task="item ? item.task : null"
                                      :job-task-name="item ? item.task.name : null"
                                      :id="item ? item.id : null">
                    </sub-invite-modal>
                </v-card>
            </div>
            <div v-else-if="generalHasSentABid(bid)">
                <h1 class="card-title mt-4">Job Tasks</h1>
                <card>

                    <v-row>
                        <div>
                                <span class="">
                                    (<b ref="job_task_length_customer">{{bid.job_tasks.length}}</b>)
                                </span> Total
                        </div>

                        <v-spacer></v-spacer>

                        <v-btn
                                class="w-40"
                                color="primary"
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
                        <template v-for="jt in bid.job_tasks">
                            <tr
                                    :class="paid(jt) ? 'paid' : ''">
                                <td colspan="4"
                                    class="uppercase text-center"
                                >
                                    {{ getLatestJobTaskStatus(jt) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="capitalize">
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
            </div>

        </section>

        <!-- images -->
        <div class="col-12">
            <h1 class="card-title mt-4">Images</h1>
            <p>Only allowable file types are JPG, PNG, GIF or WebP files</p>
            <card>
                <div class="row">

                    <div class="col-12">
                        <task-images :job="bid" type="notsub">
                        </task-images>
                    </div>
                </div>
            </card>
        </div>

        <!-- / tasks -->

        <section class="col-12" v-if="(isCustomer && needsApproval()) || !isCustomer">
            <h1 class="card-title mt-4">Actions</h1>
            <card class="mb-4">
                <!-- /customer approve bid form -->
                <approve-bid v-if="isCustomer && needsApproval()" :bid="bid">
                </approve-bid>

                <v-sheet
                        class="text-center uppercase successful-submit"
                        v-if="submittedMessage"
                >You have successfully submitted a bid
                </v-sheet>

                <general-contractor-bid-actions
                        @bidSubmitted="bidSubmitted()"
                        :bid="bid" v-if="!isCustomer">
                </general-contractor-bid-actions>
            </card>
        </section>

        <section class="col-12" v-if="customerHasCompletedTasks()">
            <h1 class="card-title mt-4">Completed Tasks</h1>
            <card>
                <main class="row w-full ml-0">
                    <completed-tasks class="w-full" :bid="bid">
                    </completed-tasks>
                </main>
            </card>
        </section>

        <section ref="job_address" class="col-12" v-if="showAddress">
            <h1 class="card-title mt-4">Job Address</h1>
            <card>

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
        </section>

        <section class="col-12">
            <h1 v-if="isCustomer" class="card-title mt-4">Special Notes For The Job</h1>
            <h1 v-else class="card-title mt-4">Special Notes From Customer</h1>
            <card>
                <main class="row">
                    <section class="col-12">

                        <div style="display: none;">{{ messageFromCustomer }}</div>
                        <div style="display: none;">{{ getPaidWithCashMessage }}</div>


                        <textarea ref="message_text_area"
                                  v-model="customerNotesMessage"
                                  name="notes" id="notes" cols="30" rows="10"
                                  class="form-control"
                                  :disabled="!isCustomer"
                        >

                            </textarea>

                        <v-btn
                                v-if="isCustomer"
                                class="mt-1rem"
                                color="primary"
                                ref="update_customer_notes_button"
                                @click="updateGeneralContractorNotes"
                        >Submit
                        </v-btn>

                    </section>
                </main>
            </card>
        </section>
        <stripe :user="getCurrentUser()">
        </stripe>

    </div>
</template>

<script>
  import Info from '../shared/Info'
  import HorizontalTable from '../shared/HorizontalTable'
  import SubInviteModal from '../../components/task/SubInviteModal'
  import Format from '../../classes/Format'
  import Card from '../shared/Card'
  import Stripe from '../stripe/Stripe'
  import { mapGetters, mapMutations, mapActions, mapState } from 'vuex'
  import ContentSection from '../shared/ContentSection'
  import JobStepper from '../../components/shared/JobStepper'
  import CompletedTasks from './CompletedTasks'
  import ApproveBid from './ApproveBid'
  import GeneralContractorBidActions from './GeneralContractorBidActions'
  import Status from '../mixins/Status.js'
  import Utilities from '../mixins/Utilities'
  import TaskImages from '../../components/task/UploadJobImages'

  export default {
    components: {
      Card,
      Stripe,
      SubInviteModal,
      Info,
      ContentSection,
      CompletedTasks,
      HorizontalTable,
      JobStepper,
      TaskImages,
      ApproveBid,
      GeneralContractorBidActions
    },
    mixins: [Status, Utilities],
    props: {
      bid: Object,
      isCustomer: Boolean,
      customerName: String
    },
    created: function() {
      Bus.$on('needsStripe', () => {
        $('#stripe-modal').modal()
      })
      this.getUser()
      document.body.scrollTop = 0 // For Safari
      document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
    },
    data() {
      return {
        el: 2,
        area: {
          area: ''
        },
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
        if (this.bid.agreed_start_date !== undefined && this.bid.agreed_start_date !== null) {
          this.addTaskStartDate = false
          let d = this.bid.agreed_start_date
          let date = d.split(' ')
          let format_date = date[0].split('-')
          return format_date[1] + '/' + format_date[2] + '/' + format_date[0]
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
        if (this.bid && this.bid.customer) {
          this.customerNotesMessage = this.bid.customer.customer.notes
          return this.bid.customer.customer.notes
        }
      },
      getPaidWithCashMessage() {
        if (this.bid) {
          if (this.bid.paid_with_cash_message) {
            this.payWithCashMessage = this.bid.paid_with_cash_message
            this.selectedPayment = 'cash'
            return this.bid.paid_with_cash_message
          }
        }
      },
      bidPrice() {
        if (
          this.bid.bid_price &&
          (this.bid.status === 'bid.initiated' ||
            this.bid.status === 'bid.in_progress' ||
            this.bid.status === 'job.approved' ||
            this.bid.status === 'bid.declined' ||
            this.bid.status === 'bid.sent'
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
          if (this.generalHasSentABid(this.bid)) {
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
            && this.getJobStatus_latest(this.bid) === 'changed'
            // && this.bid.status === 'bid.declined'
          )
        }
      },

      disableSubmitBid() {
        // return this.bid.status === 'bid.sent'
        return this.generalCanSubmitABid(this.bid)
      },

      showAddress() {
        return (
          this.bid.location_id !== undefined &&
          this.bid.location_id !== null &&
          this.bid.location !== null &&
          !this.isCustomer
        )
      }
    },
    watch: {
      bid: function() {
        this.bid = this.bid
      }
    },
    methods: {

      bidSubmitted() {
        this.submittedMessage = true
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

      approveSubsWork(jobTask) {
        GeneralContractor.approveSubsTask(jobTask)
      },

      jobIsNotFinishedAndNotApproved(item) {
        if (item) {
          return !(item.status === 'job.approved'
            || item.status === 'bid.paid')
        }
      },
      showFinishedBtn(jobTask) {
        if (this.isGeneral() &&
          this.isAssignedToMe(jobTask, Spark.state.user.id) &&
          (jobTask.status === 'bid_task.approved_by_customer'
            || jobTask.status === 'bid.in_progress'
            || jobTask.status === 'bid_task.reopened'
            || jobTask.status === 'bid_task.finished_by_sub'
            || jobTask.status === 'bid_task.denied'
          )) {
          return true
        }
        return false
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
          && latestStatus !== 'paid'
      },
      getLatestJobTaskStatus(task) {

        if (task) {
          if (task.job_task_statuses) {
            return this.formatStatus(this.getJobTaskStatus_latest(task))
          } else {
            return this.formatStatus(this.getTheLatestJobTaskStatus(task.job_task_status))
          }
        }

        if (task && task.job_task_statuses) {
          return this.formatStatus(this.getJobTaskStatus_latest(task))
        }
      },
      getSelectedJob() {
        if (this.bid && this.bid.job_statuses) {
          return this.bid.job_statuses[this.bid.job_statuses.length - 1].status
        }
        // if (this.selectedJob && this.selectedJob.length > 0) {
        //   return this.selectedJob[0].status
        // }
      },
      canAddATask() {
        return this.bid.status !== 'job.approved' && this.bid.status !== 'bid.sent'
      },
      viewContractorInfo() {
        this.$router.push({name: 'contractor-info', params: {contractorId: this.bid.contractor.id}})
      },

      viewCustomerInfo() {
        this.$router.push({name: 'customer-info', params: {customerId: this.bid.customer.id}})
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
        if (this.bid) {
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
        if (this.bid && this.bid.job_tasks) {
          for (let i = 0; i < this.bid.job_tasks.length; i++) {
            let status = ''
            if (this.bid.job_tasks[i].job_task_status && this.bid.job_tasks[i].job_task_status.length > 0) {
              status = this.bid.job_tasks[i].job_task_status[this.bid.job_tasks[i].job_task_status.length - 1].status
            } else {
              status = this.bid.job_tasks[i].job_task_statuses[this.bid.job_tasks[i].job_task_statuses.length - 1].status
            }
            if (status === 'approved_subs_work' || status === 'general_finished_work') {
              taskIsFinished = true
            }
          }
        }
        return this.isCustomer && taskIsFinished
      },
      getCompanyName() {
        if (this.bid) {
          if (this.bid.job_tasks && (this.bid.job_tasks.length !== 0)) {
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
        if (this.bid.job_tasks) {
          return this.bid.job_tasks.length === 0
        }
      },
      getCurrentUser() {
        if (Spark.state) {
          return Spark.state.user
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
        for (let i = 0; i < this.bid.job_tasks.length; i++) {
          if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
            subTaskWarning = true
          }
        }

        if (!subTaskWarning) {
          this.subTaskWarning = false
          GeneralContractor.notifyCustomerOfFinishedBid(this.bid, this.disabled)
        } else {
          console.log('subs price is higher than contractor price')
          this.subTaskWarning = true
        }

      },

      isGeneral(contractor_id, user_id) {
        if (this.bid !== null) {
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
            setTimeout(function() {
              this.successfulUpdate = ''
              this.disabled.submitMessage = false
            }.bind(this), 2000)
          } else {
            this.successfulUpdate = 'false'
            setTimeout(function() {
              this.successfulUpdate = ''
              this.disabled.submitMessage = false
            }.bind(this), 2000)
          }

        } catch (error) {
          console.log('error')
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
      updateGeneralContractorNotes() {
        Customer.updateNotesForJob(
          this.customerNotesMessage,
          this.bid.customer.id
        )
      },
      updateArea() {
        // Customer.updateArea (this.area.area, this.bid.id);
      },
      showArea() {
        // console.log('user type: ' + User.isContractor())
        return this.area.area !== '' && !this.isCustomer
      },
      initializePayWithCashMessageValue() {
        if (this.bid && this.bid.paid_with_cash_message) {
          console.log('I am true')
          console.log(this.bid.paid_with_cash_message)
          this.payWithCashMessage = this.bid.paid_with_cash_message
          this.customerNotesMessage = this.bid.customer.customer.notes
        }
        if (this.bid && this.bid.customer) {
          console.log(this.bid.customer.customer.notes)
          this.customerNotesMessage = this.bid.customer.customer.notes
        }
      }
    },
    mounted() {
      this.initializePayWithCashMessageValue()
    }
  }
</script>

<style lang="less" scoped>

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