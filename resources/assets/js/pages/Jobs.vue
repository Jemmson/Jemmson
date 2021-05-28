<template>
  <!-- /all bids shown in a list as a customer should see it -->

  <div class="container-fluid" :class="getTopMargin()">
    <v-overlay :value="overlay">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <div v-if="bidsContractorSectionPicked" ref="jobs">
      <search-bar>
        <input type="text" class="form-control" placeholder="Search Jobs" v-model="searchTerm" @keyup="search">
      </search-bar>

      <info-modal-generic
          :text="modalText.jobs"
          title="Jobs"
          modal="jobs"
          :open-dialog="modal.jobs"
          @close-modal="closeModal($event)"
      >
      </info-modal-generic>

      <!--            <v-icon-->
      <!--                    color="primary"-->
      <!--                    @click="showHelp()"-->
      <!--                    class="w-break mt-3 pr-3 w-95 justify-content-end">mdi-information-->
      <!--            </v-icon>-->

      <v-tabs
          center-active
          class="flex justify-content-center"
      >

        <v-tab
            @click="selectTab('inProgress')"
        >
          <v-badge
              v-if="count.in_progress"
              :content="count.in_progress"
          >
            In Progress
          </v-badge>
          <div v-else>
            In Progress
          </div>
        </v-tab>
        <v-tab
            @click="selectTab('approved')"
        >
          <v-badge
              v-if="count.approved"
              :content="count.approved"
          >
            Approved
          </v-badge>
          <div v-else>
            Approved
          </div>

        </v-tab>
        <v-tab
            @click="selectTab('paid')"
        >
          <v-badge
              v-if="count.paid"
              :content="count.paid"
          >
            Paid
          </v-badge>
          <div v-else>
            Paid
          </div>

        </v-tab>
      </v-tabs>

      <v-card
          class="mt-3"
          v-for="bid in sBids" v-bind:key="bid.id"
          v-if="getJobStatus(bid) !== 'paid' && getJobStatus(bid) !== 'approved' && inProgress"
      >
        <div class="flex justify-between align-center">
          <div class="flex align-center">
            <v-card-title>{{ jobName(bid.job_name) }}</v-card-title>
            <div>
              <v-icon
                  v-if="bid.payment_type === 'cash'"
              >mdi-cash
              </v-icon>
              <v-icon
                  v-else-if="bid.payment_type === 'creditCard'"
              >mdi-credit-card
              </v-icon>
            </div>
          </div>
          <v-spacer></v-spacer>
          <div class="flex align-center ml-36">
            <v-btn
                @click="goToJob(bid.id)"
                text
                color="primary"
            >VIEW
            </v-btn>
            <v-icon color="red" @click="showDeleteJobModal(bid)">mdi-delete</v-icon>
          </div>
        </div>
      </v-card>

      <v-simple-table
          v-if="approved"
      >
        <template v-slot:default>
          <thead>
          <tr>
            <th class="text-left">Job Name</th>
            <th class="text-left">Date Approved</th>
            <th class="text-left"></th>
            <th class="text-left"></th>
          </tr>
          </thead>
          <tbody>
          <tr
              v-if="getJobStatus(item) === 'approved'"
              v-for="item in sBids" :key="item.id">
            <td>{{ jobName(item.job_name) }}</td>
            <td>{{ dateOnlyHyphenDBTimestampFromLatestStatus(item.job_statuses) }}</td>
            <td>
              <v-btn
                  @click="goToJob(item.id)"
                  text
                  color="primary"
                  class="w-40"
              >VIEW
              </v-btn>
            </td>
            <td><v-icon color="red" @click="showDeleteJobModal(item)">mdi-delete</v-icon></td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>

      <v-simple-table
          v-if="paid"
      >
        <template v-slot:default>
          <thead>
          <tr>
            <th class="text-left">Job Name</th>
            <th class="text-left">Date Paid</th>
            <th class="text-left"></th>
          </tr>
          </thead>
          <tbody>
          <tr
              v-if="getJobStatus(item) === 'paid'"
              v-for="item in sBids" :key="item.id">
            <td>{{ jobName(item.job_name) }}</td>
            <td>{{ dateOnlyHyphenDBTimestampFromLatestStatus(item.job_statuses) }}</td>
            <td>
              <v-btn
                  @click="goToJob(item.id)"
                  text
                  color="primary"
                  class="w-40"
              >VIEW
              </v-btn>
            </td>
          </tr>
          </tbody>
        </template>
      </v-simple-table>

    </div>
    <tasks v-else>
    </tasks>

    <delete-job-modal
        @action="deleteTheJob($event)"
        title="Do You Wish To Delete This Job?"
    >
    </delete-job-modal>
    <feedback
        page="jobs"
    ></feedback>
  </div>
</template>

<script>
import {mapState, mapMutations} from 'vuex'
import Tasks from './Tasks'
import SearchBar from '../components/shared/SearchBar'
import Card from '../components/shared/Card'
import DeleteJobModal from '../components/job/DeleteJobModal'
import Feedback from '../components/shared/Feedback'
import Status from '../components/mixins/Status'
import InfoModalGeneric from '../components/documentation/InfoModalGeneric'
import Utilities from "../components/mixins/Utilities";

