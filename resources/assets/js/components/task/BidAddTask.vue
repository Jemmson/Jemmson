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
                        <h1 class="text-center error-lg" v-show="errors.general.errorExists">{{ errors.general.message }}</h1>
                        <div class="flex m-t-2">
                            <div class="flex-1 m-r-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('taskName')}">
                                <label for="taskName">Task Description *</label>

                                <input type="text" class="form-control" id="taskName" name="taskName" autofocus
                                       autocomplete="false"
                                       v-model="addNewTaskForm.taskName" @keyup="getExistingTask($event.target.value)">

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
                                           v-model="addNewTaskForm.taskPrice"
                                           @keyup="checkIfPriceChanged($event.target.value)"
                                           @blur="verifyInputIsANumber($event.target.value, 'price')"
                                    >
                                </div>
                                <span :class="{ error: errors.notANumber.price }"
                                      v-show="errors.notANumber.price">Customer Price {{ errors.notANumber.message }}
                                </span>
                                <span class="help-block" v-show="addNewTaskForm.errors.has('taskPrice')">
                                {{ addNewTaskForm.errors.get('taskPrice') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex m-t-2">
                            <div class="flex-1 m-r-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('qty')}">
                                <label for="qty">Quantity</label>
                                <input type="number"
                                       class="form-control"
                                       min="1"
                                       id="qty"
                                       name="qty"
                                       required
                                       @blur="verifyInputIsANumber($event.target.value, 'quantity')"
                                       v-model="addNewTaskForm.qty"
                                >
                                <span :class="{ error: errors.notANumber.quantity }"
                                      v-show="errors.notANumber.quantity">Quantity {{ errors.notANumber.message }}
                                </span>
                                <span class="help-block" v-show="addNewTaskForm.errors.has('qty')">
                                    {{ addNewTaskForm.errors.get('qty') }}
                                </span>
                            </div>

                            <div class="flex-1 m-l-4"
                                 :class="{'has-error': addNewTaskForm.errors.has('qtyUnit')}">
                                <label for="qtyUnit">Quanity Description</label>
                                <input type="text" class="form-control" min="1" id="qtyUnit"
                                       placeholder="ex. ft, sq. ft, etc."
                                       name="qtyUnit" v-model="addNewTaskForm.qtyUnit" @blur="validateInput()"
                                       @keyup="checkIfQuantityUnitHasChanged($event.target.value)"
                                >
                                <span :class="{ error: addNewTaskForm.hasQtyUnitError }"
                                      v-show="addNewTaskForm.hasQtyUnitError">{{ addNewTaskForm.qtyUnitErrorMessage }}
                                </span>
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
                                           v-model="addNewTaskForm.subTaskPrice"
                                           @keyup="checkIfSubTaskPriceHasChanged($event.target.value)"
                                           @blur="verifyInputIsANumber($event.target.value, 'subTaskPrice')"
                                    >
                                </div>
                                <span :class="{ error: errors.notANumber.subTaskPrice }"
                                      v-show="errors.notANumber.subTaskPrice">Sub Price {{ errors.notANumber.message }}
                                </span>
                                <span class="help-block" v-show="addNewTaskForm.errors.has('subTaskPrice')">
                                {{ addNewTaskForm.errors.get('subTaskPrice') }}
                            </span>
                            </div>

                            <div class="flex-1"
                                 :class="{'has-error': addNewTaskForm.errors.has('start_date')}">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required
                                       v-model="addNewTaskForm.start_date"
                                       @blur="checkDateIsTodayorLater($event.target.value)"
                                >
                                <!--<span class="help-block" v-show="addNewTaskForm.errors.has('start_date')">-->
                                <!--{{ addNewTaskForm.errors.get('start_date') }}-->
                            <!--</span>-->

                                <span :class="{ error: addNewTaskForm.hasStartDateError }"
                                      v-show="addNewTaskForm.hasStartDateError">{{ addNewTaskForm.startDateErrorMessage }}
                                </span>
                                <!--<span class="help-block" v-show="addNewTaskForm.errors.has('startDate')">-->
                                    <!--{{ addNewTaskForm.errors.get('startDate') }}-->
                                <!--</span>-->

                            </div>
                        </div>

                        <div class="flex flex-col m-t-4">
                            <div class="form-group customer-notes"
                                 :class="{'has-error': addNewTaskForm.errors.has('customer_message')}">
                                <label for="customer_message">Customer Instructions</label>
                                <textarea class="form-control" id="customer_message" name="customer_message"
                                          v-model="addNewTaskForm.customer_message"
                                          @keyup="checkIfCustomerMessageHasChanged($event.target.value)"
                                >
                                </textarea>
                                <span class="help-block" v-show="addNewTaskForm.errors.has('customer_message')">
                                    {{ addNewTaskForm.errors.get('customer_message') }}
                                </span>
                            </div>

                            <div class="form-group sub-notes"
                                 :class="{'has-error': addNewTaskForm.errors.has('sub_message')}">
                                <label for="sub_message">Subcontractor Instructions</label>
                                <textarea class="form-control" id="sub_message" name="sub_message"
                                          v-model="addNewTaskForm.sub_message"
                                          @keyup="checkIfSubMessageHasChanged($event.target.value)"
                                >
                                </textarea>
                                <span class="help-block" v-show="addNewTaskForm.errors.has('sub_message')">
                                {{ addNewTaskForm.errors.get('sub_message') }}
                                </span>
                            </div>
                        </div>
                    </form>
                    <!-- /end col-md-6 -->
                </div>
                <div class="modal-footer">
                    <!--<div class="form-group ">-->
                    <!--<button id="addTaskToInvoice" class="btn btn-success"-->
                    <!--@click.prevent="checkForExistingTaskChanges()">-->
                    <!--Add Task-->
                    <!--</button>-->
                    <!--</div>-->
                    <label for="taskResultsChange"></label>
                    <div id="taskResultsChange" class="flex justify-around">

                        <!-- show if
                                drop down is selected
                                any of the selected values change -->
                        <button v-if="(nameExistsInDB && !nameChanged) ||
                                    (dropdownSelected && valueChanged)" class="btn btn-green"
                                :disabled="checkErrors"
                                @click.prevent="changeTask('Update')">Update and Add
                        </button>

                        <!-- show if
                            drop down is selected and
                            any of the values have changed -->
                        <button v-if="(nameExistsInDB && !nameChanged) ||
                                    (dropdownSelected && valueChanged)" class="btn btn-green"
                                :disabled="checkErrors"
                                @click.prevent="changeTask('Ignore')">Ignore and Add
                        </button>

                        <!-- show if
                            drop down is selected
                            drop down name is changed -> gives option to create a new task based on an existing one -->
                        <button v-if="(!dropdownSelected && !nameExistsInDB && !submitted)"
                                class="btn btn-green" :disabled="checkErrors"
                                @click.prevent="changeTask('New')">
                            Create New and Add
                        </button>

                        <!-- show if
                            drop down selected but no values have changed or
                            drop down not selected -> if drop down not selected then create a new standard task -->
                        <button v-if="(dropdownSelected && !valueChanged && !nameChanged)"
                                class="btn btn-green" :disabled="checkErrors"
                                @click.prevent="changeTask('Add')">
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
          createNew: false,
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
          updateTask: false,
          qtyUnitErrorMessage: '',
          hasQtyUnitError: false,
          startDateErrorMessage: '',
          hasStartDateError: false
        }),
        result: {
          resultReturned: false,
          taskName: '',
          standardCustomerTaskPrice: 0.0,
          // quantity: 1,
          quantityUnit: '',
          standardSubTaskPrice: 0.0,
          // start_date: '',
          customer_instructions: '',
          sub_instructions: ''
        },
        errors: {
          general: {
            errorExists: false,
            message: 'Errors exist on page. Please review'
          },
          notANumber: {
            price: false,
            quantity: false,
            subTaskPrice: false,
            message: 'must be a number'
          }
        },
        dropdownSelected: false,
        dropDownSelectedNameIsDifferent: false,
        valueChanged: false,
        taskResultsChange: false,
        taskResults: [],
        nameExistsInDB: false,
        nameChanged: false,
        priceChanged: false,
        quantityChanged: false,
        quantityUnitChanged: false,
        subTaskPriceChanged: false,
        startDateChanged: false,
        customerMessageChanged: false,
        subMessageChanged: false,
        startDateError: false,
        submitted: false
      }
    },
    computed: {
      newTask () {
        return this.addNewTaskForm.taskName !== this.result.taskName;
      },
      checkErrors () {
        return this.addNewTaskForm.hasQtyUnitError || this.addNewTaskForm.hasStartDateError;
      }
    },
    methods: {
      checkDateIsTodayorLater (date) {
        let dateArray = GeneralContractor.checkDateIsTodayorLater(date, 'today');
        this.addNewTaskForm.startDateErrorMessage = dateArray[0];
        this.addNewTaskForm.hasStartDateError = dateArray[1];
      },
      validateInput () {
        if (this.addNewTaskForm.qtyUnit !== '' && !isNaN (this.addNewTaskForm.qtyUnit)) {
          this.addNewTaskForm.qtyUnitErrorMessage = 'numbers not allowed';
          this.addNewTaskForm.hasQtyUnitError = true;
        } else {
          this.addNewTaskForm.qtyUnitErrorMessage = '';
          this.addNewTaskForm.hasQtyUnitError = false;
        }
      },
      verifyInputIsANumber (input, target){
        if (input !== '' && isNaN (input)) {
          if (target === 'price') {
            this.errors.notANumber.price = true
          } else if (target === 'quantity') {
            this.errors.notANumber.quantity = true
          } else if (target === 'subTaskPrice') {
            this.errors.notANumber.subTaskPrice = true
          }
        } else {
          if (target === 'price') {
            this.errors.notANumber.price = false
          } else if (target === 'quantity') {
            this.errors.notANumber.quantity = false
          } else if (target === 'subTaskPrice') {
            this.errors.notANumber.subTaskPrice = false
          }
        }
      },
      strippedTaskPrice (taskPrice) {
        if (taskPrice.charAt (0) === '$') {
          return parseInt (taskPrice.substr (1));
        } else {
          return parseInt (taskPrice);
        }
      },
      checkIfPriceChanged (value) {
        if (this.dropdownSelected) {
          value = parseInt (value);
          if (this.result.standardCustomerTaskPrice !== value) {
            this.priceChanged = true;
          } else {
            this.priceChanged = false;
          }
          this.checkIfValuesChanged ();
        }
      },
      checkIfQuantityUnitHasChanged (value) {
        if (this.dropdownSelected) {
          if (this.result.quantityUnit !== value) {
            this.quantityUnitChanged = true;
          } else {
            this.quantityUnitChanged = false;
          }
          this.checkIfValuesChanged ();
        }
      },
      checkIfSubTaskPriceHasChanged (value) {
        if (this.dropdownSelected) {
          value = parseInt (value);
          if (this.result.standardSubTaskPrice !== value) {
            this.subTaskPriceChanged = true;
          } else {
            this.subTaskPriceChanged = false;
          }
          this.checkIfValuesChanged ();
        }
      },
      checkIfCustomerMessageHasChanged (value) {
        if (this.dropdownSelected) {
          if (this.result.customer_instructions !== value) {
            this.customerMessageChanged = true;
          } else {
            this.customerMessageChanged = false;
          }
          this.checkIfValuesChanged ();
        }
      },
      checkIfSubMessageHasChanged (value) {
        if (this.dropdownSelected) {
          if (this.result.sub_instructions !== value) {
            this.subMessageChanged = true;
          } else {
            this.subMessageChanged = false;
          }
          this.checkIfValuesChanged ();
        }
      },
      checkIfValuesChanged () {
        if (this.dropdownSelected) {
          if (
            !this.nameChanged &&
            !this.priceChanged &&
            !this.quantityChanged &&
            !this.quantityUnitChanged &&
            !this.subTaskPriceChanged &&
            !this.startDateChanged &&
            !this.customerMessageChanged &&
            !this.subMessageChanged
          ) {
            this.valueChanged = false;
          } else {
            this.valueChanged = true;
          }
        }
      },
      getExistingTask (message) {
        this.taskResults = [];
        this.submitted = false;
        if (this.addNewTaskForm.taskName.length > 1) {
          axios.post ('/api/search/task', {
            taskname: this.addNewTaskForm.taskName,
            jobId: this.bid.id
          }).then (response => {
            console.log (response.data)
            this.taskResults = response.data
            // debugger
            for (let i = 0; i < this.taskResults.length; i++) {
              if (this.taskResults[i].name === message) {
                this.nameExistsInDB = true;
                this.fillTaskValues(this.taskResults[i]);
              } else {
                this.nameExistsInDB = false;
                // this.clearTaskResults('notName');
              }
            }
            this.checkIfValuesChanged ();
          })
        }

        // if (this.dropdownSelected && (message !== this.result.taskName)) {
        //   this.nameChanged = true;
        // } else {
        //   this.nameChanged = false;
        // }

      },
      fillTaskValues (result) {  // this method fills values of the form when a drop down item is selected  x
        console.log (result)
        this.dropdownSelected = true;
        this.taskExists = true;
        this.result.resultReturned = true;

        this.addNewTaskForm.taskId = result.id;

        // Task Name
        this.addNewTaskForm.taskName = result.name;
        this.result.taskName = result.name;

        // Task Price
        if (result.proposed_cust_price === null) {
          this.addNewTaskForm.taskPrice = 0;
          this.result.standardCustomerTaskPrice = 0;
        } else {
          this.addNewTaskForm.taskPrice = result.proposed_cust_price;
          this.result.standardCustomerTaskPrice = result.proposed_cust_price;
        }

        this.addNewTaskForm.qty = 1;
        this.result.quantity = 1;

        // Quantity Unit
        if (result.qtyUnit !== null) {
          this.addNewTaskForm.qtyUnit = result.qtyUnit;
          this.result.quantityUnit = result.qtyUnit;
        } else {
          this.addNewTaskForm.qtyUnit = '';
          this.result.quantityUnit = '';
        }

        // Sub price
        if (result.proposed_sub_price === null) {
          this.addNewTaskForm.subTaskPrice = 0;
          this.result.standardSubTaskPrice = 0;
        } else {
          this.addNewTaskForm.subTaskPrice = result.proposed_sub_price;
          this.result.standardSubTaskPrice = result.proposed_sub_price;
        }

        // Sub Instructions
        if (result.sub_instructions === null) {
          this.addNewTaskForm.sub_message = '';
          this.result.sub_instructions = '';
        } else {
          this.addNewTaskForm.sub_message = result.sub_instructions;
          this.result.sub_instructions = result.sub_instructions;
        }

        // Sub price
        if (result.customer_instructions === null) {
          this.addNewTaskForm.customer_message = '';
          this.result.customer_instructions = '';
        } else {
          this.addNewTaskForm.customer_message = result.customer_instructions;
          this.result.customer_instructions = result.customer_instructions;
        }
        this.priceChange = false;
        this.messageChange = false;
        this.taskResults = [];
      },
      clearTaskResults () {
        this.taskResults = [];
        this.addNewTaskForm.taskId = -1;  // if -1 then the task did not come from the drop down
        this.addNewTaskForm.taskExists = '';
        this.addNewTaskForm.jobId = this.bid.id;
        this.addNewTaskForm.subTaskPrice = 0.0;
        this.addNewTaskForm.taskPrice = 0.0;
        this.addNewTaskForm.taskName = '';
        this.addNewTaskForm.contractorId = '';
        this.addNewTaskForm.area = this.bid.city;
        this.addNewTaskForm.start_date = '';
        this.addNewTaskForm.start_when_accepted = true;
        this.addNewTaskForm.useStripe = false;
        this.addNewTaskForm.sub_message = '';
        this.addNewTaskForm.customer_message = '';
        this.addNewTaskForm.qty = 1;
        this.addNewTaskForm.qtyUnit = '';
        this.addNewTaskForm.updateTask = false;
        this.addNewTaskForm.qtyUnitErrorMessage = '';
        this.addNewTaskForm.hasQtyUnitError = false;
        this.result.resultReturned = false;
        this.result.standardCustomerTaskPrice = 0.0;
        this.result.sub_instructions = '';
        this.result.customer_instructions = '';
        this.result.taskName = '';
        this.result.standardSubTaskPrice = 0.0;
        this.taskResultsChange = false;
        this.taskResults = [];
        this.valueChanged = false;
        this.dropdownSelected = false;
        this.nameExistsInDB = false;
        this.submitted = true;
        this.addNewTaskForm.hasQtyUnitError = false;
        this.addNewTaskForm.hasStartDateError = false;
        this.errors.notANumber.price = false;
        this.errors.notANumber.quantity = false;
        this.errors.notANumber.subTaskPrice = false;
        this.errors.general.errorExists = false;

      },
      changeTask (message) {
        if (message === 'Update') {
          this.addNewTaskForm.updateTask = true;
          this.addNewTaskForm.createNew = false;
        } else if (message === 'Ignore') {
          this.addNewTaskForm.updateTask = false;
          this.addNewTaskForm.createNew = false;
        } else if (message === 'New' || message === 'Add') {
          this.addNewTaskForm.updateTask = false;
          this.addNewTaskForm.createNew = true;
        }
        this.addNewTaskToBid ();
      },
      checkForExistingTaskChanges () {
        if (this.result.resultReturned && (
          this.result.standardCustomerTaskPrice !== this.addNewTaskForm.taskPrice ||
          this.result.standardSubTaskPrice !== this.addNewTaskForm.subTaskPrice ||
          this.result.sub_instructions !== this.addNewTaskForm.sub_message ||
          this.result.customer_instructions !== this.addNewTaskForm.customer_message ||
          this.result.taskName !== this.addNewTaskForm.taskName
        )) {
          this.taskResultsChange = true;
        } else {
          this.addNewTaskToBid ();
        }
      },
      addNewTaskToBid () {
        if (this.addNewTaskForm.subTaskPrice === ''){
          this.addNewTaskForm.subTaskPrice = 0.0
        }
        if (
          !this.addNewTaskForm.hasQtyUnitError &&
          !this.addNewTaskForm.hasStartDateError &&
          !this.errors.notANumber.price &&
          !this.errors.notANumber.quantity &&
          !this.errors.notANumber.subTaskPrice
        ) {
          GeneralContractor.addNewTaskToBid (this.bid, this.addNewTaskForm);
          // console.log (newTask);
          // debugger;
          this.clearTaskResults ();
        } else {
          this.errors.general.errorExists = true;
        }
      },
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

    .error-lg {
        color: red;
        font-size: 20pt;
        font-weight: 900;
    }

</style>