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
                    <tr v-for="task in bid.tasks">
                        <td>{{ task.name }}</td>
                        <td>{{ task.proposed_cust_price }}</td>
                        <td>{{ status(task.job_task.status) }}</td>
                        <td>
                            <button class="btn btn-primary" @click.prevent="openTask(task)">Details</button>
                            <button class="btn btn-success" v-if="isContractor && isAssignedToMe(task)" @click="finishedTask(task)">Finished</button>
                            <button class="btn btn-success" v-if="isGeneral && !isAssignedToMe(task)" @click="approveTaskHasBeenFinished(task)">Approve</button>
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
          // is the task assigned to the currently logged in user
          isAssignedToMe(task) {
              return this.user.id === task.job_task.contractor_id;
          },
          openTask(task) {
              this.$emit('openTaskPanel', task);
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