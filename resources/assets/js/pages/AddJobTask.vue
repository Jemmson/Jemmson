<template>
    <!-- Modal -->
    <div class="container" id="top" v-if="isContractor()">
        <v-card>

            <v-card-title class="w-break">Add New Task</v-card-title>
            <v-card-subtitle>This adds a new task to the job so you can sub out a portion of the job.</v-card-subtitle>

            <v-card-title ref="errorMessage"
                          class="text-center error-lg" v-show="errors.general.errorExists">
                {{ errors.general.message }}
            </v-card-title>

            <v-card-text>
                <v-combobox
                        label="Task Description *"
                        id="taskNameComboBox"
                        v-model="selected"
                        :search-input.sync="search"
                        :rules="[
                           taskNameExistsRule()
                       ]"
                        :items="comboResults"
                        :value="selected">
                </v-combobox>

                <div class="flex justify-content-between">
                    <v-text-field
                            @focus="clearTaskPrice()"
                            type="text"
                            v-model="addNewTaskForm.taskPrice"
                            class="input-margins"
                            id="custTaskPrice"
                            label="Input Bid Price Here"
                            @change="formatInput($event)"
                    ></v-text-field>

                    <v-switch
                            v-if="dropDownTaskWasSelected()"
                            style="margin-left: 5rem"
                            ref="updateBasePrice"
                            v-model="addNewTaskForm.updateBasePrice"
                            label="Update Base Price"
                    ></v-switch>
                </div>

                <v-text-field
                        v-model="addNewTaskForm.qty"
                        label="Quantity"
                        v-mask="'#########'"
                        id="qty"
                        name="qty"
                        :rules="[
                        quantityIsGreaterThanZeroRule()
                       ]"
                        ref="qty"></v-text-field>


                <v-text-field
                        v-model="addNewTaskForm.qtyUnit"
                        label="Quantity Unit"
                        placeholder="ex. ft, sq. ft, etc."
                        id="qtyUnit"
                        name="qtyUnit"
                        ref="qtyUnit"></v-text-field>

                <v-textarea
                        v-model="addNewTaskForm.customer_message"
                        id="customer_message"
                        name="customer_message"
                        auto-grow
                        clearable
                        clear-icon="mdi-cancel"
                        label="Instructions For The Customer"
                ></v-textarea>

                <v-textarea
                        v-model="addNewTaskForm.sub_message"
                        id="sub_message"
                        name="sub_message"
                        auto-grow
                        clearable
                        clear-icon="mdi-cancel"
                        label="Instructions For Sub Contractor"
                ></v-textarea>
            </v-card-text>

            <v-card-actions>
                <v-btn
                        class="w-40" a
                        color="red"
                        text
                        :disabled="addingTheTask"
                        @click.prevent="goBack()">
                    Back
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                        class="w-40"
                        color="primary"
                        text
                        @click.prevent="changeTask('Add')"
                        :disabled="addingTheTask"
                        ref="add_task"
                        :loading="addingTheTask"
                >Submit
                </v-btn>
            </v-card-actions>
        </v-card>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <feedback
                page="AddJobTask"
        ></feedback>
    </div>
</template>

