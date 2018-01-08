<template>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel">
          <!-- <div class="panel-heading">Dashboard</div> -->
          <div class="panel-body">
            Hello, {{ user.name }}
            <br> These are your bid tasks
          </div>
        </div>
      </div>
      <!-- / League Actions -->
      <div class="col-md-8 col-md-offset-2">
        <div class="panel">
          <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Task Name</th>
                  <th scope="col">Price</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="bidTask in bidTasks" v-bind:value="bidTask.id">
                  <th scope="row">{{ bidTask.id }}</th>
                  <td>{{ bidTask.name }}</td>
                  <td><input type="text" /></td>
                  <td><button class="btn btn-primary">Submit</button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['user', 'bidTasks'],
    data() {
      return {
        example: ''
      }
    },
    methods: {}
  }
</script>