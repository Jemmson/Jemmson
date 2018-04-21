<template>
    <!-- Modal -->
    <div class="modal fade" id="add-task-modal" tabindex="-1" role="dialog" aria-labelledby="add-task-modal"
         aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add A New Task</h4>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('taskName')}">
                            <label for="taskName">Task Name</label>
                            <input type="text" class="form-control" id="taskName" name="taskName" autofocus
                                   v-model="addNewTaskForm.taskName" v-on:keyup="getExistingTask">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('taskName')">
                                {{ addNewTaskForm.errors.get('taskName') }}
                            </span>
                            <div class="panel-footer" v-if="taskResults.length">
                                <ul class="list-group">
                                    <button class="list-group-item" v-for="result in taskResults" v-bind:key="result.id"
                                            @click.prevent="fillTaskValues(result)">
                                        {{ result.name }}
                                    </button>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group"
                             :class="{'has-error': addNewTaskForm.errors.has('qty')}">
                            <label for="qty">QTY</label>
                            <input type="number" class="form-control" min="1" id="qty"
                                   name="qty" required v-model="addNewTaskForm.qty">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('qty')">
                                {{ addNewTaskForm.errors.get('qty') }}
                            </span>
                        </div>

                        <div class="form-group"
                             :class="{'has-error': addNewTaskForm.errors.has('start_when_accepted')}">
                            <label for="start_when_accepted">Start Job When Customer Accepts Bid?</label>
                            <input type="checkbox" class="checkbox-inline accepted-checkbox" id="start_when_accepted"
                                   name="start_when_accepted" required v-model="addNewTaskForm.start_when_accepted">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('start_when_accepted')">
                                {{ addNewTaskForm.errors.get('start_when_accepted') }}
                            </span>
                        </div>

                        <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('start_date')}"
                             v-if="!addNewTaskForm.start_when_accepted">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required
                                   v-model="addNewTaskForm.start_date">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('start_date')">
                                {{ addNewTaskForm.errors.get('start_date') }}
                            </span>
                        </div>

                        <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('taskPrice')}">
                            <label for="custTaskPrice">Customer Task Price</label>
                            <input type="tel" class="form-control" id="custTaskPrice" name="taskPrice"
                                   v-model="addNewTaskForm.taskPrice" @blur="formatPrice('taskPrice')">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('taskPrice')">
                                {{ addNewTaskForm.errors.get('taskPrice') }}
                            </span>
                        </div>

                        <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('subTaskPrice')}">
                            <label for="subTaskPrice">Sub Task Price</label>
                            <input type="tel" class="form-control" id="subTaskPrice" name="subTaskPrice"
                                   v-model="addNewTaskForm.subTaskPrice" @blur="formatPrice('subTaskPrice')">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('subTaskPrice')">
                                {{ addNewTaskForm.errors.get('subTaskPrice') }}
                            </span>
                        </div>

                        <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('sub_message')}">
                            <label for="sub_message">Details For Sub To See</label>
                            <textarea class="form-control" id="sub_message" name="sub_message"
                                   v-model="addNewTaskForm.sub_message">
                            </textarea>
                            <span class="help-block" v-show="addNewTaskForm.errors.has('sub_message')">
                                {{ addNewTaskForm.errors.get('sub_message') }}
                            </span>
                        </div>

                        <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('customer_message')}">
                            <label for="customer_message">Details For Customer To See</label>
                            <textarea class="form-control" id="customer_message" name="customer_message"
                                   v-model="addNewTaskForm.customer_message">
                            </textarea>
                            <span class="help-block" v-show="addNewTaskForm.errors.has('customer_message')">
                                {{ addNewTaskForm.errors.get('customer_message') }}
                            </span>
                        </div>

                        <h3>Use Stripe For Payment</h3>
                        <label class="switch">
                            <input type="checkbox" @click="toggleStripePaymentOption()">
                            <span class="slider round"></span>
                        </label>

                    </form>
                    <!-- /end col-md-6 -->
                </div>
                <div class="modal-footer">
                    <div class="form-group ">
                        <button id="addTaskToInvoice" class="btn btn-success" @click.prevent="addNewTaskToBid()">
                            Add Task
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      bid: Object,
      show: Boolean
    },
    data () {
      return {
        addNewTaskForm: new SparkForm ({
          taskId: '',
          taskExists: '',
          jobId: this.bid.id,
          subTaskPrice: '',
          taskPrice: '',
          taskName: '',
          contractorId: '',
          area: this.bid.city,
          start_date: '',
          start_when_accepted: true,
          useStripe: false,
          sub_message: '',
          customer_message: '',
          qty: 1,
        }),
        taskResults: [],
      }
    },
    methods: {
      formatPrice (price) {
        Format.addDollarSign (this.addNewTaskForm, price);
      },
      getExistingTask () {
        this.taskResults = [];
        if (this.addNewTaskForm.taskName.length > 1) {
          axios.post ('/api/search/task', {
            taskname: this.addNewTaskForm.taskName,
            jobId: this.bid.id
          }).then (response => {
            console.log (response.data)
            this.taskResults = response.data
          })
        }
      },
      filterReturnedTasks (responseData, allTasks) {
        let responseDataLength = responseData.length
        let allTasksDataLength = allTasks.length
        let newTasks = []

        for (let i = 0; i < responseDataLength; i++) {
          let flag = false
          for (let j = 0; j < allTasksDataLength; j++) {
            if (responseData[i].id === allTasks[j].id) {
              flag = true
            }
          }
          // debugger
          if (flag === false) {
            newTasks.push (responseData[i])
          }
        }
        return newTasks
      },
      fillTaskValues (result) {
        console.log (result)
        this.taskExists = true
        this.addNewTaskForm.taskName = result.name;
        this.addNewTaskForm.taskPrice = result.proposed_cust_price;
        this.addNewTaskForm.subTaskPrice = result.proposed_sub_price;
        this.formatPrice ('taskPrice');
        this.formatPrice ('subTaskPrice');
        this.clearTaskResults ()
      },
      clearTaskResults () {
        this.taskResults = [];
      },
      addNewTaskToBid () {
        GeneralContractor.addNewTaskToBid(this.bid, this.addNewTaskForm);
        this.addNewTaskToBid.qty = 1;
      },
      // // showStripeToggle (jobTask) {
      // //   return User.isAssignedToMe (jobTask);
      // // },
      toggleStripePaymentOption () {
        this.addNewTaskForm.useStripe = !this.addNewTaskForm.useStripe
      }
    },
    mounted: function () {
    }
  }
</script>

<style scoped>
    .accepted-checkbox {

    }
</style>