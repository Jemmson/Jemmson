<template>
    <div class="wrapper">
        <div>
            <label for="taskName">Task Name
                <input type="text" id="taskName">
            </label>
        </div>
        <div>
            <label for="price">Price
                <input type="text" id="price">
            </label>
        </div>
        <jemm-date class="startDate" label="Start Date" serverurl="/job/update"
                   dbcolumn="agreed_start_date"
        ></jemm-date>
        <jemm-date class="endDate" label="End Date" serverurl="/job/update" dbcolumn="agreed_end_date"
        ></jemm-date>
        <!--<div class="taskType">-->
            <!--<label for="taskType">Task Type *</label>-->
            <!--<input name="taskType" id="taskType" :value="task" :placeholder="task" type="text" v-model="query"-->
                   <!--v-on:keyup="autoComplete"-->
                   <!--class="form-control" required>-->
            <!--<div class="panel-footer" v-if="results.length">-->
                <!--<ul class="list-group">-->
                    <!--<button class="list-group-item" v-for="result in results" :name="result.phone"-->
                            <!--@click="fillFields(result)">-->
                        <!--{{ result.name }}-->
                    <!--</button>-->
                <!--</ul>-->
            <!--</div>-->
        <!--</div>-->
        <!--<div class="price">-->
            <!--&lt;!&ndash;<input-value name="price" label="Price for Task"></input-value>&ndash;&gt;-->
        <!--</div>-->
        <button class="btn btn-primary btn-large" @click="addTask()">Add Task</button>
    </div>
</template>

<script>
  import JemmDate from './JemmDate.vue'
  import axios from 'axios'
//  import InputValue from './InputValue.vue'

  export default {
    name: 'Task',
    components: {
//      InputValue
      JemmDate
    },
    data () {
      return {
        query: '',
        results: [],
        taskName: '',
        price: '',
        task: ''
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

<style scoped>
    .wrapper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-left: 36rem;
        margin-right: 36rem;
        /*padding-left: 25px;*/
        /*padding-right: 25px;*/
        grid-column-gap: 10px;
        /*grid-row-gap: 20px;*/
        border: solid black thin;
    }
</style>
