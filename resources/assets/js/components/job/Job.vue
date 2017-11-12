<template>
    <div class="wrapper">
        <info-label label="Job Name" value="job123"></info-label>
        <!--<span>{{ new Date() | moment("dddd, MMMM Do YYYY") }}</span>-->
        <label for="startDate" class="control-label">
            <input type="date" id="startDate" class="form-control">
        </label>
        <label for="dueDate" class="control-label">
            <input type="date" id="dueDate" class="form-control">
        </label>
        <!--<input-value type="date" name="startDate" label="Start Date"></input-value>-->
        <!--<input-value type="date" name="dueDate" label="Due Date"></input-value>-->
        <contracts></contracts>
        <currentTasksForJob :allTasks="allTasks"></currentTasksForJob>
        <button class="btn btn-primary-btn-large" @click="showAddTask()">Add Task</button>
        <task v-show="showTaskToAdd" @taskIsAdded="updateTasksForJob()"></task>
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
        ]
      }
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
        return this.$moment(new Date(), 'MMMM YYYY')
      }
    },
    methods: {
      showAddTask () {
        this.$data.showTaskToAdd = true
      },
      updateTasksForJob () {
        axios.post('', {
          params: {
            query: this.query
          }
        }).then(response => {
          console.log(response.data)
          this.results = response.data
          this.$emit('taskIsAdded')
        })
      }
    }
  }
</script>

<style scoped>
    .wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px;
        grid-column-gap: 10px;
    }

    @media (min-width: 768px)

    .wrapper {
        width: 750px;
    }
</style>
