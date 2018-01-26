<template>
    <!-- /all details of a bid -->
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Task Price</th>
                        <th>Task Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(task, index) in bid.tasks">
                        <td>{{ task.name }}</td>
                        <td>{{ task.proposed_cust_price }}</td>
                        <td>{{ status(task.job_task.status) }}</td>
                        <td>
                            <button class="btn btn-primary" @click.prevent="openTask(index)">Details</button>
                            <button class="btn btn-success" v-if="showPayForTaskBtn(task)" @click.prevent="payForTask(task)">Pay</button>
                            <button class="btn btn-success" v-if="showFinishedBtn(task)" @click="finishedTask(task)">Finished</button>
                            <button class="btn btn-success" v-if="showApproveBtn(task)" @click="approveTaskHasBeenFinished(task)">Approve</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
</template>

<script>
  export default {
      props: {
          bid: Object
      },
      data() {
          return {
              user: '',
          }
      },
      computed: {
          // was the one who created the bid the one logged in?
          // if so this is a general contractor and should be shown 
          // everything
          isGeneral() {
              return User.isGeneral(this.bid);
          },
          isContractor() {
              return User.isContractor();
          }
      },
      methods: {
          showPayForTaskBtn(task) {
              return (task.job_task.status === 'bid_task.finished_by_general' || task.job_task.status === 'bid_task.approved_by_general') && User.isCustomer();
          },
          showFinishedBtn(task) {
              if (this.isContractor && this.isAssignedToMe(task) && task.job_task.status === 'bid_task.approved_by_customer') {
                  return true;
              }
              return false;
          },
          showApproveBtn(task) {
              if (this.isGeneral && !this.isAssignedToMe(task) && task.job_task.status === 'bid_task.finished_by_sub') {
                  return true;
              }
              return false;
          },
          payForTask(task) {
              Customer.payForTask(task);
          },
          // is the task assigned to the currently logged in user
          isAssignedToMe(task) {
              return this.user.id === task.job_task.contractor_id;
          },
          openTask(index) {
              this.$emit('openTaskPanel', index);
          },
          finishedTask(task) {
              SubContractor.finishedTask(task);
          },
          approveTaskHasBeenFinished(task) {
              GeneralContractor.approveTaskHasBeenFinished(task);
          },
          status(status){
              return User.status(status, this.bid);
          }
      },
      mounted: function () {
          this.user = Spark.state.user;
      }
  }
</script>