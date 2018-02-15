<template>
    <!-- /all bids shown in a list as a customer should see it -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <!-- <div class="panel-heading">Dashboard</div> -->
                    <div class="panel-body">
                        Hello, {{ user.name }}
                        <br> These are your bids
                    </div>
                </div>
            </div>
            <transition name="">
                <div class="col-md-12" v-show="showBidList">
                    <div class="panel panel-default">
                        <!-- <div class="panel-heading">Dashboard</div> -->
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Task Name</th>
                                        <th scope="col">Start Date</th>
                                        <th scope="col">status</th>
                                        <th scope="col">Price</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(bid, index) in bids" v-bind:key="bid.id">
                                        <th scope="row">{{ bid.id }}</th>
                                        <td>{{ bid.job_name }}</td>
                                        <td>{{ prettyDate(bid.agreed_start_date) }}</td>
                                        <td>{{ status(bid) }}</td>
                                        <td>{{ bid.bid_price }}</td>
                                        <td>
                                            <button class="btn btn-primary" name="review" @click="openBid(index)">Review</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </transition>
            <!-- /end col-md-8 -->
            <transition name="slide-fade">
                <bid v-if="showBid && bids[bidIndex] !== undefined" v-on:closeBid="closeBid" :bid="bids[bidIndex]">
                </bid>
            </transition>
            <!-- /end transition -->
        </div>
    </div>
</template>

<script>
  export default {
      props: {
          user: Object,
          pbids: Array
      },
      data() {
          return {
              bids: [],
              showBidList: true,
              showBid: false,
              bidIndex: 0
          }
      },
      methods: {
          status(bid) {
              return User.status(bid.status, bid);
          },
          prettyDate(date) {
              if (date == null)
                  return '';
              // return the date and ignore the time
              date = date.split(' ');
              return date[0];
          },
          openBid(index) {
              console.log('openBid');

              // clone bid
              this.bidIndex = index;
              
              // hide show components
              this.showBidList = false;
              this.showBid = true;
          },
          closeBid() {
              console.log('closeBid');
              this.showBidList = true;
              this.showBid = false;
          },
          getBids() {
              console.log('getBids');
              axios.post('/jobs').then((response) => {
                  this.bids = response.data;
              });
          },
          previewSubForTask(bidId, taskId, subBidId) {
                console.log(TaskUtil.previewSubForTask(this.bids, bidId, taskId, subBidId));
          }
      },
      created() {
          Bus.$on('bidUpdated', (payload) => {
              this.getBids();
          });
          Bus.$on('previewSubForTask', (payload) => {
              this.previewSubForTask(payload[0], payload[1], payload[2]);
          });
      },
      mounted() {
          this.bids = this.pbids;
          const bidId = User.getParameterByName('jobId');
          if (bidId !== null && bidId !== '') {
              this.openBid(User.getBidIndex(bidId, this.bids));
          }
          
      }
  }
</script>