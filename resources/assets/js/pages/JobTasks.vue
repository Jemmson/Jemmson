<template>
  <!-- /all tasks of a bid -->
  <div class="container-fluid">
    <search-bar>
      <input type="text" class="form-control" placeholder="Search Job Tasks" v-model="searchTerm" @keyup="search">
    </search-bar>
    <!-- <paginate ref="paginator" name="jobTasks" :list="jobTasks" :per="6" class="paginated"
                  v-if="jobTasks.length > 0"> -->
    <!-- / status -->
    <div class="mt-4 mb-1">
      <card class="list-card" v-for="(jTask, index) of jobTasks" v-bind:key="jTask.id" :id="'task-' + jTask.id" @click.native="goToJobTask(index)">
        <!-- <bid-task :job-task="jobTask" :bid="job" :user="globalUser">
        </bid-task> -->
        <div class="row">
          <div class="col-12 page-header-title">
            {{ jTask.task.name }}
          </div>
          <div class="col-12">
            <span class="dot" :class="'bg-' + getLabelClass(jTask)"></span>
            <span :class="getLabelClass(jTask)">
              {{ status(jTask) }}
            </span>

            <span class="float-right list-card-info">{{ getTotalSubsForTasks() }} Subs
              <i class="fas fa-users"></i>
            </span>

            <span class="float-right mr-2 list-card-info">{{ jobTasks.length }} Tasks
              <i class="far fa-check-square"></i>
            </span>
          </div>
        </div>
      </card>
    </div>
    <!-- </paginate> -->

    <!-- <div class="card p-5 card-body justify-center">
            <paginate-links for="jobTasks" :limit="2" :show-step-links="true">
            </paginate-links>
        </div> -->
  <job-task-bid-modal :jobTask="jobTask">
  </job-task-bid-modal>
  </div>
</template>

<script>
  import BidTask from '../components/job/BidTask';
  import {mapState} from 'vuex';

  export default {
    components: {
      BidTask,
    },
    data() {
      return {
        globalUser: User,
        paginate: ['jobTasks'],
        jobTask: null,
        message: '',
        sendSubMessage: true,
        sendCustomerMessage: true,
        customerMessage: '',
        searchTerm: '',
        disabled: {
          showDenyForm: false,
          pay: false,
          finished: false,
          approve: false,
          reopen: false,
          deny: false,
          deleteTask: false,
          payCash: false,
          accept: false,
        }
      }
    },
    computed: {
      show() {
        return this.jobTasks.length > 0
      },
      jobTasks() {
        return this.job.job_tasks !== undefined ? this.job.job_tasks : []
      },
      ...mapState({
        job: state => state.job.model
      })
    },
    methods: {
      getTotalSubsForTasks() {
        
        let length = 0

        for (let i = 0; i < this.jobTasks; i++) {
          length = length + this.jobTasks[i].bid_contractor_job_tasks.length
        }

        return length

      },
      setCurrentJobTaskToBidOn (jobTask) {
        this.jobTask = jobTask;
        $('#job-task-bid-modal').modal();
      },
      search () {

      },
      goToJobTask(index) {
        console.log(index);
        this.$router.push('/job/task/' + index);
      },
      getLabelClass(bid) {
        return Format.statusLabel(bid.status, User.isCustomer, User.isGeneral(bid, this.user.id))
      },
      status(bid) {
        return User.status(bid.status, bid)
      },
    },
    mounted: function() {

    }
  }
</script>

<style lang="less" scoped>

    .error {
        color: red;
        font-size: 12pt;
        font-weight: 900;
    }

    .messageHeader {
        font-size: 12pt;
        font-weight: bold;
        font-family: Roboto, serif;
        text-align: center;
    }

    .totalCost {
        padding-left: .5rem;
    }

    .task-status {
        margin-top: -1.25rem;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .pr-1 {
        padding-right: .25rem;
    }
</style>
