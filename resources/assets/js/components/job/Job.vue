<template>
    <div>
        <pre>{{ $store.state.job }}</pre>
        <div class="wrapper">
            <info-label label="Job Name" :value="jobName()"></info-label>
            <jemm-date label="Job Start Date" serverurl="/job/update" dbcolumn="agreed_start_date"
            ></jemm-date>
            <jemm-date label="Job End Date" serverurl="/job/update" dbcolumn="agreed_end_date"
            ></jemm-date>
            <!--<contracts class="contracts"></contracts>-->
            <currentTasksForJob class="currentTasksForJob" :allTasks="allTasks"></currentTasksForJob>
            <button class="btn btn-primary-btn-large" @click="showAddTask()">Add Task</button>
            <task class="task" v-show="showTaskToAdd" @taskIsAdded="updateTasksForJob()"></task>
        </div>
    </div>
</template>

<script>
  //  import InputValue from './InputValue.vue'
  import JemmDate from './JemmDate.vue'
  import CurrentTasksForJob from './CurrentTasksForJob.vue'
  import InfoLabel from './InfoLabel'
//  import Contracts from './Contracts.vue'
  import Task from './Task.vue'
  //  import 'bootstrap/dist/css/bootstrap.css'
  //  import 'bootstrap-vue/dist/bootstrap-vue.css'
  import axios from 'axios'
  import {mapMutations} from 'vuex'
  //  import UiDropdown from 'vue-ui'

  export default {
    name: 'Job',
    data () {
      return {
//        simpleContract: false,
//        mediumContract: false,
//        complicatedContract: false,
//        customContract: false,
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
      job: {
        type: String,
      },
      contractor: {
        type: String
      },
      customer: {
        type: String
      }
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
      }
    },
    methods: {
//      ...mapMutations([
//          'loadJobStore'
//      ]),
      jobName () {
        let jobName = JSON.parse (this.job)
        return jobName.job_name
      },
      loadJobStore () {
        this.$store.commit ('job/loadStore', this.job)
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
        grid-template-columns: 469px 200px 1fr 1fr 1fr;
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px;
        grid-column-gap: 10px;
        grid-row-gap: 20px;
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
        grid-column-end: 3;
    }

    @media (min-width: 768px)

    .wrapper {
        width: 750px;
    }
</style>
