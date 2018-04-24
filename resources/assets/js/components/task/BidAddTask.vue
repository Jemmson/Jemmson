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
                    <form role="form" class="wrapper">


                        <div class="form-group wrapper-task-name" :class="{'has-error': addNewTaskForm.errors.has('taskName')}">
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

                        <div class="form-group qty"
                             :class="{'has-error': addNewTaskForm.errors.has('qty')}">
                            <label for="qty">Quantity</label>
                            <input type="number" class="form-control" min="1" id="qty"
                                   name="qty" required v-model="addNewTaskForm.qty">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('qty')">
                                {{ addNewTaskForm.errors.get('qty') }}
                            </span>
                        </div>

                        <div class="form-group qty-unit"
                             :class="{'has-error': addNewTaskForm.errors.has('qtyUnit')}">
                            <label for="qtyUnit">Quantity Unit</label>
                            <input type="text" class="form-control" min="1" id="qtyUnit"
                                   name="qtyUnit" required v-model="addNewTaskForm.qtyUnit">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('qtyUnit')">
                                {{ addNewTaskForm.errors.get('qtyUnit') }}
                            </span>
                        </div>


                        <div class="form-group customer-price"
                             :class="{'has-error': addNewTaskForm.errors.has('taskPrice')}">
                            <label for="custTaskPrice">Unit Price For Customer</label>
                            <input type="tel" class="form-control" id="custTaskPrice" name="taskPrice"
                                   v-model="addNewTaskForm.taskPrice" @blur="formatPrice('taskPrice')">
                            <div v-if="priceChange">
                                <label for="acceptNewStandardPrice">Would you like for this to be the new standard
                                    Price?</label>
                                <div id="acceptNewStandardPrice" class="btn-group">
                                    <button class="btn btn-sm btn-primary" @click.prevent="changePrice('Yes')">Yes
                                    </button>
                                    <button class="btn btn-sm btn-primary" @click.prevent="changePrice('No')">No
                                    </button>
                                </div>
                            </div>
                            <span class="help-block" v-show="addNewTaskForm.errors.has('taskPrice')">
                                {{ addNewTaskForm.errors.get('taskPrice') }}
                            </span>
                        </div>

                        <div class="form-group sub-price"
                             :class="{'has-error': addNewTaskForm.errors.has('subTaskPrice')}">
                            <label for="subTaskPrice">Unit Price For Sub</label>
                            <input type="tel" class="form-control" id="subTaskPrice" name="subTaskPrice"
                                   v-model="addNewTaskForm.subTaskPrice" @blur="formatPrice('subTaskPrice')">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('subTaskPrice')">
                                {{ addNewTaskForm.errors.get('subTaskPrice') }}
                            </span>
                        </div>


                        <div class="form-group start-date-flag"
                             :class="{'has-error': addNewTaskForm.errors.has('start_when_accepted')}">
                            <label for="start_when_accepted">Start Job When Customer Accepts Bid?</label>
                            <input type="checkbox" class="checkbox-inline accepted-checkbox" id="start_when_accepted"
                                   name="start_when_accepted" required v-model="addNewTaskForm.start_when_accepted">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('start_when_accepted')">
                                {{ addNewTaskForm.errors.get('start_when_accepted') }}
                            </span>
                        </div>

                        <div class="form-group start-date"
                             :class="{'has-error': addNewTaskForm.errors.has('start_date')}"
                             v-if="!addNewTaskForm.start_when_accepted">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required
                                   v-model="addNewTaskForm.start_date">
                            <span class="help-block" v-show="addNewTaskForm.errors.has('start_date')">
                                {{ addNewTaskForm.errors.get('start_date') }}
                            </span>
                        </div>

                        <div class="form-group sub-notes"
                             :class="{'has-error': addNewTaskForm.errors.has('sub_message')}">
                            <label for="sub_message">Details For Sub To See</label>
                            <textarea class="form-control" id="sub_message" name="sub_message"
                                      v-model="addNewTaskForm.sub_message">
                            </textarea>
                            <span class="help-block" v-show="addNewTaskForm.errors.has('sub_message')">
                                {{ addNewTaskForm.errors.get('sub_message') }}
                            </span>
                        </div>

                        <div class="form-group customer-notes"
                             :class="{'has-error': addNewTaskForm.errors.has('customer_message')}">
                            <label for="customer_message">Details For Customer To See</label>
                            <textarea class="form-control" id="customer_message" name="customer_message"
                                      v-model="addNewTaskForm.customer_message">
                            </textarea>
                            <span class="help-block" v-show="addNewTaskForm.errors.has('customer_message')">
                                {{ addNewTaskForm.errors.get('customer_message') }}
                            </span>
                        </div>

                        <!-- <div class="stripe-tag">
                            <h3>Use Stripe For Payment</h3>
                            <label class="switch">
                                <input type="checkbox" @click="toggleStripePaymentOption()">
                                <span class="slider round"></span>
                            </label>
                        </div> -->

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
          subTaskPrice: 0,
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
          qtyUnit: '',
          changePrice: false
        }),
        result: {
          resultReturned: false,
          standardCustomerTaskPrice: 0,
          standardSubTaskPrice: 0
        },
        priceChange: false,
        taskResults: [],
      }
    },
    methods: {
      formatPrice (price) {
        console.log (price)
        console.log (this.addNewTaskForm.taskPrice)
        Format.addDollarSign (this.addNewTaskForm, price);
        if (this.result.standardCustomerTaskPrice !== this.addNewTaskForm.taskPrice &&
          this.result.resultReturned === true
        ) {
          this.priceChange = true
        }
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
        this.addNewTaskForm.qtyUnit = result.qtyUnit;
        this.formatPrice ('taskPrice');
        this.formatPrice ('subTaskPrice');
        this.result.resultReturned = true
        this.result.standardCustomerTaskPrice = result.proposed_cust_price
        this.result.standardSubTaskPrice = result.proposed_sub_price
        this.clearTaskResults ()
      },
      clearTaskResults () {
        this.taskResults = [];
      },
      changePrice (value) {
        if (value === 'Yes') {
          this.addNewTaskForm.changePrice = true
        } else {
          this.addNewTaskForm.changePrice = false
        }
        this.priceChange = false
      },
      addNewTaskToBid () {
        GeneralContractor.addNewTaskToBid (this.bid, this.addNewTaskForm);
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

    .wrapper {
        display: grid;
        grid-template-columns: repeat(12, [col-start] 1fr);
        grid-column-gap: 7.5px;
    }

    .wrapper > * {
        grid-column: col-start / span 12;
    }

    @media (min-width: 300px) {

        .wrapper-task-name {
            grid-column: col-start / span 12;
            grid-row: 1;
        }

        .qty {
            grid-column: col-start  / span 4;
            grid-row: 2;
        }

        .qty-unit {
            grid-column: col-start 5 / span 8;
            grid-row: 2;
        }

        .customer-price {
            grid-column: col-start / span 6;
            grid-row: 3;

        }

        .sub-price {
            grid-column: col-start 7 / span 6;
            grid-row: 3;

        }

        .start-date-flag {
            grid-column: col-start / span 6;
            grid-row: 4;

        }

        .start-date {
            grid-column: col-start 7 / span 6;
            grid-row: 4;

        }

        .customer-notes {
            grid-column: col-start / span 12;
            grid-row: 5;

        }

        .sub-notes {
            grid-column: col-start / span 12;
            grid-row: 6;

        }

        .stripe-tag {
            grid-column: col-start / span 6;
            grid-row: 7;

        }

    }

    @media (min-width: 500px) {

        .wrapper-task-name {
            grid-column: col-start / span 8;
            grid-row: 1;
        }

        .qty {
            grid-column: col-start 9 / span 2;
            grid-row: 1;
        }

        .qty-unit {
            grid-column: col-start 11 / span 2;
            grid-row: 1;
        }

        .customer-price {
            grid-column: col-start / span 3;
            grid-row: 2;

        }

        .sub-price {
            grid-column: col-start 4 / span 3;
            grid-row: 2;

        }

        .start-date-flag {
            grid-column: col-start 7 / span 3;
            grid-row: 2;

        }

        .start-date {
            grid-column: col-start 10 / span 3;
            grid-row: 2;

        }

        .customer-notes {
            grid-column: col-start / span 6;
            grid-row: 3;

        }

        .sub-notes {
            grid-column: col-start 7 / span 6;
            grid-row: 3;

        }

        .stripe-tag {
            grid-column: col-start / span 6;
            grid-row: 4;

        }
    }

    .accepted-checkbox {

    }

    .qty-wrapper {
        display: grid;
        grid-template-columns: 33rem 7rem 12rem;
        grid-gap: 2rem;
        grid-auto-flow: column;
    }

    .price-wrapper {
        display: grid;
        grid-template-columns: 16rem 19rem;
        grid-gap: 2rem;
    }

    /*.task-name {*/
    /*grid-area: theTaskName*/
    /*}*/

    /*.qty {*/
    /*grid-area: qty*/
    /*}*/

    /*.qty-unit {*/
    /*grid-area: qtyUnit*/
    /*}*/

    /*.start-date-flag {*/
    /*grid-area: startDateFlag*/
    /*}*/

    /*.start-date {*/
    /*grid-area: startDate*/
    /*}*/

    /*.customer-price {*/
    /*grid-area: customerPrice*/
    /*}*/

    /*.sub-price {*/
    /*grid-area: subPrice*/
    /*}*/

    /*.customer-notes {*/
    /*grid-area: customerNotes*/
    /*}*/

    /*.sub-notes {*/
    /*grid-area: subNotes*/
    /*}*/

    /*.stripe-tag {*/
    /*grid-area: stripeTag*/
    /*}*/

    /*.add-task {*/
    /*grid-area: addTask*/
    /*}*/

    /*.wrapper {*/
    /*display: grid;*/
    /*grid-gap: 5px;*/
    /*grid-template-areas:*/
    /*"theTaskName"*/
    /*"qty"*/
    /*"qtyUnit"*/
    /*"customerPrice"*/
    /*"subPrice"*/
    /*"startDateFlag"*/
    /*"startDate"*/
    /*"customerNotes"*/
    /*"subNotes"*/
    /*"stripeTag";*/
    /*}*/

    /*@media (min-width: 300px) {*/
    /*.wrapper {*/
    /*grid-template-columns: 1fr 3fr;*/
    /*grid-template-areas:*/
    /*"theTaskName theTaskName"*/
    /*"qty qtyUnit"*/
    /*"customerPrice subPrice"*/
    /*"startDateFlag startDateFlag"*/
    /*"startDate startDate"*/
    /*"customerNotes customerNotes"*/
    /*"subNotes subNotes"*/
    /*"stripeTag";*/
    /*}*/
    /*}*/

</style>