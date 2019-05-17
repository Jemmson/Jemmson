<template>
    <!-- /all tasks of a bid -->
    <div class="container">
        <!-- <search-bar>
            <input type="text" class="form-control" placeholder="Search Jobs" v-model="searchTerm" @keyup="search">
        </search-bar> -->
        <!-- <paginate ref="paginator" name="jobTasks" :list="jobTasks" :per="6" class="paginated"
                  v-if="jobTasks.length > 0"> -->
            <!-- / status -->
            <card v-for="jobTask of jobTasks" v-bind:key="jobTask.id"
                  :id="'task-' + jobTask.id">

                <bid-task
                        :job-task="jobTask"
                        :bid="bid"
                        :user="globalUser"
                >
                </bid-task>

            </card>
        <!-- </paginate> -->

        <!-- <div class="card p-5 card-body justify-center">
            <paginate-links for="jobTasks" :limit="2" :show-step-links="true">
            </paginate-links>
        </div> -->

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
        jTask: {},
        message: '',
        sendSubMessage: true,
        sendCustomerMessage: true,
        customerMessage: '',
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
    methods: {},
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
