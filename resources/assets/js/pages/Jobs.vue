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
                      @click.native="goToJob(bid.id)">

                    <div class="job">
                        <section ref="job" class="row">
                            <header ref="job_name" class="col-12 page-header-title">
                                {{ jobName(bid.job_name) }}
                            </header>
                            <div class="col-12">
                                <span class="dot" :class="'bg-' + getLabelClass(bid)"></span>
                                <label :class="getLabelClass(bid)">
                                    {{ status(bid) }}
                                </label>
                                <span ref="total_number_of_subs"
                                      class="float-right list-card-info">
                                    {{ totalNumberOfSubsBiddingForTheJob(bid.job_tasks) }} Subs
                                    <i class="fas fa-users"></i></span>

                                <span class="float-right mr-2 list-card-info"
                                      ref="show_number_of_job_tasks">
                                    {{ bid.job_tasks.length }} Tasks
                                    <i class="far fa-check-square"></i>
                            </span>
                            </div>
                        </section>
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
    </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Tasks from './Tasks'
  import SearchBar from '../components/shared/SearchBar'
  import Card from '../components/shared/Card'

  export default {
    name: 'Jobs',
    components: {
      Tasks,
      Card,
      SearchBar,
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
        paginate: ['sBids']
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
        axios.post('/jobs').then((response) => {
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