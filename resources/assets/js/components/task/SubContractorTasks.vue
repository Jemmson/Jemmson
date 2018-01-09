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
                <tr v-for="bidTask in tasks" v-bind:value="bidTask.id">
                  <th scope="row">{{ bidTask.id }}</th>
                  <td>{{ bidTask.name }}</td>
                  <td>
                    <div v-if="bidTask.bid_price == 0">
                      <input type="text" v-bind:id="'price-' + bidTask.id" v-bind:value="bidTask.bid_price" />
                    </div>
                    <div v-else>
                      <input type="text" v-bind:id="'price-' + bidTask.id" v-bind:value="bidTask.bid_price" disabled/>
                    </div>
                    <span class="help-block label label-danger" v-bind:id="'error-' + bidTask.id" style="display: none;">
                    </span>
                    <span class="help-block label label-success" v-bind:id="'success-' + bidTask.id" style="display: none;">
                    </span>
                  </td>
                  <td>
                    <div v-if="bidTask.bid_price == 0">
                      <button class="btn btn-primary" @click.prevent="update" v-bind:id="bidTask.id">Submit</button>
                    </div>
                    <div v-else>
                      <button class="btn btn-primary" @click.prevent="update" v-bind:id="bidTask.id" disabled>Submit</button>
                    </div>
                  </td>
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
        tasks: this.bidTasks,
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
        }).then((response) => {
          // TODO: security review
          console.log(response);

          $('#price-' + id).prop('disabled', true);
          $('#' + id).prop('disabled', true);

          $('#success-' + id).css('display', 'block');
          $('#success-' + id).text('Bid has been sent.');
          setTimeout(() => {
            $('#success-' + id).css('display', 'none');
          }, 10000);

        }).catch((error) => {

          console.log(error.response, '#error-' + id);

          $('#error-' + id).css('display', 'block');
          $('#error-' + id).text(error.response.data.message);
          setTimeout(() => {
            $('#error-' + id).css('display', 'none');
          }, 10000);

        });

      }
    },
    created: function () {
      console.log('created');
    }
  }
</script>