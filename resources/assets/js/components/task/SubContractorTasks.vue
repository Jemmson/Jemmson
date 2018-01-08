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
                  <td><input type="text" v-bind:id="'price-' + bidTask.id" v-bind:value="bidTask.bid_price"/></td>
                  <td><button class="btn btn-primary" @click.prevent="update" v-bind:id="bidTask.id">Submit</button></td>
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
        tasks: [],
        price: ''
      }
    },
    methods: {
      update: function (e) {
        let id = e.target.id;
        let bid_price = $('#price-' + id).val();

        console.log(id, bid_price);
        axios.put('/api/bid/task/' + id, {
            id: id,
            bid_price: bid_price 
          }).then(function (response) {
            console.log(response);
          })
          .catch(function (error) {
            console.log(error);
          });

      }
    },
    created: function () {
      console.log('created');
    }
  }
</script>