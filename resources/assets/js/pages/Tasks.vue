<template>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card card-1">
          <!-- <div class="panel-heading">Dashboard</div> -->
          <div class="panel-body">
            <center>
              <h2 class="page-title">Open Tasks</h2>
            </center>
          </div>
        </div>
      </div>
      <!-- / end title -->
      <div class="col-md-12">
        <div class="card card-1">
          <div class="panel-body">
            <div class="form-group">
              <label for="task-search">Search Tasks</label>
              <input type="text" id="task-search" class="form-control" placeholder="Search" v-model="searchTerm" @keyup="search">
            </div>
          </div>
        </div>
      </div>
      <!-- / end search bar -->
      <paginate ref="paginator" name="sTasks" :list="sTasks" :per="4" class="paginated">
        <div class="col-sm-12 col-md-6" v-for="bidTask in paginated('sTasks')" v-bind:key="bidTask.id" :id="'task_' + bidTask.task_id" style="z-index:2;">
          <!--<pre>{{ bidTask }}</pre>-->
          <div class="card card-1" v-if="showBid(bidTask)">
            <div class="panel-body">
              <div class="col-xs-12">
                <h4>
                  <label for="job-stats" class="label" :class="getLabelClass(bidTask.job_task.status)">{{ status(bidTask) }}</label>
                </h4>
                <label for="job-name" class="job-name">{{ jobName(bidTask.job_task.task.name) }}</label>
              </div>
              <div class="col-xs-6">
                <p>
                  Start On:
                  <label for="start-date">{{ prettyDate(bidTask.job_task.start_date) }}</label>
                </p>
                <!-- <div v-if="showStripeToggle(bidTask.job_task)">
                  <p>
                    Stripe Payment:
                  </P>
                  <label class="switch">
                    <input :id="'toggle-stripe-' + bidTask.job_task.id" type="checkbox" v-model="bidTask.job_task.stripe" @click="toggleStripePaymentOption(bidTask.job_task)">
                    <span class="slider round"></span>
                  </label>
                </div> -->
              </div>
              <div v-if="isBidOpen(bidTask)" class="form-group col-xs-6">
                <label for="details">Task Price:</label>
                <input v-if="bidTask.job_task.sub_sets_own_price_for_job === 1"
                       type="text"
                       class="form-control bid-task-price"
                       v-bind:id="'price-' + bidTask.id"
                       v-model="bidTask.bid_price"
                       @keyup="bidPrice('price-' + bidTask.id)"/>
                <input v-else
                       type="text"
                       class="form-control bid-task-price"
                       v-bind:id="'price-' + bidTask.id"
                       v-model="bidTask.bid_price"
                       @keyup="bidPrice('price-' + bidTask.id)"/>
              </div>
              <div class="col-xs-6" v-else>
                <span class="right-label">
                  Accepted Bid Price:
                  <label>${{ bidTask.bid_price }}</label>
                </span>
              </div>
              <!-- / end main info section -->

              <div class="col-xs-12">
                <div class="divider2"></div>
              </div>

              <div class="col-xs-6 form-group">
                <label>QTY: {{ bidTask.job_task.qty }}</label>
              </div>
              <div class="col-xs-6 form-group text-right">
                <label>Total: {{ bidTask.bid_price * bidTask.job_task.qty }}</label>
              </div>
              <!-- / end qty section -->

              <div class="col-xs-12" v-if="showAddress(bidTask)">
                <div class="divider2"></div>
              </div>
              <div class="col-xs-12" v-if="showAddress(bidTask)">
                <p>
                <a target="_blank" :href="'https://www.google.com/maps/search/?api=1&query=' + getAddress(bidTask)">
                  <i class="fas fa-map-marker icon"></i>
                  {{ getAddress(bidTask) }}
                </a>
                </p>
              </div>
              <div class="col-xs-12" v-if="bidTask.job_task.sub_message !== null">
                <div class="divider2"></div>
              </div>
              <div class="col-xs-12" v-if="bidTask.job_task.sub_message !== null">
                <p>
                  {{ bidTask.job_task.sub_message }}
                </p>
              </div>
            </div>
            <div class="panel-footer">
              <div class="row">
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
        </div>
      </paginate>
      <div class="col-md-12">
        <div class="card card-1">
          <div class="panel-body">
            <center>
              <h4>
                <paginate-links for="sTasks" :limit="2" :show-step-links="true">
                </paginate-links>
              </h4>
            </center>
          </div>
        </div>
      </div>
      <!-- / end tasks -->
      <stripe :user='user'>
      </stripe>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      user: Object
    },
    data() {
      return {
        paginate: ['sTasks'],
        address: '',
        location: {
          location: []
        },
        localArea: '',
        area: {
          area: ''
        },
        hello: 'world',
        tasks: [],
        sTasks: [],
        price: '',
        searchTerm: '',
        disabled: {
          submit: false,
          finished: false
        },
      }
    },
    methods: {
      getLabelClass(status) {
        return Format.statusLabel(status);
      },
      search() {
        this.sTasks = this.tasks.filter((task) => {
          if (this.searchTerm == '' || this.searchTerm.length <= 1) {
            return true;
          }
          return task.job_task.task.name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1;
        });
        if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
            this.$refs.paginator.goToPage(1);
        }
      },
      showBid(bid) {
        // TODO: backend what should happen to the bids that wheren't accepted
        if (bid.job_task === null) {
          return false;
        }
        return (bid.id === bid.job_task.bid_id && (bid.job_task.job.status === 'job.approved' || bid.job_task.job.status === 'job.completed' || bid.job_task.status === 'bid_task.accepted')) || (bid.job_task.status ===
          'bid_task.bid_sent' || bid.job_task.status === 'bid_task.initiated');
      },
      jobName(name) {
        return Format.jobName(name);
      },
      bidPrice(target) {
        let price = $('#' + target).val().replace(/[^0-9.]/g, "");
        $('#' + target).val(price);
      },
      getArea(bidTask) {
        // console.log(bidTask)
        // debugger
        // Customer.getArea(bidTask.job_id, this.area)
        // this.localArea = this.area

        // return this.localArea.area
      },
      showAddress(bidTask) {
         const status = bidTask.job_task.status;
         return status !== 'bid_task.initiated' && status !== 'bid_task.bid_sent' && status !== 'bid_task.finished_by_sub';
      },
      getAddress(bidTask) {
            let location_id = 0;
            if (bidTask.job_task.location_id !== null) {
              location_id = bidTask.job_task.location_id;
            } else {
              location_id = bidTask.job_task.job.location_id;
            }
            Customer.getAddress(location_id, this.location)
            return this.location.location
      },
      showFinishedBtn(bid) {
        return bid.job_task.status === 'bid_task.approved_by_customer' || bid.job_task.status === 'bid_task.denied';
      },
      isBidOpen(bid) {
        let acceptedBid = bid.job_task.bid_id;

        // the contractor has not chosen a bid for the
        // task yet
        if (acceptedBid === null) {
          return true;
        }

        return false;
      },
      status(bid_task) {
        return User.status(bid_task.job_task.status, bid_task.job_task, true);
      },
      prettyDate(date) {

        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split(' ');
        return date[0];
      },
      showStripeToggle(jobTask) {
        return jobTask.contractor_id === User.getId() && (jobTask.job.status === 'bid.initiated' || jobTask.job.status === 'bid.in_progress');
      },
      toggleStripePaymentOption(jobTask) {
        SubContractor.toggleStripePaymentOption(jobTask);
      },
      finished(bid) {
        SubContractor.finishedTask(bid, this.disabled);
      },
      update(e) {
        let id = e.target.id;
        let bid_price = $('#price-' + id).val();
        this.disabled.submit = true;
        console.log(id, bid_price);
        axios.put('/api/bid/task/' + id, {
          id: id,
          bid_price: bid_price
        }).then((response) => {
          // TODO: security review
          console.log(response);
          Vue.toasted.success('Bid Sent.');
          User.emitChange('bidUpdated');
          this.disabled.submit = false;
        }).catch((error) => {
          console.log(error.response, '#error-' + id);
          Vue.toasted.error(error.response.data.message);
          this.disabled.submit = false;
        });
      },
      getTasks() {
        console.log('getTasks');
        axios.post('/bid/tasks').then((response) => {
          this.tasks = response.data;
          this.sTasks = this.tasks;
        });
      }
    },
    created: function () {
      this.getTasks();
      Bus.$on('bidUpdated', (payload) => {
        this.getTasks();
      });
      Bus.$on('needsStripe', () => {
        $('#stripe-modal').modal();
      });

      window.Echo.private('')

    },
    mounted() {
      const taskId = User.getParameterByName('taskId');
      if (taskId !== null && taskId !== '') {
        $('#task_' + taskId).addClass('info');
      }
      const success = this.$route.query.success;
      Vue.toasted.success(success);
      const error = this.$route.query.error;
      Vue.toasted.error(error);
    }
  }
</script>