<template>
    <div class="wrapper">
        <div></div>
        <info-label label="Job Name" value="job123"></info-label>
        <!--<span>{{ new Date() | moment("dddd, MMMM Do YYYY") }}</span>-->
        <pre>{{ sDate }}</pre>
        <pre>{{ startdate }}</pre>
        <label for="startDate" class="startDate control-label">Job Start Date
            <input type="date" id="startDate" v-model="sDate" class="form-control" @blur="mouseLeave()">
        </label>
        <label for="dueDate" class="dueDate control-label">Job End Date
            <input type="date" id="dueDate" class="form-control">
        </label>
        <!--<input-value type="date" name="startDate" label="Start Date"></input-value>-->
        <!--<input-value type="date" name="dueDate" label="Due Date"></input-value>-->
        <contracts class="contracts"></contracts>
        <currentTasksForJob class="currentTasksForJob" :allTasks="allTasks"></currentTasksForJob>
        <button class="btn btn-primary-btn-large" @click="showAddTask()">Add Task</button>
        <task class="task" v-show="showTaskToAdd" @taskIsAdded="updateTasksForJob()"></task>
    </div>
</template>

<script>
  //  import InputValue from './InputValue.vue'
  import CurrentTasksForJob from './CurrentTasksForJob.vue'
  import InfoLabel from './InfoLabel'
  import Contracts from './Contracts.vue'
  import Task from './Task.vue'
  //  import 'bootstrap/dist/css/bootstrap.css'
  //  import 'bootstrap-vue/dist/bootstrap-vue.css'
  import axios from 'axios'
  //  import UiDropdown from 'vue-ui'

  export default {
    name: 'Job',
    data () {
      return {
        simpleContract: false,
        mediumContract: false,
        complicatedContract: false,
        customContract: false,
        numberOfTasks: '',
        showTaskToAdd: false,
        allTasks: [
          {taskName: 'task1', price: 90},
          {taskName: 'task2', price: 50},
          {taskName: 'task3', price: 100}
        ],
        sDate: '',
        eDate: ''
      }
    },
    mounted () {
      this.sDate = this.startdate
    },
    props: {
      id: {
        type: String
      },
      startdate: {
        type: String
      },
      enddate: {
        type: String
      },
      jobname: {
        type: String
      },
      contractor: {
        type: String
      },
      bidprice: {
        type: String
      },
      tasks: {
        type: String
      },
      customercontract: {
        type: String
      },
      customer: {
        type: String
      },
      subcontractor: {
        type: String
      },
      subcontractorcontract: {
        type: String
      },
    },
    components: {
//      InputValue,
      InfoLabel,
      Contracts,
      Task,
      CurrentTasksForJob
    },
    computed: {
      date () {
        return this.$moment (new Date (), 'MMMM YYYY')
      }
    },
    methods: {
      mouseLeave: function () {
        console.log (this.id)
        console.log (this.startDate)
        axios.post ('/job/update', {
          params: {
            dateType: 'agreed_start_date',
            date: this.sDate,
            id: this.id
          }
        }).then (response => {
          console.log (response.data)
//          this.results = response.data
//          this.$emit('taskIsAdded')
        })
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
