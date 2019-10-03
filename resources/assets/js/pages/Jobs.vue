<template>
    <!-- /all bids shown in a list as a customer should see it -->
    <div class="container-fluid">

        <div v-if="bidsContractorSectionPicked" ref="jobs">
            <search-bar>
                <input type="text" class="form-control" placeholder="Search Jobs" v-model="searchTerm" @keyup="search">
            </search-bar>

            <!-- <paginate name="sBids" :list="sBids" :per="6" tag="div" class="paginated mt-4" v-show="sBids.length > 0"> -->

            <div class="mt-4 mb-1">

                <card class="list-card"
                      v-for="bid in sBids" v-bind:key="bid.id"
                      classes="pt-half-rem pb-half-rem">

                    <div class="job">
                        <section ref="job" class="row">
                            <header ref="job_name" class="col-12 page-header-title text-center">
                                {{ jobName(bid.job_name) }}
                            </header>
                            <div class="flex flex-col w-full ">
                                <div class="flex align-content-baseline status-height justify-items-center">
<!--                                    <span class="dot ml-half-rem" :class="'bg-' + getLabelClass(bid)"></span>-->
                                    <div class="fs-1rem ml-half-rem text-center w-full" :class="getLabelClass(bid)">
                                        {{ status(bid) }}
                                    </div>
                                </div>

                                <div v-if="isContractor()">
                                     <span ref="total_number_of_subs"
                                           class="float-right list-card-info">
                                            {{ totalNumberOfSubsBiddingForTheJob(bid.job_tasks) }} Subs
                                        <i class="fas fa-users"></i>
                                     </span>

                                    <span class="float-right mr-2 list-card-info"
                                          ref="show_number_of_job_tasks">
                                            {{ bid.job_tasks.length }} Tasks
                                        <i class="far fa-check-square"></i>
                                    </span>
                                </div>
                            </div>
                        </section>
                        <div class="flex mt-1rem">
                            <button class="btn btn-normal btn-sm w-full mr-1rem" @click="showDeleteJobModal(bid)">DELETE
                            </button>
                            <button @click.native="goToJob(bid.id)" class="btn btn-normal btn-sm w-full ml-1rem">SELECT</button>
                        </div>
                    </div>
                </card>
            </div>
            <!-- </paginate> -->

            <!-- <div class="card mb-4 mt-3">
            <div class="card-body d-flex justify-content-center">
              <paginate-links for="sBids" :async="true" :limit="2" :show-step-links="true">
              </paginate-links>
            </div>
            </div> -->
        </div>
        <tasks v-else>
        </tasks>

        <delete-task-modal
                @action="deleteTheJob($event)"
        >
        </delete-task-modal>

    </div>
</template>

<script>
  import { mapState, mapMutations } from 'vuex'
  import Tasks from './Tasks'
  import SearchBar from '../components/shared/SearchBar'
  import Card from '../components/shared/Card'
  import DeleteTaskModal from '../components/job/DeleteTaskModal'

  export default {
    name: 'Jobs',
    components: {
      Tasks,
      Card,
      SearchBar,
      DeleteTaskModal
    },
    props: {
      user: Object
    },
    data() {
      return {
        bids: [],
        sBids: [],
        showBid: false,
        bidIndex: 0,
        searchTerm: '',
        paginate: ['sBids'],
        disabled: {
          deleteJob: false
        },
        deleteJob: {
          id: ''
        },
        job: {}
      }
    },
    watch: {
      '$route'(to, from) {
        // get the bids
        this.getBids()
      }
    },
    computed: {
      ...mapState({
        page: state => state.page,
        bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
      })
    },
    methods: {
      ...mapMutations([
        'toggleBidsContractor'
      ]),
      showDeleteJobModal(job) {
        this.deleteJob.id = job.id
        this.job = job
        $('#delete-task-modal').modal('show')
      },
      deleteTheJob(action) {
        if (action === 'delete') {
          this.deleteTheActualJob(this.deleteJob.id)
        }
        $('#delete-task-modal').modal('hide')
      },
      async deleteTheActualJob(id) {
        try {
          const data = await axios.post('/job/delete/', {
            id: id
          })
          this.getBid(this.job.id)
        } catch (error) {
          console.log('error')
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
      isContractor(){
        if (this.user) {
          return this.user.usertype === 'contractor'
        }

      },
      totalNumberOfSubsBiddingForTheJob(jobTasks) {
        if (jobTasks) {
          let total = 0
          for (let i = 0; i < jobTasks.length; i++) {
            if (jobTasks[i].bid_contractor_job_tasks) {
              total = total + jobTasks[i].bid_contractor_job_tasks.length
            }
          }
          return total
        }
      },
      search() {
        this.sBids = this.bids.filter((bid) => {
          if (this.searchTerm === '' || this.searchTerm.length <= 1) {
            return true
          }
          return bid.job_name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
        })
        if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
          this.$refs.paginator.goToPage(1)
        }
      },
      getLabelClass(bid) {
        return Format.statusLabel(bid.status, User.isCustomer, this.isGeneral(bid))
      },
      jobName(name) {
        return Format.jobName(name)
      },
      isGeneral(bid) {
        if (bid !== null && this.user) {
          return bid.contractor_id === this.user.id
        }
        return false
      },
      status(bid) {
        if (bid !== null && this.user !== undefined) {
          return User.status(bid.status, bid, this.user)
        }
      },
      prettyDate(date) {
        if (date == null)
          return ''
        // return the date and ignore the time
        date = date.split(' ')
        return date[0]
      },
      goToJob(id) {
        this.$router.push('/bid/' + id)
      },
      getBids() {
        let url = ''
        if (User.isCustomer()) {
          url = 'getJobsForCustomer'
        } else {
          url = 'jobs'
        }
        axios.get(url).then((response) => {
          if (Array.isArray(response.data)) {
            this.bids = response.data
            this.sBids = this.bids
          }
        })
      },
      previewSubForTask(bidId, jobTaskId, subBidId) {
        console.log(TaskUtil.previewSubForTask(this.bids, bidId, jobTaskId, subBidId))
      }
    },
    mounted() {

      if (this.$route.path === '/bids/subs') {
        this.toggleBidsContractor(false)
      }

      this.$store.commit('setCurrentPage', this.$router.history.current.path)
    },
    created() {
      this.getBids()
      Bus.$on('bidUpdated', (payload) => {
        this.getBids()
      })
      Bus.$on('jobCanceled', (payload) => {
        this.getBids()
      })
      Bus.$on('previewSubForTask', (payload) => {
        this.previewSubForTask(payload[0], payload[1], payload[2])
      })

    },
  }
</script>

<style lang="less" scoped>

    .list-card {
        margin-left: 0px;
    }

    .v-height {
        height: 125vh;
    }

    .bid-btn {
        height: 100%;
        padding-top: 1.25rem;
        padding-right: .5rem;
        padding-left: .5rem;
    }

    .job-section {
        height: 3.75rem;
    }

    #bids-item {
        background-color: #2779BD;
    }

    .customer {
        display: flex;
        justify-content: center;
    }

    .customer span {
        margin: 1rem 2rem;
    }

    .card-1 {
        width: 90%;
        display: flex;
        align-items: stretch;
    }

    .search {
        flex-direction: column;
        padding-top: 1rem;
        padding-bottom: 2rem;
    }

    .bid {
        flex-direction: column;
    }

    label {
        width: 100%;
        padding: .75rem 0rem;
        font-size: 2rem;
    }

    button {
        margin-bottom: 1rem;
    }
</style>