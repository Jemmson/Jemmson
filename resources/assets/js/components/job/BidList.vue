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
                                    <tr v-for="bid in bids" v-bind:value="bid.id">
                                        <th scope="row">{{ bid.id }}</th>
                                        <td>{{ bid.job_name }}</td>
                                        <td>{{ prettyDate(bid.agreed_start_date) }}</td>
                                        <td>{{ status(bid) }}</td>
                                        <td>{{ bid.bid_price }}</td>
                                        <td>
                                            <button class="btn btn-primary" name="review" @click="openBid(bid)">Review</button>
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
                <bid v-if="showBid" v-on:closeBid="closeBid" :bid="bid">
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
          bids: Array
      },
      data() {
          return {
              showBidList: true,
              showBid: false,
              bid: {},
          }
      },
      methods: {
          status: function (bid) {
              return User.status(bid.status, bid);
          },
          prettyDate: function (date) {
              if (date == null)
                  return '';
              // return the date and ignore the time
              date = date.split(' ');
              return date[0];
          },
          openBid: function (bid) {
              console.log('openBid');

              // clone bid
              this.bid = bid;
              
              // hide show components
              this.showBidList = false;
              this.showBid = true;
          },
          closeBid: function () {
              console.log('closeBid');
              this.showBidList = true;
              this.showBid = false;
          }
      },
      created: function () {
          console.log('created');
      }
  }
</script>