<template>
    <div>
        <div class="flex flex-col items-center">
            <div class="upper text-white text-xs">{{ user.name }}</div>
            <div class="-m-t-2 upper text-white text-5xl">home</div>
            <div class="home-icon
                        flex
                        justify-center
                        items-center
                        rounded-circle
                        m-t-8
                        bg-white">
                <span class="text-6xl font-black text-center">J</span>
            </div>
            <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
                <div class="text-white upper m-t-3 text-3xl">{{ user.contractor.company_name }}</div>
            </div>
        </div>
        <div class="container
                    w-90
                    bg-white
                    rounded-lg
                    card-1">
            <div class="flex flex-col items-center">
                <div class="m-t-10 text-sm text-grey upper">mini slogan</div>
                <div class="text-4xl font-black upper">main slogan</div>
            </div>
            <a href="/#/bids" class="flex flex-col m-l-5 m-r-5 m-t-6">
                <div class="flex justify-between">
                    <div class="flex">
                        <img class="m-r-4" src="img/bid2.png" alt="">
                        <div class="text-4xl font-black" v-if="bids.length === 0">No Current Bids</div>
                        <div class="text-4xl font-black" v-else-if="bids.length === 1">1 Bid</div>
                        <div class="text-4xl font-black" v-else="">{{ bids.length }} Bids</div>
                    </div>
                    <div class="flex">
                        <div class="m-r-4 text-4xl font-black">Manage</div>
                        <a href="/#/bids"><i class="fas fa-4x fa-angle-right"></i></a>
                    </div>
                </div>
            </a>
            <div class="flex flex-col items-start">
                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.initiated') === 1">
                    {{ bidData(bids, 'bid.initiated') }} has been Initiated
                </div>
                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.initiated') > 1">
                    {{ bidData(bids, 'bid.initiated') }} are Initiated
                </div>

                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.in_progress') === 1">
                    {{ bidData(bids, 'bid.in_progress') }} is in Progress
                </div>
                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.in_progress') > 1">
                    {{ bidData(bids, 'bid.in_progress') }} are in Progress
                </div>

                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.sent') === 1">
                    {{ bidData(bids, 'bid.sent') }} has been Sent
                </div>
                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.sent') > 1">
                    {{ bidData(bids, 'bid.sent') }} have been Sent
                </div>

                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.declined') === 1">
                    {{ bidData(bids, 'bid.declined') }} has been Declined
                </div>
                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'bid.declined') > 1">
                    {{ bidData(bids, 'bid.declined') }} have been Declined
                </div>

                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'job.approved') === 1">
                    {{ bidData(bids, 'job.approved') }} has been Approved
                </div>
                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'job.approved') > 1">
                    {{ bidData(bids, 'job.approved') }} are Approved
                </div>

                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'job.completed') === 1">
                    {{ bidData(bids, 'job.completed') }} has been Completed
                </div>
                <div class="m-l-5 text-dark-grey p-l-46" v-if="bidData(bids, 'job.completed') > 1">
                    {{ bidData(bids, 'job.completed') }} are Completed
                </div>
            </div>
            <hr>
            <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
                <div class="flex flex-col m-l-5 m-r-5">
                    <div class="flex justify-between">
                        <div class="flex">
                            <img class="m-r-4" src="img/task.png" alt="">
                            <div class="text-4xl font-black" v-if="tasks.length === 0">No Current Tasks</div>
                            <div class="text-4xl font-black" v-else-if="tasks.length === 1">1 Task</div>
                            <div class="text-4xl font-black" v-else="">{{ tasks.length }} Tasks</div>
                        </div>
                        <div class="flex">
                            <div class="m-r-4 text-4xl font-black">Manage</div>
                            <a href="/#/tasks"><i class="fas fa-4x fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="feature-main-section-statuses">
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.initiated') === 1">
                        {{ taskData(tasks, 'bid_task.initiated') }} has been Initiated
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.initiated') > 1">
                        {{ taskData(tasks, 'bid_task.initiated') }} are Initiated
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.bid_sent') === 1">
                        {{ taskData(tasks, 'bid_task.bid_sent') }} has been Sent
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.bid_sent') > 1">
                        {{ taskData(tasks, 'bid_task.bid_sent') }} have been Sent
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.accepted') === 1">
                        {{ taskData(tasks, 'bid_task.accepted') }} has been Accepted
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.accepted') > 1">
                        {{ taskData(tasks, 'bid_task.accepted') }} have been Accepted
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.finished_by_sub') === 1">
                        {{ taskData(tasks, 'bid_task.finished_by_sub') }} has been Finished by the Sub
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.finished_by_sub') > 1">
                        {{ taskData(tasks, 'bid_task.finished_by_sub') }} have been Finished by the Sub
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.approved_by_general') === 1">
                        {{ taskData(tasks, 'bid_task.approved_by_general') }} has been Approved by the General
                        Contractor
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.approved_by_general') > 1">
                        {{ taskData(tasks, 'bid_task.approved_by_general') }} have been Approved by the General
                        Contractor
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.finished_by_general') === 1">
                        {{ taskData(tasks, 'bid_task.finished_by_general') }} has been Finished by the General
                        Contractor
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.finished_by_general') > 1">
                        {{ taskData(tasks, 'bid_task.finished_by_general') }} have been Finished by the General
                        Contractor
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.approved_by_customer') === 1">
                        {{ taskData(tasks, 'bid_task.approved_by_customer') }} has been Approved by the Customer
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.approved_by_customer') > 1">
                        {{ taskData(tasks, 'bid_task.approved_by_customer') }} have been Approved by the Customer
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.customer_sent_payment') === 1">
                        {{ taskData(tasks, 'bid_task.customer_sent_payment') }} has had the Customer Send Payment
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.reopened') === 1">
                        {{ taskData(tasks, 'bid_task.reopened') }} has been Reopened
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.reopened') > 1">
                        {{ taskData(tasks, 'bid_task.reopened') }} have been Reopened
                    </div>

                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.denied') === 1">
                        {{ taskData(tasks, 'bid_task.denied') }} has been Denied
                    </div>
                    <div class="m-l-5 text-dark-grey p-l-46" v-if="taskData(tasks, 'bid_task.denied') > 1">
                        {{ taskData(tasks, 'bid_task.denied') }} have been Denied
                    </div>
                </div>
                <hr>
            </div>
            <div class="flex flex-col m-l-5 m-r-5">
                <div class="flex justify-between">
                    <div class="flex">
                        <img class="m-r-4" src="img/invoice.png" alt="">
                        <div class="text-4xl font-black" v-if="invoices.length === 0">No Current Invoices</div>
                        <div class="text-4xl font-black" v-else-if="invoices.length === 1">1 Invoice</div>
                        <div class="text-4xl font-black" v-else="">{{ invoices.length }} Invoices</div>
                    </div>
                    <div class="flex">
                        <div class="m-r-4 text-4xl font-black">Manage</div>
                        <a href="/#/invoices"><i class="fas fa-4x fa-angle-right"></i></a>
                    </div>
                </div>
                <hr>
            </div>
            <div class="flex flex-col m-l-5 m-r-5" v-if="user.contractor !== null && user.contractor.stripe_express !== null">
                <div class="flex justify-between">
                    <div class="flex">
                        <i class="fas fa-3x fa-money-bill-alt icon m-r-3"></i>
                        <div class="text-4xl font-black">Stripe</div>
                    </div>
                    <div class="flex">
                        <div class="m-r-4 text-4xl font-black">Manage</div>
                        <a @click="route('express')"><i class="fas fa-4x fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
            <!--<div class="feature-main-section-statuses">-->
            <!--<div class="m-l-5 text-dark-grey p-l-46">4 are wating for payment</div>-->
            <!--<div class="m-l-5 text-dark-grey p-l-46">1 is processing</div>-->
            <!--</div>-->
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

<style>

    .home-icon {
        height: 9rem;
        width: 9rem;
    }

</style>