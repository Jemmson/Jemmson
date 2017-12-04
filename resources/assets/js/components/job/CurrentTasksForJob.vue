<template>
    <div>
        <!--<pre>{{ allTasks }}</pre>-->
        <!--<pre>{{ getUser }}</pre>-->

        <!--<pre>{{ showDetails[0].show }}</pre>-->
        <!--<pre>{{ showDetails[1].show }}</pre>-->
        <!--<pre>{{ showDetails[2].show }}</pre>-->
        <!--<pre>{{ showDetails[3].show }}</pre>-->
        <!--<pre>{{ showDetails[4].show }}</pre>-->
        <!--<pre>{{ taskId }}</pre>-->
        <div class="joblist" v-if="getUser === 'customer'">

        </div>
        <div class="joblist" v-if="getUser === 'contractor'">
            <div class="wrapper1">
                <div class="header">Task Name</div>
                <div class="header">Final Customer Price</div>
                <div class="header">Final Sub Price</div>
                <div></div>
            </div>
            <div class="mainTaskWrapper">
                <div v-for="(task, index) in allTasks" :key="task.id">
                    <div class="wrapper1">
                        <div class="taskName">{{ task.name }}</div>
                        <div class="price">{{ task.cust_final_price }}</div>
                        <div class="price">{{ task.sub_final_price }}</div>
                        <button @click="initiateSub(task.id, task.name)" class="button btn btn-sm btn-primary">
                            Initiate Bid For Sub
                        </button>
                        <button class="btn btn-sm btn-primary button"
                                @click="showDetails[index].show = !showDetails[index].show">
                            Details
                        </button>
                    </div>
                    <div class="subwrapper" v-if="showDetails[index].show">
                        <div class="customer">Customer</div>
                        <div class="contractor">Contractor</div>
                    </div>
                </div>
            </div>
            <div class="alert-success" v-show="showNotificationSent">A notification was succesfully sent</div>
            <div class="alert-warning" v-show="taskAlreadyExistsWarning">
                Task Already exists and was not added for this contractor <span class="glyphicon glyphicon-remove-sign"
                                                                                @click="hidewarning()"></span></div>
            <div v-show="initiateSubTask">
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
        contractorName: '',
        email: '',
        initiateBidUrl: '/task/notify',
        initiateSubTask: false,
        name: '',
        phone: '',
        query: '',
        results: [],
        showDetails: [
          {show: true},
          {show: false},
          {show: false},
          {show: false},
          {show: false}
        ],
        showNotificationSent: false,
        taskAlreadyExistsWarning: false,
        taskId: '',
        taskName: ''
      }
    },
    props: {
      allTasks: {
        type: Array
      },
      user: {
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
      }
    },
    methods: {
      initiateSub (taskId, taskName) {
        // show the sub task
        this.showSubTask (taskId)
        this.taskName = taskName
        // set the task Id for the sub
      },
      hidewarning () {
        this.taskAlreadyExistsWarning = false
      },
      sendNotificationToSubForParticularTask () {
        // send ajax notification for sub task initiation
        console.log ('sendNotificationToSubForParticularTask is being called')
        axios.post ('/task/notify', {
          taskId: this.taskId,
          contractor: this.contractor,
          phone: this.phone,
          email: this.email
        }).then (function (response) {
          console.log (JSON.stringify (response))
          console.log (response.data)
//          if (response.data !== 'success') {
//            this.checkValidation (response.data)
//          }
          if (response.data === 'task already exists') {
            this.taskAlreadyExistsWarning = true
          } else {
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

    .addBidTask {
        margin-top: 4rem;
    }

    .wrapper1 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        grid-column-gap: 10px;
    }

    .button {
        margin-left: 50px;
        margin-right: 50px;
    }

    .joblist {
        margin-left: 27rem;
        margin-right: 27rem;
    }

    .subwrapper {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        grid-row-gap: 100px;
    }

    .mainTaskWrapper {
        display: grid;
        grid-row-gap: 10px;
    }

    .customer {
        grid-row-start: 1;
        grid-row-end: 2;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .contractor {
        grid-row-start: 1;
        grid-row-end: 2;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .contcustpricelabel {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .contcustprice {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .contsubpricelabel {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .contsubprice {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    .custpricelabel {
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .custprice {
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .subpricelabel {
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .subprice {
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    .custaccepted {
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .subaccepted {
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .contcustaccepted {
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .contsubaccepted {
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    .custstartdate {
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 1;
        grid-column-end: 2;
    }

    .custenddate {
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .substartdate {
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 3;
        grid-column-end: 4;
    }

    .subenddate {
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 4;
        grid-column-end: 5;
    }

</style>
