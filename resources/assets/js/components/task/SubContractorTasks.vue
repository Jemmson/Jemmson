<template>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="panel">
          <!-- <div class="panel-heading">Dashboard</div> -->
          <div class="panel-body">
            <center>
              <h2 class="page-title">Open Tasks</h2>
            </center>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-6" v-for="bidTask in tasks" v-bind:key="bidTask.id" :id="'task_' + bidTask.task_id">
        <div class="panel" v-if="showBid(bidTask)">
          <div class="panel-body">
            <div class="col-xs-12">
              <label for="job-name" class="job-name">{{ jobName(bidTask.task.name) }}</label>
              <label for="job-stats" class="label label-info label-small job-status">{{ status(bidTask) }}</label>
            </div>
            <div class="col-xs-6">
              <p>
              Start On:
              <label for="start-date">{{ prettyDate(bidTask.job_task.start_date) }}</label>
              </p>
              <div v-if="showStripeToggle(bidTask.job_task)">
                              <p>
              Stripe Payment:
              </P>
              <!-- Rounded switch -->
              <label class="switch">
                <input :id="'toggle-stripe-' + bidTask.task.id" type="checkbox" v-model="bidTask.job_task.stripe" @click="toggleStripePaymentOption(bidTask.task)">
                <span class="slider round"></span>
              </label>
              </div>
            </div>
            <div v-if="isBidOpen(bidTask)" class="form-group col-md-6">
              <label for="details">Task Price</label>
              <input type="text" class="form-control bid-task-price" v-bind:id="'price-' + bidTask.id" v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)"
              />
            </div>
            <div class="col-xs-6" v-else>
              <span class="right-label">
                Accepted Bid Price: <label>${{ bidTask.bid_price }}</label>
              </span> 
            </div>
            <div class="col-xs-12">
              <span class="primary-action-btn">
                <div v-if="isBidOpen(bidTask)">
                  <button class="btn btn-primary" @click.prevent="update" v-bind:id="bidTask.id" :disabled="disabled.submit">
                    <span v-if="disabled.submit">
                      <i class="fa fa-btn fa-spinner fa-spin"></i>
                    </span>
                    Submit
                  </button>
                </div>
                <div v-if="showFinishedBtn(bidTask)">
                  <button class="btn btn-success" @click="finished(bidTask)" :disabled="disabled.finished">
                    <span v-if="disabled.finished">
                      <i class="fa fa-btn fa-spinner fa-spin"></i>
                    </span>
                    Finished
                  </button>
                </div>
              </span>
            </div>
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
      showBid(bid) {
        // TODO: backend what should happen to the bids that wheren't accepted
        return (bid.id === bid.job_task.bid_id && bid.task.jobs[0].status === 'job.approved') || (bid.task.jobs[0].status !== 'job.approved');
      },
      jobName(name) {
        return Format.jobName(name);
      },
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
        return User.status(bid_task.job_task.status, bid_task.job_task);
      },
      prettyDate (date) {

        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split (' ');
        return date[0];
      },
      showStripeToggle(jobTask) {
        return jobTask.contractor_id === User.getId();
      },
      toggleStripePaymentOption(task) {
        SubContractor.toggleStripePaymentOption(task);
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