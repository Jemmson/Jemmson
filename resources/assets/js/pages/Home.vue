<template>
    <div>
        <div class="intro">
            <div class="intro-header">
                <div class="slogan intro-main-slogan">Welcome {{ user.name }}</div>
                <div class="slogan intro-sub-slogan">Please review and navigate below</div>
            </div>
        </div>
        <div class="main-content summary">
            <div class="flex flex-col">
                <div>
                    <div class="text-2xl font-black" v-if="bids.length === 0">No Current Bids</div>
                    <div class="text-2xl font-black" v-else-if="bids.length === 1">1 Bid</div>
                    <div class="text-2xl font-black" v-else>{{ bids.length }} Bids</div>
                </div>
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

    .home-icon {
        height: 7rem;
        width: 7rem;
    }

</style>