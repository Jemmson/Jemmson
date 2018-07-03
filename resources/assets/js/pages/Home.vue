<template>
    <div>
        <div class="header-section">
            <div class="upper txt-white font-12">{{ user.name }}</div>
            <div class="home-title upper txt-white font-3rem">home</div>
            <div class="home-icon">
                <span class="text-center">J</span>
            </div>
            <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
                <div class="txt-white upper company-name">{{ user.contractor.company_name }}</div>
            </div>
        </div>
        <div class="container card-1">
            <div class="header-section">
                <div class="mini-slogan upper">mini slogan</div>
                <div class="main-slogan upper">main slogan</div>
            </div>
            <div class="feature-section first-feature">
                <div class="feature-main-section">
                    <div class="feature-main-icon-section">
                        <img class="icon" src="img/bid2.png" alt="">
                        <div class="feature" v-if="bids.length === 0">No Current Bids</div>
                        <div class="feature" v-else-if="bids.length === 1">1 Bid</div>
                        <div class="feature" v-else="">{{ bids.length }} Bids</div>
                    </div>
                    <div class="feature-main-icon-section">
                        <div class="manage">Manage</div>
                        <a href="/#/bids"><i class="fas fa-4x fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="feature-main-section-statuses">
                <div class="status" v-if="bidData(bids, 'bid.initiated') === 1">
                    {{ bidData(bids, 'bid.initiated') }} has been Initiated
                </div>
                <div class="status" v-if="bidData(bids, 'bid.initiated') > 1">
                    {{ bidData(bids, 'bid.initiated') }} are Initiated
                </div>
                
                <div class="status" v-if="bidData(bids, 'bid.in_progress') === 1">
                    {{ bidData(bids, 'bid.in_progress') }} is in Progress
                </div>
                <div class="status" v-if="bidData(bids, 'bid.in_progress') > 1">
                    {{ bidData(bids, 'bid.in_progress') }} are in Progress
                </div>

                <div class="status" v-if="bidData(bids, 'bid.sent') === 1">
                    {{ bidData(bids, 'bid.sent') }} has been Sent
                </div>
                <div class="status" v-if="bidData(bids, 'bid.sent') > 1">
                    {{ bidData(bids, 'bid.sent') }} have been Sent
                </div>

                <div class="status" v-if="bidData(bids, 'bid.declined') === 1">
                    {{ bidData(bids, 'bid.declined') }} has been Declined
                </div>
                <div class="status" v-if="bidData(bids, 'bid.declined') > 1">
                    {{ bidData(bids, 'bid.declined') }} have been Declined
                </div>

                <div class="status" v-if="bidData(bids, 'job.approved') === 1">
                    {{ bidData(bids, 'job.approved') }} has been Approved
                </div>
                <div class="status" v-if="bidData(bids, 'job.approved') > 1">
                    {{ bidData(bids, 'job.approved') }} are Approved
                </div>

                <div class="status" v-if="bidData(bids, 'job.completed') === 1">
                    {{ bidData(bids, 'job.completed') }} has been Completed
                </div>
                <div class="status" v-if="bidData(bids, 'job.completed') > 1">
                    {{ bidData(bids, 'job.completed') }} are Completed
                </div>
            </div>
            <hr>
            <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
                <div class="feature-section">
                    <div class="feature-main-section">
                        <div class="feature-main-icon-section">
                            <img class="icon" src="img/task.png" alt="">
                            <div class="feature" v-if="tasks.length === 0">No Current Tasks</div>
                            <div class="feature" v-else-if="tasks.length === 1">1 Task</div>
                            <div class="feature" v-else="">{{ tasks.length }} Tasks</div>
                        </div>
                        <div class="feature-main-icon-section">
                            <div class="manage">Manage</div>
                            <a href="/#/tasks"><i class="fas fa-4x fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="feature-main-section-statuses">
                    <div class="status" v-if="taskData(tasks, 'bid_task.initiated') === 1">
                        {{ taskData(tasks, 'bid_task.initiated') }} has been Initiated
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.initiated') > 1">
                        {{ taskData(tasks, 'bid_task.initiated') }} are Initiated
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.bid_sent') === 1">
                        {{ taskData(tasks, 'bid_task.bid_sent') }} has been Sent
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.bid_sent') > 1">
                        {{ taskData(tasks, 'bid_task.bid_sent') }} have been Sent
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.accepted') === 1">
                        {{ taskData(tasks, 'bid_task.accepted') }} has been Accepted
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.accepted') > 1">
                        {{ taskData(tasks, 'bid_task.accepted') }} have been Accepted
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.finished_by_sub') === 1">
                        {{ taskData(tasks, 'bid_task.finished_by_sub') }} has been Finished by the Sub
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.finished_by_sub') > 1">
                        {{ taskData(tasks, 'bid_task.finished_by_sub') }} have been Finished by the Sub
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.approved_by_general') === 1">
                        {{ taskData(tasks, 'bid_task.approved_by_general') }} has been Approved by the General Contractor
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.approved_by_general') > 1">
                        {{ taskData(tasks, 'bid_task.approved_by_general') }} have been Approved by the General Contractor
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.finished_by_general') === 1">
                        {{ taskData(tasks, 'bid_task.finished_by_general') }} has been Finished by the General Contractor
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.finished_by_general') > 1">
                        {{ taskData(tasks, 'bid_task.finished_by_general') }} have been Finished by the General Contractor
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.approved_by_customer') === 1">
                        {{ taskData(tasks, 'bid_task.approved_by_customer') }} has been Approved by the Customer
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.approved_by_customer') > 1">
                        {{ taskData(tasks, 'bid_task.approved_by_customer') }} have been Approved by the Customer
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.customer_sent_payment') === 1">
                        {{ taskData(tasks, 'bid_task.customer_sent_payment') }} has had the Customer Send Payment
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.reopened') === 1">
                        {{ taskData(tasks, 'bid_task.reopened') }} has been Reopened
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.reopened') > 1">
                        {{ taskData(tasks, 'bid_task.reopened') }} have been Reopened
                    </div>

                    <div class="status" v-if="taskData(tasks, 'bid_task.denied') === 1">
                        {{ taskData(tasks, 'bid_task.denied') }} has been Denied
                    </div>
                    <div class="status" v-if="taskData(tasks, 'bid_task.denied') > 1">
                        {{ taskData(tasks, 'bid_task.denied') }} have been Denied
                    </div>
                </div>
                <hr>
            </div>
            <div class="feature-section">
                <div class="feature-main-section">
                    <div class="feature-main-icon-section">
                        <img class="icon" src="img/invoice.png" alt="">
                        <div class="feature" v-if="invoices.length === 0">No Current Invoices</div>
                        <div class="feature" v-else-if="invoices.length === 1">1 Invoice</div>
                        <div class="feature" v-else="">{{ invoices.length }} Invoices</div>
                    </div>
                    <div class="feature-main-icon-section">
                        <div class="manage">Manage</div>
                        <a href="/#/invoices"><i class="fas fa-4x fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="feature-section" v-if="user.contractor.stripe_express !== null">
                <div class="feature-main-section">
                    <div class="feature-main-icon-section">
                        <i class="fas fa-3x fa-money-bill-alt icon"></i>
                        <div class="feature">Stripe</div>
                    </div>
                    <div class="feature-main-icon-section">
                        <div class="manage">Manage</div>
                        <a @click="route('express')"><i class="fas fa-4x fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
            <!--<div class="feature-main-section-statuses">-->
                <!--<div class="status">4 are wating for payment</div>-->
                <!--<div class="status">1 is processing</div>-->
            <!--</div>-->
            <hr>
        </div>
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
        invoices: '',
        tasks: '',
        sBids: 0,
        sTasks: 0,
        sInvoices: 0,
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
      console.log ('getTasks');
      axios.post ('/bid/tasks').then ((response) => {
        this.tasks = response.data;
        this.sTasks = this.tasks;
      });
      console.log ('getInvoices');
      axios.get ('/invoices').then ((response) => {
        this.invoices = response.data;
        this.sInvoices = this.invoices;
      });
    }
  }
