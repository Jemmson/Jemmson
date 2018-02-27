<template>
  <!-- /all tasks of a bid -->
  <div class="col-md-12">
    <table class="table">
      <thead>
        <tr>
          <th>Task Name</th>
          <th>Task Price</th>
          <th>Task Sub Price</th>
          <th>Task Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(task, index) in bid.tasks" v-if="task.job_task !== null" v-bind:key="task.id" :id="'task-' + task.id">
          <td>{{ task.name }}</td>
          <td>
            <input v-if="showTaskPriceInput()" type="text" :value="taskCustFinalPrice(task.job_task.cust_final_price)" @blur="updateCustomerTaskPrice($event.target.value, task.id, bid.id, task)">
            <label v-if="isCustomer || !showTaskPriceInput()"> {{taskCustFinalPrice(task.job_task.cust_final_price)}} </label>
          </td>
          <td v-if="isContractor">${{ subTaskPrice(task) }}</td>
          <td>{{ status(task.job_task.status) }}</td>
          <td>
            <button class="btn btn-primary" @click.prevent="openTaskPanel(index)">Details</button>

            <button class="btn btn-success" v-if="showPayForTaskBtn(task)" @click.prevent="payForTask(task)" :disabled="disabled.pay">
              <span v-if="disabled.pay">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Pay
            </button>
            <button class="btn btn-success" v-if="showPayForTaskBtn(task)" @click.prevent="paidWithCashTask(task)" :disabled="disabled.payCash">
              <span v-if="disabled.payCash">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Paid With Cash
            </button>
            <button class="btn btn-success" v-if="showFinishedBtn(task)" @click="finishedTask(task)" :disabled="disabled.finished">
              <span v-if="disabled.finished">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Finished
            </button>
            <button class="btn btn-success" v-if="showApproveBtn(task)" @click="approveTaskHasBeenFinished(task)" :disabled="disabled.approve">
              <span v-if="disabled.approve">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Approve
            </button>
            <button class="btn btn-primary" v-if="showDenyBtn(task)" @click="openDenyTaskForm(task)">
              Deny
            </button>
            <button class="btn btn-warning" v-if="showReopenBtn(task)" @click="reopenTask(task)" :disabled="disabled.reopen">
              <span v-if="disabled.reopen">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Reopen
            </button>
            <button class="btn btn-danger" v-if="showDeleteBtn(task)" @click="deleteTask(task)" :disabled="disabled.deleteTask">
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
              Deny Approval For: {{task.name}}
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
</template>

<script>
  export default {
    props: {
      bid: Object
    },
    data () {
      return {
        user: '',
        task: {},
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
        for (const task of this.bid.tasks) {
          if (task.job_task !== null) {
            total += task.job_task.cust_final_price;
          }
        }
        return total;
      },
      subTotalTaskPrices () {
        let total = 0;
        for (const task of this.bid.tasks) {
          total += this.subTaskPrice (task);
        }
        return total;
      }
    },
    methods: {
      openDenyTaskForm(task) {
        if (task.id === this.task.id) {
            this.disabled.showDenyForm = this.disabled.showDenyForm ? false : true;
        } else {
            this.disabled.showDenyForm = true;
        }
        this.task = task;
        $("#deny-form").insertAfter('#task-' + task.id);
      },
      showTaskPriceInput() {
        return this.isGeneral && (this.bid.status === 'bid.in_progress' || this.bid.status === 'bid.initiated');
      },
      updateCustomerTaskPrice (price, taskId, bidId, task) {
        price = price.replace(/[^0-9.]/g, "");
        let taskPrice = task.job_task.cust_final_price;
        taskPrice = taskPrice.toString();
        // debugger
        if ((taskPrice !== price)) {
          GeneralContractor.updateCustomerPrice (price, taskId, bidId)
        }
      },
      showDenyBtn (task) {
        return this.isCustomer && (task.job_task.status === 'bid_task.finished_by_general' || task.job_task.status === 'bid_task.approved_by_general');
      },
      taskCustFinalPrice (price) {
        return '$' + price;
      },
      showReopenBtn (task) {
        if (this.isContractor && (task.job_task.status === 'bid_task.finished_by_general' || task.job_task.status === 'bid_task.approved_by_general')) {
          return true;
        }
        return false;
      },
      showPayForTaskBtn (task) {
        return (task.job_task.status === 'bid_task.finished_by_general' || task.job_task.status === 'bid_task.approved_by_general') && User.isCustomer ();
      },
      showFinishedBtn (task) {
        if (this.isContractor && this.isAssignedToMe (task) && (task.job_task.status === 'bid_task.approved_by_customer' || task.job_task.status === 'bid_task.reopened' || task.job_task.status === 'bid_task.denied')) {
          return true;
        }
        return false;
      },
      showApproveBtn (task) {
        if (this.isGeneral && !this.isAssignedToMe (task) && (task.job_task.status === 'bid_task.finished_by_sub' || task.job_task.status === 'bid_task.reopened')) {
          return true;
        }
        return false;
      },
      showDeleteBtn (task) {
        const status = task.job_task.status;
        if (this.isGeneral && (status === 'bid_task.initiated' || status === 'bid_task.bid_sent')) {
          return true;
        }
        return false;
      },
      reopenTask (task) {
        SubContractor.reopenTask (task, this.disabled);
      },
      deleteTask (task) {
        GeneralContractor.deleteTask (task, this.disabled);
      },
      /**
       * customer task price
       */
      subTaskPrice (task) {
        if (task.job_task === null) {
          return 0;
        }
        if (task.job_task.bid_id === null) {
          return 0;
        } else {
          return User.findTaskBid (task.job_task.bid_id, task.bid_contractor_job_tasks)[0].bid_price;
        }
      },
      payForTask (task) {
        Customer.payForTask (task, this.disabled);
      },
      paidWithCashTask (task) {
        Customer.paidWithCashTask (task, this.disabled);
      },
      // is the task assigned to the currently logged in user
      isAssignedToMe (task) {
        return this.user.id === task.job_task.contractor_id;
      },
      openTaskPanel (index) {
        this.$emit ('openTaskPanel', index);
      },
      finishedTask (task) {
        SubContractor.finishedTask (task, this.disabled);
      },
      approveTaskHasBeenFinished (task) {
        GeneralContractor.approveTaskHasBeenFinished (task, this.disabled);
      },
      denyTask() {
        this.task.message = this.message;
        Customer.denyTask(this.task, this.disabled);
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