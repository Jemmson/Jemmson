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
        <pre>{{ bids }}</pre>
        <div class="joblist" v-if="getUser === 'customer'">
        </div>
        <div class="joblist" v-if="getUser === 'contractor'">
            <div class="container">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Final Customer Price</th>
                        <th>Final Sub Price</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="task in allTasksData" :key="task.id">
                        <td>{{ task.name }}</td>
                        <td><input type="text" :value="task.pivot.cust_final_price"></td>
                        <td v-if="task.pivot.sub_final_price > 0">{{ task.pivot.sub_final_price }}</td>
                        <td v-else>Pending</td>
                        <td>
                            <button @click="initiateSub(task.id, task.name)" class="button btn btn-sm btn-primary">
                                Initiate Bid For Sub
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary button"
                                    @click="showTheDetails(task.id)">
                                Details
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="alert-success" v-show="showNotificationSent">A notification was succesfully sent</div>
                <div class="alert-warning" v-show="taskAlreadyExistsWarning">
                    Task Already exists and was not added for this contractor <span
                        class="glyphicon glyphicon-remove-sign"
                        @click="hidewarning()"></span></div>
                <div class="wrapper">
                    <div class="details" v-for="(task, index) in allTasksData">
                        <div v-show="showDetails[index].show">
                            <h3 class="text-center">Sub Details</h3>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Sub</th>
                                    <th>Price</th>
                                    <th>Accept</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="table" v-for="bid in bidTasks" :key="bid.id" v-if="task.id === bid.task_id">
                                    <td>{{ bid.contractorName[0].name }}</td>
                                    <td>{{ bid.bid_price }}</td>
                                    <td>
                                        <button :click="acceptBid(bid.id)" class="button btn btn-sm btn-primary">Accept
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="initiateBid" v-show="initiateSubTask">
                        <div class="addBidTask">
                            <h1 class="text-center">Task: {{ taskName }}</h1>
                            <div class="form-group">
                                <label
                                        for="contractorName">Contractor Name</label>
                                <input
                                        type="text"
                                        class="form-control"
                                        id="contractorName"
                                        name="contractorName"
                                        :value="contractorName"
                                        :placeholder="contractorName"
                                        v-model="query"
                                        v-on:keyup="autoComplete"
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
                                        for="phone">Phone</label>
                                <input
                                        type="tel"
                                        class="form-control"
                                        id="phone"
                                        name="phone"
                                        v-model="phone">
                            </div>
                            <div class="form-group">
                                <label
                                        for="email">Email</label>
                                <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        v-model="email">
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
        initiateBidUrl: '/task/notify',
        initiateSubTask: false,
        name: '',
        phone: '',
        query: '',
        results: [],
        showDetails: [],
        showNotificationSent: false,
        taskAlreadyExistsWarning: false,
        taskId: '',
        taskName: ''
      }
    },
    mounted () {
      this.allTasksData = this.allTasks
      this.bidTasks = JSON.parse(this.bids)
      console.log(typeof this.bidTasks)
      this.setUpShowDetailsArray ()
      console.log ('all tasks data')
      console.log (this.allTasks)
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
        console.log (this.user)
        console.log (typeof this.user)
        return this.user
      },
      subFinalPrice(price){
        if (price > 0) {
          return price
        } else {
          return 'pending'
        }
      }
    },
    methods: {
      initiateSub (taskId, taskName) {
        // show the sub task
        this.showSubTask (taskId)
        this.taskName = taskName
        // set the task Id for the sub
      },
      setUpShowDetailsArray () {
        for (let i = 0; i < this.allTasks.length; i++) {
          this.showDetails.push ({tableIndex: this.allTasks[i].id, show: false});
        }
      },
      hidewarning () {
        this.taskAlreadyExistsWarning = false
      },
      acceptBid () {
        return true
      },
      showTheDetails (index) {
        this.hideAllTables ()
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
        console.log ('sendNotificationToSubForParticularTask is being called')
        axios.post ('/task/notify', {
          taskId: this.taskId,
          jobId: this.jobid,
          phone: this.phone,
          email: this.email
        }).then (function (response) {
          console.log (response.data)
          // if (response.data !== 'success') {
          //   this.checkValidation (response.data)
          // }
          if (response.data === 'task already exists') {
            this.taskAlreadyExistsWarning = true
          } else {
            // let responseObject = JSON.parse (response.data)
            console.log (typeof response.data)
            console.log (response.data[0].contractor_id)
            console.log (response.data[0].contractorName[0])
            console.log (response.data[0].contractorName[0].name)
            this.bidTasks = response.data
            this.display ()
          }
//          debugger
          // display flash message was sent
        }.bind (this))
      },
      checkValidaton (responseData) {
        if (responseData === 'allFieldsAreEmpty') {
          // display message
        } else if (responseData === 'email') {
          // display message
        } else if (responseData === 'phone') {
          // display message
        }
      },
      display () {
//        debugger
        // hide the initiate task box
        this.initiateSubTask = false
        // show flash message for one second
        this.showNotificationSent = true
        setTimeout (function () {
          this.showNotificationSent = false
        }.bind (this), 3000)
      },
      autoComplete () {
        this.results = [];
        if (this.query.length > 2) {
          axios.get ('/api/search', {
            params: {
              query: this.query
            }
          }).then (response => {
            console.log (response.data)
            this.results = response.data
          })
        }
      },
      fillFields (result) {
        console.log (result)
        console.log (result.email)
        console.log (result.phone)
        console.log (result.name)
        this.email = result.email
        this.phone = result.phone
        this.name = result.name
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
