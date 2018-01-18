<template>
    <!-- /all details of a bid -->
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
                            <button class="btn btn-success" v-if="isGeneral" @click="finishedTask(task)">Finished</button>
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
          isGeneral() {
              return this.bid.contractor_id === this.user.id;
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
          this.user = Spark.state.user;
      }
  }
</script>