export default {
  name: 'Jobs',
  components: {
    Tasks,
    Card,
    SearchBar,
    Feedback,
    InfoModalGeneric,
    DeleteJobModal
  },
  mixins: [
    Status, Utilities
  ],
  props: {
    user: Object
  },
  data() {
    return {
      count: {
        in_progress: null,
        approved: null,
        paid: null,
      },
      inProgress: true,
      approved: false,
      paid: false,
      modal: {
        jobs: false
      },
      modalText: {
        jobs: ''
      },
      bids: [],
      overlay: false,
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

    getCounts() {
      this.count.in_progress = null;
      this.count.approved = null;
      this.count.paid = null;
      for (let i = 0; i < this.bids.length; i++) {
        let status = this.bids[i].job_statuses[this.bids[i].job_statuses.length - 1].status
        if (status === 'initiated' || status === 'in_progress' || status === 'sent') {
          this.count.in_progress++;
        } else if (status === 'approved') {
          this.count.approved++;
        } else if (status === 'paid') {
          this.count.paid++;
        }
      }
    },

    selectTab(status) {
      if (status === 'inProgress') {
        this.approved = false;
        this.paid = false;
        this.inProgress = true;
      } else if (status === 'approved') {
        this.paid = false;
        this.inProgress = false;
        this.approved = true;
      } else {
        this.inProgress = false;
        this.approved = false;
        this.paid = true;
      }
    },

    async getUser() {
      const {data} = await axios.get('user/current')
      if (data.error) {
        console.log('getting user error', data)
      } else {
        this.user = data;
        this.mountPage();
      }
      this.overlay = false;
    },

    mountPage() {
      this.getBids()
      if (this.$route.path === '/bids/subs') {
        this.toggleBidsContractor(false)
      }
      this.$store.commit('setCurrentPage', this.$router.history.current.path)
    },

    getTopMargin() {
      return this.isCustomer() ? 'customer-top-margin' : 'contractor-top-margin'
    },

    ...mapMutations([
      'toggleBidsContractor'
    ]),

    showHelp() {
      this.$router.push({
        path: '/help/jobs'
      });
    },

    setModalText(status) {
      if (status === 'changed') {
        this.modalText.jobs = `Job Has Changed`
      }
    },

    showModal(modal, status) {
      if (modal === 'jobs') {
        this.setModalText(status)
        this.modal.jobs = true;
      }
    },

    closeModal(modal) {
      if (modal === 'jobs') {
        this.modal.jobs = false;
      }
    },

    getJobStatus(bid) {
      const status = this.formatStatus(this.getJobStatus_latest(bid))

      if (status === 'sent' && this.isCustomer()) {
        return 'Job has been submitted. Please approve the bid.'
      } else {
        return status
      }
    },
    showDeleteJobModal(job) {
      this.deleteJob.id = job.id
      this.job = job
      $('#delete-job-modal').modal('show')
    },
    deleteTheJob(action) {
      if (action === 'delete') {
        this.deleteTheActualJob(this.deleteJob.id)
      }
      $('#delete-job-modal').modal('hide')
    },
    async deleteTheActualJob(id) {
      this.overlay = true;
      try {
        const data = await axios.post('/job/delete', {
          id: id
        })
        // await this.getBids()
        this.getBids()
      } catch (error) {
      }

    },
    isContractor() {
      if (this.user) {
        return this.user.usertype === 'contractor'
      }

    },
    totalNumberOfSubsBiddingForTheJob(jobTasks) {
      if (jobTasks && jobTasks.bid_contractor_job_tasks) {
        return jobTasks.bid_contractor_job_tasks.length
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
    isCustomer() {
      return this.spark.state.user.usertype === 'customer'
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

    // async getBids() {
    //   let url = ''
    //   if (User.isCustomer()) {
    //     url = '/getJobsForCustomer'
    //   } else {
    //     url = '/jobs'
    //   }
    //   const {data} = await axios.get(url)
    //   if (data.error) {
    //
    //   } else {
    //     if (Array.isArray(response.data)) {
    //       this.bids = response.data
    //       this.sBids = this.bids
    //     }
    //     this.overlay = false;
    //   }
    // },

    getBids() {
      let url = ''
      if (User.isCustomer()) {
        url = '/getJobsForCustomer'
      } else {
        url = '/jobs'
      }
      axios.get(url).then((response) => {
        if (Array.isArray(response.data)) {
          this.bids = response.data
          this.sBids = this.bids
          this.getCounts()
        }
        this.overlay = false;
      })
    },

    previewSubForTask(bidId, jobTaskId, subBidId) {
      // console.log(TaskUtil.previewSubForTask(this.bids, bidId, jobTaskId, subBidId))
    }
  },

  mounted() {
    this.overlay = true;
    if (this.user.user === null) {
      this.getUser()
    } else {
      this.mountPage()
    }
  },
  created() {
    document.body.scrollTop = 0 // For Safari
    document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
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

.customer-top-margin {
  margin-top: 10px
}

.contractor-top-margin {
  margin-top: -62px
}

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

.row-sizing {
  padding: 0;
}

</style>