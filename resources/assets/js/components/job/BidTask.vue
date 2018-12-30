<template>
    <div>
        <div class="box shadow-md">
            <!--<pre>{{ isCustomer }} is a customer</pre>-->
            <!--<pre>{{ isContractor() }} is a contractor</pre>-->
            <div for="task-status" class="status task-status mb-6" :class="getLabelClass(jobTask.status)">
                {{ status(jobTask.status) }}
            </div>
            <div>
                <!-- task name-->
                <div class="uppercase flex justify-center text-2xl font-bold text-black mb-6">
                    {{ jobTask.task.name }}
                </div>
                <div class="flex">
                    <button
                            @click="showTheJobTaskDetails('show')"
                            class="uppercase btn btn-primary btn-bid-task flex-1"
                            v-show="!showDetails">show
                    </button>
                    <button
                            @click="showTheJobTaskDetails('hide')"
                            class="uppercase btn btn-primary btn-bid-task flex-1"
                            v-show="showDetails">hide
                    </button>
                </div>
            </div>
        </div>
        <div class="box shadow-md" v-show="showDetails">
            <div class="flex flex-col">
                <div class="bid-task-wrapper">
                    <div>
                        Total Task Price
                    </div>
                    <div>
                        <i class="fas fa-money-bill-alt icon"></i>
                        <span class="totalCost" v-if="jobTask.task.qty !== null">{{taskCustFinalPrice(jobTask.cust_final_price)}}</span>
                    </div>
                    <div v-if="isContractor()">Total Task Sub Price</div>
                    <div v-if="isContractor()">
                        <i class="fas fa-user icon"></i>
                        <span class="totalCost">{{taskCustFinalPrice(jobTask.sub_final_price)}}</span>
                    </div>

                    <div class="flex flex-col mr-6" v-if="isContractor()">
                        <label class="">Quantity:</label>
                        <input v-if="showTaskPriceInput()"
                               type="text" ref="quantity"
                               class="form-control mt-2"
                               :value="jobTask.qty" @blur="updateCustomerTaskQuantity(
                                   $event.target.value,
                                   jobTask.id,
                                   jobTask.qty)">
                        <div v-else class="mt-1">
                            {{ jobTask.qty }}
                        </div>
                    </div>

                    <div class="flex flex-col" v-if="isContractor()">
                        <label class="">Price:</label>
                        <input v-if="showTaskPriceInput()"
                               type="text" ref="price"
                               class="form-control mt-2"
                               :value="taskCustFinalPrice(jobTask.unit_price)"
                               @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)">
                        <div v-else class="mt-1">
                            {{ taskCustFinalPrice(jobTask.unit_price) }}
                        </div>
                    </div>
                </div>
                <button v-if="isContractor()"
                        class="btn btn-green btn-large m-t-3"
                        v-show="
                            jobTask.status === 'bid_task.reopened' ||
                            bid.status === 'bid.initiated' ||
                            bid.status === 'bid.in_progress' ||
                            bid.status === 'bid.declined'
                        ">Update
                </button>

                <div class="bid-task-wrapper mb-2 mt-2">
                    <label>Task Start Date</label>
                    <!-- <i class="fas fa-clock icon m-r-2"></i> -->
                    <input type="date" class="form-control form-control-date" style=""
                           v-if="showTaskStartDate()" :value="prettyDate(jobTask.start_date)"
                           @blur="updateTaskStartDate($event.target.value, jobTask.id)">
                    <div v-else>
                        {{ prettyDate(jobTask.start_date) }}
                    </div>
                    <span :class="{ error: hasStartDateError }" v-show="hasStartDateError">{{ startDateErrorMessage }}
                        </span>
                </div>

            </div>
        </div>
        <div class="box shadow-md" v-show="showDetails">
            <div class="flex flex-col">
                <div class="bid-task-wrapper">
                    <div v-if="location(jobTask, bid) === 'No Address Set Yet'">
                        <i class="fas fa-map-marker icon"></i>
                        {{ location(jobTask, bid) }}
                    </div>
                    <div class="flex flex-col" v-else-if="location(jobTask, bid) === 'Same as Job Location'">
                        <span class="label mb-2">Change Task Location</span>
                        <button class="btn btn-blue btn-small" @click="openUpdateTaskLocation(jobTask.id)">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div v-else class="flex flex-col">
                        <a target="_blank"
                           :href="'https://www.google.com/maps/search/?api=1&query=' + location(jobTask, bid)">
                            <i class="fas fa-map-marker icon"></i>
                            {{ location(jobTask, bid) }}
                        </a>
                        <button class="btn btn-blue btn-small" @click="openUpdateTaskLocation(jobTask.id)">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="box shadow-md" v-show="showDetails">
            <task-images class="mb-4" :jobTask="jobTask" type="notsub">
            </task-images>
        </div>
        <div class="box shadow-md" v-show="showDetails">
            <div class="messageHeader mb-4">Messages</div>

            <div class="flex flex-col">
                <div class="flex flex-col mb-3" v-if="!isCustomer">
                    <message
                            label="Notes for Subcontractor"
                            :jobId="jobTask.id"
                            :server-message="jobTask.sub_message"
                            actor='sub'
                            :disable-messages="disableMessages"
                    ></message>
                </div>
                <div class="flex flex-col" v-if="isContractor()">
                    <message
                            label="Notes For Customer"
                            :jobId="jobTask.id"
                            :server-message="jobTask.customer_message"
                            actor='customer'
                            :disable-messages="disableMessages"
                    ></message>
                </div>

                <div class="flex flex-col" v-if="isCustomer">
                    <span class="label mb-2">Notes from Contractor</span>
                    <textarea cols="0" rows="0" class="form-control"
                              disabled :value="jobTask.customer_message" style="color: black;"></textarea>
                </div>

            </div>
        </div>


        <!--show this box if
            the show button has been selected and
            if showSubsPanel is selected then show -->
        <div class="box shadow-md" v-show="showDetails &&
                                           showSubsPanel &&
                                           (isGeneral() && !taskApproved && jobTask.bid_contractor_job_tasks.length > 0) &&
                                           ((!checkIfBidHasBeenAccepted(jobTask) && checkIfBidHasBeenSent(bid)) ||
                                            (checkIfBidHasBeenAccepted(jobTask)) ||
                                            (!checkIfBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(bid)))">
        <!--<div class="box shadow-md">-->
            <!--<pre>{{ showDetails }} -> the show hide button was selected </pre>-->
            <!--<pre>{{ showSubsPanel }}-> show the sub panel if the user is a contractor and-->
                                    <!--the job is not approved and-->
                                    <!--the job has not been completed</pre>-->
            <!--<pre>{{ isGeneral() && !taskApproved && jobTask.bid_contractor_job_tasks.length > 0 }} the user is a general && the task is not approved && subs have been added</pre>-->
            <!--<pre>{{ !checkIfBidHasBeenAccepted(jobTask) && checkIfBidHasBeenSent(bid) }} the bid has not been accepted && the bid has not been sent by the sub</pre>-->
            <!--<pre>{{ checkIfBidHasBeenAccepted(jobTask) }} the bid has been accepted</pre>-->
            <!--<pre>{{ !checkIfBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(bid) }} the bid has not been accepted and the sub has not sent a bid</pre>-->
            <!--<pre>{{ !checkIfBidHasBeenAccepted(jobTask) && checkIfBidHasBeenSent(bid) }} if the bid has not been accepted and the bid has been sent</pre>-->
            <!--<pre>{{ checkIfBidHasBeenAccepted(jobTask) && bid.accepted === 1 }} if the bid has been accepted</pre>-->
            <!--<pre>{{ !checkIfBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(bid) }} if the bid has not been accepted and the bid has not been sent</pre>-->
            <!--<pre>{{ isGeneral() }} -> checks to see the contractor is a general contractor</pre>-->
            <!--<pre>{{ !taskApproved }} -> this task is not approved</pre>-->
            <!--<pre>{{ !checkIfBidHasBeenAccepted(jobTask) }} -> checks if the contractor has accepted one of the bids</pre>-->
            <!--<pre>{{ checkIfBidHasBeenSent(bid) }} -> checks if the contractor has sent the bid</pre>-->
            <!--<pre>{{ bid.accepted }} -> value of the bid being accepted or not</pre>-->
            <!--<pre>{{ !checkIfBidHasBeenSent(bid) }} -> checks if the contractor has sent the bid</pre>-->
            <!--<pre>{{ isGeneral() && !taskApproved && jobTask.bid_contractor_job_tasks.length > 0 }}</pre>-->
            <!--<pre>{{ showDetails &&-->
                                           <!--showSubsPanel &&-->
                                           <!--(isGeneral() && !taskApproved) &&-->
                                           <!--((!checkIfBidHasBeenAccepted(jobTask) && checkIfBidHasBeenSent(bid)) ||-->
                                            <!--(checkIfBidHasBeenAccepted(jobTask) && bid.accepted === 1) ||-->
                                            <!--(!checkIfBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(bid)))-->
                <!--}} -> total value of all settings</pre>-->
            <div v-if="showSubsPanel" class="mt-4">
                <div :id="'task-divider-' + jobTask.id" :key="1"></div>

                <div :id="'task-subs-' + jobTask.id"
                     v-if="isGeneral() && !taskApproved && jobTask.bid_contractor_job_tasks.length > 0"
                     :key="3">
                    <div class="flex flex-col">
                        <div class="table-header">
                            <div class="flex-1">Sub</div>
                            <div class="flex-1">Payment Type</div>
                            <div class="flex-1">Bid</div>
                            <div class="flex-1">Action</div>
                        </div>
                        <div class="flex pl-2 mb-2" v-for="bid in jobTask.bid_contractor_job_tasks" :key="bid.id">
                            <div class="flex-1">{{ bid.contractor.name }}</div>
                            <div class="flex-1 uppercase">{{ bid.payment_type }}</div>
                            <div class="flex-1">${{ bid.bid_price }}</div>
                            <div class="flex-1">
                                <!-- <button v-if="showAcceptBtn(jobTask.status)" -->
                                <button v-if="!checkIfBidHasBeenAccepted(jobTask) && checkIfBidHasBeenSent(bid)"
                                        @click="acceptSubBidForTask(bid, jobTask)" class="btn btn-green"
                                        :disabled="disabled.accept">
                                            <span v-if="disabled.accept">
                                              <i class="fa fa-btn fa-spinner fa-spin"></i>
                                            </span>
                                    Accept
                                </button>
                                <div v-else-if="checkIfBidHasBeenAccepted(jobTask)">
                                    <h5>Bid Has Been Accepted</h5>
                                </div>
                                <div v-else-if="!checkIfBidHasBeenAccepted(jobTask) && !checkIfBidHasBeenSent(bid)">
                                    <h5>Pending</h5></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="box shadow-md" v-show="showDetails &&
                                            (showDenyBtn(jobTask) || showDeleteBtn(jobTask) ||
                                            (isGeneral && showSendSubInvite && !checkIfBidHasBeenAccepted(jobTask)) ||
                                            (showFinishedBtn(jobTask) || showApproveBtn(jobTask)))">
            <div class="flex w-full justify-between">
                <button class="btn btn-red" v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask.id)">
                    Deny
                </button>

                <button class="btn btn-red" v-if="showDeleteBtn(jobTask)" @click="deleteTask(jobTask)"
                        :disabled="disabled.deleteTask">
                  <span v-if="disabled.deleteTask">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                    Delete
                </button>

                <div v-if="isContractor()" class=" justify-between">
                    <!-- / not sure we need this show subs button -->
                    <!-- <button v-show="jobTask.bid_contractor_job_tasks.length > 0 && showSubsPanel" class="btn btn-blue" @click.prevent="openTaskBids(jobTask.id)"
                      v-if="isGeneral">
                      Show Subs
                    </button> -->
                    <button class="btn btn-blue" @click.prevent="openSubInvite(jobTask.id)"
                            v-if="isGeneral() && showSendSubInvite && !checkIfBidHasBeenAccepted(jobTask)">
                        Add A Sub
                    </button>
                </div>

                <div v-if="showFinishedBtn(jobTask) || showApproveBtn(jobTask)" class=" justify-between">
                    <button class="btn btn-green" v-if="showFinishedBtn(jobTask)" @click="finishedTask(jobTask)"
                            :disabled="disabled.finished">
                              <span v-if="disabled.finished">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                              </span>
                        Finished
                    </button>

                    <button class="btn btn-green" v-if="showApproveBtn(jobTask)"
                            @click="approveTaskHasBeenFinished(jobTask)"
                            :disabled="disabled.approve">
                              <span v-if="disabled.approve">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                              </span>
                        Approve
                    </button>

                </div>
            </div>
        </div>

        <sub-invite-modal
                v-if="isContractor() && !checkIfBidHasBeenAccepted(jobTask)"
                :job-task="jobTask"
                :job-task-task="jobTask.task"
                :job-task-name="jobTask.task.name"
                :id="jobTask.id"
        >
        </sub-invite-modal>
        <deny-task-modal
                v-if="isContractor()"
                :job-task="jobTask"
                :id="jobTask.id"
        >
        </deny-task-modal>
        <update-task-location-modal
                :job-task="jobTask"
                :id="jobTask.id"
        >
        </update-task-location-modal>

    </div>
