<template>
    <div class="currenttasksforjob">
        <!--<pre>{{ allTasks }}</pre>-->
        <!--<pre>{{ getUser }}</pre>-->

        <!--<pre>{{ showDetails[0].show }}</pre>-->
        <!--<pre>{{ showDetails[1].show }}</pre>-->
        <!--<pre>{{ showDetails[2].show }}</pre>-->
        <!--<pre>{{ showDetails[3].show }}</pre>-->
        <!--<pre>{{ showDetails[4].show }}</pre>-->
        <!--<pre>{{ taskId }}</pre>-->
        <!--<pre>{{ allTasks }}</pre>-->
        <!--<pre>{{ showDetails }}</pre>-->
        <!--<pre>{{ bids }}</pre>-->
        <!--<pre>{{ bidTasks }}</pre>-->
        <!--<pre>{{ this.emailInputPassed }}</pre>-->
        <!--<pre>{{ this.phoneInputPassed }}</pre>-->
        <!--<pre>{{ allTasksData }}</pre>-->
        <!--<pre>{{ task }}</pre>-->
        <!--<pre>{{ newTaskName }}</pre>-->
        <!--<pre>{{ showDetails }}</pre>-->
        <div class="joblist" v-if="getUser === 'customer'">
            <div class="container">
                <div v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                    <h1>Bid Is In Progress</h1>
                </div>
                <div v-else-if="jobStatus === 'Waiting For Customer Approval' || jobStatus === 'The Job Has Been Accepted'">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Task Name</th>
                            <th>Final Customer Price</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="task in allTasksData" :key="task.id">
                            <td>
                                {{ task.name }}
                            </td>
                            <td>
                                {{ task.pivot.cust_final_price }}
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-sm btn-primary button"
                                        @click="declineJob()">
                                    Decline Job
                                </button>
                                <button class="btn btn-sm btn-primary button"
                                        @click="acceptJob()">
                                    Accept Job
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="joblist" v-if="getUser === 'contractor'">
            <div class="container">
                <table class="table">
                    <thead>
                    <tr v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                        <th>Task Name</th>
                        <th>Final Customer Price</th>
                        <th>Final Sub Price</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <tr v-else>
                        <th>Task Name</th>
                        <th>Final Customer Price</th>
                        <th>Final Sub Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="task in allTasksData" :key="task.id">
                        <!--<td>{{ jobStatus }}</td>-->
                        <td v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                            <input type="text" :value="task.name"
                                   @blur="updateTaskName($event.target.value, task.id)">
                        </td>
                        <td v-else>
                            {{ task.name }}
                        </td>
                        <td v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                            <input type="text" :value="task.pivot.cust_final_price"
                                   @blur="updateCustomerPrice($event.target.value, task.id)">
                        </td>
                        <td v-else>
                            {{ task.pivot.cust_final_price }}
                        </td>
                        <td v-if="task.pivot.sub_final_price !== 0">{{ task.pivot.sub_final_price }}</td>
                        <td v-else>Pending</td>
                        <td v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                            <button @click="initiateSub(task.id, task.name)" class="button btn btn-sm btn-primary">
                                Initiate Bid For Sub
                            </button>
                        </td>
                        <td v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                            <button class="btn btn-sm btn-primary button"
                                    @click="showTheDetails(task.id)">
                                Details
                            </button>
                        </td>
                        <!--<td>-->
                        <!--<button class="btn btn-sm btn-primary button"-->
                        <!--@click="editTask(task.id)">-->
                        <!--Edit-->
                        <!--</button>-->
                        <!--</td>-->
                        <td v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                            <button class="btn btn-sm btn-primary button"
                                    @click="deleteTask(task.id)">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Totals</td>
                        <td>{{ customerTotal }}</td>
                        <td>{{ subTotal }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr v-if="jobStatus === 'Bid In Progress' || jobStatus === 'initiated'">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button class="btn btn-sm btn-primary button"
                                    @click="showNewTask = !showNewTask">
                                Add Task To Bid
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary button"
                                    @click="notifyCustomerOfFinishedBid()">
                                Notify Customer of Finished Bid
                            </button>
                        </td>
                        <td></td>
                    </tr>
                    <tr v-else-if="jobStatus === 'Bid Has Been Approved'">

                    </tr>
                    <tr v-show="showNewTask">
                        <td>
                            <div class="form-group">
                                <label
                                        for="taskName">Task Name</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="taskName"
                                        name="taskName"
                                        v-model="newTaskName"
                                        v-on:keyup="getExistingTask"
                                >
                                <div class="panel-footer" v-if="taskResults.length">
                                    <ul class="list-group">
                                        <button class="list-group-item" v-for="result in taskResults"
                                                :name="result.phone"
                                                @click="fillTaskPrice(result)">
                                            {{ result.name }}
                                        </button>
                                    </ul>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label
                                        for="custTaskPrice">Customer Task Price</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="custTaskPrice"
                                        name="taskPrice"
                                        v-model="taskPrice">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <label
                                        for="subTaskPrice">Sub Task Price</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="subTaskPrice"
                                        name="taskPrice"
                                        v-model="subTaskPrice">
                            </div>
                        </td>
                        <td>
                            <button
                                    style="margin-top: 27px"
                                    id="submitTask"
                                    class="btn btn-default btn-primary"
                                    v-on:click="addNewTask()">
                                Submit
                            </button>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
                <div class="alert-success" v-show="showNotificationSent">A notification was succesfully sent</div>
                <div class="alert-warning" v-show="taskAlreadyExistsWarning">
                    Task Already exists and was not added for this contractor <span
                        class="glyphicon glyphicon-remove-sign"
                        @click="hideTaskWarning()"></span>
                </div>
                <div class="alert-warning" v-show="possibleDuplicateUserAlert">
                    This Contractor May Already Exist in the Database. Please use the drop down to select the correct
                    name <span
                        class="glyphicon glyphicon-remove-sign"
                        @click="hideDuplicateUserWarning()"></span>
                </div>
                <div class="wrapper">
                    <div class="details" v-for="(task, index) in allTasksData">
                        <div v-show="showDetails[index].show">
                            <h3 class="text-center">Sub Details: {{ task.name }}</h3>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Sub</th>
                                    <th>Price</th>
                                    <th>Accept</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="table" v-for="bid in bidTasks" :key="bid.id" v-if="task.id === bid.task_id">
                                    <td>{{ bid.contractorName[0].name }}</td>
                                    <td>{{ bid.bid_price }}</td>
                                    <td>
                                        <button @click="acceptBid(bid.id, task.id, bid.bid_price)"
                                                class="button btn btn-sm btn-primary">Accept
                                        </button>
                                    </td>
                                    <td>
                                        <button @click="notify(bid.id)" class="button btn btn-sm btn-primary">Notify
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="initiateBid" v-show="initiateSubTask">
                        <div class="addBidTask">
                            <h3 class="text-center">Task: {{ taskName }}</h3>
                            <div class="form-group">
                                <label
                                        for="contractorName">Contractor Name *</label>
                                <span class="validationError" v-show="hasNameError">Please Enter A Name</span>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="contractorName"
                                        name="contractorName"
                                        :value="contractorName"
                                        :placeholder="contractorName"
                                        v-model="query"
                                        v-bind:class="{ 'text-danger': hasNameError }"
                                        required
                                        v-on:keyup="autoComplete"
                                        @blur="mouseLeave('notNow')"
                                >
                                <div class="panel-footer" v-if="results.length">
                                    <ul class="list-group">
                                        <button class="list-group-item" v-for="result in results" :name="result.phone"
                                                @click="fillFields(result)">
                                            {{ result.name }}
                                        </button>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                        for="phone">Phone *</label>
                                <span class="validationError"
                                      v-show="hasPhoneError">Please Enter A Valid Phone Number - xxx-xxx-xxxx</span>
                                <input
                                        type="tel"
                                        class="form-control"
                                        id="phone"
                                        name="phone"
                                        required
                                        v-bind:class="{ 'text-danger': hasPhoneError }"
                                        v-model="phone"
                                        @blur="mouseLeave('phone')">
                            </div>
                            <div class="form-group">
                                <label
                                        for="email">Email *</label>
                                <span class="validationError"
                                      v-show="hasEmailError">Please Enter A Valid Email Address</span>
                                <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        required
                                        v-bind:class="{ 'text-danger': hasEmailError }"
                                        v-model="email"
                                        @blur="mouseLeave('email')">
                            </div>
                            <div class="form-group">
                                <button @click="sendNotificationToSubForParticularTask()" class="btn btn-sm btn-primary"
                                        type="submit">Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import JemmDate from './JemmDate.vue'
  import axios from 'axios'

  export default {
    name: 'CurrentTasksForJob',
    components: {
      JemmDate
    },
    data () {
      return {
        allTasksData: '',
        bidTasks: '',
        contractorName: '',
        email: '',
        emailInputPassed: false,
        hasEmailError: false,
        hasNameError: false,
        hasPhoneError: false,
        initiateBidUrl: '/task/notify',
        initiateSubTask: false,
        isActive: true,
        name: '',
        nameInputPassed: false,
        newTaskName: '',
        newTask: {
          name: '',
          price: ''
        },
        phone: '',
        phoneInputPassed: false,
        possibleDuplicateUserAlert: false,
        query: '',
        results: [],
        selectedSubTaskPrice: '',
        selectedTaskName: '',
        selectedTaskPrice: '',
        showDetails: [],
        showNewTask: false,
        showNotificationSent: false,
        subTaskPrice: '',
        task: {},
        taskAlreadyExistsWarning: false,
        taskExists: false,
        taskId: '',
        taskName: '',
        taskPrice: '',
        taskResults: []
      }
    },
    mounted () {
      if (this.allTasksData === '') {
        this.allTasksData = this.allTasks
      }
      this.bidTasks = JSON.parse(this.bids)
      // console.log(typeof this.bidTasks)
      this.setUpShowDetailsArray()
      console.log('all tasks data')
      console.log(this.allTasks)
    },
    props: {
      allTasks: {
        type: Array
      },
      user: {
        type: String
      },
      jobid: {
        type: Number
      },
      jobStatus: {
        type: String
      },
      customerId: {
        type: Number
      },
      contractorId: {
        type: Number
      },
      bids: {
        type: String
      }
    },
    computed: {
      totalPrice () {
        let totalPrice = 0
        for (let i = 0; i < this.allTasks.length; i++) {
          totalPrice = totalPrice + this.allTasks[0].price
        }
        return totalPrice
      },
      getUser () {
        console.log(this.user)
        console.log(typeof this.user)
        return this.user
      },
      subFinalPrice (price) {
        if (price > 0) {
          return price
        } else {
          return 'pending'
        }
      },
      subTotal () {
        let atd = this.allTasksData;
        let total = 0;
        // debugger;
        for (let i = 0; i < atd.length; i++) {
          if (atd[i].pivot.sub_final_price !== 'Pending') {
            total = total + atd[i].pivot.sub_final_price
          }
        }
        return total
      },
      customerTotal () {
        let atd = this.allTasksData;
        let total = 0;
        // debugger;
        for (let i = 0; i < atd.length; i++) {
          total = total + parseInt(atd[i].pivot.cust_final_price)
        }
        return total
      }
    },
    methods: {
      addNewTask () {
        // I want the status to go from initiated to in progress when the first new task is added
        // I want each task to be added to the the tasks table
        // I want the task to associated to a job, customer, and contractor
        console.log('adding a new task method is being called')
        // I want to add the existing task to the job
        console.log(this.task)
        console.log(this.task.id)
        console.log(this.taskExists)
        console.log(this.jobid)
        console.log(this.taskPrice)
        console.log(this.newTaskName)
        console.log(this.contractorId)
        console.log(this.subTaskPrice)
        // debugger
        this.checkIfTaskExists()
        axios.post('/api/task/addTask', {
          taskId: this.task.id,
          taskExists: this.taskExists,
          jobId: this.jobid,
          subTaskPrice: this.subTaskPrice,
          taskPrice: this.taskPrice,
          taskName: this.newTaskName,
          contractorId: this.contractorId
        }).then(function (response) {
          console.log(this.allTasksData)
          console.log(response.data)
          this.allTasksData.push(response.data)
          this.setUpShowDetailsArray()
          this.clearTaskResults()
          console.log(this.allTasksData)
        }.bind(this))

      },
      clearTaskResults () {
        this.taskResults = ''
      },
      checkIfTaskExists () {
        if (this.selectedTaskName === this.newTaskName) {
          this.taskExists = true
        } else {
          this.taskExists = false
          this.task = {} // cant pass in a task if the task does not exist and I dont want to pass a prior selected task
        }
      },
      notifyCustomerOfFinishedBid () {
        axios.post('/api/task/finishedBidNotification', {
          jobId: this.jobid,
          customerId: this.customerId
        }).then(function (response) {
          console.log(response.data)
          this.updateAllTasksData(response.data.price, response.data.taskId)
        }.bind(this))
      },
      acceptTask (taskId) {
        axios.post('/api/task/acceptTask', {
          taskId: taskId,
          contractorId: this.contractorId,
          jobId: this.jobid
        }).then(function (response) {
          console.log(response.data)
//          this.updateAllTasksData(response.data.price, response.data.taskId)
        }.bind(this))
      },
      acceptJob () {
        axios.post('/api/task/acceptJob', {
//          taskId: taskId,
          contractorId: this.contractorId,
          jobId: this.jobid
        }).then(function (response) {
          console.log(response.data)
//          this.updateAllTasksData(response.data.price, response.data.taskId)
        }.bind(this))
      },
      declineJob () {
        axios.post('/api/task/declineJob', {
//          taskId: taskId,
          contractorId: this.contractorId,
          jobId: this.jobid
        }).then(function (response) {
          console.log(response.data)
//          this.updateAllTasksData(response.data.price, response.data.taskId)
        }.bind(this))
      },
      initiateSub (taskId, taskName) {
        // show the sub task
        this.showSubTask(taskId)
        this.taskName = taskName
        // set the task Id for the sub
      },
      setUpShowDetailsArray () {
        this.showDetails = []
        for (let i = 0; i < this.allTasksData.length; i++) {
          this.showDetails.push({tableIndex: this.allTasksData[i].id, show: false});
        }
      },
      hideTaskWarning () {
        this.taskAlreadyExistsWarning = false
      },
      hideDuplicateUserWarning () {
        this.possibleDuplicateUserAlert = false
      },
      acceptBid (bidId, taskId, price) {
        console.log('id: ' + bidId)
        console.log('task id: ' + taskId)
        axios.post('/api/task/accept', {
          bidId: bidId,
          jobId: this.jobid,
          taskId: taskId,
          price: price
        }).then(function (response) {
          console.log(response.data)
          this.updateAllTasksData(response.data.price, response.data.taskId)
        }.bind(this))

        // return true
      },
      updateAllTasksData (price, id) {
        let atd = this.allTasksData;
        // debugger;
        for (let i = 0; i < atd.length; i++) {
          if (atd[i].id === id) {
            atd[i].pivot.sub_final_price = price
          }
        }
      },
      updateAllTasksDataWithCustomerPrice (price, id) {
        let atd = this.allTasksData;
        // debugger;
        for (let i = 0; i < atd.length; i++) {
          if (atd[i].id === id) {
            atd[i].pivot.cust_final_price = price
          }
        }
      },
      notify (bidId) {
        console.log('id: ' + bidId)
        axios.post('/api/task/notifyAcceptedBid', {
          bidId: bidId
        }).then(function (response) {
          console.log(response.data)
          // this.updateAllTasksData(response.data.price, response.data.taskId)
        }.bind(this))
      },
      showTheDetails (index) {
//        debugger
        this.hideAllTables()
        for (let obj of this.showDetails) {
          if (obj.tableIndex === index) {
            obj.show = true
          }
        }
      },
      hideAllTables () {
        for (let obj of this.showDetails) {
          obj.show = false
        }
      },
      sendNotificationToSubForParticularTask () {
        // send ajax notification for sub task initiation
        console.log('sendNotificationToSubForParticularTask is being called')
        if (this.emailInputPassed && this.phoneInputPassed) {
          axios.post('/api/task/notify', {
            taskId: this.taskId,
            jobId: this.jobid,
            phone: this.phone,
            email: this.email,
            name: this.query
          }).then(function (response) {
            console.log(response.data)
            if (typeof response.data === 'string' && response.data !== 'success') {
              this.checkValidation(response.data)
            } else {
              // let responseObject = JSON.parse (response.data)
              console.log(typeof response.data)
              console.log(response.data[0].contractor_id)
              console.log(response.data[0].contractorName[0])
              console.log(response.data[0].contractorName[0].name)
              this.bidTasks = response.data
              this.display()
            }
//          debugger
            // display flash message was sent
          }.bind(this))
        }
      },
      checkValidation (responseData) {
        if (responseData === 'allFieldsAreEmpty') {
          console.log(responseData)
          this.hasEmailError = true
          this.hasPhoneError = true
        } else if (responseData === 'task already exists') {
          this.taskAlreadyExistsWarning = true
        } else if (responseData === 'emailIsEmpty') {
          console.log(responseData)
          this.hasEmailError = true
        } else if (responseData === 'phoneIsEmpty') {
          console.log(responseData)
          this.hasPhoneError = true
        } else if (responseData === 'user may already exist in database') {
          this.hasNameError = true
          this.possibleDuplicateUserAlert = true
          console.log(responseData)
        }
      },
      updateCustomerPrice (price, taskId) {
        console.log(price)
        if (price === '') {
          price = 0
        }
        console.log(price)
        axios.post('/api/task/updateCustomerPrice', {
          jobId: this.jobid,
          taskId: taskId,
          price: price
        }).then(function (response) {
          console.log(response.data)
          this.updateAllTasksDataWithCustomerPrice(response.data.price, response.data.taskId)
        }.bind(this))
      },
      updateTaskName (taskName, taskId) {
        console.log(taskName)
        axios.post('/api/task/updateTaskName', {
          jobId: this.jobid,
          taskId: taskId,
          taskName: taskName
        }).then(function (response) {
          console.log(response.data)
          this.updateTaskNameInAllTasksData(taskName, taskId)
        }.bind(this))
      },
      updateTaskNameInAllTasksData (taskName, taskId) {
        for (let i = 0; i < this.allTasksData.length; i++) {
          let obj = this.allTasksData[i];
          if (obj.id === taskId) {
            obj.name = taskName
          }
        }
      },
      mouseLeave (inputType) {
        if (inputType === 'name') {
          let nameRegex = '/^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$/'
          if (this.name.match(nameRegex)) {
            this.nameInputPassed = true
            this.hasNameError = false
          } else {
            this.nameInputPassed = false
            this.hasNameError = true
          }
        } else if (inputType === 'phone') {
          // this.manipulateThePhoneNumber ()
          let phoneRegex = new RegExp('(1-?)?(([2-9]\\d{2})|[2-9]\\d{2})-?[2-9]\\d{2}-?\\d{4}')
          if (this.phone.match(phoneRegex)) {
            this.phoneInputPassed = true
            this.hasPhoneError = false
          } else {
            this.phoneInputPassed = false
            this.hasPhoneError = true
          }
        } else if (inputType === 'email') {
          let emailRegex = '(?:[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\\])'
          if (this.email.match(emailRegex)) {
            this.emailInputPassed = true
            this.hasEmailError = false
          } else {
            this.emailInputPassed = false
            this.hasEmailError = true
          }
        }
      },
      display () {
//        debugger
        // hide the initiate task box
        this.initiateSubTask = false
        // show flash message for one second
        this.showNotificationSent = true
        setTimeout(function () {
          this.showNotificationSent = false
        }.bind(this), 3000)
      },
      autoComplete () {
        this.results = [];
        if (this.query.length > 2) {
          axios.get('/api/search', {
            params: {
              query: this.query
            }
          }).then(function (response) {
            console.log(response.data)
            this.results = response.data
          }.bind(this))
        }
      },
      editTask (taskId) {

      },
      deleteTask (taskId) {
        axios.post('/api/task/delete', {
          taskId: taskId,
          jobId: this.jobid
        }).then(function (response) {
          console.log(response.data)
          this.removeTaskFromAllTaskData(taskId)
        }.bind(this))
      },
      removeTaskFromAllTaskData(taskId) {
        for (let i = 0; i < this.allTasksData.length; i++) {
          if (this.allTasksData[i].id === taskId) {
            this.allTasksData.splice(i, 1)
          }
        }
        for (let i = 0; i < this.showDetails.length; i++) {
          if (this.showDetails[i].tableIndex === taskId) {
            this.showDetails.splice(i, 1)
          }
        }
      },
      getExistingTask () {
        this.taskResults = [];
        console.log(this.newTaskName)
        console.log(this.jobid)
        if (this.newTaskName.length > 2) {
          axios.post('/api/search/task', {
            taskname: this.newTaskName,
            jobId: this.jobid
          }).then(response => {
            console.log(response)
            console.log(response.data)
            // TODO: remove this function once the query to only pull tasks not related to job is figured out
            let newTasks = this.filterReturnedTasks(response.data, this.allTasks)
            console.log(this.allTasks)
            this.taskResults = newTasks
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
            newTasks.push(responseData[i])
          }
        }


        // if (responseDataLength > allTasksDataLength) {
        //
        // } else {
        //   for (let i = 0; i < responseDataLength; i++) {
        //     let flag = false
        //     for (let j = 0; i < allTasksDataLength; j++) {
        //       if (responseData[i].id === allTasks[j].id) {
        //         flag = true
        //       }
        //     }
        //     if (flag === false) {
        //       newTasks.push(responseData[i])
        //     }
        //   }
        // }

        return newTasks
      },
      fillFields (result) {
        console.log(result)
        console.log(result.email)
        console.log(result.phone)
        console.log(result.name)
        this.email = result.email
        this.phone = result.phone
        this.name = result.name
        this.hasEmailError = false
        this.hasPhoneError = false
        this.emailInputPassed = true
        this.phoneInputPassed = true
      },
      fillTaskPrice (result) {
        console.log(result)
        this.taskExists = true
        this.task = result
        // input fields
        this.taskPrice = result.proposed_cust_price
        this.newTaskName = result.name
        this.subTaskPrice = result.proposed_sub_price
        // comparison values
        this.selectedTaskName = result.name
        this.selectedTaskPrice = result.proposed_cust_price
        this.selectedSubTaskPrice = result.proposed_sub_price
        this.clearTaskResults()
      },
      showSubTask (taskId) {
        // if task id is the same then hide it
        // if the task id is different then leave shown and update the taskId in the store
        if (taskId === this.taskId) {
          this.initiateSubTask = !this.initiateSubTask
        } else {
          this.initiateSubTask = true
          this.taskId = taskId
        }
      }
    }
  }
</script>

<style scoped>

    /*.addBidTask {*/
    /*margin-top: 4rem;*/
    /*margin-left: 1rem;*/
    /*margin-right: 1rem;*/
    /*}*/

    /*.currenttasksforjob {*/
    /*background-color: white;*/
    /*margin-left: 27rem;*/
    /*margin-right: 27rem;*/
    /*border-radius: 2.5%;*/
    /*border: solid thin black;*/
    /*}*/

    /*.ctfheader {*/
    /*font-weight: 900;*/
    /*font-size: larger;*/
    /*margin-top: 1rem;*/
    /*margin-left: 1rem;*/
    /*}*/

    .wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-column-gap: 20px;
        margin-bottom: 1rem;
    }

    .details {
        grid-row-start: 1;
        grid-row-end: 2;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .initiateBid {
        grid-row-start: 1;
        grid-row-end: 2;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .text-danger {
        background-color: rgba(255, 64, 47, 0.38);
    }

    .validationError {
        margin-left: 1rem;
        color: red;
    }

    /*.button {*/
    /*margin-left: 50px;*/
    /*margin-right: 50px;*/
    /*}*/

    /*.joblist {*/
    /*!*margin-left: 27rem;*!*/
    /*!*margin-right: 27rem;*!*/
    /*}*/

    /*.task {*/
    /*font-size: medium;*/
    /*font-weight: 400;*/
    /*margin-left: 1rem;*/
    /*}*/

    /*.subwrapper {*/
    /*display: grid;*/
    /*grid-template-columns: 1fr 1fr 1fr 1fr;*/
    /*grid-row-gap: 5px;*/
    /*grid-auto-flow: column dense;*/
    /*}*/

    /*.mainTaskWrapper {*/
    /*display: grid;*/
    /*grid-row-gap: 10px;*/
    /*}*/

    /*.customer {*/
    /*grid-row-start: 1;*/
    /*grid-row-end: 2;*/
    /*grid-column-start: 2;*/
    /*grid-column-end: 3;*/
    /*}*/

    /*.contractor {*/
    /*grid-row-start: 1;*/
    /*grid-row-end: 2;*/
    /*grid-column-start: 3;*/
    /*grid-column-end: 4;*/
    /*}*/

    /*.contcustpricelabel {*/
    /*grid-row-start: 2;*/
    /*grid-row-end: 3;*/
    /*grid-column-start: 1;*/
    /*grid-column-end: 2;*/
    /*}*/

    /*.contcustprice {*/
    /*grid-row-start: 2;*/
    /*grid-row-end: 3;*/
    /*grid-column-start: 2;*/
    /*grid-column-end: 3;*/
    /*}*/

    /*.contsubpricelabel {*/
    /*grid-row-start: 2;*/
    /*grid-row-end: 3;*/
    /*grid-column-start: 3;*/
    /*grid-column-end: 4;*/
    /*}*/

    /*.contsubprice {*/
    /*grid-row-start: 2;*/
    /*grid-row-end: 3;*/
    /*grid-column-start: 4;*/
    /*grid-column-end: 5;*/
    /*}*/

    /*.custpricelabel {*/
    /*grid-row-start: 3;*/
    /*grid-row-end: 4;*/
    /*grid-column-start: 1;*/
    /*grid-column-end: 2;*/
    /*}*/

    /*.custprice {*/
    /*grid-row-start: 3;*/
    /*grid-row-end: 4;*/
    /*grid-column-start: 2;*/
    /*grid-column-end: 3;*/
    /*}*/

    /*.subpricelabel {*/
    /*grid-row-start: 3;*/
    /*grid-row-end: 4;*/
    /*grid-column-start: 3;*/
    /*grid-column-end: 4;*/
    /*}*/

    /*.subprice {*/
    /*grid-row-start: 3;*/
    /*grid-row-end: 4;*/
    /*grid-column-start: 4;*/
    /*grid-column-end: 5;*/
    /*}*/

    /*.custaccepted {*/
    /*grid-row-start: 4;*/
    /*grid-row-end: 5;*/
    /*grid-column-start: 1;*/
    /*grid-column-end: 2;*/
    /*}*/

    /*.subaccepted {*/
    /*grid-row-start: 4;*/
    /*grid-row-end: 5;*/
    /*grid-column-start: 2;*/
    /*grid-column-end: 3;*/
    /*}*/

    /*.contcustaccepted {*/
    /*grid-row-start: 4;*/
    /*grid-row-end: 5;*/
    /*grid-column-start: 3;*/
    /*grid-column-end: 4;*/
    /*}*/

    /*.contsubaccepted {*/
    /*grid-row-start: 4;*/
    /*grid-row-end: 5;*/
    /*grid-column-start: 4;*/
    /*grid-column-end: 5;*/
    /*}*/

    /*.custstartdate {*/
    /*grid-row-start: 5;*/
    /*grid-row-end: 6;*/
    /*grid-column-start: 1;*/
    /*grid-column-end: 2;*/
    /*}*/

    /*.custenddate {*/
    /*grid-row-start: 5;*/
    /*grid-row-end: 6;*/
    /*grid-column-start: 2;*/
    /*grid-column-end: 3;*/
    /*}*/

    /*.substartdate {*/
    /*grid-row-start: 5;*/
    /*grid-row-end: 6;*/
    /*grid-column-start: 3;*/
    /*grid-column-end: 4;*/
    /*}*/

    /*.subenddate {*/
    /*grid-row-start: 5;*/
    /*grid-row-end: 6;*/
    /*grid-column-start: 4;*/
    /*grid-column-end: 5;*/
    /*}*/

</style>
