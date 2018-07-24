<template>
    <!-- Modal -->
    <div class="modal fade m-b-lg" id="add-task-modal" tabindex="-1" role="dialog" aria-labelledby="add-task-modal"
         aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content styled">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add A New Task</h4>
                </div>
                <div class="modal-body">
                    <form role="form" class="wrapper">

                        <div class="flex m-t-2">
                            <div class="flex-1 m-r-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('taskName')}">
                                <label for="taskName">Task Description</label>

                                <input type="text" class="form-control" id="taskName" name="taskName" autofocus
                                       autocomplete="false"
                                       v-model="addNewTaskForm.taskName" v-on:keyup="getExistingTask">

                                <span class="help-block" v-show="addNewTaskForm.errors.has('taskName')">
                                 {{ addNewTaskForm.errors.get('taskName') }}
                                </span>
                                <div class="panel-footer" v-if="taskResults.length">
                                    <ul class="list-group">
                                        <button class="list-group-item" v-for="result in taskResults"
                                                v-bind:key="result.id"
                                                @click.prevent="fillTaskValues(result)">
                                            {{ result.name }}
                                        </button>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex-1 m-l-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('taskPrice')}">
                                <label for="custTaskPrice">Price</label>
                                <div class="flex items-center">
                                    <span class="dollarSign">$</span>
                                    <input type="text" class="form-control"
                                           id="custTaskPrice"
                                           name="taskPrice"
                                           autocomplete="text"
                                           v-model="addNewTaskForm.taskPrice" @blur="formatPrice($event.target.value)">
                                </div>
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
                        </div>

                        <div class="flex m-t-2">
                            <div class="flex-1 m-r-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('qty')}">
                                <label for="qty">Quantity</label>
                                <input type="number" class="form-control" min="1" id="qty"
                                       name="qty" required v-model="addNewTaskForm.qty">
                                <span class="help-block" v-show="addNewTaskForm.errors.has('qty')">
                                {{ addNewTaskForm.errors.get('qty') }}
                            </span>
                            </div>

                            <div class="flex-1 m-l-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('qtyUnit')}">
                                <label for="qtyUnit">Quanity Description</label>
                                <input type="text" class="form-control" min="1" id="qtyUnit"
                                       placeholder="ex. ft, sq. ft, etc."
                                       name="qtyUnit" v-model="addNewTaskForm.qtyUnit" @onblur="validateInput()">
                                <span :class="{ error: addNewTaskForm.hasQtyUnitError }" v-show="addNewTaskForm.hasQtyUnitError">{{ addNewTaskForm.qtyUnitErrorMessage }}</span>
                                <span class="help-block" v-show="addNewTaskForm.errors.has('qtyUnit')">
                                {{ addNewTaskForm.errors.get('qtyUnit') }}
                            </span>
                            </div>
                        </div>

                        <div class="flex m-t-2">
                            <div class="flex-1 m-r-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('subTaskPrice')}">
                                <label for="subTaskPrice">Subcontractor Price</label>
                                <div class="flex items-center">
                                    <span class="dollarSign">$</span>
                                    <input type="text"
                                           autocomplete="text"
                                           class="form-control" id="subTaskPrice" name="subTaskPrice"
                                           v-model="addNewTaskForm.subTaskPrice" @blur="formatPrice('subTaskPrice')">
                                </div>
                                <span class="help-block" v-show="addNewTaskForm.errors.has('subTaskPrice')">
                                {{ addNewTaskForm.errors.get('subTaskPrice') }}
                            </span>
                            </div>

                            <div class="flex-1"
                                 :class="{'has-error': addNewTaskForm.errors.has('start_date')}">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required
                                       v-model="addNewTaskForm.start_date">
                                <span class="help-block" v-show="addNewTaskForm.errors.has('start_date')">
                                {{ addNewTaskForm.errors.get('start_date') }}
                            </span>
                            </div>
                        </div>

                        <div class="flex flex-col m-t-4">
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
          taskId: -1,  // if -1 then the task did not come from the drop down
          taskExists: '',
          jobId: this.bid.id,
          subTaskPrice: 0.0,
          taskPrice: 0.0,
          taskName: '',
          contractorId: '',
          area: this.bid.city,
          start_date: '',
          start_when_accepted: true,
          // sub_sets_own_price_for_job: true,
          useStripe: false,
          sub_message: '',
          customer_message: '',
          qty: 1,
          qtyUnit: '',
          changePrice: false,
          qtyUnitErrorMessage: '',
          hasQtyUnitError: false
        }),
        result: {
          resultReturned: false,
          standardCustomerTaskPrice: 0.0,
          standardSubTaskPrice: 0.0
        },
        priceChange: false,
        taskResults: [],
      }
    },
    methods: {
      validateInput () {
        // debugger;
        if (this.addNewTaskForm.qtyUnit !== '' && !isNaN (this.addNewTaskForm.qtyUnit)) {
          this.addNewTaskForm.qtyUnitErrorMessage = 'numbers not allowed';
          this.addNewTaskForm.hasQtyUnitError = true;
        } else {
          this.addNewTaskForm.qtyUnitErrorMessage = '';
          this.addNewTaskForm.hasQtyUnitError = false;
        }
      },
      formatPrice (price) {

        // debugger

        // want to add a $ to the number
        // Format.addDollarSign (this.addNewTaskForm, price);

        // send flag that the price has been changed
        if (this.result.standardCustomerTaskPrice !== price &&
          this.result.resultReturned === true
        ) {
          this.priceChange = true
        }

        //
        //
        // console.log (price)  //
        // console.log (this.addNewTaskForm.taskPrice)
        //
        // let strippedTaskPrice = this.strippedTaskPrice (this.addNewTaskForm.taskPrice);
        //
        //
        // console.log (strippedTaskPrice)
        //
        //
        // console.log (this.result.standardCustomerTaskPrice)


      },
      strippedTaskPrice (taskPrice) {
        if (taskPrice.charAt (0) === '$') {
          return parseInt (taskPrice.substr (1));
        } else {
          return parseInt (taskPrice);
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
      fillTaskValues (result) {  // this method fills values of the form when a drop down item is selected  x
        console.log (result)
        this.taskExists = true
        this.addNewTaskForm.taskId = result.id;
        this.addNewTaskForm.taskName = result.name;
        this.addNewTaskForm.sub_message = result.sub_instructions;
        this.addNewTaskForm.customer_message = result.customer_instructions;
        this.addNewTaskForm.taskPrice = result.proposed_cust_price;
        if (result.proposed_cust_price === null) {
          this.addNewTaskForm.taskPrice = 0;
        } else {
          this.addNewTaskForm.taskPrice = result.proposed_cust_price;
        }
        if (result.proposed_sub_price === null) {
          this.addNewTaskForm.subTaskPrice = 0;
        } else {
          this.addNewTaskForm.subTaskPrice = result.proposed_sub_price;
        }
        if (result.qtyUnit !== null) {
          this.addNewTaskForm.qtyUnit = result.qtyUnit;
        }
        this.formatPrice ('taskPrice');
        this.formatPrice ('subTaskPrice');
        this.result.resultReturned = true
        this.result.standardCustomerTaskPrice = result.proposed_cust_price
        this.result.standardSubTaskPrice = result.proposed_sub_price
        this.clearTaskResults ()
        this.priceChange = false
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
        if(!this.addNewTaskForm.hasQtyUnitError){
          GeneralContractor.addNewTaskToBid (this.bid, this.addNewTaskForm);
          this.clearTaskResults ();
        }
      },
      // // showStripeToggle (jobTask) {
      // //   return User.isAssignedToMe (jobTask);
      // // },
      toggleStripePaymentOption () {
        this.addNewTaskForm.useStripe = !this.addNewTaskForm.useStripe
      },
    },
    mounted: function () {
    }
  }
</script>

<style scoped>

    .error {
        color: red;
        font-size: 12pt;
        font-weight: 900;
    }

    /*.styled {*/
    /*margin-top: 10rem;*/
    /*margin-bottom: 10rem;*/
    /*}*/

    /*.wrapper {*/
    /*display: grid;*/
    /*grid-template-columns: repeat(12, [col-start] 1fr);*/
    /*grid-column-gap: 7.5px;*/
    /*}*/

    /*.wrapper > * {*/
    /*grid-column: col-start / span 12;*/
    /*}*/

    /*@media (min-width: 300px) {*/

    /*.wrapper-task-name {*/
    /*grid-column: col-start / span 12;*/
    /*grid-row: 1;*/
    /*}*/

    /*.qty {*/
    /*grid-column: col-start  / span 4;*/
    /*grid-row: 2;*/
    /*}*/

    /*.qty-unit {*/
    /*grid-column: col-start 5 / span 8;*/
    /*grid-row: 2;*/
    /*}*/

    /*.customer-price {*/
    /*grid-column: col-start / span 12;*/
    /*grid-row: 3;*/

    /*}*/

    /*.sub_sets_own_price_for_job {*/
    /*grid-column: col-start 0 / span 6;*/
    /*grid-row: 4;*/

    /*}*/

    /*.sub-price {*/
    /*grid-column: col-start 7 / span 6;*/
    /*grid-row: 4;*/

    /*}*/

    /*.start-date-flag {*/
    /*grid-column: col-start / span 6;*/
    /*grid-row: 5;*/

    /*}*/

    /*.start-date {*/
    /*grid-column: col-start 7 / span 6;*/
    /*grid-row: 5;*/

    /*}*/

    /*.customer-notes {*/
    /*grid-column: col-start / span 12;*/
    /*grid-row: 6;*/

    /*}*/

    /*.sub-notes {*/
    /*grid-column: col-start / span 12;*/
    /*grid-row: 7;*/

    /*}*/

    /*.stripe-tag {*/
    /*grid-column: col-start / span 6;*/
    /*grid-row: 8;*/

    /*}*/

    /*}*/

    /*@media (min-width: 500px) {*/

    /*.wrapper-task-name {*/
    /*grid-column: col-start / span 8;*/
    /*grid-row: 1;*/
    /*}*/

    /*.qty {*/
    /*grid-column: col-start 9 / span 2;*/
    /*grid-row: 1;*/
    /*}*/

    /*.qty-unit {*/
    /*grid-column: col-start 11 / span 2;*/
    /*grid-row: 1;*/
    /*}*/

    /*.customer-price {*/
    /*grid-column: col-start / span 6;*/
    /*grid-row: 2;*/

    /*}*/

    /*.sub_sets_own_price_for_job {*/
    /*grid-column: col-start 0 / span 6;*/
    /*grid-row: 3;*/
    /*}*/

    /*.sub-price {*/
    /*grid-column: col-start 7 / span 6;*/
    /*grid-row: 3;*/

    /*}*/

    /*.start-date-flag {*/
    /*grid-column: col-start  / span 6;*/
    /*grid-row: 4;*/
    /*}*/

    /*.start-date {*/
    /*grid-column: col-start 7 / span 6;*/
    /*grid-row: 4;*/

    /*}*/

    /*.customer-notes {*/
    /*grid-column: col-start / span 6;*/
    /*grid-row: 5;*/

    /*}*/

    /*.sub-notes {*/
    /*grid-column: col-start 7 / span 6;*/
    /*grid-row: 5;*/

    /*}*/

    /*.stripe-tag {*/
    /*grid-column: col-start / span 6;*/
    /*grid-row: 6;*/

    /*}*/
    /*}*/

    /*.accepted-checkbox {*/

    /*}*/

    /*.qty-wrapper {*/
    /*display: grid;*/
    /*grid-template-columns: 33rem 7rem 12rem;*/
    /*grid-gap: 2rem;*/
    /*grid-auto-flow: column;*/
    /*}*/

    /*.price-wrapper {*/
    /*display: grid;*/
    /*grid-template-columns: 16rem 19rem;*/
    /*grid-gap: 2rem;*/
    /*}*/

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