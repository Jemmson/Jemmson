<template>
    <div class="container">
        <icon-header icon="jobs"
                      mainHeader="My Jobs"
                      subHeader="Includes jobs with tasks assigned to subcontractors">
        </icon-header>
        <card>
          <list>
            <list-item :left="'Initiated'" :right="bidData('bid.initiated')"></list-item>
            <list-item :left="'In Progress'" :right="bidData('bid.in_progress')"></list-item>
            <list-item :left="'Sent'" :right="bidData('bid.sent')"></list-item>
            <list-item :left="'Declined'" :right="bidData('bid.declined')"></list-item>
            <list-item :left="'Approved'" :right="bidData('bid.approved')"></list-item>
            <list-item :left="'Completed'" :right="bidData('bid.completed')" noDivider="true"></list-item>
          </list>
        </card>
        <br>
        <!-- / end jobs -->

        <icon-header v-if="checkIfUserIsAContractor" icon="tasks"
                      mainHeader="My Tasks"
                      subHeader="Tasks you've accepted or subbed out to subs">
        </icon-header>
        <card v-if="checkIfUserIsAContractor">
          <list>
            <list-item :left="'Initiated'" :right="bidData('bid_task.initiated')"></list-item>
            <list-item :left="'Sent'" :right="bidData('bid_task.bid_sent')"></list-item>
            <list-item :left="'Accepted'" :right="bidData('bid_task.accepted')"></list-item>
            <list-item :left="'Finished By Sub'" :right="bidData('bid_task.finished_by_sub')"></list-item>
            <list-item :left="'Approved'" :right="bidData('bid_task.approved_by_general')"></list-item>
            <list-item :left="'Finished By General'" :right="bidData('bid_task.finished_by_general')"></list-item>
            <list-item :left="'Approved By Customer'" :right="bidData('bid_task.approved_by_customer')"></list-item>
            <list-item :left="'Payment Sent'" :right="bidData('bid_task.customer_sent_payment')"></list-item>
            <list-item :left="'Reopened'" :right="bidData('bid_task.reopened')"></list-item>
            <list-item :left="'Denied'" :right="bidData('bid_task.denied')" noDivider="true"></list-item>

          </list>
        </card>
        <br>
        <!-- / end tasks -->

        <icon-header icon="invoices"
                      mainHeader="My Invoices"
                      subHeader="All Invoices for completed jobs and tasks">
        </icon-header>
        <card>
          <list>
            <list-item :left="'Total Invoices'" :right="invoices.length" noDivider="true"></list-item>
          </list>
        </card>
        <br>
        <!-- / end invoices -->
    </div>
</template>

<script>

  import { mapState } from 'vuex'
  import Feedback from '../components/shared/Feedback'

  export default {
    props: {
      user: Object
    },
    components: {
      Feedback
    },
    data() {
      return {
        bids: '',
        invoices: '',
        tasks: '',
        sBids: 0,
        sTasks: 0,
        sInvoices: 0
      }
    },
    computed: {
      ...mapState({
        job: state => state.job.model
      })
    },
    methods: {
      contractorName() {
        if (this.user !== undefined && this.user !== null) {
          return this.user.name
        }
      },
      checkIfUserIsAContractor() {
        return this.user.usertype === 'contractor'
      },
      checkContractorStripeIsValid() {
        if (
          this.user !== undefined &&
          this.user !== null
        ) {
          if (
            this.user.contractor !== null &&
            this.user.contractor !== undefined
          ) {
            if (
              this.user.contractor.stripe_express !== null &&
              this.user.contractor.stripe_express !== undefined
            ) {
              return true
            } else {
              return false
            }
          }
        }
      },
      route(value) {
        if (value === 'express') {
          axios.post('/stripe/express/dashboard').then((response) => {
            window.location = response.data.url
          })
        } else {
          this.$router.push(value)
        }
      },
      bidData(message) {
        let count = 0
        for (let i = 0; i < this.bids.length; i++) {
          if (this.bids[i].status === message) {
            count++
          }
        }
        return count
      },
      bidTaskData(message) {
        let count = 0
        for (let i = 0; i < this.tasks.length; i++) {
          if (this.tasks[i].status === message) {
            count++
          }
        }
        return count
      },
      taskData(message) {
        let count = 0
        for (let i = 0; i < this.tasks.length; i++) {
          if (this.tasks[i].job_task.status === message) {
            count++
          }
        }
        return count
      }
    },
    mounted: function() {
      this.$store.commit('setCurrentPage', this.$router.history.current.path);
      axios.post('/jobs').then((response) => {
        if(response.data !== undefined) {
          this.bids = response.data;
          this.sBids = this.bids;
        } else {
          this.bids = [];
          this.sBids = [];
        }
      })
      axios.post('/bid/tasks').then((response) => {
        if(response.data !== undefined) {
          this.tasks = response.data
          this.sTasks = this.tasks
        } else {
          this.tasks = []
          this.sTasks = []
        }

      })
      axios.get('/invoices').then((response) => {
        if(response.data !== undefined) {
          this.invoices = response.data
          this.sInvoices = this.invoices
        } else {
          this.invoices = []
          this.sInvoices = []
        }

      })
      // console.log(this.bids)
      // console.log(JSON.stringify(this.bids))
    }
  }
</script>
