<template>
  <div class="flex flex-col justify-between mt-1">
    <div>
      <v-btn
          @click="connectWithStripe($route.path)"
          class="w-full mt-6"
          color="aliceblue"
          text
          elevation="2"
          single-line
          sticky
          style="background-color: cornflowerblue; font-size: 9pt;"
          v-show="needsStripeForCreditCardPayments() && oneOpenBidIsACreditCardJob()"
      ><span style="color: white">Click To Accept Credit Cards</span></v-btn>
      <search-bar>
        <input type="text" class="form-control" placeholder="Search Tasks" v-model="searchTerm" @keyup="search">
      </search-bar>


      <v-card style="margin-bottom: 1rem">
        <v-card-actions
            class="flex flex-col"
        >
          <div class="flex justify-content-around w-full">
            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  class="nav-btn-position"
                  :color="initiated ? 'success': ''"
                  @click="selectTab('initiated')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="initiated ? 'nav-icon-label-selected': ''">
                <v-badge
                    v-if="count.initiated"
                    :color="initiated ? 'success': 'grey'"
                    :content="count.initiated"
                >
                  Initiated
                </v-badge>
                <div v-else>Initiated</div>
              </div>
            </div>
            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="sent ? 'success': ''"
                  class="nav-btn-position"
                  @click="selectTab('sent')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="sent ? 'nav-icon-label-selected': ''">
                <v-badge
                    v-if="count.sent"
                    :color="sent ? 'success': 'grey'"
                    :content="count.sent"
                >
                  Sent
                </v-badge>
                <div v-else>Sent</div>
              </div>
            </div>
            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="accepted ? 'success': ''"
                  class="nav-btn-position"
                  @click="selectTab('accepted')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="accepted ? 'nav-icon-label-selected': ''">
                <v-badge
                    v-if="count.accepted"
                    :color="accepted ? 'success': 'grey'"
                    :content="count.accepted"
                >
                  Accepted
                </v-badge>
                <div v-else>Accepted</div>
              </div>
            </div>
            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="approved ? 'success': ''"
                  class="nav-btn-position"
                  @click="selectTab('approved')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="approved ? 'nav-icon-label-selected': ''">
                <v-badge
                    v-if="count.approved"
                    :color="approved ? 'success': 'grey'"
                    :content="count.approved"
                >
                  Approved
                </v-badge>
                <div v-else>Approved</div>
              </div>
            </div>
            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="finished ? 'success': ''"
                  class="nav-btn-position"
                  @click="selectTab('finished')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="finished ? 'nav-icon-label-selected': ''">
                <v-badge
                    v-if="count.finished"
                    :color="finished ? 'success': 'grey'"
                    :content="count.finished"
                >
                  Finished
                </v-badge>
                <div v-else>Finished</div>
              </div>
            </div>
            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="paid ? 'success': ''"
                  class="nav-btn-position"
                  @click="selectTab('paid')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="paid ? 'nav-icon-label-selected': ''">
                <v-badge
                    v-if="count.paid"
                    :color="paid ? 'success': 'grey'"
                    :content="count.paid"
                >
                  Paid
                </v-badge>
                <div v-else>Paid</div>
              </div>
            </div>
            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="denied ? 'success': ''"
                  class="nav-btn-position"
                  @click="selectTab('denied')"
              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="denied ? 'nav-icon-label-selected': ''">
                <v-badge
                    v-if="count.denied"
                    :color="denied ? 'success': 'grey'"
                    :content="count.denied"
                >
                  Denied
                </v-badge>
                <div v-else>Denied</div>
              </div>
            </div>
          </div>
        </v-card-actions>
      </v-card>

      <!--      <v-tabs-->
      <!--          center-active-->
      <!--          class="flex justify-content-center"-->
      <!--      >-->
      <!--        <v-tab-->
      <!--            @click="selectTab('initiated')"-->
      <!--        >Initiated-->
      <!--        </v-tab>-->
      <!--        <v-tab-->
      <!--            @click="selectTab('accepted')"-->
      <!--        >Accepted-->
      <!--        </v-tab>-->
      <!--        <v-tab-->
      <!--            @click="selectTab('approved')"-->
      <!--        >Approved-->
      <!--        </v-tab>-->
      <!--        <v-tab-->
      <!--            @click="selectTab('paid')"-->
      <!--        >Paid-->
      <!--        </v-tab>-->
      <!--      </v-tabs>-->

      <v-card v-if="initiated">
        <v-card-title>Initiated</v-card-title>
        <v-card-text>
          <v-simple-table>
            <template>
              <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Select</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="bid in sTasks" v-bind:key="bid.id" v-if="getLatestStatus(bid) === 'initiated'">
                <td class="uppercase">{{ bid.job_task.task.name }}</td>
                <td>{{ bid.payment_type }}</td>
                <td>
                  <v-btn
                      color="primary"
                      @click="showSelectedBid(bid)"
                  >View
                  </v-btn>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>

      <v-card v-if="accepted">
        <v-card-title>Accepted</v-card-title>
        <v-card-text>
          <v-simple-table>
            <template>
              <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Select</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="bid in sTasks" v-bind:key="bid.id" v-if="getLatestStatus(bid) === 'accepted'">
                <td class="uppercase">{{ bid.job_task.task.name }}</td>
                <td>{{ bid.payment_type }}</td>
                <td>
                  <v-btn
                      color="primary"
                      @click="showSelectedBid(bid)"
                  >View
                  </v-btn>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>

      <v-card v-if="sent">
        <v-card-title>Sent</v-card-title>
        <v-card-text>
          <v-simple-table>
            <template>
              <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Select</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="bid in sTasks" v-bind:key="bid.id" v-if="getLatestStatus(bid) === 'sent a bid'">
                <td class="uppercase">{{ bid.job_task.task.name }}</td>
                <td>{{ bid.payment_type }}</td>
                <td>
                  <v-btn
                      color="primary"
                      @click="showSelectedBid(bid)"
                  >View
                  </v-btn>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>

      <v-card v-if="approved">
        <v-card-title>Approved</v-card-title>
        <v-card-text>
          <v-simple-table>
            <template>
              <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Select</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="bid in sTasks" v-bind:key="bid.id" v-if="getLatestStatus(bid) === 'approved by customer'">
                <td class="uppercase">{{ bid.job_task.task.name }}</td>
                <td>{{ bid.payment_type }}</td>
                <td>
                  <v-btn
                      color="primary"
                      @click="showSelectedBid(bid)"
                  >View
                  </v-btn>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>

      <v-card v-if="finished">
        <v-card-title>Finished</v-card-title>
        <v-card-text>
          <v-simple-table>
            <template>
              <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Select</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="bid in sTasks" v-bind:key="bid.id" v-if="getLatestStatus(bid) === 'finished job'">
                <td class="uppercase">{{ bid.job_task.task.name }}</td>
                <td>{{ bid.payment_type }}</td>
                <td>
                  <v-btn
                      color="primary"
                      @click="showSelectedBid(bid)"
                  >View
                  </v-btn>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>

      <v-card v-if="paid">
        <v-card-title>Paid</v-card-title>
        <v-card-text>
          <v-simple-table>
            <template>
              <thead>
              <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Select</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="bid in sTasks" v-bind:key="bid.id" v-if="getLatestStatus(bid) === 'paid'">
                <td class="uppercase">{{ bid.job_task.task.name }}</td>
                <td>{{ bid.payment_type }}</td>
                <td>
                  <v-btn
                      color="primary"
                      @click="showSelectedBid(bid)"
                  >View
                  </v-btn>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>

      <v-card v-if="denied">
        <v-card-title>Denied</v-card-title>
        <v-card-text>
          <v-simple-table>
            <template>
              <thead>
              <tr>
                <th>Name</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="bid in sTasks" v-bind:key="bid.id" v-if="
                  getLatestStatus(bid) === 'denied'
              ">
                <td class="uppercase">{{ bid.job_task.task.name }}</td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
      </v-card>

      <v-dialog
          v-if="showSelected"
          v-model="showSelected"
      >
        <task
            :bidTask="bid"
            :user="current_user"
            @close="showSelected=false"
            @getTasks="getTasks()"
        ></task>
      </v-dialog>

      <!--      <section>-->
      <!--        <paginate ref="paginator"-->
      <!--                  :list="sTasks"-->
      <!--                  :per="5"-->
      <!--                  name="sTasks"-->
      <!--                  v-show="sTasks.length > 0"-->
      <!--        >-->
      <!--          <div v-for="bidTask in paginated('sTasks')"-->
      <!--               v-bind:key="bidTask.id"-->
      <!--               :id="'task_' + bidTask.job_task_id"-->
      <!--               style="z-index:2;"-->
      <!--          >-->

      <!--            &lt;!&ndash;                        <pre>{{ bidTask }}</pre>&ndash;&gt;-->

      <!--            <task-->
      <!--                :bidTask="bidTask"-->
      <!--                :user="current_user"-->
      <!--            ></task>-->
      <!--          </div>-->
      <!--        </paginate>-->
      <!--        <div class="card card-body justify-center">-->
      <!--          <paginate-links :limit="2"-->
      <!--                          :show-step-links="true"-->
      <!--                          name="tasks"-->
      <!--                          class="m-center"-->
      <!--                          for="sTasks">-->
      <!--          </paginate-links>-->
      <!--        </div>-->
      <!--      </section>-->
    </div>

    <!-- / end tasks -->
    <stripe :user='user'>
    </stripe>
    <feedback
        page="tasks"
    ></feedback>
  </div>
