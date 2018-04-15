<template>
  <!-- /all tasks of a bid -->
  <div v-if="show">
    <paginate ref="paginator" name="jobTasks" :list="jobTasks" :per="6" class="paginated">
      <div class="col-md-4" v-for="jobTask of paginated('jobTasks')" v-bind:key="jobTask.id" :id="'task-' + jobTask.id">
        <div class="panel">
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-12 form-group">
                <!-- / status -->
                <h4>
                  <label for="task-status" class="label" :class="getLabelClass(jobTask.status)">
                    {{ status(jobTask.status) }}
                  </label>
                </h4>
                <h4 class="task-name">
                  {{ jobTask.task.name }}
                </h4>
              </div>
              <!-- / quick info secion date/price -->
              <div class="col-xs-6 form-group">
                <i class="fas fa-money-bill-alt icon"></i>
                <input type="tel" class="form-control form-control-text" v-if="showTaskPriceInput()" :value="taskCustFinalPrice(jobTask.cust_final_price)"
                  @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)">
                <label v-if="isCustomer || !showTaskPriceInput()">
                  {{taskCustFinalPrice(jobTask.cust_final_price)}} </label>
              </div>
              <!-- / end total price -->
              <div class="col-xs-6 form-group text-right" v-if="isContractor">
                <i class="fas fa-user icon" style="margin-right: 3.5px;"></i>
                <!-- <input type="tel" class="form-control form-control-text" v-if="showTaskPriceInput()" :value="taskCustFinalPrice(jobTask.sub_final_price)"
                              @blur="updateSubTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)"> -->
                <label> {{taskCustFinalPrice(jobTask.sub_final_price)}} </label>
              </div>
              <!-- / end total task price -->

              <div class="col-xs-12 form-group" v-if="isContractor">
                <i class="fas fa-clock icon"></i>
                <input type="date" class="form-control form-control-date" v-if="showTaskStartDate()" :value="prettyDate(jobTask.start_date)"
                  @blur="updateTaskStartDate($event.target.value, jobTask.id, bid.id, jobTask)">
                <label v-if="isCustomer || !showTaskStartDate()">
                  {{prettyDate(jobTask.start_date)}} </label>
              </div>
              <!-- / end quick info section -->

              <div class="col-xs-12">
                <div class="divider2"></div>
              </div>

              <section class="col-xs-12" style="margin-bottom: 8px;">
                <a target="_blank" :href="'https://www.google.com/maps/search/?api=1&query=' + location(jobTask, bid)">
                  <i class="fas fa-map-marker icon"></i>
                  {{ location(jobTask, bid) }}
                </a>
                <button class="btn btn-small pull-right" @click="openUpdateTaskLocation(jobTask)">
                  <i class="fas fa-edit"></i>
                </button>
              </section>
              <!-- / end address section -->

              <div class="col-xs-12" v-if="showSubMessage(jobTask)">
                <div class="divider2"></div>
              </div>

              <section class="col-xs-12" v-if="showSubMessage(jobTask)">
                <p>
                  {{ jobTask.sub_message }}
                </p>
              </section>

              <div class="col-xs-12" v-if="showCustomerMessage(jobTask)">
                <div class="divider2"></div>
              </div>

              <section class="col-xs-12" v-if="showCustomerMessage(jobTask)">
                <p>
                  {{ jobTask.customer_message }}
                </p>
              </section>
              <!-- / end details section -->

            </div>

          </div>
          <div class="panel-footer">
            <div class="row" v-if="showPanelActions(jobTask.status)">
              <center>
                <div class="col-xs-4" v-if="isContractor">
                  <button class="btn btn-secondary" @click.prevent="openTaskBids(jobTask.id)" v-if="isGeneral">
                    <i class="fas fa-users fa-2x"></i>
                  </button>
                </div>
                <div class="col-xs-4" v-if="isContractor">
                  <button class="btn btn-secondary" @click.prevent="openSubInvite(jobTask)" v-if="isGeneral && showSendSubInvite">
                    <i class="fas fa-user-plus fa-2x"></i>
                  </button>
                </div>
                <div class="col-xs-4" v-if="isContractor">
                  <button class="btn btn-secondary" @click.prevent="openTaskActions(jobTask.id)">
                    <i class="fas fa-cogs fa-2x"></i>
                  </button>
                </div>

                <!-- <div class="col-xs-4" v-if="isCustomer">
                  <button class="btn btn-primary" v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                    Deny
                  </button>
                </div> -->
                <!-- <div class="col-xs-4" v-if="isCustomer">
                  <button class="btn btn-success" v-if="showPayCashForTaskBtn(jobTask)" @click.prevent="paidWithCashTask(jobTask)" :disabled="disabled.payCash">
                    <span v-if="disabled.payCash">
                      <i class="fa fa-btn fa-spinner fa-spin"></i>
                    </span>
                    Cash
                  </button>
                </div> -->
                <!-- <div class="col-xs-4" v-if="isCustomer">
                  <button class="btn btn-success" v-if="showPayForTaskBtn(jobTask)" @click.prevent="payForTask(jobTask)" :disabled="disabled.pay">
                    <span v-if="disabled.pay">
                      <i class="fa fa-btn fa-spinner fa-spin"></i>
                    </span>
                    Pay
                  </button>
                </div> -->

                <transition-group name="slide-fade" v-if="isContractor">
                  <div class="col-xs-12 hidden" :id="'task-divider-' + jobTask.id" :key="1">
                    <div class="divider2"></div>
                  </div>

                  <!-- / task options -->
                  <div class="col-xs-12 hidden" :id="'task-options-' + jobTask.id" :key="2">
                    <div class="col-xs-4">
                      <!-- Rounded switch -->
                      <span class="stripe-label" v-if="showStripeToggle(jobTask)">Use Stripe</span>
                      <label v-if="showStripeToggle(jobTask)" class="switch">
                        <input :id="'toggle-stripe-' + jobTask.id" type="checkbox" v-model="jobTask.stripe" @click="toggleStripePaymentOption(jobTask)">
                        <span class="slider round"></span>
                      </label>
                    </div>
                    <div class="col-xs-4">
                      <button class="btn btn-primary" v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
                        Deny
                      </button>
                      <!-- <button class="btn btn-warning" v-if="showReopenBtn(jobTask)" @click="reopenTask(jobTask)" :disabled="disabled.reopen">
                        <span v-if="disabled.reopen">
                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Reopen
                      </button> -->
                      <button class="btn btn-danger" v-if="showDeleteBtn(jobTask)" @click="deleteTask(jobTask)" :disabled="disabled.deleteTask">
                        <span v-if="disabled.deleteTask">
                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Delete
                      </button>
                    </div>
                    <div class="col-xs-4">
                      <button class="btn btn-success" v-if="showFinishedBtn(jobTask)" @click="finishedTask(jobTask)" :disabled="disabled.finished">
                        <span v-if="disabled.finished">
                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Finished
                      </button>
                      <button class="btn btn-success" v-if="showApproveBtn(jobTask)" @click="approveTaskHasBeenFinished(jobTask)" :disabled="disabled.approve">
                        <span v-if="disabled.approve">
                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Approve
                      </button>
                    </div>
                  </div>
                  <div class="col-xs-12 hidden" :id="'task-subs-' + jobTask.id" v-if="isGeneral && !taskApproved" :key="3">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Sub</th>
                          <th>Price</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="table" v-for="bid in jobTask.bid_contractor_job_tasks" :key="bid.id">
                          <td>{{ bid.contractor.name }}</td>
                          <td>${{ bid.bid_price }}</td>
                          <td>
                            <button v-if="showAcceptBtn(jobTask.status)" @click="acceptSubBidForTask(bid, jobTask)" class="button btn btn-sm btn-success"
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
                  <!-- /end col-md-6 -->
                </transition-group>
              </center>
            </div>
          </div>
        </div>
      </div>
    </paginate>
    <div class="col-md-12">
      <div class="panel">
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
    data() {
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
      jobTasks() {
        return User.getAllUnpaidTasks(this.bid.job_tasks);
      },
      show() {
        return this.jobTasks.length > 0;
      },
      taskApproved() {
        return this.jTask.status === 'bid_task.approved_by_customer';
      },
      isCustomer() {
        return User.isCustomer();
      },
      isGeneral() {
        return User.isGeneral(this.bid);
      },
      isContractor() {
        return User.isContractor();
      },
      showSendSubInvite() {
        if (this.bid.status === 'bid.initiated' || this.bid.status === 'bid.in_progress' || this.bid.status ===
          'bid.sent') {
          return true;
        }
        return false;
      },
      generalTotalTaskPrice() {
        let total = 0;
        for (const jobTask of this.bid.job_tasks) {
          if (jobTask !== null) {
            total += jobTask.cust_final_price;
          }
        }
        return total;
      },
      subTotalTaskPrices() {
        let total = 0;
        for (const jobTask of this.bid.job_tasks) {
          total += this.subTaskPrice(jobTask);
        }
        return total;
      }
    },
    methods: {
      showSubMessage(jobTask) {
        let msg = jobTask.sub_message;
        return (msg != null && msg != '' && msg != ' ' && this.isContractor) || (msg != null && msg != '' && msg != ' ' && this.isGeneral);
      },
      showCustomerMessage(jobTask) {
        let msg = jobTask.customer_message;
        return (msg != null && msg != '' && msg != ' ' && this.isCustomer) || (msg != null && msg != '' && msg != ' ' && this.isGeneral);
      },
      openUpdateTaskLocation(jobTask) {
        this.jTask = jobTask;
        $('#update-task-location-modal').modal();
      },
      showPanelActions(status) {
        if (status !== 'bid_task.customer_sent_payment') {
          return true;
        }
        return false;
      },
      getLabelClass(status) {
        return Format.statusLabel(status);
      },
      showAcceptBtn(status) {
        return status === 'bid_task.bid_sent';
      },
      openTaskBids(id) {
        if ($('#task-options-' + id).hasClass('hidden') || this.isCustomer) {
          $('#task-divider-' + id).toggleClass('hidden');
        }
        $('#task-subs-' + id).toggleClass('hidden');
      },
      openSubInvite(jobTask) {
        this.jTask = jobTask;
        $('#sub-invite-modal').modal();
      },
      openTaskActions(id) {
        if ($('#task-subs-' + id).hasClass('hidden') || this.isCustomer) {
          $('#task-divider-' + id).toggleClass('hidden');
        }
        $('#task-options-' + id).toggleClass('hidden');
      },
      preview(jobTask, subId) {
        Bus.$emit('previewSubForTask', [
          jobTask.job_id,
          jobTask.id,
          subId
        ]);
      },
      acceptSubBidForTask(bid, jobTask) {
        GeneralContractor.acceptSubBidForTask(jobTask, bid, this.disabled);
      },
      location(jobTask, bid) {
        const task_location = jobTask.location_id;
        const job_location = this.bid.location_id;
        console.log("task_location: " + task_location)
        console.log("job_location: " + job_location)
        if (task_location === null && job_location === null) {
          return 'No Address Set Yet';
        } else if (task_location !== null) {
          return jobTask.location.address_line_1;
        } else if (job_location !== null) {
          return bid.location.address_line_1;
        }
      },
      prettyDate(date) {
        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split(' ');
        return date[0];
      },
      showStripeToggle(jobTask) {
        return User.isAssignedToMe(jobTask);
      },
      openDenyTaskForm(jobTask) {
        this.jTask = jobTask;
        $('#deny-task-modal').modal();
      },
      showTaskStartDate() {
        return this.isGeneral && (this.bid.status === 'bid.in_progress' || this.bid.status === 'bid.initiated' || this.bid.status === 'bid.declined');
      },
      showTaskPriceInput() {
        console.log('isGeneral: ' + this.isGeneral)
        console.log('bid.in_progress' + this.bid.status)
        console.log('bid.initiated' + this.bid.status)
        return this.isGeneral && (this.bid.status === 'bid.in_progress' || this.bid.status === 'bid.initiated' || this.bid.status === 'bid.declined');
      },
      updateTaskStartDate(date, jobTaskId, bidId, jobTa) {
        console.log(date);

      },
      updateCustomerTaskPrice(price, jobTaskId, bidId, jobTask) {
        price = price.replace(/[^0-9.]/g, '');
        let taskPrice = jobTask.cust_final_price;
        taskPrice = taskPrice.toString();
        // debugger
        if ((taskPrice !== price)) {
          GeneralContractor.updateCustomerPrice(price, jobTaskId, bidId)
        }
      },
      showDenyBtn(jobTask) {
        const status = jobTask.status;
        if (this.isCustomer) {
          return (status === 'bid_task.finished_by_general' || status === 'bid_task.approved_by_general');
        }
        return (status === 'bid_task.finished_by_sub');
        // return (status === 'bid_task.finished_by_sub' || this.bid.status === 'bid.declined');
      },
      taskCustFinalPrice(price) {
        return '$' + price;
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
      showFinishedBtn(jobTask) {
        if (this.isContractor && User.isAssignedToMe(jobTask) && (jobTask.status === 'bid_task.approved_by_customer' ||
            jobTask.status === 'bid_task.reopened' || jobTask.status === 'bid_task.denied')) {
          return true;
        }
        return false;
      },
      showApproveBtn(jobTask) {
        if (this.isGeneral && !User.isAssignedToMe(jobTask) && (jobTask.status === 'bid_task.finished_by_sub' ||
            jobTask.status === 'bid_task.reopened')) {
          return true;
        }
        return fa
      },
      showDeleteBtn(jobTask) {
        const status = jobTask.status;
        if (this.isGeneral && (status === 'bid_task.initiated' || status === 'bid_task.bid_sent' || this.bid.status === 'bid.declined')) {
          return true;
        }
        return false;
      },
      // reopenTask(jobTask) {
      //   SubContractor.reopenTask(jobTask, this.disabled);
      // },
      deleteTask(jobTask) {
        GeneralContractor.deleteTask(jobTask, this.disabled);
      },
      /**
       * customer task price
       */
      subTaskPrice(jobTask) {
        if (jobTask === null) {
          return 0;
        }
        if (jobTask.bid_id === null) {
          return 0;
        } else {
          return User.findTaskBid(jobTask.bid_id, jobTask.bid_contractor_job_tasks)[0].bid_price;
        }
      },
      toggleStripePaymentOption(jobTask) {
        jobTask.checked = $('#toggle-stripe-' + jobTask.id).is(':checked');
        SubContractor.toggleStripePaymentOption(jobTask);
      },
      // payForTask(jobTask) {
      //   Customer.payForTask(jobTask, this.disabled);
      // },
      // paidWithCashTask(jobTask) {
      //   Customer.paidWithCashTask(jobTask, this.disabled);
      // },
      openTaskPanel(index) {
        this.$emit('openTaskPanel', index);
      },
      finishedTask(jobTask) {
        SubContractor.finishedTask(jobTask, this.disabled);
      },
      approveTaskHasBeenFinished(jobTask) {
        GeneralContractor.approveTaskHasBeenFinished(jobTask, this.disabled);
      },
      status(status) {
        return User.status(status, this.bid);
      }
    },
    mounted: function () {
      this.user = Spark.state.user;
    }
  }
</script>

<style scope>
  .stripe-label {
    /*margin-bottom: .5rem;*/
  }
</style>