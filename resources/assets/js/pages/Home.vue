<template>
    <div class="container">

<!--        <figcaption class="caption small-header" :class="isCurrentPage('/initiate-bid', '/initiate-bid/')">New Job</figcaption>-->

        <button
                v-if="isContractor()"
                class="btn btn-normal btn-lg w-full mb-1rem"
                @click="goToNewJob()"
        >CREATE A NEW JOB</button>

        <div v-if="isContractor()">
            <icon-header icon="jobs"
                         mainHeader="My Jobs as a General"
                         subHeader="The statuses of all jobs that you are working on">
            </icon-header>
        </div>
        <div v-else>
            <icon-header icon="jobs"
                         mainHeader="My Jobs"
                         subHeader="">
            </icon-header>
        </div>

        <card>
          <list>
            <list-item v-if="isContractor()" :left="'Initiated'" :right="bidData('bid.initiated')"></list-item>
            <list-item :left="'In Progress'" :right="bidData('bid.in_progress')"></list-item>
            <list-item v-if="isContractor()" :left="'Sent'" :right="bidData('bid.sent')"></list-item>
            <list-item v-if="isContractor()" :left="'Declined'" :right="bidData('bid.declined')"></list-item>
            <list-item :left="'Approved'" :right="bidData('bid.approved')"></list-item>
            <list-item :left="'Completed'" :right="bidData('bid.completed')" noDivider="true"></list-item>
          </list>
        </card>
        <br>

        <!-- / end jobs -->

        <div v-if="isContractor()">
            <icon-header icon="tasks"
                         mainHeader="My Jobs as a Sub"
                         subHeader="Jobs that your as a Sub Contractor">
            </icon-header>
            <card>
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
        </div>
        <br>
        <!-- / end tasks -->

        <icon-header icon="invoices"
                      mainHeader="My Receipts"
                      subHeader="All Receipts for my Completed Jobs and Tasks">
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
  import IconHeader from '../components/shared/IconHeader'
  import Card from '../components/shared/Card'
  import List from '../components/shared/List'
  import ListItem from '../components/shared/ListItem'

  export default {
    props: {
      user: Object
    },
    components: {
      Feedback,
      ListItem,
      List,
      IconHeader,
      Card
    },
    data() {
      return {
        bids: '',
        invoices: '',
        tasks: '',
        theUser: '',
        sBids: 0,
        sTasks: 0,
        sInvoices: 0
      }
    },
    computed: {
      ...mapState({
        job: state => state.job.model,
        userFromState: state => state.user.user,
      })
    },
    methods: {
      goToNewJob(){
        this.$router.push('/initiate-bid');
      },
      contractorName() {
        if (this.theUser !== undefined && this.theUser !== null) {
          return this.theUser.name
        }
      },
      isContractor() {
        return this.theUser.usertype === 'contractor'
      },
      checkContractorStripeIsValid() {
        if (
          this.theUser !== undefined &&
          this.theUser !== null
        ) {
          if (
            this.theUser.contractor !== null &&
            this.theUser.contractor !== undefined
          ) {
            if (
              this.theUser.contractor.stripe_express !== null &&
              this.theUser.contractor.stripe_express !== undefined
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
      axios.get('/jobs').then((response) => {
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
      if (this.user.user === null || this.user.user === undefined ) {
        if (this.userFromState !== '') {
          this.theUser = this.userFromState;
        } else {
          this.theUser = Spark.state.user;
        }
      } else {
        this.theUser = this.user;
      }
      // console.log(this.bids)
      // console.log(JSON.stringify(this.bids))
    }
  }
</script>