</template>

<script>

  import SubInviteModal from '../task/SubInviteModal'
  import DenyTaskModal from '../task/DenyTaskModal'
  import UpdateTaskLocationModal from '../task/UpdateTaskLocationModal'
  import Message from './Message.vue'
  import TaskImages from '../../components/task/UploadTaskImages'

  export default {
    name: 'BidTask',
    components: {
      SubInviteModal,
      DenyTaskModal,
      Message,
      TaskImages,
      UpdateTaskLocationModal
    },
    data() {
      return {
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
    props: {
      jobTask: Object,
      bid: Object,
      user: Object
    },
    computed: {
      jobTasks() {
        return this.bid.job_tasks !== undefined ? this.bid.job_tasks : []
      },
      taskApproved() {
        return this.jobTask.status === 'bid_task.approved_by_customer'
      },
      show() {
        return this.jobTasks.length > 0
      },
      isCustomer() {
        return this.user.isCustomer()
      },
      // isContractor() {
      //   return this.user.isContractor()
      // },
      showSubsPanel() {
        return this.isContractor() && this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed'
      },
      showSendSubInvite() {
        if (this.bid.status === 'bid.initiated' || this.bid.status === 'bid.in_progress' || this.bid.status ===
          'bid.sent') {
          return true
        }
        return false
      },
      generalTotalTaskPrice() {
        let total = 0
        for (const jobTask of this.bid.job_tasks) {
          if (jobTask !== null) {
            total += jobTask.cust_final_price
          }
        }
        return total
      },
      subTotalTaskPrices() {
        let total = 0
        for (const jobTask of this.bid.job_tasks) {
          total += this.subTaskPrice(jobTask)
        }
        return total
      },
      disableMessages() {
        return this.bid.status === 'job.completed'
      }
    },
    methods: {
      checkIfBidHasBeenSent(bid) {
        if (bid.updated_at !== null && bid.status === 'sent') {
          return true
        } else {
          return false
        }
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
        return this.user.isAssignedToMe(jobTask) && (this.bid.status === 'bid.initiated' || this.bid.status ===
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
      showDenyBtn(jobTask) {
        const status = jobTask.status
        if (this.isCustomer) {
          return (status === 'bid_task.finished_by_general' || status === 'bid_task.approved_by_general')
        }
        return (status === 'bid_task.finished_by_sub' || status === 'bid_task.reopened')
        // return (status === 'bid_task.finished_by_sub' || this.bid.status === 'bid.declined');
      },
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
      showFinishedBtn(jobTask) {
        if (this.isContractor() &&
          this.user.isAssignedToMe(jobTask) && (jobTask.status === 'bid_task.approved_by_customer' ||
            jobTask.status === 'bid_task.reopened' ||
            jobTask.status === 'bid_task.denied'
          )) {
          return true
        }
        return false
      },
      showApproveBtn(jobTask) {
        if (this.isGeneral() &&
          !this.user.isAssignedToMe(jobTask) &&
          (jobTask.status === 'bid_task.finished_by_sub' || jobTask.status === 'bid_task.reopened')
        ) {
          return true
        }
        return false
      },
      showDeleteBtn(jobTask) {
        const status = jobTask.status
        if (this.isGeneral() && (status === 'bid_task.initiated' || status === 'bid_task.bid_sent' || this.bid.status ===
          'bid.declined')) {
          return true
        }
        return false
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
          return this.user.findTaskBid(jobTask.bid_id, jobTask.bid_contractor_job_tasks)[0].bid_price
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
      checkIfBidHasBeenAccepted(jobTask) {
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
      isGeneral() {
        return this.user.isGeneral(this.bid)
      },
      prettyDate(date) {
        if (date == null)
          return ''
        // return the date and ignore the time
        date = date.split(' ')
        return date[0]
      },
      showTaskStartDate() {
        return this.isGeneral() && (
          this.bid.status === 'bid.in_progress' ||
          this.bid.status === 'bid.initiated' ||
          this.bid.status === 'bid.declined')
      },
      updateTaskStartDate(date, jobTaskId) {

        // debugger

        let dateArray = GeneralContractor.checkDateIsTodayorLater(date, this.bid.created_at)
        this.startDateErrorMessage = dateArray[0]
        this.hasStartDateError = dateArray[1]

        let bidStartDate = new Date

        if (!this.hasStartDateError) {
          GeneralContractor.updateTaskStartDate(date, jobTaskId)
        } else {
          this.startDateErrorMessage = 'Task Date Cannot Be Before Bid Creation Date'
        }
      },
      openUpdateTaskLocation(jobTaskId) {
        $('#update-task-location-modal_'+jobTaskId).modal()
      },
      openDenyTaskForm(jobTaskId) {
        $('#deny-task-modal_'+jobTaskId).modal()
      },
      openSubInvite(jobTaskId) {
        // debugger;
        // this.currentJobTask = jobTask;
        $('#sub-invite-modal_'+ jobTaskId).modal()
      },
      location(jobTask, bid) {
        // debugger;
        const task_location = jobTask.location_id
        const job_location = this.bid.location_id
        if (task_location === null && job_location === null) {
          return 'No Address Set Yet'
        } else if (job_location === job_location) {
          return 'Same as Job Location'
        } else if (task_location !== null) {
          return jobTask.location.address_line_1
        } else if (job_location !== null) {
          return bid.location.address_line_1
        }
      },
      updateCustomerTaskPrice(price, jobTaskId, bidId, jobTask) {
        price = price.replace(/[^0-9.]/g, '')
        let taskPrice = jobTask.unit_price
        taskPrice = taskPrice.toString()
        // debugger
        if ((taskPrice !== price)) {
          GeneralContractor.updateCustomerPrice(price, jobTaskId, bidId)
        }
      },
      isContractor() {
        return this.user.isContractor()
      },
      taskCustFinalPrice(price) {

        let a = price
        let b = a.toString()

        if (b.indexOf('.') === -1) {
          price = '$' + price + '.00'
        } else {
          price = '$' + price
        }

        return price
      },
      showTheJobTaskDetails(value) {
        if (value === 'show') {
          this.showDetails = true
        } else {
          this.showDetails = false
        }
      },
      status(status) {
        return this.user.status(status, this.bid)
      },
      getLabelClass(status) {
        return Format.statusLabel(status)
      },
      showTaskPriceInput() {
        return this.isGeneral() && (this.bid.status === 'bid.in_progress' || this.bid.status === 'bid.initiated' || this.bid
          .status === 'bid.declined')
      },
      updateCustomerTaskQuantity(quantity, taskId, currentQuantityValue) {

        quantity = Number(quantity)

        if (quantity != currentQuantityValue) {
          GeneralContractor.updateCustomerTaskQuantity(quantity, taskId)
        }

      },
      taskCustFinalPrice(price) {

        let a = price
        let b = a.toString()

        if (b.indexOf('.') === -1) {
          price = '$' + price + '.00'
        } else {
          price = '$' + price
        }

        return price
      },
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
    }
  }
</script>

<style scoped>

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

</style>
