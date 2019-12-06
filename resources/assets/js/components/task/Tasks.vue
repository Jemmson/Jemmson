<template>
    <div>
        <search-bar>
            <input type="text" class="form-control" placeholder="Search Tasks" v-model="searchTerm" @keyup="search">
        </search-bar>


        <div class="mt-4 mb-1">

            <card class="list-card " v-for="bid in sTasks" v-bind:key="bid.id" @click.native="bid.id">
                <div class="row">
                    <div class="col-12 page-header-title">
                        {{ bid.job_task.task.name }}
                    </div>
                    <div class="col-12">
                        <span class="dot" :class="'bg-' + getLabelClass(bid)"></span>
                        <span :class="getLabelClass(bid)">
                            {{ status(bid) }}
                        </span>

                        <span class="float-right list-card-info">2 Subs
                            <i class="fas fa-users"></i>
                        </span>

                        <span class="float-right mr-2 list-card-info">3 Tasks
                            <i class="far fa-check-square"></i>
                        </span>
                    </div>
                </div>
            </card>
        </div>

        <!-- <div class="card card-body mt-4 mb-1">
            <paginate ref="paginator" name="sTasks" :list="sTasks" :per="8" class="paginated"
                v-show="sTasks.length > 0">
                <div class="" v-for="bidTask in paginated('sTasks')" v-bind:key="bidTask.id"
                    :id="'task_' + bidTask.task_id" style="z-index:2;">
                    <task :bidTask="bidTask"></task>
                </div>
            </paginate>
        </div> -->

        <!-- <div class="card card-body text-center">
            <paginate-links for="sTasks" :limit="2" :show-step-links="true">
            </paginate-links>
        </div> -->
        <!-- / end tasks -->
        <!-- <stripe :user='user'>
        </stripe> -->
    </div>
</template>


<script>


  export default {
    name: 'Tasks',
    data() {
      return {
        showTasks: {},
        paginate: ['sTasks'],
        address: '',
        location: {
          location: []
        },
        localArea: '',
        area: {
          area: ''
        },
        hello: 'world',
        tasks: [],
        sTasks: [],
        price: '',
        searchTerm: '',
      }
    },
    methods: {
        getLabelClass(status) {
        return Format.statusLabel(status)
      },
      status(bid_task) {
        return User.status(bid_task.status, bid_task.job_task, true)
      },
      showTheTask(index, action) {

        if (action === 'show') {
          let a = window.document.getElementById('showTask' + index)
          a.setAttribute('style', '')
          // return true
        } else {
          let a = window.document.getElementById('showTask' + index)
          a.setAttribute('style', 'display:none;')
        }

        // for (let i = 0; i < this.tasks.length; i++) {
        // }
      },
      getLabelClass(status) {
        return Format.statusLabel(status)
      },
      search() {
        this.sTasks = this.tasks.filter((task) => {
          if (this.searchTerm == '' || this.searchTerm.length <= 1) {
            return true
          }
          return task.job_task.task.name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
        })
        if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
          this.$refs.paginator.goToPage(1)
        }
      },
      showBid(bid) {
        // TODO: backend what should happen to the bids that wheren't accepted
        if (bid.job_task === null) {
          return false
        }
        return (bid.id === bid.job_task.bid_id && (bid.job_task.job.status === 'job.approved' || bid.job_task.job.status === 'job.completed' || bid.job_task.status === 'bid_task.accepted')) || (bid.job_task.status ===
          'bid_task.bid_sent' || bid.job_task.status === 'bid_task.initiated')
      },
      getArea(bidTask) {
        // console.log(bidTask)
        // debugger
        // Customer.getArea(bidTask.job_id, this.area)
        // this.localArea = this.area

        // return this.localArea.area
      },
      showAddress(bidTask) {
        const status = bidTask.job_task.status
        return status !== 'bid_task.initiated' && status !== 'bid_task.bid_sent' && status !== 'bid_task.finished_by_sub'
      },
      showStripeToggle(jobTask) {
        return jobTask.contractor_id === User.getId() && (jobTask.job.status === 'bid.initiated' || jobTask.job.status === 'bid.in_progress')
      },
      toggleStripePaymentOption(jobTask) {
        SubContractor.toggleStripePaymentOption(jobTask)
      },
      getTasks() {
        console.log('getTasks')
        axios.post('/bid/tasks').then((response) => {
          this.tasks = response.data
          this.sTasks = this.tasks
        })
      }
    },
    created: function() {
      this.getTasks()
      Bus.$on('bidUpdated', (payload) => {
        this.getTasks()
      })
      Bus.$on('needsStripe', () => {
        $('#stripe-modal').modal()
      })
    },
    mounted() {

      console.log(this.tasks)

      let i = -1

      // this.showTasks = this.tasks.map(function(task) {
      //   i++
      //   return this.showTasks[i]
      // }, i)

      for (let j = 0; j < this.tasks.length; j++) {
        this.showTasks[j] = false
      }

      const taskId = User.getParameterByName('taskId')
      if (taskId !== null && taskId !== '') {
        $('#task_' + taskId).addClass('info')
      }
      let success = this.$route.query.success
      if (success !== undefined) {
        success = Language.lang().sub.stripe_success
        Vue.toasted.success(success)
      }
      const error = this.$route.query.error
      Vue.toasted.error(error)
    }
  }
</script>

<style scoped>

    .main {
        background-color: white;
        height: 200vh;
        /*padding: .25rem;*/
    }

    .search-bar {
        /*width: 100%;*/
        /*background-color: white;*/
        /*padding: .25rem .25rem 0rem .25rem;*/
        /*border: black thin solid;*/
    }

</style>