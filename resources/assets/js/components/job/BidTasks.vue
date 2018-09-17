<template>
  <!-- /all tasks of a bid -->
  <div v-if="show">
    <paginate ref="paginator" name="jobTasks" :list="jobTasks" :per="6" class="paginated" v-if="jobTasks.length > 0">
        <!-- / status -->
        <card footer="true" v-for="jobTask of paginated('jobTasks')" v-bind:key="jobTask.id" :id="'task-' + jobTask.id">
          <div for="task-status" class="status task-status mb-6" :class="getLabelClass(jobTask.status)">
            {{ status(jobTask.status) }}
          </div>

          <!-- task name-->
          <div class="flex justify-center text-2xl font-bold text-black mb-6">
            {{ jobTask.task.name }}
          </div>

          <!-- Task Prices -->

          <div class="flex mb-4">
            <div class="flex-1 form-group">
              <div class="flex flex-col">
                <span class="label mb-2">Total Task Price</span>
                <div>
                  <i class="fas fa-money-bill-alt icon"></i>
                  <span class="totalCost" v-if="jobTask.task.qty !== null">{{taskCustFinalPrice(jobTask.cust_final_price)}}</span>
                </div>
              </div>
            </div>
            <!-- / end total price -->
            <div class="flex-1 form-group" v-if="isContractor">
              <div class="flex flex-col">
                <span class="label mb-2">Total Task Sub Price</span>
                <div class="flex items-center">
                  <i class="fas fa-user icon"></i>
                  <span class="totalCost">{{taskCustFinalPrice(jobTask.sub_final_price)}}</span>
                </div>
              </div>
            </div>
          </div>
          <!-- / end total task prices -->

          <div v-if="isContractor" class="flex flex-col m-b-4">
            <div class="flex">
              <div class="flex-1 flex-col pr-2">
                <label class="label">Quantity:</label>
                <input type="text" ref="quantity" class="form-control mt-2" :disabled="!showTaskPriceInput()" :value="jobTask.qty" @blur="updateCustomerTaskQuantity(
                                   $event.target.value,
                                   jobTask.id,
                                   jobTask.qty)">
              </div>
              <div class="flex-1 flex-col">
                <label class="label">Price:</label>
                  <input type="text" ref="price" class="form-control mt-2" :disabled="!showTaskPriceInput()" :value="taskCustFinalPrice(jobTask.unit_price)"
                    @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)">
              </div>
            </div>
            <div class="flex justify-end">
              <button class="btn btn-green btn-large m-t-3" v-show="jobTask.status !== 'bid_task.customer_sent_payment'">Update</button>
            </div>
          </div>


          <div class="flex mb-6">
            <div class="flex-1 flex-col">
              <span v-if="location(jobTask, bid) === 'No Address Set Yet'">
                <!--No Address Set Yet-->
                <i class="fas fa-map-marker icon"></i>
                {{ location(jobTask, bid) }}
              </span>
              <div class="flex flex-col" v-else-if="location(jobTask, bid) === 'Same as Job Location'">
                <span class="label mb-2">Change Task Location</span>
                <button class="btn btn-blue btn-small" @click="openUpdateTaskLocation(jobTask)">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
              <div v-else class="flex flex-col">
                <a target="_blank" :href="'https://www.google.com/maps/search/?api=1&query=' + location(jobTask, bid)">
                  <i class="fas fa-map-marker icon"></i>
                  {{ location(jobTask, bid) }}
                </a>
                <button class="btn btn-blue btn-small" @click="openUpdateTaskLocation(jobTask)">
                  <i class="fas fa-edit"></i>
                </button>
              </div>
            </div>

            <div class="flex-1" v-if="isContractor">
              <div class="flex flex-col">
                <label class="label mb-2">Task Start Date</label>
                <!-- <i class="fas fa-clock icon m-r-2"></i> -->
                <input type="date" class="form-control form-control-date" style="" v-if="showTaskStartDate()" :value="prettyDate(jobTask.start_date)"
                  @blur="updateTaskStartDate($event.target.value, jobTask.id)">
                <span :class="{ error: hasStartDateError }" v-show="hasStartDateError">{{ startDateErrorMessage }}
                </span>
              </div>
              <!--<label v-if="isCustomer || !showTaskStartDate()">-->
              <!--{{prettyDate(jobTask.start_date)}} </label>-->
            </div>
          </div>
          <!-- / end inputs -->

          <task-images class="mb-4" :jobTask="jobTask" type="notsub">
          </task-images>

          <div>
            <div class="messageHeader mb-4">Messages</div>

            <div class="flex flex-col">
              <div class="flex flex-col box mb-3">
                <span class="label mb-2">Subcontractor</span>
                <textarea cols="0" rows="0" class="form-control" @blur="updateMessage($event.target.value, jobTask.id, jobTask.sub_message, 'sub')"
                  :disabled="disableMessages">{{ jobTask.sub_message }}</textarea>

                <!--<input-->
                <!--type="text"-->
                <!--class="form-control"-->
                <!--@blur="updateMessage($event.target.value, jobTask.id, jobTask.sub_message, 'sub')"-->
                <!--:value="jobTask.sub_message">-->
              </div>
              <div class="flex flex-col box">
                <span class="label mb-2">Customer</span>
                <textarea cols="0" rows="0" class="form-control" @blur="updateMessage($event.target.value, jobTask.id, jobTask.customer_message, 'customer')"
                  :disabled="disableMessages">{{ jobTask.customer_message }}</textarea>
              </div>
              <button class="btn btn-green m-t-3 mb-4">Send</button>
            </div>
          </div>

          <div v-if="showSubsPanel" class="mt-4">
            <div :id="'task-divider-' + jobTask.id" :key="1"></div>

            <div :id="'task-subs-' + jobTask.id" v-if="isGeneral && !taskApproved && jobTask.bid_contractor_job_tasks.length > 0"
              :key="3">
              <div class="flex flex-col">
                <div class="table-header">
                  <div class="flex-1">Sub</div>
                  <div class="flex-1">Bid</div>
                  <div class="flex-1">Action</div>
                </div>
                <div class="flex pl-2 mb-2" v-for="bid in jobTask.bid_contractor_job_tasks" :key="bid.id">
                  <div class="flex-1">{{ bid.contractor.name }}</div>
                  <div class="flex-1">${{ bid.bid_price }}</div>
                  <div class="flex-1">
                    <button v-if="showAcceptBtn(jobTask.status)" @click="acceptSubBidForTask(bid, jobTask)" class="btn btn-green"
                      :disabled="disabled.accept">
                      <span v-if="disabled.accept">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>
                      </span>
                      Accept
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <template slot="card-footer">
            <div class="flex w-full justify-between">
              <div class="">
                <button class="btn btn-red" v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                  Deny
                </button>

                <button class="btn btn-red" v-if="showDeleteBtn(jobTask)" @click="deleteTask(jobTask)" :disabled="disabled.deleteTask">
                  <span v-if="disabled.deleteTask">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                  Delete
                </button>
              </div>

              <div v-if="isContractor" class=" justify-between">
                <!-- / not sure we need this show subs button -->
                <!-- <button v-show="jobTask.bid_contractor_job_tasks.length > 0 && showSubsPanel" class="btn btn-blue" @click.prevent="openTaskBids(jobTask.id)"
                  v-if="isGeneral">
                  Show Subs
                </button> -->
                <button class="btn btn-blue" @click.prevent="openSubInvite(jobTask)" v-if="isGeneral && showSendSubInvite">
                  Add A Sub
                </button>
              </div>

              <div v-if="showFinishedBtn(jobTask) || showApproveBtn(jobTask)" class=" justify-between">
                <button class="btn btn-green" v-if="showFinishedBtn(jobTask)" @click="finishedTask(jobTask)" :disabled="disabled.finished">
                  <span v-if="disabled.finished">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                  Finished
                </button>

                <button class="btn btn-green" v-if="showApproveBtn(jobTask)" @click="approveTaskHasBeenFinished(jobTask)"
                  :disabled="disabled.approve">
                  <span v-if="disabled.approve">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                  Approve
                </button>
              </div>
            </div>
          </template>

          <!--<button class="btn btn-secondary" @click.prevent="openSubInvite(jobTask)" v-if="isGeneral && showSendSubInvite">-->
          <!--&lt;!&ndash;<i class="fas fa-user-plus fa-2x"></i>&ndash;&gt;-->
          <!--Add Sub-->
          <!--</button>-->
        </card>
    </paginate>

    <div class="card p-5 card-body justify-center">
      <paginate-links for="jobTasks" :limit="2" :show-step-links="true">
      </paginate-links>
    </div>

    <sub-invite-modal v-if="isContractor" :jobTask="jTask">
    </sub-invite-modal>
    <deny-task-modal v-if="isContractor" :jobTask="jTask">
    </deny-task-modal>
    <update-task-location-modal :jobTask="jTask">
    </update-task-location-modal>
  </div>
