<template>
  <div>
    <!-- / end title -->
    <search-bar>
      <input type="text" placeholder="Search Tasks" v-model="searchTerm" @keyup="search">
    </search-bar>

    <!-- / end search bar -->
    <paginate ref="paginator" name="sTasks" :list="sTasks" :per="4" class="paginated" v-show="sTasks.length > 0">
      <div class="flex flex-col" v-for="bidTask in paginated('sTasks')" v-bind:key="bidTask.id" :id="'task_' + bidTask.task_id"
        style="z-index:2;">
        <!--<pre>{{ bidTask }}</pre>-->
        <card v-if="showBid(bidTask)" header="true" footer="true">
          <template slot="card-header">
            <label for="job-stats" class="status" :class="getLabelClass(bidTask.job_task.status)">{{ status(bidTask) }}</label>
          </template>

          <label for="job-name" class="text-3xl font-semibold uppercase text-center mb-4">{{ jobName(bidTask.job_task.task.name) }}</label>
          <!-- / end task name -->

          <div class="flex border-b mb-4 pb-4">
            <div class="flex-1 flex-col">
              <span class="label">
                Start On:
              </span>
              <label class="font-normal mt-2">{{ prettyDate(bidTask.job_task.start_date) }}</label>
            </div>

            <div v-if="isBidOpen(bidTask)" class="flex-1 flex-col">
              <label for="details" class="label">Task Price:</label>
              <input v-if="bidTask.job_task.sub_sets_own_price_for_job === 1" type="text" class="form-control mt-2 w-4/5" v-bind:id="'price-' + bidTask.id"
                v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)" />
              <input v-else type="text" class="form-control mt-2 w-4/5" v-bind:id="'price-' + bidTask.id" v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)"
              />
            </div>
            <div v-else class="flex-1 flex-col">
              <span class="label">
                Accepted Bid Price:
              </span>
              <label class="mt-2">${{ bidTask.bid_price }}</label>
            </div>
          </div>
          <!-- / end date and price -->

          <div class="flex border-b mb-4">
            <div class="flex-1 form-group">
              <label class="label">QTY: </label>
              {{ bidTask.job_task.qty }}
            </div>
            <div class="flex-1 form-group text-right">
              <label class="label">Total: </label>
              ${{ bidTask.bid_price }}
            </div>
          </div>
          <!-- / end qty section -->

          <div v-if="showAddress(bidTask)" class="flex border-b mb-4">
            <p>
              <a target="_blank" :href="'https://www.google.com/maps/search/?api=1&query=' + getAddress(bidTask)">
                <i class="fas fa-map-marker icon"></i>
                {{ getAddress(bidTask) }}
              </a>
            </p>
          </div>
          <!-- / end address -->

          <div v-if="showDeclinedMsg(bidTask.job_task.declined_message)" class="flex border-b mb-4">
            <label for="declined_message" class="label label-danger">Declined Reason</label>
            <p>
              {{ bidTask.job_task.declined_message }}
            </p>
          </div>
          <!-- / end declined message section -->

          <div v-if="bidTask.job_task.sub_message !== null && bidTask.job_task.sub_message != ''" class="flex border-b mb-4">
            <p>
              {{ bidTask.job_task.sub_message }}
            </p>
          </div>
          <!-- / end sub message -->

          <task-images :jobTask="bidTask.job_task" type="sub">
          </task-images>

          <template slot="card-footer">
            <div class="flex w-full flex-row-reverse">
              <button v-if="isBidOpen(bidTask)" class="btn btn-green" @click.prevent="update" v-bind:id="bidTask.id" :disabled="disabled.submit">
                <span v-if="disabled.submit">
                  <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Submit
              </button>
              <button v-if="showFinishedBtn(bidTask)" class="btn btn-green" @click="finished(bidTask)" :disabled="disabled.finished">
                <span v-if="disabled.finished">
                  <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Finished
              </button>
            </div>
          </template>
        </card>
      </div>
    </paginate>

    <div class="card p-5 card-body justify-center">
      <paginate-links for="sTasks" :limit="2" :show-step-links="true">
      </paginate-links>
    </div>
    <!-- / end tasks -->
    <stripe :user='user'>
    </stripe>
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
      showDeclinedMsg(msg) {
        return msg !== null && msg !== "";
      },
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
      let success = this.$route.query.success;
      if (success !== undefined) {
        success = Language.lang().sub.stripe_success;
        Vue.toasted.success(success);
      }      
      const error = this.$route.query.error;
      Vue.toasted.error(error);
    }
  }
</script>