</script>

<style scoped>

    .header-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .company-name {
        font-size: 1.75rem;
        margin-top: .75rem;
    }

    .home-icon {
        margin-top: 2rem;
        border-radius: 50%;
        background-color: white;
        height: 9rem;
        width: 9rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .home-title {
        margin-top: -.5rem;
    }

    .home-icon span {
        font-size: 4rem;
        font-weight: bolder;
    }

    .txt-white {
        color: white;
    }

    .upper {
        text-transform: uppercase;
    }

    .font-12 {
        font-size: 12px;
    }

    .font-3rem {
        font-size: 3rem;

    }

    .mini-slogan {
        margin-top: 2.15rem;
        font-size: 12px;
        color: #888888;
    }

    .main-slogan {
        font-size: 2.5rem;
        font-family: 'Anton', sans-serif;
        font-weight: 900;
    }

    .container {
        background-color: white;

        height: auto;
        margin-top: 3rem;
        /*border-radius: 10px;*/
        /*background-image: linear-gradient(to right, rgba(22, 20, 17, 0.20), rgba(130, 182, 144, 0.20));*/
    }

    @media (min-width: 575px) {
        .container {
            /*width: 1200px;*/
        }
    }

    .feature-section {
        display: flex;
        flex-direction: column;
        margin-left: 1.25rem;
        margin-right: 1.25rem;
    }

    .feature-main-section {
        display: flex;
        justify-content: space-between;
    }

    .feature-main-icon-section {
        display: flex;
    }

    .feature-main-section-statuses {
        display: flex;
        flex-direction: column;
        align-items: left;
    }

    .icon {
        margin-right: 1rem;
    }

    .feature {
        font-size: 2.25rem;
        font-weight: 900;
        font-family: 'Anton', sans-serif;
    }

    .manage {
        margin-right: 1rem;
        font-size: 2.25rem;
        font-weight: 900;
        font-family: 'Anton', sans-serif;
    }

    .status {
        margin-left: 5rem;
        color: #0000005c;
    }

    .first-feature {
        margin-top: 1.13rem;
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