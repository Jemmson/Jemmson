<template>
    <!-- /all tasks of a bid -->
    <div class="container-fluid">

        <div class="flex mb-1rem">
            <v-btn
                    class="w-40"
                    color="primary"
                    @click.prevent="goBack()">
                Back
            </v-btn>
        </div>

        <search-bar>
            <input type="text" class="form-control" placeholder="Search Job Tasks" v-model="searchTerm" @keyup="search">
        </search-bar>
        <!-- <paginate ref="paginator" name="jobTasks" :list="jobTasks" :per="6" class="paginated"
                      v-if="jobTasks.length > 0"> -->
        <!-- / status -->
        <div class="mt-4 mb-1">
            <card class="list-card" v-for="(jTask, index) of jobTasks" v-bind:key="jTask.id" :id="'task-' + jTask.id">

                <horizontal-table
                        :data="jobTaskObject(jTask)"
                ></horizontal-table>


                <!--                <table class="table">-->
                <!--                    <thead>-->
                <!--                    <tr>-->
                <!--                        <th>Task</th>-->
                <!--                        <th>Status</th>-->
                <!--                        <th>{{ !isContractor ? 'Contractor' : 'Sub' }}</th>-->
                <!--                        <th>Phone</th>-->
                <!--                    </tr>-->
                <!--                    </thead>-->
                <!--                    <tbody>-->
                <!--                    <tr>-->
                <!--                        <td>{{ jTask.task.name }}</td>-->
                <!--                        <td>-->
                <!--                                <span :class="getLabelClass(jTask)">-->
                <!--                                    {{ status(jTask) }}-->
                <!--                                </span>-->
                <!--                        </td>-->
                <!--                        <td>{{ jTask.contractor ? jTask.contractor.company_name : 'none'}}</td>-->
                <!--                        <td>{{ jTask.contractor ? jTask.contractor.phone : 'none'}}</td>-->
                <!--                    </tr>-->
                <!--                    </tbody>-->
                <!--                </table>-->


                <div class="row">
                    <!--                    <div class="col-12 page-header-title">-->
                    <!--                        {{ jTask.task.name }}-->
                    <!--                    </div>-->
                    <div class="col-12">
                        <!--                        <span class="dot" :class="'bg-' + getLabelClass(jTask)"></span>-->
                        <!--                        <span :class="getLabelClass(jTask)">-->
                        <!--                            {{ status(jTask) }}-->
                        <!--                        </span>-->
                        <span v-if="isContractor"
                              class="float-right list-card-info">{{ jTask.bid_contractor_job_tasks.length }} Subs
                      <i class="fas fa-users"></i>
                    </span>

                    </div>

                    <div class="flex w-full btn-spacing">
                        <v-btn
                                class="w-40"
                                color="red"
                                @click="showDeleteTaskModal(jTask, index)">
                            DELETE
                            <i v-if="checkSpinner(index)" class="fa fa-btn fa-spinner fa-spin"></i>
                        </v-btn>
                        <v-btn
                                class="w-40"
                                color="primary"
                                @click="goToJobTask(index)">SELECT</v-btn>
                    </div>
                </div>
            </card>
        </div>
        <!-- </paginate> -->

        <!-- <div class="card p-5 card-body justify-center">
                <paginate-links for="jobTasks" :limit="2" :show-step-links="true">
                </paginate-links>
            </div> -->

        <delete-task-modal
                @action="deleteTheTask($event)"
                title="Do You Wish To Delete This Task?"
        >
        </delete-task-modal>

        <job-task-bid-modal :jobTask="jobTask">
        </job-task-bid-modal>
        <feedback
                page="JobTasks"
        ></feedback>
    </div>
</template>