</template>

<script>
  export default {
    props: {
      bid: Object
    },
    data () {
      return {
        paginate: ['jobTasks'],
        jTask: {},
        message: '',

        start_date: '',
        start_when_accepted: true,
        startDateErrorMessage: '',
        hasStartDateError: false,
        startDateChanged: false,
        startDateError: false,

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
      jobTasks () {
        return this.bid.job_tasks !== undefined ? this.bid.job_tasks : [];
      },
      show () {
        return this.jobTasks.length > 0;
      },
      taskApproved () {
        return this.jTask.status === 'bid_task.approved_by_customer';
      },
      isCustomer () {
        return User.isCustomer();
      },
      isGeneral () {
        return User.isGeneral(this.bid);
      },
      isContractor () {
        return User.isContractor();
      },
      showSubsPanel () {
        return this.isContractor && this.bid.status !== 'job.approved' && this.bid.status !== 'job.completed';
      },
      showSendSubInvite () {
        if (this.bid.status === 'bid.initiated' || this.bid.status === 'bid.in_progress' || this.bid.status ===
          'bid.sent') {
          return true;
        }
        return false;
      },
      generalTotalTaskPrice () {
        let total = 0;
        for (const jobTask of this.bid.job_tasks) {
          if (jobTask !== null) {
            total += jobTask.cust_final_price;
          }
        }
        return total;
      },
      subTotalTaskPrices () {
        let total = 0;
        for (const jobTask of this.bid.job_tasks) {
          total += this.subTaskPrice (jobTask);
        }
        return total;
      },
      disableMessages () {
        return this.bid.status === "job.completed";
      }
    },
    methods: {
      showSubMessage (msg) {
        return (msg != null &&
          msg != '' &&
          msg != ' ' &&
          this.isContractor) ||
          (msg != null &&
            msg != '' &&
            msg != ' ' &&
            this.isGeneral);
      },
      showCustomerMessage (msg) {
        return (msg != null &&
          msg != '' &&
          msg != ' ' &&
          this.isCustomer) ||
          (msg != null &&
            msg != '' &&
            msg != ' ' &&
            this.isGeneral);
      },
      openUpdateTaskLocation (jobTask) {
        this.jTask = jobTask;
        $ ('#update-task-location-modal').modal ();
      },
      showPanelActions (status) {
        if (status !== 'bid_task.customer_sent_payment') {
          return true;
        }
        return false;
      },
      getLabelClass (status) {
        return Format.statusLabel (status);
      },
      showAcceptBtn (status) {
        return status === 'bid_task.bid_sent';
      },
      openTaskBids (id) {
        if ($ ('#task-options-' + id).hasClass ('hidden') || this.isCustomer) {
          $ ('#task-divider-' + id).toggleClass ('hidden');
        }
        $ ('#task-subs-' + id).toggleClass ('hidden');
      },
      openSubInvite (jobTask) {
        this.jTask = jobTask;
        $ ('#sub-invite-modal').modal ();
      },
      openTaskActions (id) {
        if ($ ('#task-subs-' + id).hasClass ('hidden') || this.isCustomer) {
          $ ('#task-divider-' + id).toggleClass ('hidden');
        }
        $ ('#task-options-' + id).toggleClass ('hidden');
      },
      preview (jobTask, subId) {
        Bus.$emit ('previewSubForTask', [
          jobTask.job_id,
          jobTask.id,
          subId
        ]);
      },
      acceptSubBidForTask (bid, jobTask) {
        GeneralContractor.acceptSubBidForTask (jobTask, bid, this.disabled);
      },
      location (jobTask, bid) {
        // debugger;
        const task_location = jobTask.location_id;
        const job_location = this.bid.location_id;
        if (task_location === null && job_location === null) {
          return 'No Address Set Yet';
        } else if (job_location === job_location) {
          return 'Same as Job Location'
        } else if (task_location !== null) {
          return jobTask.location.address_line_1;
        } else if (job_location !== null) {
          return bid.location.address_line_1;
        }


      },
      prettyDate (date) {
        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split (' ');
        return date[0];
      },
      showStripeToggle (jobTask) {
        return User.isAssignedToMe (jobTask) && (this.bid.status === 'bid.initiated' || this.bid.status ===
          'bid.in_progress');
      },
      openDenyTaskForm (jobTask) {
        this.jTask = jobTask;
        $ ('#deny-task-modal').modal ();
      },
      showTaskStartDate () {
        return this.isGeneral && (this.bid.status === 'bid.in_progress' || this.bid.status === 'bid.initiated' || this.bid
          .status === 'bid.declined');
      },
      showTaskPriceInput () {
        return this.isGeneral && (this.bid.status === 'bid.in_progress' || this.bid.status === 'bid.initiated' || this.bid
          .status === 'bid.declined');
      },
      updateTaskStartDate (date, jobTaskId) {

        // debugger

        let dateArray = GeneralContractor.checkDateIsTodayorLater (date, this.bid.created_at);
        this.startDateErrorMessage = dateArray[0];
        this.hasStartDateError = dateArray[1];

        let bidStartDate = new Date;

        if (!this.hasStartDateError) {
          GeneralContractor.updateTaskStartDate (date, jobTaskId);
        } else {
          this.startDateErrorMessage = 'Task Date Cannot Be Before Bid Creation Date'
        }
      },
      updateMessage (message, jobTaskId, currentMessage, actor) {

        if (message !== currentMessage) {
          GeneralContractor.updateMessage (message, jobTaskId, actor);
        }

      },
      updateCustomerTaskQuantity (quantity, taskId, currentQuantityValue) {

        quantity = Number (quantity);

        if (quantity != currentQuantityValue) {
          GeneralContractor.updateCustomerTaskQuantity (quantity, taskId);
        }

      },
      updateCustomerTaskPrice (price, jobTaskId, bidId, jobTask) {
        price = price.replace (/[^0-9.]/g, '');
        let taskPrice = jobTask.unit_price;
        taskPrice = taskPrice.toString ();
        // debugger
        if ((taskPrice !== price)) {
          GeneralContractor.updateCustomerPrice (price, jobTaskId, bidId)
        }
      },
      showDenyBtn (jobTask) {
        const status = jobTask.status;
        if (this.isCustomer) {
          return (status === 'bid_task.finished_by_general' || status === 'bid_task.approved_by_general');
        }
        return (status === 'bid_task.finished_by_sub' || status === 'bid_task.reopened');
        // return (status === 'bid_task.finished_by_sub' || this.bid.status === 'bid.declined');
      },
      taskCustFinalPrice (price) {

        let a = price;
        let b = a.toString();

        if (b.indexOf('.') === -1) {
          price = '$' + price + '.00';
        } else {
          price = '$' + price;
        }

        return price;
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
      //     User.isCustomer();
      // },
      // showPayForTaskBtn(jobTask) {
      //   return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status === 'bid_task.approved_by_general') &&
      //     User.isCustomer() && jobTask.stripe;
      // },
      showFinishedBtn (jobTask) {
        if (this.isContractor && User.isAssignedToMe (jobTask) && (jobTask.status === 'bid_task.approved_by_customer' ||
          jobTask.status === 'bid_task.reopened' || jobTask.status === 'bid_task.denied')) {
          return true;
        }
        return false;
      },
      showApproveBtn (jobTask) {
        if (this.isGeneral && !User.isAssignedToMe (jobTask) && (jobTask.status === 'bid_task.finished_by_sub' ||
          jobTask.status === 'bid_task.reopened')) {
          return true;
        }
        return false;
      },
      showDeleteBtn (jobTask) {
        const status = jobTask.status;
        if (this.isGeneral && (status === 'bid_task.initiated' || status === 'bid_task.bid_sent' || this.bid.status ===
          'bid.declined')) {
          return true;
        }
        return false;
      },
      // reopenTask(jobTask) {
      //   SubContractor.reopenTask(jobTask, this.disabled);
      // },
      deleteTask (jobTask) {
        GeneralContractor.deleteTask (jobTask, this.disabled);
      },
      /**
       * customer task price
       */
      subTaskPrice (jobTask) {
        if (jobTask === null) {
          return 0;
        }
        if (jobTask.bid_id === null) {
          return 0;
        } else {
          return User.findTaskBid (jobTask.bid_id, jobTask.bid_contractor_job_tasks)[0].bid_price;
        }
      },
      toggleStripePaymentOption (jobTask) {
        jobTask.checked = $ ('#toggle-stripe-' + jobTask.id).is (':checked');
        SubContractor.toggleStripePaymentOption (jobTask);
      },
      // payForTask(jobTask) {
      //   Customer.payForTask(jobTask, this.disabled);
      // },
      // paidWithCashTask(jobTask) {
      //   Customer.paidWithCashTask(jobTask, this.disabled);
      // },
      openTaskPanel (index) {
        this.$emit ('openTaskPanel', index);
      },
      finishedTask (jobTask) {
        SubContractor.finishedTask (jobTask, this.disabled);
      },
      approveTaskHasBeenFinished (jobTask) {
        GeneralContractor.approveTaskHasBeenFinished (jobTask, this.disabled);
      },
      status (status) {
        return User.status (status, this.bid);
      }
    },
    mounted: function () {
    }
  }
</script>

<style lang="less" scoped>

    .error {
        color: red;
        font-size: 12pt;
        font-weight: 900;
    }

    .box {
        width: 100%;
    }

    .messageHeader {
        font-size: 12pt;
        font-weight: bold;
        font-family: Roboto, serif;
        text-align: center;
    }

    .totalCost {
        padding-left: .5rem;
    }

    .task-status {
      margin-top: -1.25rem;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
    }

    .pr-1 {
      padding-right: .25rem;
    }
</style>
