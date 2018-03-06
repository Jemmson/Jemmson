<template>
  <!-- /all tasks of a bid -->
  <div class="panel">
    <div class="panel-body">
      <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Task Name</th>
          <th>Task Price</th>
          <th>Task Sub Price</th>
          <th>Task Status</th>
          <th>Stripe Payment</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(jobTask, index) in bid.job_tasks" v-if="jobTask !== null" v-bind:key="jobTask.id" :id="'task-' + jobTask.id">
          <td>{{ jobTask.task.name }}</td>
          <td>
            <input v-if="showTaskPriceInput()" type="text" :value="taskCustFinalPrice(jobTask.cust_final_price)" @blur="updateCustomerTaskPrice($event.target.value, jobTask.id, bid.id, jobTask)">
            <label v-if="isCustomer || !showTaskPriceInput()"> {{taskCustFinalPrice(jobTask.cust_final_price)}} </label>
          </td>
          <td v-if="isContractor">${{ subTaskPrice(jobTask) }}</td>
          <td>{{ status(jobTask.status) }}</td>
          <td>
            <!-- Rounded switch -->
            <label v-if="showStripeToggle(jobTask)" class="switch">
              <input :id="'toggle-stripe-' + jobTask.id" type="checkbox" v-model="jobTask.stripe" @click="toggleStripePaymentOption(jobTask)">
              <span class="slider round"></span>
            </label>
          </td>
          
          <td>
            <button class="btn btn-primary" @click.prevent="openTaskPanel(index)">Details</button>

            <button class="btn btn-success" v-if="showPayForTaskBtn(jobTask)" @click.prevent="payForTask(jobTask)" :disabled="disabled.pay">
              <span v-if="disabled.pay">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Pay
            </button>
            <button class="btn btn-success" v-if="showPayCashForTaskBtn(jobTask)" @click.prevent="paidWithCashTask(jobTask)" :disabled="disabled.payCash">
              <span v-if="disabled.payCash">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Paid With Cash
            </button>
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
            <button class="btn btn-primary" v-if="showDenyBtn(jobTask)" @click="openDenyTaskForm(jobTask)">
              Deny
            </button>
            <button class="btn btn-warning" v-if="showReopenBtn(jobTask)" @click="reopenTask(jobTask)" :disabled="disabled.reopen">
              <span v-if="disabled.reopen">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Reopen
            </button>
            <button class="btn btn-danger" v-if="showDeleteBtn(jobTask)" @click="deleteTask(jobTask)" :disabled="disabled.deleteTask">
              <span v-if="disabled.deleteTask">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Delete
            </button>
          </td>
        </tr>
        <tr v-if="isContractor">
          <td></td>
          <td>Total: ${{ generalTotalTaskPrice }}</td>
          <td>Total: ${{ subTotalTaskPrices }}</td>
          <td></td>
          <td>
          </td>
        </tr>
        <transition name="slide-fade">
          <tr v-show="disabled.showDenyForm" id="deny-form">
            <td>
              <!-- Deny Approval For: {{jobTask.task.name}} -->
            </td>
            <td>
              <input type="text" class="form-control" v-model="message">
            </td>
            <td>
              <button class="btn btn-danger" @click.prevent="denyTask" :disabled="disabled.deny">
                              <span v-if="disabled.deny">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
                Deny Approval
              </button>
            </td>
          </tr>
        </transition>
      </tbody>
    </table>
    </div>
      </div>
    
  </div>
</template>

