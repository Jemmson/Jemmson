<template>
    <!-- Modal -->
    <div class="container" id="top" v-if="isContractor()">
        <icon-header icon="tasks" mainHeader="Add New Task"
                     :subHeader="'This adds a new task to the job so you can sub out a portion of the job.'">
        </icon-header>
        <card class="mb-4">

            <form role="form" class="wrapper">
                <h1 class="text-center error-lg" v-show="errors.general.errorExists">
                    {{ errors.general.message }}
                </h1>
                <!--Task Name-->
                <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('taskName')}">
                    <label for="taskName">Task Description *</label>

                    <input type="text"
                           class="form-control bat-input mb-1"
                           id="taskName" name="taskName"
                           autofocus
                           v-model="addNewTaskForm.taskName" autocomplete="off"
                           @blur="checkIfNameExistsInDB($event.target.value)"
                           @focus="checkIfNameExistsInDB($event.target.value)"
                           @keyup="getExistingTask($event.target.value)">

                    <span class="help-block" v-show="addNewTaskForm.errors.has('taskName')">
                            {{ addNewTaskForm.errors.get('taskName') }}
                        </span>
                    <div class="flex flex-col" v-if="taskResults.length && showTaskResults">
                        <v-btn
                                class="w-full m-15"
                                color="primary"
                                v-for="result in taskResults"
                                v-bind:key="result.id" @click.prevent="fillTaskValues(result)">
                            {{ result.name }}
                        </v-btn>
                    </div>
                </div>

                <div
                        v-if="taskExistsInJob"
                        style="color: red; font-size: 10pt;"
                        class="uppercase">
                    This Task already exists for this job
                </div>

                <div class="form-row">

                    <!--Task Price-->
                    <div class="form-group col-6" :class="{'has-error': addNewTaskForm.errors.has('taskPrice')}">
                        <label for="custTaskPrice">Price</label>
                        <div class="flex items-center">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input
                                    @blur="verifyInputIsANumber($event.target.value, 'price')"
                                    @focus="hideTaskResults"
                                    @keyup="checkIfPriceChanged($event.target.value)"
                                    autocomplete="text"
                                    class="form-control bat-input"
                                    :class="(errors.subPriceTooHigh.exists || errors.notANumber.price) ? 'box-error': ''"
                                    :disabled="taskExistsInJob"
                                    id="custTaskPrice" name="taskPrice"
                                    ref="task_price"
                                    type="text"
                                    v-model="addNewTaskForm.taskPrice"
                            >
                        </div>
                        <span :class="{ error: errors.notANumber.price }" v-show="errors.notANumber.price">Customer
                            Price
                            {{ errors.notANumber.message }}
                        </span>
                        <span class="help-block" v-show="addNewTaskForm.errors.has('taskPrice')">
                            {{ addNewTaskForm.errors.get('taskPrice') }}
                        </span>
                    </div>


                    <!--Quantity-->
                    <div class="form-group col-6" :class="{'has-error': addNewTaskForm.errors.has('qty')}">
                        <label for="qty">Quantity</label>
                        <input type="number" class="form-control bat-input" min="1" id="qty" name="qty" required
                               :disabled="taskExistsInJob" @focus="hideTaskResults"
                               @blur="verifyInputIsANumber($event.target.value, 'quantity')"
                               v-model="addNewTaskForm.qty">
                        <span :class="{ error: errors.notANumber.quantity }"
                              v-show="errors.notANumber.quantity">Quantity
                            {{ errors.notANumber.message }}
                        </span>
                        <span class="help-block" v-show="addNewTaskForm.errors.has('qty')">
                            {{ addNewTaskForm.errors.get('qty') }}
                        </span>
                    </div>
                </div>


                <!--Quantity Unit-->
                <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('qtyUnit')}">
                    <label for="qtyUnit">Quantity Description</label>
                    <input type="text" class="form-control bat-input" min="1" id="qtyUnit"
                           placeholder="ex. ft, sq. ft, etc." name="qtyUnit" v-model="addNewTaskForm.qtyUnit"
                           :disabled="taskExistsInJob" @blur="validateInput()" @focus="hideTaskResults"
                           @keyup="checkIfQuantityUnitHasChanged($event.target.value)">
                    <span :class="{ error: addNewTaskForm.hasQtyUnitError }"
                          v-show="addNewTaskForm.hasQtyUnitError">{{ addNewTaskForm.qtyUnitErrorMessage }}
                        </span>
                    <span class="help-block" v-show="addNewTaskForm.errors.has('qtyUnit')">
                            {{ addNewTaskForm.errors.get('qtyUnit') }}
                        </span>
                </div>

                <!--Start Date-->
                <div class="form-group" :class="{'has-error': addNewTaskForm.errors.has('start_date')}">
                    <label for="start_date">Start Date</label>
                    <!--<span class="help-block" v-show="addNewTaskForm.errors.has('start_date')">-->
                    <!--{{ addNewTaskForm.errors.get('start_date') }}-->
                    <!--</span>-->
                    <input
                            @blur="checkDateIsTodayorLater($event.target.value)"
                            class="form-control bat-input"
                            :disabled="taskExistsInJob"
                            @focus="hideTaskResults"
                            id="start_date"
                            name="start_date"
                            ref="start_date"
                            required
                            type="date"
                            v-model="addNewTaskForm.start_date">

                    <span :class="{ error: addNewTaskForm.hasStartDateError }"
                          v-show="addNewTaskForm.hasStartDateError">{{ addNewTaskForm.startDateErrorMessage }}
                        </span>
                    <!--<span class="help-block" v-show="addNewTaskForm.errors.has('startDate')">-->
                    <!--{{ addNewTaskForm.errors.get('startDate') }}-->
                    <!--</span>-->

                </div>
                <!-- <div class="flex flex-col ml-2 items-center">
                          <label>Start Date <br> To Be Determined</label>
                          <input type="checkbox" :checked="checked">
                        </div> -->

                <!--                 <div v-if="user.contractor.accounting_software === 'quickbooks'">-->
                <div v-if="checkIfUserUsesQuickbooks()">
                    <!--                <div>-->
                    <!--<div class="m-auto">Income Account Ref</div>-->
                    <div class="form-group">
                        <input type="text" class="form-control bat-input "
                               v-model="addNewTaskForm.incomeAccountRef.name" placeholder="Income Account name">
                        <input type="text" class="form-control bat-input "
                               v-model="addNewTaskForm.incomeAccountRef.value" placeholder="Income Account value">
                    </div>

                    <!--<div class="m-auto">Expense Account Ref</div>-->
                    <div class="form-group">
                        <input type="text" class="form-control bat-input "
                               v-model="addNewTaskForm.expenseAccountRef.name" placeholder="Expense Account name">
                        <input type="text" class="form-control bat-input "
                               v-model="addNewTaskForm.expenseAccountRef.value" placeholder="Expense Account value">
                    </div>

                    <!--<div class="m-auto">Asset Account Ref</div>-->
                    <div class="form-group">
                        <input type="text" class="form-control bat-input "
                               v-model="addNewTaskForm.assetAccountRef.name" placeholder="Asset Account name">
                        <input type="text" class="form-control bat-input "
                               v-model="addNewTaskForm.assetAccountRef.value" placeholder="Asset Account value">
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control bat-input " v-model="addNewTaskForm.type"
                               placeholder="Type">
                        <input type="text" class="form-control bat-input " v-model="addNewTaskForm.invStartDate"
                               placeholder="Item Added Date">
                    </div>

                    <div class="form-group">
                        <div class="flex">
                            <div class="small mr-2">Track Qty On Hand</div>
                            <input type="checkbox" class="bat-input ml-2" :checked="addNewTaskForm.trackQtyOnHand"
                                   v-model="addNewTaskForm.trackQtyOnHand">
                        </div>
                        <input type="text" class="form-control w-1/4 bat-input ml-2" v-model="addNewTaskForm.qtyOnHand"
                               placeholder="Quantity On Hand">
                    </div>
                </div>


                <!--Customer Message-->
                <div class="form-group customer-notes"
                     :class="{'has-error': addNewTaskForm.errors.has('customer_message')}">
                    <label for="customer_message">Instructions For The Customer</label>
                    <textarea cols="5" rows="10" class="form-control ta-input" id="customer_message"
                              name="customer_message"
                              :disabled="taskExistsInJob" v-model="addNewTaskForm.customer_message"
                              @focus="hideTaskResults" @keyup="checkIfCustomerMessageHasChanged($event.target.value)">
                                </textarea>
                    <span class="help-block" v-show="addNewTaskForm.errors.has('customer_message')">
                                {{ addNewTaskForm.errors.get('customer_message') }}
                            </span>
                </div>

                <!--Sub Message-->
                <div class="form-group sub-notes"
                     :class="{'has-error': addNewTaskForm.errors.has('sub_message')}">
                    <label for="sub_message">Subcontractor Instructions</label>
                    <textarea cols="30" rows="10" class="form-control ta-input" id="sub_message" name="sub_message"
                              :disabled="taskExistsInJob" v-model="addNewTaskForm.sub_message"
                              @focus="hideTaskResults" @keyup="checkIfSubMessageHasChanged($event.target.value)">
                                </textarea>
                    <span class="help-block" v-show="addNewTaskForm.errors.has('sub_message')">
                                {{ addNewTaskForm.errors.get('sub_message') }}
                            </span>
                </div>
            </form>
            <!-- /end col-md-6 -->

            <div class="flex mb-1rem">
                <v-btn
                        class="w-40"
                        color="primary"
                        @click.prevent="goBack()">
                    Back
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                        class="w-40"
                        color="primary"
                        @click.prevent="changeTask('Add')"
                        :disabled="checkErrors()"
                        ref="add_task"
                        :loading="addTask"
                >Add Task
                </v-btn>
            </div>

        </card>
        <feedback
                page="AddJobTask"
        ></feedback>
    </div>
