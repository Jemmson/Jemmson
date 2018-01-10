<template>
    <div>
        <!--<pre>{{ $store.state.job }}</pre>-->
        <!--<pre>{{ getTasks }}</pre>-->
        <!--<pre>{{ getUserType }}</pre>-->
        <!--<pre> {{ getCustomer }}</pre>-->
        <div class="customerRegisteredAlert" v-show="getCustomerObject">Customer Has Not Registered Yet</div>
        <div class="wrapper">
            <h3 class="customerName">Customer Name: {{ customer.user.name }}</h3>
            <info-label class="infoLabel" label="Job Name" :value="job.job_name"></info-label>
            <!--<jemm-date class="startDate" label="Job Start Date" serverurl="/job/update" dbcolumn="agreed_start_date"-->
            <!--&gt;</jemm-date>-->
            <!--<jemm-date class="endDate" label="Job End Date" serverurl="/job/update" dbcolumn="agreed_end_date"-->
            <!--&gt;</jemm-date>-->
        </div>
        <currentTasksForJob
                :customerId="customer.id"
                :bids="bids"
                :user="contractor.user.usertype"
                :jobid="job.id"
                :jobStatus="job.status"
                class="currentTasksForJob"
                :allTasks="tasks"
                :contractorId="contractor.user.id"
        >
        </currentTasksForJob>
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
        },
        bids: {},
        job: {},
        contractor: {},
        customer: {},
        tasks: {},
      }
    },
    beforeMount: function () {
      this.loadJobStore ()
      this.bids = JSON.parse(this.pBids);
      this.job = JSON.parse(this.pJob);
      this.contractor = JSON.parse(this.pContractor);
      this.customer = JSON.parse(this.pCustomer);
      this.tasks = JSON.parse(this.pTasks);
    },
    props: {
      pBids: {
        type: String,
      },
      pJob: {
        type: String,
      },
      pContractor: {
        type: String
      },
      pCustomer: {
        type: String
      },
      pTasks: {
        type: String
      },
      usertype: {
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
      date () {
        return this.$moment (new Date (), 'MMMM YYYY')
      },
      getCustomerObject () {
//        console.log(this.customer)
        return this.customer === '[]'
      }
    },
    methods: {
//      ...mapMutations([
//          'loadJobStore'
//      ]),
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
    },
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