<script>

    import IconHeader from '../components/shared/IconHeader'
    import Card from '../components/shared/Card'
    import Feedback from '../components/shared/Feedback'
    import Currency from "../components/mixins/Currency";

    export default {
        name: 'AddJobTask',
        components: {
            Card,
            Feedback,
            IconHeader
        },
        mixins: [Currency],
        beforeRouteUpdate(to, from, next) {
            // this.goToTop();
        },
        data() {
            return {
                formattedPrice: '',
                addingTheTask: false,
                overlay: false,
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
                    taskPrice: '$ 0.00',
                    taskName: '',
                    trackQtyOnHand: true,
                    type: 'Inventory',
                    updateBasePrice: false,
                    updateTask: false,
                    useStripe: false
                }),
                comboResults: [],
                checked: 'checked',
                search: null,
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
                taskPriceAssigned: false,
                valueChanged: false,
                dropdownSelected: false,
                nameExistsInDB: false,
                nameChanged: false,
                showTaskResults: false,
                taskExistsInJob: false,
                currentTasks: [],
                selected: null,
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
        watch: {
            search(val) {
                if (val) {
                    let tasks = this.getExistingTask()
                    if (tasks === undefined) {
                        this.addNewTaskForm.taskName = val
                    }
                }
            },
            selected(val) {
                if (val) {
                    const result = this.getComboResult(val)
                    this.fillTaskValues(result)
                }
            }
        },
        computed: {
            newTask() {
                return this.addNewTaskForm.taskName !== this.result.taskName
            }
        },
        methods: {

            clearTaskPrice(){
              if (this.addNewTaskForm.taskPrice === '$ 0.00') {
                  this.addNewTaskForm.taskPrice = '';
              }
            },

            dropDownTaskWasSelected() {
                return this.selected !== null
                    && this.search === this.selected.text
            },

            formatInput(input) {
                if (typeof input === 'string') {
                    const numLength = input.length
                    let pricef = ''
                    if (numLength < 3) {
                        pricef = '.' + input
                        this.formattedBidPrice = pricef
                    } else if (numLength > 2) {
                        let price = ''
                        for (let i = 0; i < numLength - 2; i++) {
                            price = price + input[i]
                        }
                        pricef = price + '.' + input[numLength - 2] + input[numLength - 1]
                        this.formattedBidPrice = pricef
                    }
                    return pricef
                } else if (typeof input === 'number') {
                    let bidPrice = input / 100
                    this.formattedBidPrice = bidPrice
                    return bidPrice
                }
            },


            setFormData(result) {
                this.addNewTaskForm.taskName = result.name
            },

            getComboResult(selected) {
                for (let i = 0; i < this.taskResults.length; i++) {
                    if (selected.value === this.taskResults[i].id) {
                        return this.taskResults[i]
                    }
                }
            },

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
            transformDataForComboBox(data) {
                let tasks = []
                for (let i = 0; i < data.length; i++) {
                    tasks.push(
                        {
                            text: data[i].name,
                            value: data[i].id
                        }
                    )
                }
                return tasks
            },
            getExistingTask(message) {
                this.taskResults = []
                // this.submitted = false

                // debugger;
                axios.post('/search/task', {
                    taskname: this.addNewTaskForm.taskName,
                    jobId: this.bid.id
                }).then(response => {
                    // debugger;
                    if (response.data.length > 0) {
                        // let filteredResults = this.filterResultsSoOnlyTasksNotCurrentlyInJobAreInDropdown(response.data)
                        this.comboResults = this.transformDataForComboBox(response.data);
                        this.currentTasks = response.data;
                        this.taskResults = response.data;
                        this.showTaskResults = true;
                    } else {
                        // if there are no results returned then the task results array should be empty and
                        // the name does not exist in the database
                        this.taskResults = []
                        this.nameExistsInDB = false
                    }
                })

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
                if (result) {
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
                        this.addNewTaskForm.taskPrice = "$ 0.00"
                        this.result.standardCustomerTaskPrice = 0
                    } else {
                        this.addNewTaskForm.taskPrice = "$ " + result.proposed_cust_price
                        this.result.standardCustomerTaskPrice = result.proposed_cust_price
                        this.taskPriceAssigned = true
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

                    // // Sub price
                    // if (result.proposed_sub_price === null) {
                    //     this.addNewTaskForm.subTaskPrice = 0
                    //     this.result.standardSubTaskPrice = 0
                    // } else {
                    //     this.addNewTaskForm.subTaskPrice = result.proposed_sub_price
                    //     this.result.standardSubTaskPrice = result.proposed_sub_price
                    // }

                    // Sub Instructions
                    if (result.sub_instructions === null) {
                        this.addNewTaskForm.sub_message = ''
                        this.result.sub_instructions = ''
                    } else {
                        this.addNewTaskForm.sub_message = result.sub_instructions
                        this.result.sub_instructions = result.sub_instructions
                    }

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
                }
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
                // this.goToTop();
            },

            goToTop() {
                window.location.href = '#top'
            },

            changeTask(message) {
                let taskIsNewOrNeedsToBeAdded = {
                    'New': true,
                    'Add': true
                }
                taskIsNewOrNeedsToBeAdded[message] ? this.createNewDontUpdate() : this.addingTheTask = false;
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

                } else {
                    this.errors.general.errorExists = true
                }
            },
            async addNewTask() {
                if (
                    this.quantityIsGreaterThanZero()
                    && this.taskNameExists()
                    && this.taskPriceIsNotEmpty()
                ) {
                    if (this.selected.text) {
                        this.addNewTaskForm.taskName = this.selected.text
                    } else {
                        this.addNewTaskForm.taskName = this.selected
                    }
                    this.overlay = true;
                    // TODO:: I want task submitted variable to be true after the addNewTaskToBid method is called
                    try {
                        this.addTask = true;
                        this.addingTheTask = true;
                        await GeneralContractor.addNewTaskToBid(this.bid, this.addNewTaskForm);
                        this.taskSubmitted = true;
                        this.overlay = false;
                        this.setDefaultStartDate();
                        this.goBack();
                    } catch (error) {
                        console.log(error);
                        this.overlay = false;
                        this.addingTheTask = false;
                    }
                    this.addTask = false;
                }
            },
            taskNameExists() {
                return (this.selected !== null && this.selected.length > 0) || (this.selected && this.selected.text && this.selected.text.length > 0);
            },
            taskPriceIsNotEmpty() {
                return this.addNewTaskForm.taskPrice.toString().length > 0
            },
            quantityIsGreaterThanZeroRule() {
                return this.quantityIsGreaterThanZero() || 'Quantity Must Be Greater Than Zero'
            },
            taskNameExistsRule() {
                return this.taskNameExists() || 'Must Have A Task Name'
            },
            taskPriceIsNotEmptyRule() {
                return this.taskPriceIsNotEmpty() || 'Task Price Cannot Be Empty'
            },
            quantityIsGreaterThanZero() {
                if (
                    this.addNewTaskForm.qty < 1
                    || this.addNewTaskForm.qty === '0'
                    || this.addNewTaskForm.qty === ''
                ) {
                    return false
                } else {
                    return true
                }
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