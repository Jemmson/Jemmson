<template>
  <div class="flex flex-col">
    <card footer="true">
      <!-- /show all bid information -->
      <bid-details :customerName="customerName" :bid="bid" :isCustomer="isCustomer">
      </bid-details>

      <template slot="card-footer">
        <!-- /customer approve bid form -->
        <approve-bid v-if="isCustomer && needsApproval" :bid="bid">
        </approve-bid>
        <!-- /buttons  -->
        <general-contractor-bid-actions :bid="bid" @openAddTask="openAddTask">
        </general-contractor-bid-actions>
      </template>
    </card>

    <!-- / show all completed tasks-->
    <completed-tasks :bid="bid">
    </completed-tasks>

    <!-- /show all tasks associated to this bid -->
    <bid-tasks v-if="bid.job_tasks !== undefined && showTasks" :bid="bid" @openTaskPanel="openTaskPanel">
    </bid-tasks>

    <!-- /add task to bid -->
    <transition name="slide-fade">
      <bid-add-task :show="showAddTaskPanel" :bid="bid" :bidId="this.$route.params.id" v-if="!jobApproved">
      </bid-add-task>
    </transition>

    <!-- / stripe testing delete after -->
    <stripe :user='user'>
    </stripe>
    <feedback></feedback>
  </div>
</template>

<script>

  import Feedback from '../components/shared/Feedback';
  import Card from '../components/shared/Card';
  import BidDetails from '../components/job/BidDetails';
  import ApproveBid from '../components/job/ApproveBid';
  import GeneralContractorBidActions from '../components/job/GeneralContractorBidActions';
  import CompletedTasks from '../components/job/CompletedTasks';
  import BidTasks from '../components/job/BidTasks';
  import Stripe from '../components/stripe/Stripe';
  import BidAddTask from '../components/task/BidAddTask';

  export default {
    props: {
      user: Object,
    },
    components: {
      Card,
      Feedback,
      BidDetails,
      ApproveBid,
      GeneralContractorBidActions,
      CompletedTasks,
      BidTasks,
      BidAddTask,
      Stripe
    },
    data() {
      return {
        bid: {},
        jobTaskIndex: 0,
        bidForm: new SparkForm({
          id: 0,
          agreed_start_date: '',
          end_date: '',
          area: '',
          status: '',
        }),
        bidApproved: false,
        showTaskPanel: false,
        showAddTaskPanel: false,
        disabled: {
          approve: false,
          declineBid: false
        },
        showStripe: false
      }
    },
    watch: {
      '$route' (to, from) {
        // get the bid
        const bidId = this.$route.params.id;
        this.getBid(bidId);
      }
    },
    computed: {
      customerName() {
        if (this.isCustomer) {
          return this.user.name;
        }

        if (this.bid.customer !== undefined) {
          return this.bid.customer.name;
        }
      },
      showTasks() {
        if (User.isCustomer()) {
          const status = this.bid.status;
          if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
            return true;
          }
          return false;
        }
        return true;
      },
      jobTask() {
        if (this.bid.job_tasks !== undefined) {
          return this.bid.job_tasks[this.jobTaskIndex];
        } else {
          return this.bid;
        }
      },
      jobApproved() {
        return this.bid.status === 'job.approved';
      },
      needsApproval() {
        // TODO: use regular status values to check these
        return this.bid.status === 'bid.sent';
      },
      isCustomer() {
        return User.isCustomer();
      },
      isGeneralContractor() {
        // General contractor is the one who created the bid
        return User.isGeneral(this.bid);
      },
    },
    methods: {
      reloadPage() {
        location.reload()
      },
      declineBid() {
        Customer.declineBid(this.bid, this.disabled);
      },
      approve() {
        Customer.approveBid(this.bidForm, this.disabled);
      },
      openTaskPanel(index) {
        console.log(index);

        if (this.jobTaskIndex === index && this.showTaskPanel) {
          this.showTaskPanel = false;
        } else {
          this.showTaskPanel = true;
          this.jobTaskIndex = index;
          this.showAddTaskPanel = false;
          this.$nextTick(() => {
            document.getElementById('task-details').scrollIntoView();
          });
        }
      },
      openAddTask() {
        $('#add-task-modal').modal();
      },
      closeBid: function () {
        console.log('closeBid');
        this.$emit('closeBid');
      },
      async getBid(id) {
        try {
          const {
            data
          // } = await axios.get('/job/' + id);
          } = await axios.get('/job/' + id);
          // debugger
          this.bid = data;
        } catch (error) {
          console.log(error);
          // debugger;  
          if (
            error.message === 'Not Authorized to access this resource/api' ||
            error.response.status === 403
          ) {
            this.$router.push('/bids');
          }
          // error = error.response.data;
          // Vue.toasted.error(error.message);
          Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.');
        }
      }
    },
    created: function () {

      // get the bid
      const bidId = this.$route.params.id;
      this.getBid(bidId);

      Bus.$on('taskAdded', () => {
        this.getBid(bidId);
      });

      Bus.$on('bidUpdated', () => {
        this.getBid(bidId);
      });

      Bus.$on('needsStripe', () => {
        $('#stripe-modal').modal();
      });
    },
    beforeDestroy () {
      // ensures no old listeners are left in the bus from old components
      Bus.$off('bidUpdated')
      Bus.$off('taskAdded')
    },
    mounted: function () {
      // set up init data
      // const bidId = this.$route.params.id;
      // this.getBid(bidId);

      this.bidForm.id = this.bid.id;
      this.bidForm.status = this.bid.status;
      const success = this.$route.query.success;
      Vue.toasted.success(success);
      const error = this.$route.query.error;
      Vue.toasted.error(error);
    },
  }
</script>