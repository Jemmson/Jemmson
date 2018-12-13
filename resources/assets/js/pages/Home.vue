<template>
    <div>
        <div class="intro">
            <div class="intro-header">
                <div class="slogan intro-main-slogan">Welcome {{ user.name }}</div>
                <div class="slogan intro-sub-slogan">Please review and navigate below</div>
            </div>
        </div>
        <div class="flex flex-col home-content">
            <div class="border m-4">
                <h4 class="text-center bid-header">Bids</h4>
                <div class="flex flex-wrap justify-between">
                    <div class="border home-box" @click="route('/bids')">
                        <div class="home-text" v-if="bidData(bids, 'bid.initiated') === 0">
                            No Initiated Bids
                        </div>

                        <div class="home-text" v-if="bidData(bids, 'bid.initiated') === 1">
                            {{ bidData(bids, 'bid.initiated') }} has been Initiated
                        </div>

                        <div class="home-text" v-if="bidData(bids, 'bid.initiated') > 1">
                            {{ bidData(bids, 'bid.initiated') }} are Initiated
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/bids')">
                        <div class="home-text" v-if="bidData(bids, 'bid.in_progress') === 0">
                            No Bids Are In Progress
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'bid.in_progress') === 1">
                            {{ bidData(bids, 'bid.in_progress') }} is in Progress
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'bid.in_progress') > 1">
                            {{ bidData(bids, 'bid.in_progress') }} are in Progress
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/bids')">
                        <div class="home-text" v-if="bidData(bids, 'bid.sent') === 0">
                            No Bids Have Been Sent
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'bid.sent') === 1">
                            {{ bidData(bids, 'bid.sent') }} has been Sent
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'bid.sent') > 1">
                            {{ bidData(bids, 'bid.sent') }} have been Sent
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/bids')">
                        <div class="home-text" v-if="bidData(bids, 'bid.declined') === 0">
                            No Bids Have Been Declined
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'bid.declined') === 1">
                            {{ bidData(bids, 'bid.declined') }} has been Declined
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'bid.declined') > 1">
                            {{ bidData(bids, 'bid.declined') }} have been Declined
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/bids')">
                        <div class="home-text" v-if="bidData(bids, 'bid.approved') === 0">
                            No Bids Have Been Approved
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'job.approved') === 1">
                            {{ bidData(bids, 'job.approved') }} has been Approved
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'job.approved') > 1">
                            {{ bidData(bids, 'job.approved') }} are Approved
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/bids')">
                        <div class="home-text" v-if="bidData(bids, 'bid.completed') === 0">
                            No Bids Have Been Completed
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'job.completed') === 1">
                            {{ bidData(bids, 'job.completed') }} has been Completed
                        </div>
                        <div class="home-text" v-if="bidData(bids, 'job.completed') > 1">
                            {{ bidData(bids, 'job.completed') }} are Completed
                        </div>
                    </div>
                </div>
            </div>


            <div class="border m-4" v-if="user.usertype === 'contractor'">
                <h4 class="text-center bid-header">Tasks</h4>
                <div class="flex flex-wrap justify-between">
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="tasks.length === 0">No Current Tasks</div>
                        <div class="home-text" v-else-if="tasks.length === 1">1 Task</div>
                        <div class="home-text" v-else>{{ tasks.length }} Tasks</div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.initiated') === 0">
                            No Tasks have been Initiated
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.initiated') === 1">
                            {{ taskData(tasks, 'bid_task.initiated') }} has been Initiated
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.initiated') > 1">
                            {{ taskData(tasks, 'bid_task.initiated') }} are Initiated
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.bid_sent') === 0">
                            No Tasks have been Sent
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.bid_sent') === 1">
                            {{ taskData(tasks, 'bid_task.bid_sent') }} has been Sent
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.bid_sent') > 1">
                            {{ taskData(tasks, 'bid_task.bid_sent') }} have been Sent
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.accepted') === 0">
                            No Tasks have been accepted
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.accepted') === 1">
                            {{ taskData(tasks, 'bid_task.accepted') }} has been Accepted
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.accepted') > 1">
                            {{ taskData(tasks, 'bid_task.accepted') }} have been Accepted
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.finished_by_sub') === 0">
                            No Tasks have been finished by the Sub
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.finished_by_sub') === 1">
                            {{ taskData(tasks, 'bid_task.finished_by_sub') }} has been Finished by the Sub
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.finished_by_sub') > 1">
                            {{ taskData(tasks, 'bid_task.finished_by_sub') }} have been Finished by the Sub
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.approved_by_general') === 0">
                            No Tasks have been approved by the general
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.approved_by_general') === 1">
                            {{ taskData(tasks, 'bid_task.approved_by_general') }} has been Approved by the General Contractor
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.approved_by_general') > 1">
                            {{ taskData(tasks, 'bid_task.approved_by_general') }} have been Approved by the General Contractor
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.finished_by_general') === 0">
                            No Tasks have been finished by the general
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.finished_by_general') === 1">
                            {{ taskData(tasks, 'bid_task.finished_by_general') }} has been Finished by the General Contractor
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.finished_by_general') > 1">
                            {{ taskData(tasks, 'bid_task.finished_by_general') }} have been Finished by the General Contractor
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.approved_by_customer') === 0">
                            No Tasks have been approved by the customer
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.approved_by_customer') === 1">
                            {{ taskData(tasks, 'bid_task.approved_by_customer') }} has been Approved by the Customer
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.approved_by_customer') > 1">
                            {{ taskData(tasks, 'bid_task.approved_by_customer') }} have been Approved by the Customer
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.customer_sent_payment') === 0">
                            No customers have sent payment
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.customer_sent_payment') === 1">
                            {{ taskData(tasks, 'bid_task.customer_sent_payment') }} has had the Customer Send Payment
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.customer_sent_payment') === 0">
                            No customers have sent payment
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.customer_sent_payment') === 1">
                            {{ taskData(tasks, 'bid_task.customer_sent_payment') }} has had the Customer Send Payment
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.reopened') === 0">
                            No tasks have been reopened
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.reopened') === 1">
                            {{ taskData(tasks, 'bid_task.reopened') }} has been Reopened
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.reopened') > 1">
                            {{ taskData(tasks, 'bid_task.reopened') }} have been Reopened
                        </div>
                    </div>
                    <div class="border home-box" @click="route('/tasks')">
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.denied') === 0">
                            No tasks have been denied
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.denied') === 1">
                            {{ taskData(tasks, 'bid_task.denied') }} has been Denied
                        </div>
                        <div class="home-text" v-if="taskData(tasks, 'bid_task.denied') > 1">
                            {{ taskData(tasks, 'bid_task.denied') }} have been Denied
                        </div>
                    </div>
                </div>
            </div>


            <div class="border m-4">
                <h4 class="text-center bid-header">Invoices</h4>
                <div class="flex flex-wrap justify-between">
                    <div class="border home-box" @click="route('/invoices')">
                        <div class="home-text" v-if="invoices.length === 0">0 Invoices</div>
                        <div class="home-text" v-else-if="invoices.length === 1">1 Invoice</div>
                        <div class="home-text" v-else>{{ invoices.length }} Invoices</div>
                    </div>
                </div>
            </div>

            <!--<div class="border m-4" v-if="user.contractor !== null &&-->
                                                       <!--user.contractor.stripe_express !== null">-->
            <div class="border m-4" v-if="user.contractor !== null && user.contractor.stripe_express !== null">
                <h4 class="text-center bid-header">Stripe</h4>
                <div class="flex flex-wrap justify-between">
                    <div class="border home-box" @click="route('express')">
                        <div class="home-text">Stripe</div>
                    </div>
                </div>
            </div>

        </div>
        <feedback></feedback>
    </div>