<script>
  import BidTask from '../components/job/BidTask'
  import DeleteTaskModal from '../components/job/DeleteTaskModal'
  import HorizontalTable from '../components/shared/HorizontalTable'
  import SearchBar from '../components/shared/SearchBar'
  import JobTaskBidModal from '../components/task/JobTaskBidModal'
  import Card from '../components/shared/Card'
  import Feedback from '../components/shared/Feedback'

  import { mapState } from 'vuex'

  export default {
    components: {
      BidTask,
      HorizontalTable,
      SearchBar,
      Feedback,
      JobTaskBidModal,
      Card,
      DeleteTaskModal
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
          spinner: [],
          showDenyForm: false,
          pay: false,
          finished: false,
          approve: false,
          reopen: false,
          deny: false,
          deleteTask: false,
          payCash: false,
          accept: false,
        },
        deleteTask: {
          id: ''
        }
      }
    },
    created() {
      document.body.scrollTop = 0 // For Safari
      document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
    },
    computed: {
      show() {
        return this.jobTasks.length > 0
      },
      isContractor() {
        return Spark.state.user.usertype === 'contractor'
      },
      jobTasks() {
        if (this.job[0] && this.job[0].job_tasks) {
          return this.job[0].job_tasks
        } else if (this.job && this.job.job_tasks) {
          return this.job.job_tasks
        } else {
          return []
        }
      },
      ...mapState({
        job: state => state.job.model
      })
    },
    methods: {
      jobTaskObject(jt) {
        if (jt && !this.isContractor) {
          return {
            Task: jt.task ? jt.task.name : '',
            Status: jt.status,
            Contractor: jt.contractor ? jt.contractor.company_name : 'none',
            Phone: jt.contractor ? jt.contractor.phone : 'none'
          }
        } else {
          return {
            Task: jt.task ? jt.task.name : '',
            Status: jt.status,
            Sub: jt.contractor ? jt.contractor.company_name : 'none',
            Phone: jt.contractor ? jt.contractor.phone : 'none'
          }
        }
      },
      checkSpinner(index) {
        if (this.disabled.spinner[index]) {
          return this.disabled.spinner[index].disabled
        }
      },
      showDeleteTaskModal(job_task, index) {
        this.disabled.spinner[index].disabled = true
        this.deleteTask.id = job_task.id
        this.jobTask = job_task
        $('#delete-task-modal').modal('show')
      },
      deleteTheTask(action) {
        if (action === 'delete') {
          this.deleteTheActualTask(this.deleteTask.id)
        }
        $('#delete-task-modal').modal('hide')
      },
      async deleteTheActualTask(id) {
        try {
          const data = await axios.post('/jobTask/delete/', {
            id: id
          })
          this.getBid(this.jobTask.job.id)
        } catch (error) {
          console.log(error)
        }
      },
      async getBid(id) {
        try {
          const {
            data
          } = await axios.get('/job/' + id)
          if (data[0]) {
            this.bid = data[0]
            this.$store.commit('setJob', data[0])
          } else {
            this.bid = data
            this.$store.commit('setJob', data)
          }
          this.$store.commit('setJob', data)
          this.setSpinnerIndexes()
        } catch (error) {
          console.log(error)
          if (
            error.message === 'Not Authorized to access this resource/api' ||
            error.response !== undefined && error.response.status === 403
          ) {
            this.$router.push('/bids')
          }
          Vue.toasted.error('You are unable to view this bid. Please pick the bid you wish to see.')
        }
      },
      goBack() {
        this.$router.go(-1)
      },
      getTotalSubsForTasks(JTask) {
        let length = 0
        for (let i = 0; i < this.jobTasks.length; i++) {
          length = length + this.jobTasks[i].bid_contractor_job_tasks.length
        }
        return length
      },
      setCurrentJobTaskToBidOn(jobTask) {
        this.jobTask = jobTask
        $('#job-task-bid-modal').modal()
      },
      search() {

      },
      goToJobTask(index) {
        console.log(index)
        this.$router.push('/job/task/' + index)
      },
      getLabelClass(bid) {
        return Format.statusLabel(bid.status, User.isCustomer, User.isGeneral(bid, User.id))
      },
      status(bid) {
        return User.status(bid.status, bid)
      },
      setSpinnerIndexes() {
        let spinner = []
        for (let i = 0; i < this.jobTasks.length; i++) {
          spinner.push({disabled: false})
        }
        this.disabled.spinner = spinner
        // Vue.set(this.disabled.spinner, spinner, false)
        // Vue.set(this.disabled, 'disable_' + i, false)
      }
    },
    mounted: function() {
      this.setSpinnerIndexes()
    }
  }
</script>

<style lang="less" scoped>

    .list-card {
        margin-left: 0px;
    }

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
        /*padding-left: .5rem;*/
    }

    .task-status {
        margin-top: -1.25rem;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .pr-1 {
        /*padding-right: .25rem;*/
    }

    .btn-spacing {
        margin-top: 1rem;
        margin-right: .5rem;
        margin-left: .5rem;
    }

</style>