<script>
  export default {
    props: {
      bid: Object
    },
    data () {
      return {
        user: '',
        jobTask: {},
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
        }
      }
    },
    computed: {
      // was the one who created the bid the one logged in?
      // if so this is a general contractor and should be shown
      // everything
      isCustomer () {
        return User.isCustomer ();
      },
      isGeneral () {
        return User.isGeneral (this.bid);
      },
      isContractor () {
        return User.isContractor ();
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
          total += this.subTaskPrice(jobTask);
        }
        return total;
      }
    },
    methods: {
      showStripeToggle(jobTask) {
        return User.isAssignedToMe(jobTask);
      },
      openDenyTaskForm(jobTask) {
        if (jobTask.id === this.jobTask.id) {
            this.disabled.showDenyForm = this.disabled.showDenyForm ? false : true;
        } else {
            this.disabled.showDenyForm = true;
        }
        this.jobTask = jobTask;
        $("#deny-form").insertAfter('#task-' + jobTask.id);
      },
      showTaskPriceInput() {
        return this.isGeneral && (this.bid.status === 'bid.in_progress' || this.bid.status === 'bid.initiated');
      },
      updateCustomerTaskPrice (price, jobTaskId, bidId, jobTask) {
        price = price.replace(/[^0-9.]/g, "");
        let taskPrice = jobTask.cust_final_price;
        taskPrice = taskPrice.toString();
        // debugger
        if ((taskPrice !== price)) {
          GeneralContractor.updateCustomerPrice(price, jobTaskId, bidId)
        }
      },
      showDenyBtn (jobTask) {
        const status = jobTask.status;
        if (this.isCustomer) {
            return (status === 'bid_task.finished_by_general' || status === 'bid_task.approved_by_general');
        }
        return status === 'bid_task.finished_by_sub';
      },
      taskCustFinalPrice (price) {
        return '$' + price;
      },
      showReopenBtn (jobTask) {
        if (this.isContractor && (jobTask.status === 'bid_task.finished_by_general' || jobTask.status === 'bid_task.approved_by_general')) {
          return true;
        }
        return false;
      },
      showPayCashForTaskBtn(jobTask) {
        return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status === 'bid_task.approved_by_general') && User.isCustomer();
      },
      showPayForTaskBtn (jobTask) {
        return (jobTask.status === 'bid_task.finished_by_general' || jobTask.status === 'bid_task.approved_by_general') && User.isCustomer() && jobTask.stripe;
      },
      showFinishedBtn (jobTask) {
        if (this.isContractor && User.isAssignedToMe(jobTask) && (jobTask.status === 'bid_task.approved_by_customer' || jobTask.status === 'bid_task.reopened' || jobTask.status === 'bid_task.denied')) {
          return true;
        }
        return false;
      },
      showApproveBtn (jobTask) {
        if (this.isGeneral && !User.isAssignedToMe(jobTask) && (jobTask.status === 'bid_task.finished_by_sub' || jobTask.status === 'bid_task.reopened')) {
          return true;
        }
        return false;
      },
      showDeleteBtn (jobTask) {
        const status = jobTask.status;
        if (this.isGeneral && (status === 'bid_task.initiated' || status === 'bid_task.bid_sent')) {
          return true;
        }
        return false;
      },
      reopenTask (jobTask) {
        SubContractor.reopenTask (jobTask, this.disabled);
      },
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
      toggleStripePaymentOption(jobTask) {
        jobTask.checked = $('#toggle-stripe-'+jobTask.id).is(':checked');
        SubContractor.toggleStripePaymentOption(jobTask);
      },
      payForTask (jobTask) {
        Customer.payForTask (jobTask, this.disabled);
      },
      paidWithCashTask (jobTask) {
        Customer.paidWithCashTask (jobTask, this.disabled);
      },
      openTaskPanel (index) {
        this.$emit ('openTaskPanel', index);
      },
      finishedTask (jobTask) {
        SubContractor.finishedTask (jobTask, this.disabled);
      },
      approveTaskHasBeenFinished (jobTask) {
        GeneralContractor.approveTaskHasBeenFinished (jobTask, this.disabled);
      },
      denyTask() {
        this.jobTask.message = this.message;
        this.jobTask.user_id = User.getId();
        Customer.denyTask(this.jobTask, this.disabled);
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