</template>


<script>

import SearchBar from '../components/shared/SearchBar'
import Feedback from '../components/shared/Feedback'
import Stripe from '../components/stripe/Stripe'
import Card from '../components/shared/Card'
import Task from '../components/task/Task'
import StripeMixin from '../components/mixins/StripeMixin'
import Phone from '../components/mixins/Phone'
import Status from "../components/mixins/Status";

export default {
  name: 'Tasks',
  props: {
    user: Object
  },
  components: {
    SearchBar,
    Feedback,
    Stripe,
    Card,
    Task
  },
  data() {
    return {
      showSelected: false,
      bid: {
        job: {}
      },
      initiated: true,
      sent: false,
      accepted: false,
      approved: false,
      finished: false,
      paid: false,
      denied: false,
      current_user: null,
      showTasks: {},
      paginate: ['sTasks'],
      address: '',
      location: {
        location: []
      },
      count: {
        initiated: null,
        sent: null,
        accepted: null,
        approved: null,
        finished: null,
        paid: null,
        denied: null
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
    }
  },
  mixins: [Phone, StripeMixin, Status],
  methods: {

    selectTab(status) {
      if (status === 'initiated') {
        this.accepted = false;
        this.sent = false;
        this.approved = false;
        this.finished = false;
        this.paid = false;
        this.denied = false;
        this.initiated = true;
      } else if (status === 'sent') {
        this.initiated = false;
        this.sent = true;
        this.approved = false;
        this.finished = false;
        this.paid = false;
        this.denied = false;
        this.accepted = false;
      } else if (status === 'accepted') {
        this.initiated = false;
        this.sent = false;
        this.finished = false;
        this.approved = false;
        this.paid = false;
        this.denied = false;
        this.accepted = true;
      } else if (status === 'approved') {
        this.initiated = false;
        this.sent = false;
        this.accepted = false;
        this.finished = false;
        this.paid = false;
        this.denied = false;
        this.approved = true;
      } else if (status === 'finished') {
        this.initiated = false;
        this.sent = false;
        this.accepted = false;
        this.finished = true;
        this.paid = false;
        this.denied = false;
        this.approved = false;
      } else if (status === 'paid') {
        this.initiated = false;
        this.sent = false;
        this.accepted = false;
        this.finished = false;
        this.denied = false;
        this.approved = false;
        this.paid = true;
      } else {
        this.initiated = false;
        this.sent = false;
        this.accepted = false;
        this.finished = false;
        this.approved = false;
        this.paid = false;
        this.denied = true;
      }
    },

    getCount() {
      let initiated = 0;
      let sent = 0;
      let accepted = 0;
      let approved = 0;
      let finished = 0;
      let paid = 0;
      let denied = 0;
      for (let i = 0; i < this.sTasks.length; i++) {
        const status = this.sTasks[i].job_task.job.sub_status[this.sTasks[i].job_task.job.sub_status.length - 1].status
        if (status === "initiated") {
          initiated++;
        } else if (status === "sent_a_bid") {
          sent++
        } else if (status === "accepted") {
          accepted++;
        } else if (status === "approved_by_customer") {
          approved++;
        } else if (status === "finished_job") {
          finished++;
        } else if (status === "paid") {
          paid++;
        } else if (status === "denied") {
          denied++;
        }
      }
      this.count.initiated = initiated;
      this.count.sent = sent;
      this.count.accepted = accepted;
      this.count.approved = approved;
      this.count.finished = finished;
      this.count.paid = paid;
      this.count.denied = denied;
    },

    showSelectedBid(bid) {
      this.bid = bid;
      this.showSelected = true;
    },

    getLatestStatus(bid) {
      if (
          bid
          && bid.job_task
          && bid.job_task.job
          && bid.job_task.job.sub_status
          && bid.job_task.job.sub_status.length > 0
      ) {
        return this.formatStatus(this.getSubStatus_latest(bid))
      }
    },

    oneOpenBidIsACreditCardJob() {
      for (let i = 0; i < this.tasks.length; i++) {
        if (
            this.tasks[i].job_task.job.payment_type === 'creditCard'
            && this.tasks[i].job_task.job.sub_status[0].status === 'initiated'
        ) {
          return true
        }
      }
      return false
    },

    goBack() {
      this.$router.go(-1)
    },
    showTheTask(index, action) {

      if (action === 'show') {
        let a = window.document.getElementById('showTask' + index)
        a.setAttribute('style', '')
        // return true
      } else {
        let a = window.document.getElementById('showTask' + index)
        a.setAttribute('style', 'display:none;')
      }

      // for (let i = 0; i < this.tasks.length; i++) {
      // }
    },
    getLabelClass(status) {
      return Format.statusLabel(status)
    },
    search() {
      this.sTasks = this.tasks.filter((task) => {
        if (this.searchTerm == '' || this.searchTerm.length <= 1) {
          return true
        }
        return task.job_task.task.name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
      })
      if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
        this.$refs.paginator.goToPage(1)
      }
    },
    showBid(bid) {
      // TODO: backend what should happen to the bids that wheren't accepted
      if (bid.job_task === null) {
        return false
      }
      return (bid.id === bid.job_task.bid_id && (bid.job_task.job.status === 'job.approved' || bid.job_task.job.status === 'job.completed' || bid.job_task.status === 'bid_task.accepted')) || (bid.job_task.status ===
          'bid_task.bid_sent' || bid.job_task.status === 'bid_task.initiated')
    },
    getArea(bidTask) {
    },

    hasStripe() {
      return this.bid.contractor.stripe_id === null
    },

    showAddress(bidTask) {
      const status = bidTask.job_task.status
      return status !== 'bid_task.initiated' && status !== 'bid_task.bid_sent' && status !== 'bid_task.finished_by_sub'
    },
    showStripeToggle(jobTask) {
      return jobTask.contractor_id === User.getId() && (jobTask.job.status === 'bid.initiated' || jobTask.job.status === 'bid.in_progress')
    },
    toggleStripePaymentOption(jobTask) {
      SubContractor.toggleStripePaymentOption(jobTask)
    },
    async getTasks() {
      if (Spark.state.user.usertype === 'contractor') {
        const {data} = await axios.post('/bid/tasks')

        if (data.error) {

        } else {
          if (data) {
            console.log('tasks', data)
            this.tasks = data;
            this.sTasks = this.tasks;
            this.bid = data[0]
            this.getCount();
          }
        }

      }
    }
  },
  created: function () {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    this.getTasks();
    Bus.$on('bidUpdated', (payload) => {
      this.getTasks();
    });
    Bus.$on('needsStripe', () => {
      $('#stripe-modal').modal();
    });

    window.Echo.private('');

  },
  mounted() {

    this.getCount()

    this.$store.commit('setCurrentPage', '/tasks');

    for (let j = 0; j < this.tasks.length; j++) {
      this.showTasks[j] = false;
    }

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

    if (!this.user) {
      this.current_user = Spark.state.user
    }
  }
}
</script>

<style scoped>

.inactive {
  background-color: #8d8d8d !important;
  border-color: #8d8d8d !important;
}

ul {
  padding: 0px !important;
}

.paginate {
  height: 1rem;
}

.main {
  background-color: white;
  height: 200vh;
  padding: .25rem;
}

.search-bar {
  /*width: 100%;*/
  /*background-color: white;*/
  /*padding: .25rem .25rem 0rem .25rem;*/
  /*border: black thin solid;*/
}

</style>