</template>

<script>

    import IconHeader from '../components/shared/IconHeader'
    import Card from '../components/shared/Card'
    import Feedback from '../components/shared/Feedback'

    export default {
        name: 'AddJobTask',
        components: {
            Card,
            Feedback,
            IconHeader
        },
        data() {
            return {
                addTask: false,
                addNewTaskForm: new SparkForm({
                    area: '',
                    assetAccountRef: {
                        value: '0',
                        name: 'Inventory Asset'
                    },
                    contractorId: '',
                    createNew: false,
                    customer_id: '',
                    customer_message: '',
                    expenseAccountRef: {
                        value: '0',
                        name: 'Cost of Goods Sold'
                    },
                    hasQtyUnitError: false,
                    hasStartDateError: false,
                    incomeAccountRef: {
                        value: '0',
                        name: 'Sales of Product Income'
                    },
                    item_id: '',
                    invStartDate: '',
                    jobId: '',
                    qty: 1,
                    qtyOnHand: '0',
                    qtyUnit: '',
                    qtyUnitErrorMessage: '',
                    start_date: '',
                    start_when_accepted: true,
                    startDateErrorMessage: '',
                    sub_message: '',
                    subTaskPrice: 0,
                    taskExists: '',
                    taskId: 0,  // if -1 then the task did not come from the drop down
                    taskPrice: 0,
                    taskName: '',
                    trackQtyOnHand: true,
                    type: 'Inventory',
                    updateTask: false,
                    useStripe: false
                }),
                checked: 'checked',
                result: {
                    resultReturned: false,
                    taskName: '',
                    standardCustomerTaskPrice: 0,
                    // quantity: 1,
                    quantityUnit: '',
                    standardSubTaskPrice: 0,
                    // start_date: '',
                    customer_instructions: '',
                    sub_instructions: ''
                },
                errors: {
                    subPriceTooHigh: {
                        exists: false,
                        message: 'Sub Price Can Not Be Higher Than Contractor Price'
                    },
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
                dropDownSelectedNameIsDifferent: false,
                taskResultsChange: false,
                taskResults: [],

                valueChanged: false,
                dropdownSelected: false,
                nameExistsInDB: false,
                nameChanged: false,
                showTaskResults: false,
                taskExistsInJob: false,
                currentTasks: [],

                priceChanged: false,
                quantityChanged: false,
                quantityUnitChanged: false,
                subTaskPriceChanged: false,
                startDateChanged: false,
                customerMessageChanged: false,
                subMessageChanged: false,
                startDateError: false,
                submitted: false,
                taskSubmitted: false,
                bid: {},
            }
        },
        computed: {
            newTask() {
                return this.addNewTaskForm.taskName !== this.result.taskName
            }
        },
        methods: {

            isContractor() {
                return Spark.state.user.usertype === 'contractor'
            },

            checkErrors() {

                if (this.addNewTaskForm.taskName === '') {
                    return true
                }

                if (this.addNewTaskForm.hasQtyUnitError) {
                    return true
                }

                if (this.addNewTaskForm.hasStartDateError) {
                    return true
                }

                if (this.errors.subPriceTooHigh.exists) {
                    return true
                }

                if (this.errors.notANumber.price) {
                    return true
                }

                return false

            },
            needsNewTask() {
                this.taskSubmitted = false
                this.clearTaskResults()
            },
            goBack() {
                // this.$router.go(-1)
                this.$router.push('/bid/' + this.bid.id)
            },
            checkIfUserUsesQuickbooks() {
                if (Spark) {
                    if (Spark.state) {
                        return Spark.state.user.contractor.accounting_software === 'quickbooks'
                    }
                }
            },
            // TODO:: move this to vuex action
            async getBid(id) {
                try {
                    const {
                        data
                        // } = await axios.get('/job/' + id);
                    } = await axios.get('/job/' + id)
                    // debugger
                    this.bid = data
                    this.$store.commit('setJob', data)
                    this.addNewTaskForm.customer_id = data.customer_id
                    this.addNewTaskForm.contractorId = data.contractor_id
                    this.addNewTaskForm.jobId = data.id

                } catch (error) {
                    console.log(error)
                    // debugger;
                    if (
                        error.message === 'Not Authorized to access this resource/api' ||
                        error.response.status === 403
                    ) {
                        this.$router.push('/bids')
                    }
                    // error = error.response.data;
                    // Vue.toasted.error(error.message);
                    Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.')
                }
            },

            checkDateIsTodayorLater(date) {
                let dateArray = GeneralContractor.checkDateIsTodayorLater(date, 'today')
                this.addNewTaskForm.startDateErrorMessage = dateArray[0]
                this.addNewTaskForm.hasStartDateError = dateArray[1]
            },
            validateInput() {
                if (this.addNewTaskForm.qtyUnit !== '' && !isNaN(this.addNewTaskForm.qtyUnit)) {
                    this.addNewTaskForm.qtyUnitErrorMessage = 'numbers not allowed'
                    this.addNewTaskForm.hasQtyUnitError = true
                } else {
                    this.addNewTaskForm.qtyUnitErrorMessage = ''
                    this.addNewTaskForm.hasQtyUnitError = false
                }
            },
            verifyInputIsANumber(input, target) {
                if (input !== '' && isNaN(input)) {
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
                this.checkErrors
            },
            strippedTaskPrice(taskPrice) {
                if (taskPrice.charAt(0) === '$') {
                    return parseInt(taskPrice.substr(1))
                } else {
                    return parseInt(taskPrice)
                }
            },
            checkIfPriceChanged(value) {
                this.checkThatSubPriceIsHigherThanContractorPrice()
                this.checkErrors

                if (this.dropdownSelected) {
                    value = parseInt(value)
                    if (this.result.standardCustomerTaskPrice !== value) {
                        this.priceChanged = true
                    } else {
                        this.priceChanged = false
                    }
                    this.checkIfValuesChanged()
                }
            },
            checkIfQuantityUnitHasChanged(value) {
                if (this.dropdownSelected) {
                    if (this.result.quantityUnit !== value) {
                        this.quantityUnitChanged = true
                    } else {
                        this.quantityUnitChanged = false
                    }
                    this.checkIfValuesChanged()
                }
            },
            checkThatSubPriceIsHigherThanContractorPrice() {
                this.errors.subPriceTooHigh.exists =
                    this.addNewTaskForm.taskPrice < this.addNewTaskForm.subTaskPrice
            },
            checkIfSubTaskPriceHasChanged(value) {
                this.checkThatSubPriceIsHigherThanContractorPrice()
                this.checkErrors
                if (this.dropdownSelected) {
                    value = parseInt(value)
                    if (this.result.standardSubTaskPrice !== value) {
                        this.subTaskPriceChanged = true
                    } else {
                        this.subTaskPriceChanged = false
                    }
                    this.checkIfValuesChanged()
                }
            },
            checkIfCustomerMessageHasChanged(value) {
                if (this.dropdownSelected) {
                    if (this.result.customer_instructions !== value) {
                        this.customerMessageChanged = true
                    } else {
                        this.customerMessageChanged = false
                    }
                    this.checkIfValuesChanged()
                }
            },
            checkIfSubMessageHasChanged(value) {
                if (this.dropdownSelected) {
                    if (this.result.sub_instructions !== value) {
                        this.subMessageChanged = true
                    } else {
                        this.subMessageChanged = false
                    }
                    this.checkIfValuesChanged()
                }
            },
            checkIfValuesChanged() {
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
                        this.valueChanged = false
                    } else {
                        this.valueChanged = true
                    }
                }
            },
            checkIfNameExistsInDB(taskName) {
                if (this.bid.job_tasks) {
                    // need to check if the task that is being typed is in the job already
                    // if it is in the job then disable all inputs and show the error
                    this.taskExistsInJob = false
                    // debugger;
                    for (let i = 0; i < this.bid.job_tasks.length; i++) {
                        if (this.bid.job_tasks[i].task.name === taskName) {
                            this.taskExistsInJob = true
                        }
                    }

                    // check if the name is in the database already so that I can show the
                    // correct buttons on the footer of the modal window
                    this.nameExistsInDB = false
                    // debugger;
                    for (let i = 0; i < this.currentTasks.length; i++) {
                        if (
                            this.currentTasks[i].name === taskName ||
                            this.currentTasks[i].name === this.addNewTaskForm.taskName
                        ) {
                            this.nameExistsInDB = true
                        }
                    }
                    this.showTaskResults = true
                }

            },
            getExistingTask(message) {
                this.taskResults = []
                this.submitted = false

                this.checkIfNameExistsInDB(this.addNewTaskForm.taskName)

                // debugger;
                if (this.addNewTaskForm.taskName.length > 1) {
                    axios.post('/search/task', {
                        taskname: this.addNewTaskForm.taskName,
                        jobId: this.bid.id
                    }).then(response => {
                        // debugger;
                        console.log(response.data)
                        if (response.data.length > 0) {
                            let filteredResults = this.filterResultsSoOnlyTasksNotCurrentlyInJobAreInDropdown(response.data)
                            this.currentTasks = filteredResults
                            this.taskResults = filteredResults
                            this.showTaskResults = true
                        } else {
                            // if there are no results returned then the task results array should be empty and
                            // the name does not exist in the database
                            this.taskResults = []
                            this.nameExistsInDB = false
                        }
                    })
                } else {
                    // if the task description box is empty then obviously the name does not exist in the database
                    this.nameExistsInDB = false
                }

            },
            filterResultsSoOnlyTasksNotCurrentlyInJobAreInDropdown(results) {
                let filteredResults = []
                let add = true
                for (let i = 0; i < results.length; i++) {
                    add = true
                    for (let j = 0; j < this.bid.job_tasks.length; j++) {
                        if (results[i].id === this.bid.job_tasks[j].task_id) {
                            add = false
                        }
                    }
                    if (add) {
                        filteredResults[filteredResults.length] = results[i]
                    }
                }
                return filteredResults
            },
            hideTaskResults() {
                this.showTaskResults = false
            },
            fillTaskValues(result) {  // this method fills values of the form when a drop down item is selected  x
                console.log(result)

                // since the user selected a drop down option then the name automatically exists in the database
                this.nameExistsInDB = true
                this.dropdownSelected = true
                this.taskExists = true
                this.result.resultReturned = true
                this.addNewTaskForm.taskId = result.id

                // Task Name
                this.addNewTaskForm.taskName = result.name
                this.result.taskName = result.name

                // Task Price
                if (result.proposed_cust_price === null) {
                    this.addNewTaskForm.taskPrice = 0
                    this.result.standardCustomerTaskPrice = 0
                } else {
                    this.addNewTaskForm.taskPrice = result.proposed_cust_price
                    this.result.standardCustomerTaskPrice = result.proposed_cust_price
                }

                this.addNewTaskForm.qty = 1
                this.result.quantity = 1

                // Quantity Unit
                if (result.qtyUnit !== null) {
                    this.addNewTaskForm.qtyUnit = result.qtyUnit
                    this.result.quantityUnit = result.qtyUnit
                } else {
                    this.addNewTaskForm.qtyUnit = ''
                    this.result.quantityUnit = ''
                }

                // Sub price
                if (result.proposed_sub_price === null) {
                    this.addNewTaskForm.subTaskPrice = 0
                    this.result.standardSubTaskPrice = 0
                } else {
                    this.addNewTaskForm.subTaskPrice = result.proposed_sub_price
                    this.result.standardSubTaskPrice = result.proposed_sub_price
                }

                // Sub Instructions
                if (result.sub_instructions === null) {
                    this.addNewTaskForm.sub_message = ''
                    this.result.sub_instructions = ''
                } else {
                    this.addNewTaskForm.sub_message = result.sub_instructions
                    this.result.sub_instructions = result.sub_instructions
                }

                // Sub price
                if (result.customer_instructions === null) {
                    this.addNewTaskForm.customer_message = ''
                    this.result.customer_instructions = ''
                } else {
                    this.addNewTaskForm.customer_message = result.customer_instructions
                    this.result.customer_instructions = result.customer_instructions
                }

                this.addNewTaskForm.customer_id = this.bid.customer_id

                this.addNewTaskForm.item_id = result.item_id

                this.priceChange = false
                this.messageChange = false
                this.taskResults = []
            },
            clearTaskResults() {
                this.taskResults = []
                this.addNewTaskForm.taskId = -1  // if -1 then the task did not come from the drop down
                this.addNewTaskForm.taskExists = ''
                this.addNewTaskForm.jobId = this.bid.id
                this.addNewTaskForm.subTaskPrice = 0
                this.addNewTaskForm.taskPrice = 0
                this.addNewTaskForm.taskName = ''
                this.addNewTaskForm.contractorId = ''
                this.addNewTaskForm.area = this.bid.city
                this.addNewTaskForm.start_date = ''
                this.addNewTaskForm.start_when_accepted = true
                this.addNewTaskForm.useStripe = false
                this.addNewTaskForm.sub_message = ''
                this.addNewTaskForm.customer_message = ''
                this.addNewTaskForm.qty = 1
                this.addNewTaskForm.qtyUnit = ''
                this.addNewTaskForm.updateTask = false
                this.addNewTaskForm.qtyUnitErrorMessage = ''
                this.addNewTaskForm.hasQtyUnitError = false
                this.result.resultReturned = false
                this.result.standardCustomerTaskPrice = 0
                this.result.sub_instructions = ''
                this.result.customer_instructions = ''
                this.result.taskName = ''
                this.result.standardSubTaskPrice = 0
                this.taskResultsChange = false
                this.taskResults = []
                this.valueChanged = false
                this.dropdownSelected = false
                this.nameExistsInDB = false
                this.submitted = true
                this.addNewTaskForm.hasQtyUnitError = false
                this.addNewTaskForm.hasStartDateError = false
                this.errors.notANumber.price = false
                this.errors.notANumber.quantity = false
                this.errors.notANumber.subTaskPrice = false
                this.errors.general.errorExists = false

            },

            createNewDontUpdate() {
                this.addNewTaskForm.updateTask = false
                this.addNewTaskForm.createNew = true
                this.addNewTaskToBid()
                this.goToTop();
            },

            goToTop() {
                window.location.href = '#top'
            },

            changeTask(message) {
                let taskIsNewOrNeedsToBeAdded = {
                    'New': true,
                    'Add': true
                }
                taskIsNewOrNeedsToBeAdded[message] ? this.createNewDontUpdate() : ''
            },

            checkForExistingTaskChanges() {
                if (this.result.resultReturned && (
                    this.result.standardCustomerTaskPrice !== this.addNewTaskForm.taskPrice ||
                    this.result.standardSubTaskPrice !== this.addNewTaskForm.subTaskPrice ||
                    this.result.sub_instructions !== this.addNewTaskForm.sub_message ||
                    this.result.customer_instructions !== this.addNewTaskForm.customer_message ||
                    this.result.taskName !== this.addNewTaskForm.taskName
                )) {
                    this.taskResultsChange = true
                } else {
                    this.addNewTaskToBid()
                }
            },
            addNewTaskToBid() {
                if (this.addNewTaskForm.subTaskPrice === '') {
                    this.addNewTaskForm.subTaskPrice = 0
                }
                if (
                    !this.addNewTaskForm.hasQtyUnitError &&
                    !this.addNewTaskForm.hasStartDateError &&
                    !this.errors.notANumber.price &&
                    !this.errors.notANumber.quantity &&
                    !this.errors.notANumber.subTaskPrice
                ) {
                    this.addNewTaskForm.customer_id = this.bid.customer_id
                    this.addNewTaskForm.jobId = this.bid.id
                    // this.addNewTaskForm.area = this.bid.location.city;

                    this.setUTCDate()

                    this.addNewTask()

                    this.setDefaultStartDate()

                    this.goBack()

                } else {
                    this.errors.general.errorExists = true
                }
            },
            async addNewTask() {
                // TODO:: I want task submitted varaiable to be true after the addNewTaskToBid method is caled
                try {
                    this.addTask = true;
                    await GeneralContractor.addNewTaskToBid(this.bid, this.addNewTaskForm)
                    this.taskSubmitted = true
                } catch (error) {
                    console.log(error)
                }
                this.addTask = false
            },
            toggleStripePaymentOption() {
                this.addNewTaskForm.useStripe = !this.addNewTaskForm.useStripe
            },
            getYear() {
                let date = new Date()
                return date.getFullYear()
            },
            getMonth() {
                let date = new Date()
                let month = date.getMonth()
                month = month + 1
                if (month < 10) {
                    month = '0' + month
                }
                return month
            },
            getDay() {
                let date = new Date()
                let day = date.getDate()
                if (day < 10) {
                    day = '0' + day
                }
                return day
            },
            setDefaultStartDate() {
                this.addNewTaskForm.start_date = this.getYear() + '-' + this.getMonth() + '-' + this.getDay()
            },
            todaysDate() {
                return this.getYear() + '-' + this.getMonth() + '-' + this.getDay()
            },
            utcDate() {
                return this.getUTCYear() + '-' + this.getUTCMonth() + '-' + this.getUTCDay()
            },
            getUTCYear() {
                let date = new Date()
                return date.getUTCFullYear()
            },
            getUTCMonth() {
                let date = new Date()
                let month = date.getUTCMonth()
                month = month + 1
                if (month < 10) {
                    month = '0' + month
                }
                return month
            },
            getUTCDay() {
                let date = new Date()
                let day = date.getUTCDate()
                if (day < 10) {
                    day = '0' + day
                }
                return day
            },
            setUTCDate() {
                if (this.addNewTaskForm.start_date === '') {
                    this.addNewTaskForm.start_date = this.utcDate()
                } else {
                    let selectedStartDate = this.addNewTaskForm.start_date
                    if (selectedStartDate === this.todaysDate()) {
                        this.addNewTaskForm.start_date = this.utcDate()
                    }
                }
            }
        },
        mounted: function () {
            this.setDefaultStartDate()
            if (this.$store) {
                this.getBid(this.$store.state.job.model.id)
            }
        }
    }
</script>

<style scoped>

    .bat-input {
        height: 2.25rem;
    }

    .ta-input {
        height: 5.25rem;
    }

    .error-lg {
        color: red;
        font-size: 20pt;
        font-weight: 900;
    }

</style>