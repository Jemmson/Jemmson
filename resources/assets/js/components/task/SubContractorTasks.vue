<template>
  <div class="container">
    <!-- <pre>{{ tasks }}</pre> -->
    <div class="row">
      <div class="col-md-12">
        <div class="panel">
          <!-- <div class="panel-heading">Dashboard</div> -->
          <div class="panel-body">
            Hello, {{ user.name }}
            <br> These are your bid tasks
          </div>
        </div>
      </div>
      <!-- / League Actions -->
      <div class="col-md-12">
        <div class="panel">
          <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Task Name</th>
                  <th scope="col">Start Date</th>
                  <th scope="col">Area</th>
                  <th scope="col">Price</th>
                  <th scope="col">Status</th>
                  <th scope="col">Address</th>
                  <th scope="col"></th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="bidTask in tasks" v-bind:key="bidTask.id" :id="'task_' + bidTask.task_id">
                  <th scope="row">{{ bidTask.id }}</th>
                  <td>{{ bidTask.task.name }}</td>
                  <td>{{ prettyDate(bidTask.job_task.start_date) }}</td>
                  <td>{{ getArea(bidTask) }}</td>
                  <td>
                    <div v-if="isBidOpen(bidTask)">
                      $<input type="text" v-bind:id="'price-' + bidTask.id" v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)"/>
                    </div>
                    <div v-else>
                      $<input type="text" v-bind:id="'price-' + bidTask.id" v-model="bidTask.bid_price" disabled/>
                    </div>
                    <span class="help-block label label-danger" v-bind:id="'error-' + bidTask.id" style="display: none;">
                    </span>
                    <span class="help-block label label-success" v-bind:id="'success-' + bidTask.id" style="display: none;">
                    </span>
                  </td>
                  <td>
                    {{ status(bidTask) }}
                  </td>
                  <td>
                    {{ getAddress(bidTask) }}
                  </td>
                  <td>
                    <div v-if="isBidOpen(bidTask)">
                      <button class="btn btn-primary" @click.prevent="update" v-bind:id="bidTask.id" :disabled="disabled.submit">
                        <span v-if="disabled.submit">
                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Submit
                      </button>
                    </div>
                    <div v-else>
                      <button class="btn btn-primary" v-bind:id="bidTask.id" disabled>Submit</button>
                    </div>
                  </td>
                  <td>
                    <div v-if="showFinishedBtn(bidTask)">
                      <button class="btn btn-success" @click="finished(bidTask)" :disabled="disabled.finished">
                        <span v-if="disabled.finished">
                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Finished
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <stripe v-if="showStripe">
        </stripe>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['user', 'bidTasks'],
    data () {
      return {
        address: '',
        location: {
          location: []
        },
        localArea: '',
        area: {
          area: ''
        },
        hello: 'world',
        tasks: this.bidTasks,
        price: '',
        disabled: {
          submit: false,
          finished: false
        },
        showStripe: false
      }
    },
    methods: {
      bidPrice(target) {
        let price = $('#' + target).val().replace(/[^0-9.]/g, "");
        $('#' + target).val(price); 
      },
      getArea (bidTask) {
        // console.log(bidTask)
        // debugger
        // Customer.getArea(bidTask.job_id, this.area)
        // this.localArea = this.area

        // return this.localArea.area
      },
      getAddress (bidTask) {
        // Customer.getAddress(bidTask.task.jobs[0].location_id, this.location)
        // if(bidTask.job_task.status === 'bid_task.accepted') {
        //     return this.location.location
        // } else {
        //   return 'Pending'
        // }
      },
      showFinishedBtn (bid) {
        return bid.job_task.status === 'bid_task.approved_by_customer' || bid.job_task.status === 'bid_task.denied';
      },
      isBidOpen (bid) {
        let acceptedBid = bid.job_task.bid_id;

        // the contractor has not chosen a bid for the
        // task yet
        if (acceptedBid === null) {
          return true;
        }

        return false;
      },
      status (bid_task) {
        return User.status (bid_task.job_task.status, bid_task.task);
      },
      prettyDate (date) {

        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split (' ');
        return date[0];
      },
      finished (bid) {
        SubContractor.finishedTask (bid, this.disabled);
      },
      update (e) {
        let id = e.target.id;
        let bid_price = $ ('#price-' + id).val ();
        this.disabled.submit = true;
        console.log(id, bid_price);
        axios.put ('/api/bid/task/' + id, {
          id: id,
          bid_price: bid_price
        }).then ((response) => {
          // TODO: security review
          console.log (response);
          Vue.toasted.success ('Bid Sent.');
          User.emitChange ('bidUpdated');
          this.disabled.submit = false;
        }).catch ((error) => {
          console.log (error.response, '#error-' + id);
          Vue.toasted.error (error.response.data.message);
          this.disabled.submit = false;
        });
      },
      getTasks () {
        console.log ('getTasks');
        axios.post ('/bid/tasks').then ((response) => {
          this.tasks = response.data;
        });
      }
    },
    created: function () {
      Bus.$on ('bidUpdated', (payload) => {
        this.getTasks ();
      });
      Bus.$on ('needsStripe', () => {
        this.showStripe = true;
      });
    },
    mounted () {
      const taskId = User.getParameterByName ('taskId');
      if (taskId !== null && taskId !== '') {
        $ ('#task_' + taskId).addClass ('info');
      }
    }
  }
</script>