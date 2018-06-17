<template>
    <div class="container text-center">
        <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
            <h1>{{ user.contractor.company_name }}</h1>
        </div>
        <div v-else-if="(user.usertype === 'customer') && user.customer !== null">
            <h1>{{ user.name }}</h1>
        </div>
        <h3 class="home-sub-heading">Bids</h3>
        <hr>
        <div class="home-summary-data">
            <h4>Initiated</h4>
            <h5 class="needsApproval">{{ bidData(bids, 'bid.initiated') }}</h5>
        </div>
        <div class="home-summary-data">
            <h4>In Progress</h4>
            <h5 class="inProcess">{{ bidData(bids, 'bid.in_progress') }}</h5>
        </div>
        <div class="home-summary-data">
            <h4>Needs Approval</h4>
            <h5 class="inProcess">{{ bidData(bids, 'bid.sent') }}</h5>
        </div>
        <div class="home-summary-data">
            <h4>Declined</h4>
            <h5 class="inProcess">{{ bidData(bids, 'bid.declined') }}</h5>
        </div>
        <div class="home-summary-data">
            <h4>Job Approved (Waiting to be Finished)</h4>
            <h5 class="waitingToBeFinished">{{ bidData(bids, 'job.approved') }}</h5>
        </div>
        <div class="home-summary-data">
            <h4>Number of Finished Jobs</h4>
            <h5 class="finishedJobs">{{ bidData(bids, 'job.completed') }}</h5>
        </div>
        <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
            <h3 class="home-sub-heading home-sub-heading-tasks">Tasks</h3>
            <hr>
            <div class="home-summary-data">
                <h4>Initiated</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.initiated') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Sent</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.bid_sent') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Accepted</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.accepted') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Finished</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.finished_by_sub') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Approved by General</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.approved_by_general') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Sent to Customer for Approval</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.finished_by_general') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Approved by Customer</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.approved_by_customer') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Customer Sent Payment</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.customer_sent_payment') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Reopened Tasks</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.reopened') }}</h5>
            </div>
            <div class="home-summary-data">
                <h4>Denied Tasks</h4>
                <h5 class="needsToBeBidOn">{{ taskData(tasks, 'bid_task.denied') }}</h5>
            </div>
        </div>
        <div class="summary-footer"></div>
    </div>
</template>

<script>

  export default {
    props: {
      user: Object
    },
    data () {
      return {
        bids: '',
        tasks: ''
      }
    },
    computed: {},
    methods: {
      route (value) {
        if (value === 'express') {
          axios.post ('/stripe/express/dashboard').then ((response) => {
            console.log (response.data);
            window.location = response.data.url;
          });
        } else {
          this.$router.push (value)
        }
      },
      bidData (bids, message) {
        let count = 0;
        for (let i = 0; i < bids.length; i++) {
          if (bids[i].status === message) {
            count++;
          }
        }
        return count;
      },
      taskData (tasks, message) {
        let count = 0;
        for (let i = 0; i < tasks.length; i++) {
          if (tasks[i].job_task.status === message) {
            count++;
          }
        }
        return count;
      },
    },
    mounted: function () {
      console.log ('getBids');
      axios.post ('/jobs').then ((response) => {
        this.bids = response.data;
        this.sBids = this.bids;
        console.log (this.bids)
      });
      console.log('getTasks');
      axios.post('/bid/tasks').then((response) => {
        this.tasks = response.data;
        this.sTasks = this.tasks;
      });
    }
  }
</script>

<style scoped>
    .container {
        background-color: white;
        width: 90%;
        border-radius: 10px;
        background-image: linear-gradient(to right, rgba(22, 20, 17, 0.20), rgba(130, 182, 144, 0.20));
    }

    .home-sub-heading {
        display: flex;
        justify-content: left;
        margin-top: 2rem;
        margin-bottom: 0px;
        color: #81171F;
    }

    h3 {
        margin-top: 0rem;
        margin-bottom: 0px;
        display: flex;
    }

    .home-summary-data {
        display: flex;
        justify-content: space-between;
        margin-top: -1.5rem;
        padding-left: 3em;
        padding-right: 1em;
        color: black;
    }

    .summary-footer {
        height: 2rem;
    }

    .home-sub-heading-tasks {
        margin-top: 5rem;
    }

    h1 {
        color: #81171F;
    }

    h5 {
        color: #81171F;
        font-size: 2rem;
        font-weight: bolder;
    }
</style>