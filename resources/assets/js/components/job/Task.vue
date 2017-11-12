<template>
    <div class="wrapper">
        <div class="taskType">
            <label for="taskType">Task Type *</label>
            <input name="taskType" id="taskType" :value="task" :placeholder="task" type="text" v-model="query"
                   v-on:keyup="autoComplete"
                   class="form-control" required>
            <div class="panel-footer" v-if="results.length">
                <ul class="list-group">
                    <button class="list-group-item" v-for="result in results" :name="result.phone"
                            @click="fillFields(result)">
                        {{ result.name }}
                    </button>
                </ul>
            </div>
        </div>
        <div class="price">
            <input-value name="price" label="Price for Task"></input-value>
        </div>
        <button class="btn btn-primary btn-large" @click="addTask()">Add Task</button>
    </div>
</template>

<script>
  import axios from 'axios'
  import InputValue from './InputValue.vue'

  export default {
    name: 'Task',
    components: {
      InputValue
    },
    data () {
      return {
        query: '',
        results: [],
        taskName: '',
        price: ''
      }
    },
    methods: {
      autoComplete () {
        this.results = []
        if (this.query.length > 2) {
          axios.get('/api/searchTask', {
            params: {
              query: this.query
            }
          }).then(response => {
            console.log(response.data)
            this.results = response.data
          })
        }
      },
      addTask () {
        axios.post('', {
          params: {
            query: this.query
          }
        }).then(response => {
          console.log(response.data)
          this.results = response.data
          this.$emit('taskIsAdded')
        })
      },
      fillFields (result) {
        console.log(result)
        console.log(result.taskName)
        console.log(result.price)
        this.email = result.taskName
        this.phone = result.price
      }
    }
  }
</script>

<style>

</style>
