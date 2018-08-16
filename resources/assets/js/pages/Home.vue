<template>
    <div class="flex flex-col">
        <div class="flex flex-col items-center">
            <div class="upper text-white text-xs">{{ user.name }}</div>
            <div class="home-icon
                        flex
                        justify-center
                        items-center
                        rounded-circle
                        m-t-4
                        bg-white">
                <span class="text-6xl text-blue-darker text-center">J</span>
            </div>
            <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
                <div class="text-white upper m-t-3 text-3xl">{{ user.contractor.company_name }}</div>
            </div>
        </div>

        <card class="mt-4">

            <!-- <div class="flex flex-col mb-4 border-b items-center">
                <div class="text-sm text-grey upper">mini slogan</div>
                <div class="mb-4 text-4xl font-black upper">main slogan</div>
            </div> -->
            <!-- / end slogan -->

            <div class="flex border-b mb-4 pb-4">
                <div class="flex flex-2">
                    <img class="m-r-4 mt-1" src="img/bid2.png" alt="">
                </div>
                <div class="flex flex-col flex-1 mt-2">
                    <div>
                        <div class="text-2xl font-black" v-if="bids.length === 0">No Current Bids</div>
                        <div class="text-2xl font-black" v-else-if="bids.length === 1">1 Bid</div>
                        <div class="text-2xl font-black" v-else>{{ bids.length }} Bids</div>
                    </div>

                    <div>
                        <div class="help-text" v-if="bidData(bids, 'bid.initiated') === 1">
                            {{ bidData(bids, 'bid.initiated') }} has been Initiated
                        </div>

                        <div class="help-text" v-if="bidData(bids, 'bid.initiated') > 1">
                            {{ bidData(bids, 'bid.initiated') }} are Initiated
                        </div>

                        <div class="help-text" v-if="bidData(bids, 'bid.in_progress') === 1">
                            {{ bidData(bids, 'bid.in_progress') }} is in Progress
                        </div>
                        <div class="help-text" v-if="bidData(bids, 'bid.in_progress') > 1">
                            {{ bidData(bids, 'bid.in_progress') }} are in Progress
                        </div>

                        <div class="help-text" v-if="bidData(bids, 'bid.sent') === 1">
                            {{ bidData(bids, 'bid.sent') }} has been Sent
                        </div>
                        <div class="help-text" v-if="bidData(bids, 'bid.sent') > 1">
                            {{ bidData(bids, 'bid.sent') }} have been Sent
                        </div>

                        <div class="help-text" v-if="bidData(bids, 'bid.declined') === 1">
                            {{ bidData(bids, 'bid.declined') }} has been Declined
                        </div>
                        <div class="help-text" v-if="bidData(bids, 'bid.declined') > 1">
                            {{ bidData(bids, 'bid.declined') }} have been Declined
                        </div>

                        <div class="help-text" v-if="bidData(bids, 'job.approved') === 1">
                            {{ bidData(bids, 'job.approved') }} has been Approved
                        </div>
                        <div class="help-text" v-if="bidData(bids, 'job.approved') > 1">
                            {{ bidData(bids, 'job.approved') }} are Approved
                        </div>

                        <div class="help-text" v-if="bidData(bids, 'job.completed') === 1">
                            {{ bidData(bids, 'job.completed') }} has been Completed
                        </div>
                        <div class="help-text" v-if="bidData(bids, 'job.completed') > 1">
                            {{ bidData(bids, 'job.completed') }} are Completed
                        </div>
                    </div>
                </div>

                <div class="flex flex-2">
                    <div class="m-r-4 mt-2 text-2xl font-black"> Manage</div>
                    <a href="/#/bids">
                        <i class="fas fa-3x fa-angle-right"></i>
                    </a>
                </div>
            </div>
            <!-- end bids -->

            <div class="flex border-b mb-4 pb-4" v-if="(user.usertype === 'contractor') && user.contractor !== null">
                <div class="flex flex-2">
                    <img class="m-r-4 mt-1" src="img/task.png" alt="">
                </div>
                <div class="flex flex-col flex-1 mt-2">
                    <div>
                        <div class="text-2xl font-black" v-if="tasks.length === 0">No Current Tasks</div>
                        <div class="text-2xl font-black" v-else-if="tasks.length === 1">1 Task</div>
                        <div class="text-2xl font-black" v-else>{{ tasks.length }} Tasks</div>
                    </div>

                    <div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.initiated') === 1">
                            {{ taskData(tasks, 'bid_task.initiated') }} has been Initiated
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.initiated') > 1">
                            {{ taskData(tasks, 'bid_task.initiated') }} are Initiated
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.bid_sent') === 1">
                            {{ taskData(tasks, 'bid_task.bid_sent') }} has been Sent
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.bid_sent') > 1">
                            {{ taskData(tasks, 'bid_task.bid_sent') }} have been Sent
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.accepted') === 1">
                            {{ taskData(tasks, 'bid_task.accepted') }} has been Accepted
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.accepted') > 1">
                            {{ taskData(tasks, 'bid_task.accepted') }} have been Accepted
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.finished_by_sub') === 1">
                            {{ taskData(tasks, 'bid_task.finished_by_sub') }} has been Finished by the Sub
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.finished_by_sub') > 1">
                            {{ taskData(tasks, 'bid_task.finished_by_sub') }} have been Finished by the Sub
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.approved_by_general') === 1">
                            {{ taskData(tasks, 'bid_task.approved_by_general') }} has been Approved by the General Contractor
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.approved_by_general') > 1">
                            {{ taskData(tasks, 'bid_task.approved_by_general') }} have been Approved by the General Contractor
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.finished_by_general') === 1">
                            {{ taskData(tasks, 'bid_task.finished_by_general') }} has been Finished by the General Contractor
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.finished_by_general') > 1">
                            {{ taskData(tasks, 'bid_task.finished_by_general') }} have been Finished by the General Contractor
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.approved_by_customer') === 1">
                            {{ taskData(tasks, 'bid_task.approved_by_customer') }} has been Approved by the Customer
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.approved_by_customer') > 1">
                            {{ taskData(tasks, 'bid_task.approved_by_customer') }} have been Approved by the Customer
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.customer_sent_payment') === 1">
                            {{ taskData(tasks, 'bid_task.customer_sent_payment') }} has had the Customer Send Payment
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.reopened') === 1">
                            {{ taskData(tasks, 'bid_task.reopened') }} has been Reopened
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.reopened') > 1">
                            {{ taskData(tasks, 'bid_task.reopened') }} have been Reopened
                        </div>

                        <div class="help-text" v-if="taskData(tasks, 'bid_task.denied') === 1">
                            {{ taskData(tasks, 'bid_task.denied') }} has been Denied
                        </div>
                        <div class="help-text" v-if="taskData(tasks, 'bid_task.denied') > 1">
                            {{ taskData(tasks, 'bid_task.denied') }} have been Denied
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="m-r-4 mt-2 text-2xl font-black">Manage</div>
                    <a href="/#/tasks">
                        <i class="fas fa-3x fa-angle-right"></i>
                    </a>
                </div>
            </div>
            <!-- / end tasks -->

            <div class="flex border-b mb-4 pb-4">
                <div class="flex flex-2">
                    <img class="m-r-4 mt-1" src="img/invoice.png" alt="">
                </div>
                <div class="flex flex-1 mt-2">
                    <div class="text-2xl font-black" v-if="invoices.length === 0">0 Invoices</div>
                    <div class="text-2xl font-black" v-else-if="invoices.length === 1">1 Invoice</div>
                    <div class="text-2xl font-black" v-else>{{ invoices.length }} Invoices</div>
                </div>
                <div class="flex flex2">
                    <div class="m-r-4 mt-2 text-2xl font-black">Manage</div>
                    <a href="/#/invoices">
                        <i class="fas fa-3x fa-angle-right"></i>
                    </a>
                </div>
            </div>
            <!-- / end invoices -->

            <div class="flex border-b mb-4 pb-4" v-if="user.contractor !== null &&
                                                       user.contractor.stripe_express !== null">
                <div class="flex flex-2">
                    <i class="fas fa-2x fa-money-bill-alt m-r-3 mt-1 text-grey"></i>
                </div>
                <div class="flex flex-1 mt-2">
                    <div class="text-2xl font-black">Stripe</div>
                </div>
                <div class="flex flex2">
                    <div class="m-r-4 mt-2 text-2xl font-black">Manage</div>
                    <a @click="route('express')">
                        <i class="fas fa-3x fa-angle-right"></i>
                    </a>
                </div>
            </div>
            <!-- / end invoices -->
        </card>

        <pre>
            <!--{{ user }}-->
        </pre>

    </div>
</template>

<script>

  // import axios from 'axios';

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
      console.log(this.bids);
      console.log(JSON.stringify(this.bids));
    }
  }
</script>

<style>

    .home-icon {
        height: 7rem;
        width: 7rem;
    }

</style>