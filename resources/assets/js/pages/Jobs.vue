<template>
  <!-- /all bids shown in a list as a customer should see it -->
  <div class="flex flex-col">
    <search-bar>
      <input type="text" class="flex" placeholder="Search Jobs" v-model="searchTerm" @keyup="search">
    </search-bar>
    <paginate ref="paginator" name="sBids" :list="sBids" :per="6" class="paginated" v-show="sBids.length > 0">
      <div class="card flex-row" v-for="bid in paginated('sBids')" v-bind:key="bid.id" style="z-index: 2;" @click="goToBid(bid.id)">
          <span :class="getLabelClass(bid.status)" class="text-white text-center">{{ status(bid) }}</span>
          <span :class="getLabelClass(bid.status)" class="text-white" v-if="user.usertype !== 'customer'">{{ bid.customer.name }}</span>
          <span :class="getLabelClass(bid.status)" class="text-white">{{ jobName(bid.job_name) }}</span>
          <span >click to view</span>
      </div>
    </paginate>
    <div class="card">
      <h4>
        <paginate-links for="sBids" :limit="2" :show-step-links="true">
        </paginate-links>
      </h4>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      user: Object
    },
    data () {
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
      '$route' (to, from) {
        // get the bids
        this.getBids ();
      }
    },
    methods: {
      search () {
        this.sBids = this.bids.filter ((bid) => {
          if (this.searchTerm === '' || this.searchTerm.length <= 1) {
            return true;
          }
          return bid.job_name.toLowerCase ().search (this.searchTerm.toLowerCase ()) > -1;
        });
        if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
          this.$refs.paginator.goToPage (1);
        }
      },
      getLabelClass (status) {
        return Format.statusLabel (status);
      },
      jobName (name) {
        return Format.jobName (name);
      },
      status (bid) {
        return User.status (bid.status, bid);
      },
      prettyDate (date) {
        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split (' ');
        return date[0];
      },
      goToBid (id) {
        this.$router.push ('/bid/' + id);
      },
      getBids () {
        console.log ('getBids');
        axios.post ('/jobs').then ((response) => {
          if (Array.isArray(response.data)) {
            this.bids = response.data;
            this.sBids = this.bids;
          }
        });
      },
      previewSubForTask (bidId, jobTaskId, subBidId) {
        console.log (TaskUtil.previewSubForTask (this.bids, bidId, jobTaskId, subBidId));
      }
    },
    created () {
      this.getBids ();
      Bus.$on ('bidUpdated', (payload) => {
        this.getBids ();
      });
      Bus.$on ('previewSubForTask', (payload) => {
        this.previewSubForTask (payload[0], payload[1], payload[2]);
      });

    },
  }
</script>

<style scoped>
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