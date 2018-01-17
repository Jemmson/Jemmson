<template>
    <!-- /all details of a bid -->
        <div class="">
            <div class="col-md-6">
                <label>Job Name: </label>
                <label class="label label-primary">
                    {{ bid.job_name }}
                </label>
            </div>
            <div class="col-md-6">
                <label>Status: </label>
                <label class="label label-warning">
                    {{ bid.status }}
                </label>
            </div>
            <!-- /end detail header -->
            <div style="padding-top: 40px"></div>
            <div class="col-md-6">
                <p>
                    <label>
                        Location:
                    </label>
                </p>
                <p>
                    {{ bid.address_line_1 }}
                </p>
                <p>
                    {{ bid.city }}, {{ bid.state }} {{ bid.zip }}
                </p>
            </div>
            <div class="col-md-6">
                <p>
                    <label for="price">Prices:</label>
                </p>
                <p>
                    Total Job Price:
                    <label class="label label-info">${{ bid.bid_price }}</label>
                </p>
            </div>
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Task Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in bid.tasks">
                            <td>{{ task.name }}</td>
                            <td>{{ task.proposed_cust_price }}</td>
                            <td>
                                <button class="btn btn-primary" @click.prevent="openTask(task)">Details</button>
                                <button class="btn btn-success" v-if="userType === 'contractor'" @click="finishedTask(task)">Finished</button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
</template>

<script>
  export default {
      props: {
          bid: Object
      },
      data() {
          return {
              userType: '',
          }
      },
      methods: {
          openTask(task) {
              this.$emit('openTaskPanel', task);
          },
          finishedTask(task) {
              console.log('finishedTask', task);
          }
      },
      mounted: function () {
          this.userType = Spark.state.user.usertype;
      }
  }
</script>