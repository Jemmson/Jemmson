<template>
    <!-- /all tasks of a bid -->
    <div v-if="show">
        <paginate ref="paginator" name="jobTasks" :list="jobTasks" :per="6" class="paginated"
                  v-if="jobTasks.length > 0">
            <div class="container">

                <!-- / status -->
                <div class="card card-1" v-for="jobTask of paginated('jobTasks')" v-bind:key="jobTask.id"
                     :id="'task-' + jobTask.id">
                    <span for="task-status" class="flex
                                                   justify-center
                                                   text-xl
                                                   rounded
                                                   m-l-24
                                                   m-r-24
                                                   p-1
                                                   text-white"
                          :class="getLabelClass(jobTask.status)">
                      {{ status(jobTask.status) }}
                    </span>


                    <!-- task name-->
                    <span class="flex
                                 justify-center
                                 text-2xl
                                 rounded
                                 m-l-24
                                 m-r-24
                                 p-1
                                 text-black">
                        {{ jobTask.task.name }}
                    </span>


                    <!-- Task Prices -->

                    <div class="flex justify-around">
                        <div class="form-group m-l-4">
                            <div class="flex flex-col">
                                <span>Total Task Price</span>
                                <div>
                                    <i class="fas fa-money-bill-alt icon"></i>
                                    <span class="totalCost" v-if="jobTask.task.qty !== null">{{taskCustFinalPrice(jobTask.cust_final_price * jobTask.qty)}}</span>
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

                    <div class="divider2"></div>
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
                                       :value="taskCustFinalPrice(jobTask.cust_final_price)"
                                       @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)">
                            </div>
                        </div>

                    </div>

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
      showSubMessage (jobTask) {
        let msg = jobTask.sub_message;
        return (msg != null && msg != '' && msg != ' ' && this.isContractor) || (msg != null && msg != '' && msg != ' ' &&
          this.isGeneral);
      },
      showCustomerMessage (jobTask) {
        let msg = jobTask.customer_message;
        return (msg != null && msg != '' && msg != ' ' && this.isCustomer) || (msg != null && msg != '' && msg != ' ' &&
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
      updateTaskStartDate (date, jobTaskId, bidId, jobTa) {
        console.log (date);

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

    .totalCost {
        padding-left: .5rem;
    }

    .stripe-label {
        /*margin-bottom: .5rem;*/
    }
</style>