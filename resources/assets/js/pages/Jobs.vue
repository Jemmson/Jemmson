<template>
    <!-- /all bids shown in a list as a customer should see it -->
    <div class="container-fluid">
        <div v-if="bidsContractorSectionPicked" ref="jobs">
            <search-bar>
                <input type="text" class="form-control" placeholder="Search Jobs" v-model="searchTerm" @keyup="search">
            </search-bar>

            <!-- <paginate name="sBids" :list="sBids" :per="6" tag="div" class="paginated mt-4" v-show="sBids.length > 0"> -->

            <div class="mt-4 mb-1" ref="all_bids">

                <card class="list-card"
                      v-for="bid in sBids" v-bind:key="bid.id"
                      @click.native="goToJob(bid.id)">

                    <section class="row">
                        <header class="col-12 page-header-title">
                            {{ jobName(bid.job_name) }}
                        </header>
                        <div class="col-12">
                            <span class="dot" :class="'bg-' + getLabelClass(bid)"></span>
                            <label :class="getLabelClass(bid)">
                                {{ status(bid) }}
                            </label>
                            <span class="float-right list-card-info">2 Subs
                                <i class="fas fa-users"></i></span>

                            <span class="float-right mr-2 list-card-info" ref="show_number_of_job_tasks">Tasks
                <i class="far fa-check-square"></i>
              </span>
                        </div>
                    </section>
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
  import { mapState, mapMutations } from 'vuex'
  import Tasks from './Tasks'



  export default {
    components: [
      Tasks
    ],
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
        return Format.statusLabel(bid.status, User.isCustomer(), User.isGeneral(bid))
      },
      jobName(name) {
        return Format.jobName(name)
      },
      status(bid) {
        return User.status(bid.status, bid)
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
        console.log('getBids')
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