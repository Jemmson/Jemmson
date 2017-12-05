<template>
    <div>
        <!--<pre>{{ $store.state.job }}</pre>-->
        <!--<pre>{{ getTasks }}</pre>-->
        <!--<pre>{{ getUserType }}</pre>-->
        <!--<pre> {{ getCustomer }}</pre>-->
        <div class="customerRegisteredAlert" v-show="getCustomerObject">Customer Has Not Registered Yet</div>
        <div class="wrapper">
            <h3 class="customerName">Customer Name: {{ getCustomerName }}</h3>
            <info-label class="infoLabel" label="Job Name" :value="jobName()"></info-label>
            <!--<jemm-date class="startDate" label="Job Start Date" serverurl="/job/update" dbcolumn="agreed_start_date"-->
            <!--&gt;</jemm-date>-->
            <!--<jemm-date class="endDate" label="Job End Date" serverurl="/job/update" dbcolumn="agreed_end_date"-->
            <!--&gt;</jemm-date>-->
        </div>
        <currentTasksForJob :bids="bids" :user="getUserType" :jobid="getJobId" class="currentTasksForJob" :allTasks="getTasks"></currentTasksForJob>
        <!--<button class="btn btn-primary btn-lg" @click="showAddTask()">Add Task</button>-->
        <!--<task class="task" v-show="showTaskToAdd" @taskIsAdded="updateTasksForJob()"></task>-->
    </div>
</template>

<script>
  import JemmDate from './JemmDate.vue'
  import _ from 'lodash'
  import CurrentTasksForJob from './CurrentTasksForJob.vue'
  import InfoLabel from './InfoLabel'
  import Task from './Task.vue'
  import axios from 'axios'
  import {mapMutations} from 'vuex'

  export default {
    name: 'Job',
    data () {
      return {
        numberOfTasks: '',
        showTaskToAdd: false,
        allTasks: [
          {taskName: 'task1', price: 90},
          {taskName: 'task2', price: 50},
          {taskName: 'task3', price: 100}
        ],
        selectedDates: {
          startDate: '',
          endDate: ''
        }
      }
    },
    beforeMount: function () {
      this.loadJobStore ()
    },
    props: {
      bids: {
        type: String,
      },
      job: {
        type: String,
      },
      contractor: {
        type: String
      },
      customer: {
        type: String
      },
      tasks: {
        type: String
      },
      usertype: {
        type: String
      },
      customeruserdata: {
        type: String
      },
    },
    components: {
      InfoLabel,
//      Contracts,
      Task,
      CurrentTasksForJob,
      JemmDate
    },
    computed: {
      getCustomerObject () {
//        console.log(this.customer)
        return this.customer === '[]'
      },
      date () {
        return this.$moment (new Date (), 'MMMM YYYY')
      },
      getJobId () {
        let job = JSON.parse(this.job)
        console.log(job.id)
        return job.id
      },
      getCustomer () {
//        return JSON.parse (this.customer)
//        return customer
      },
      getCustomerName () {
        let cud = this.customeruserdata
        let val = cud.substr(1, cud.length - 2)
        let customerName = JSON.parse(val)
        return customerName.name
      },
      getTasks () {
        return JSON.parse (this.tasks)
      },
      getUserType () {
        console.log (this.usertype)
        console.log (typeof this.usertype)
        return this.usertype;
      }
    },
    methods: {
//      ...mapMutations([
//          'loadJobStore'
//      ]),
      jobName () {
        let job = JSON.parse (this.job)
        console.log(job)
        return job.job_name
      },
      loadJobStore () {
//        this.$store.commit ('job/loadStore', this.job)
      },
      showAddTask () {
        this.$data.showTaskToAdd = true
      },
      updateTasksForJob () {
        axios.post ('', {
          params: {
            query: this.query
          }
        }).then (response => {
          console.log (response.data)
          this.results = response.data
          this.$emit ('taskIsAdded')
        })
      }
    }
  }
</script>

<style scoped>
    .wrapper {

        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        /*margin-right: auto;*/
        /*margin-left: auto;*/
        margin-left: 36rem;
        margin-right: 36rem;
        grid-column-gap: 10px;
        grid-row-gap: 20px;
        /*border: solid black thin;*/
    }

    label {
        grid-column: 3 / 4;
        grid-row: 1 / 2
    }

    .startDate {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .dueDate {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 3;
        grid-column-end: 4;
        width: 200px;
    }

    .contracts {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    .currentTasksForJob {
        grid-row-start: 3;
        grid-row-end: 4;
        grid-column-start: 2;
        grid-column-end: 5;
    }

    button {
        grid-row-start: 4;
        grid-row-end: 5;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .task {
        grid-row-start: 5;
        grid-row-end: 6;
        grid-column-start: 2;
        grid-column-end: 5;
    }

    @media (min-width: 768px)

    .wrapper {
        width: 750px;
    }

    .customerName {
        grid-column-start: 1;
        grid-column-end: 4;
        margin-bottom: 4rem;
    }

    .infoLabel {
        grid-column-start: 4;
        grid-column-end: 7;
    }

    .startDate {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 2;
        grid-column-end: 3;
    }

    .endDate {
        grid-row-start: 2;
        grid-row-end: 3;
        grid-column-start: 4;
        grid-column-end: 5;
    }

    button {
        margin-left: 36rem;
        margin-right: 36rem;
    }

</style>
