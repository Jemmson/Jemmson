<template>
    <div>
        <div class="intro">
            <div class="intro-header">
                <div class="slogan intro-main-slogan flex flex-col items-center justify-center">
                    <div>Welcome</div>
                    <div>{{ user.name }}</div>
                </div>
                <div class="slogan intro-sub-slogan">Please review and navigate below</div>
            </div>
        </div>
        <div class="flex flex-col home-content">
            <div class="border m-4 shadow-md">
                <div @click="route('/bids')" class="border-b pb-4">
                    <div class="status text-center bg-grey shadow-md ml-1 mr-1">
                        <span>Bids</span>
                    </div>
                </div>
                <div class="statuses mb-1">
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>INITIATED</div>
                        <div>{{ bidData('bid.initiated') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>IN PROGRESS</div>
                        <div>{{ bidData('bid.in_progress') }}</div>
                    </div>
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>SENT</div>
                        <div>{{ bidData('bid.sent') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>DECLINED</div>
                        <div>{{ bidData('bid.declined') }}</div>
                    </div>
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>APPROVED</div>
                        <div>{{ bidData('bid.approved') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>COMPLETED</div>
                        <div>{{ bidData('bid.completed') }}</div>
                    </div>
                </div>
            </div>


            <div class="border m-4 shadow-md" v-if="user.usertype === 'contractor'">
                <div @click="route('/tasks')" class="border-b pb-4">
                    <div class="status text-center bg-grey shadow-md ml-1 mr-1">
                        <span>TASKS</span>
                    </div>
                </div>
                <div class="statuses mb-1">
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>INITIATED</div>
                        <div>{{ bidTaskData('bid_task.initiated') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>SENT</div>
                        <div>{{ bidTaskData('bid_task.bid_sent') }}</div>
                    </div>
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>ACCEPTED</div>
                        <div>{{ taskData('bid_task.accepted') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>FINISHED BY SUB</div>
                        <div>{{ taskData('bid_task.finished_by_sub') }}</div>
                    </div>
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>APPROVED</div>
                        <div>{{ taskData('bid_task.approved_by_general') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>FINISHED BY GENERAL</div>
                        <div>{{ taskData('bid_task.finished_by_general') }}</div>
                    </div>
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>APPROVED BY CUSTOMER</div>
                        <div>{{ taskData('bid_task.approved_by_customer') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>PAYMENT SENT</div>
                        <div>{{ taskData('bid_task.customer_sent_payment') }}</div>
                    </div>
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>REOPENED</div>
                        <div>{{ taskData('bid_task.reopened') }}</div>
                    </div>
                    <div class="current-status bg-brown-lightest pt-1 pb-1">
                        <div>DENIED</div>
                        <div>{{ taskData('bid_task.denied') }}</div>
                    </div>
                </div>
            </div>


            <div class="border m-4 shadow-md">
                <div @click="route('/invoices')" class="border-b pb-4">
                    <div class="status text-center bg-grey shadow-md ml-1 mr-1">
                        <span>Invoices</span>
                    </div>
                </div>
                <div class="statuses mb-1">
                    <div class="current-status bg-blue-lightest pt-1 pb-1">
                        <div>FINISHED</div>
                        <div>{{ invoices.length }}</div>
                    </div>
                </div>
            </div>

            <div class="border m-4 shadow-md"
                 v-if="user.contractor !== null && user.contractor.stripe_express !== null">
                <div @click="route('/express')" class="border-b pb-4">
                    <div class="status text-center bg-grey shadow-md ml-1 mr-1">
                        <span>stripe</span>
                    </div>
                </div>
            </div>
        </div>
        <feedback></feedback>
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
        job: state => state.job
      })
    },
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

    .border-b {
        margin: .5rem .25rem 0rem .25rem;
    }

    .current-status {
        display: grid;
        grid-template-columns: 70% 1fr;
        padding-left: 2.5rem;
    }

    .statuses {
        display: flex;
        flex-direction: column;
        /*margin-left: .5rem;*/
        /*align-items: center;*/
        justify-content: flex-start;
    }

    .home-content {
        min-height: 100vh;
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
        /*align-items: center;*/
        width: 40%;
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
        /*text-align: center;*/
        /*vertical-align: middle;*/
        height: 100%;
        width: 100%;
        /*margin-right: .5rem;*/
        /*margin-left: .5rem;*/
        margin-top: .15rem;
        margin-bottom: .15rem;
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