</template>

<script>

  import { mapState } from 'vuex'

  export default {
    props: {
      user: Object
    },
    data() {
      return {
        bids: '',
        invoices: '',
        tasks: '',
        sBids: 0,
        sTasks: 0,
        sInvoices: 0,
      }
    },
    computed: mapState({
      job: state => state.job,
    }),
    methods: {
      route(value) {
        if (value === 'express') {
          axios.post('/stripe/express/dashboard').then((response) => {
            console.log(response.data)
            window.location = response.data.url
          })
        } else {
          this.$router.push(value)
        }
      },
      bidData(bids, message) {
        let count = 0
        for (let i = 0; i < bids.length; i++) {
          if (bids[i].status === message) {
            count++
          }
        }
        return count
      },
      taskData(tasks, message) {
        let count = 0
        for (let i = 0; i < tasks.length; i++) {
          if (tasks[i].job_task.status === message) {
            count++
          }
        }
        return count
      },
    },
    mounted: function() {
      console.log('getBids')
      axios.post('/jobs').then((response) => {
        this.bids = response.data
        this.sBids = this.bids
        console.log(this.bids)
      })
      console.log('getTasks')
      axios.post('/bid/tasks').then((response) => {
        this.tasks = response.data
        this.sTasks = this.tasks
      })
      console.log('getInvoices')
      axios.get('/invoices').then((response) => {
        this.invoices = response.data
        this.sInvoices = this.invoices
      })
      console.log(this.bids)
      console.log(JSON.stringify(this.bids))
    }
  }
</script>

<style>

    .home-content {
        height: 100%;
        background-color: white;
    }

    .bid-header {
        margin-top: 1rem;
        font-size: 18pt;
        font-weight: bold;
        text-transform: uppercase;
    }

    .home-box {
        display: flex;
        align-items: center;
        width: 43%;
        height: 10rem;
        margin: .8rem;
        border-radius: 3px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, .12), 0 2px 4px 0 rgba(0, 0, 0, .08);
    }

    .home-box:active {
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .1);
    }

    .home-text {
        color: black;
        text-align: center;
        vertical-align: middle;
        height: 100%;
        width: 100%;
        margin-right: .5rem;
        margin-left: .5rem;
        margin-top: 1rem;
    }

    .home-icon {
        height: 7rem;
        width: 7rem;
    }

    .sub-label {
        color: red;
        font-weight: bold;
        font-size: 1.3rem;
    }

</style>