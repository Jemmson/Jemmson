<template>
    <!-- /all tasks of a bid -->
    <div v-if="show">
        <paginate ref="paginator" name="jobTasks" :list="jobTasks" :per="6" class="paginated"
                  v-if="jobTasks.length > 0">
            <div class="container">

                <!-- / status -->
                <div class="card card-1 flex flex-col" v-for="jobTask of paginated('jobTasks')" v-bind:key="jobTask.id"
                     :id="'task-' + jobTask.id">
                    <div for="task-status" class="flex
                                                  justify-center
                                                  text-xl
                                                  rounded
                                                  m-l-24
                                                  m-r-24
                                                  p-1
                                                  text-white"
                         :class="getLabelClass(jobTask.status)">{{ status(jobTask.status) }}
                    </div>


                    <!-- task name-->
                    <div class="flex
                                 justify-center
                                 text-2xl
                                 rounded
                                 m-l-24
                                 m-r-24
                                 p-1
                                 text-black" style="font-size: 20pt">
                        {{ jobTask.task.name }}
                    </div>

                    <!-- Task Prices -->

                    <div class="flex justify-around">
                        <div class="form-group m-l-4">
                            <div class="flex flex-col">
                                <span>Total Task Price</span>
                                <div>
                                    <i class="fas fa-money-bill-alt icon"></i>
                                    <span class="totalCost" v-if="jobTask.task.qty !== null">{{taskCustFinalPrice(jobTask.cust_final_price)}}</span>
                                </div>
                            </div>
                            <label v-if="isCustomer || !showTaskPriceInput()">{{taskCustFinalPrice(jobTask.cust_final_price)}}</label>
                        </div>
                        <!-- / end total price -->
                        <div class="form-group m-r-4" v-if="isContractor">
                            <div class="flex flex-col">
                                <span>Total Task Sub Price</span>
                                <div class="flex items-center">
                                    <i class="fas fa-user icon"></i>
                                    <span class="totalCost">{{taskCustFinalPrice(jobTask.sub_final_price)}}</span>
                                </div>
                            </div>
                            <!-- <input type="tel" class="form-control form-control-text" v-if="showTaskPriceInput()" :value="taskCustFinalPrice(jobTask.sub_final_price)"
                                                          @blur="updateSubTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)"> -->
                        </div>
                    </div>

                    <div class="flex flex-col m-l-2 m-r-2 m-b-10">
                        <div class="flex justify-around">
                            <div class="flex flex-col">
                                <label>Quantity:</label>
                                <input type="text" class="form-control" v-if="showTaskPriceInput()"
                                       :value="jobTask.qty"
                                       @blur="updateCustomerTaskQuantity(
                                   $event.target.value,
                                   jobTask.id,
                                   jobTask.qty)">
                            </div>
                            <div class="flex flex-col">
                                <label class="m-l-6">Price:</label>
                                <div class="flex">
                                    <span class="dollarSign m-l-6">$</span>
                                    <input type="text" class="form-control" v-if="showTaskPriceInput()"
                                           :value="taskCustFinalPrice(jobTask.unit_price)"
                                           @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success btn-large m-t-3" style="width: 20%">Update</button>
                    </div>


                    <div class="flex justify-around m-b-10">
                        <div>
                            <span v-if="location(jobTask, bid) === 'No Address Set Yet'">
                            <!--No Address Set Yet-->
                              <i class="fas fa-map-marker icon"></i>
                              {{ location(jobTask, bid) }}
                            </span>
                            <div class="flex justify-around items-center"
                                 v-else-if="location(jobTask, bid) === 'Same as Job Location'">
                                <span>Change Task Location</span>
                                <button class="btn btn-small pull-right" @click="openUpdateTaskLocation(jobTask)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                            <div v-else class="flex justify-around items-center">
                                <a target="_blank"
                                   :href="'https://www.google.com/maps/search/?api=1&query=' + location(jobTask, bid)">
                                    <i class="fas fa-map-marker icon"></i>
                                    {{ location(jobTask, bid) }}
                                </a>
                                <button class="btn btn-small pull-right" @click="openUpdateTaskLocation(jobTask)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center" v-if="isContractor">
                            <i class="fas fa-clock icon m-r-2"></i>
                            <div class="flex flex-col">
                                <input type="date" class="form-control form-control-date" style=""
                                       v-if="showTaskStartDate()" :value="prettyDate(jobTask.start_date)"
                                       @blur="updateTaskStartDate($event.target.value, jobTask.id, bid.id, jobTask)">
                                <span :class="{ error: hasStartDateError }"
                                      v-show="hasStartDateError">{{ startDateErrorMessage }}
                                </span>
                            </div>
                            <!--<label v-if="isCustomer || !showTaskStartDate()">-->
                                <!--{{prettyDate(jobTask.start_date)}} </label>-->
                        </div>
                    </div>

                    <task-images :jobTask="jobTask" type="notsub">
                    </task-images>

                    <div>
                        <div
                                class="messageHeader">Messages
                        </div>

                        <div class="flex flex-col">
                            <div class="container">
                                <div class="flex flex-col items-center box">
                                    <span>Subcontractor</span>
                                    <textarea
                                            cols="0"
                                            rows="0"
                                            class="form-control"
                                            @blur="updateMessage($event.target.value, jobTask.id, jobTask.sub_message, 'sub')"
                                    >{{ jobTask.sub_message }}</textarea>

                                    <!--<input-->
                                    <!--type="text"-->
                                    <!--class="form-control"-->
                                    <!--@blur="updateMessage($event.target.value, jobTask.id, jobTask.sub_message, 'sub')"-->
                                    <!--:value="jobTask.sub_message">-->
                                </div>
                                <div class="flex flex-col items-center box">
                                    <span>Customer</span>
                                    <textarea
                                            cols="0"
                                            rows="0"
                                            class="form-control"
                                            @blur="updateMessage($event.target.value, jobTask.id, jobTask.customer_message, 'customer')"
                                    >{{ jobTask.customer_message }}</textarea>
                                </div>
                                <button class="btn btn-success btn-large m-t-3">Send</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-around m-t-6 m-b-6">

                        <div v-if="isContractor">
                            <button class="bg-blue
                                            p-r-4
                                            p-l-4
                                            p-t-1
                                            p-b-1
                                            m-t-2
                                            text-center
                                            text-white
                                            rounded-lg"
                                    @click.prevent="openSubInvite(jobTask)" v-if="isGeneral && showSendSubInvite">
                                Add A Sub
                            </button>

                            <button v-show="jobTask.bid_contractor_job_tasks.length > 0" class="bg-blue
                                            p-r-4
                                            p-l-4
                                            p-t-1
                                            p-b-1
                                            m-t-2
                                            text-center
                                            text-white
                                            rounded-lg"
                                    @click.prevent="openTaskBids(jobTask.id)" v-if="isGeneral">
                                Display Subs
                            </button>

                        </div>

                        <button class="bg-blue
                                        p-r-4
                                        p-l-4
                                        p-t-1
                                        p-b-1
                                        m-t-2
                                        text-center
                                        text-white
                                        rounded-lg"
                                v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                            Deny
                        </button>

                        <button class="bg-blue
                                        p-r-4
                                        p-l-4
                                        p-t-1
                                        p-b-1
                                        m-t-2
                                        text-center
                                        text-white
                                        rounded-lg"
                                v-if="showDeleteBtn(jobTask)" @click="deleteTask(jobTask)"
                                :disabled="disabled.deleteTask">
                            <span v-if="disabled.deleteTask">
                              <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>
                            Delete
                        </button>


                        <button class="bg-blue
                                        p-r-4
                                        p-l-4
                                        p-t-1
                                        p-b-1
                                        m-t-2
                                        text-center
                                        text-white
                                        rounded-lg"
                                v-if="showFinishedBtn(jobTask)" @click="finishedTask(jobTask)"
                                :disabled="disabled.finished">
                            <span v-if="disabled.finished">
                              <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>
                            Finished
                        </button>

                        <button class="bg-blue
                                        p-r-4
                                        p-l-4
                                        p-t-1
                                        p-b-1
                                        m-t-2
                                        text-center
                                        text-white
                                        rounded-lg"
                                v-if="showApproveBtn(jobTask)" @click="approveTaskHasBeenFinished(jobTask)"
                                :disabled="disabled.approve">
                            <span v-if="disabled.approve">
                              <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>
                            Approve
                        </button>


                    </div>

                    <div>
                        <div v-if="showPanelActions(jobTask.status)">
                            <transition-group name="slide-fade" v-if="isContractor">

                                <div :id="'task-divider-' + jobTask.id" :key="1"></div>

                                <div :id="'task-subs-' + jobTask.id"
                                     v-if="isGeneral && !taskApproved &&  jobTask.bid_contractor_job_tasks.length > 0"
                                     :key="3">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Sub</th>
                                            <th>Price</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="table" v-for="bid in jobTask.bid_contractor_job_tasks"
                                            :key="bid.id">
                                            <td>{{ bid.contractor.name }}</td>
                                            <td>${{ bid.bid_price }}</td>
                                            <td>
                                                <button v-if="showAcceptBtn(jobTask.status)"
                                                        @click="acceptSubBidForTask(bid, jobTask)"
                                                        class="button btn btn-sm btn-success"
                                                        :disabled="disabled.accept">
                                                          <span v-if="disabled.accept">
                                                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                                                          </span>
                                                    Accept
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!--&lt;!&ndash; /end col-md-6 &ndash;&gt;-->
                            </transition-group>
                        </div>
                    </div>

                    <!--<button class="btn btn-secondary" @click.prevent="openSubInvite(jobTask)" v-if="isGeneral && showSendSubInvite">-->
                    <!--&lt;!&ndash;<i class="fas fa-user-plus fa-2x"></i>&ndash;&gt;-->
                    <!--Add Sub-->
                    <!--</button>-->


                </div>
            </div>

        </paginate>


        <div class="col-md-12">
            <div class="card card-1">
                <div class="panel-body">
                    <center>
                        <h4>
                            <paginate-links for="jobTasks" :limit="2" :show-step-links="true">
                            </paginate-links>
                        </h4>
                    </center>
                </div>
            </div>
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
        user: '',
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
        return User.getAllUnpaidTasks (this.bid.job_tasks);
      },
      show () {
        return this.jobTasks.length > 0;
      },
      taskApproved () {
        return this.jTask.status === 'bid_task.approved_by_customer';
      },
      isCustomer () {
        return User.isCustomer ();
      },
      isGeneral () {
        return User.isGeneral (this.bid);
      },
      isContractor () {
        return User.isContractor ();
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
        const task_location = jobTask.location_id;
        const job_location = this.bid.location_id;
        console.log (task_location)
        console.log (job_location)
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
        let taskPrice = jobTask.cust_final_price;
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
        let b = a.toString ();

        if (b.indexOf ('.') === -1) {
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
      this.user = Spark.state.user;
    }
  }
</script>

<style scope>

    .error {
        color: red;
        font-size: 12pt;
        font-weight: 900;
    }

    .box {
        width: 95%;
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

    .stripe-label {
        /*margin-bottom: .5rem;*/
    }
</style>