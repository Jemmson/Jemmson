<template>
    <!-- /all bids shown in a list as a customer should see it -->
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel">
                    <!-- <div class="panel-heading">Dashboard</div> -->
                    <div class="panel-body">
                        Hello, {{ user.name }}
                        <br> These are your bids
                    </div>
                </div>
            </div>
            <transition name="">
                <div class="col-md-8 col-md-offset-2" v-show="showBidList">
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
                                        <td>{{ status(bid.status) }}</td>
                                        <td>{{ bid.bid_price }}</td>
                                        <td>
                                            <button class="btn btn-primary" @click="openBid(bid)">Review</button>
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
                <div class="col-md-8 col-md-offset-2" v-if="showBid">
                    <div class="panel panel-default">
                        <!-- <div class="panel-heading">Dashboard</div> -->
                        <div class="panel-body">
                            <form role="form">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <span style="float: right;">
                                            <button class="btn btn-danger btn-close" @click.prevent="closeBid">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>
                                        </span>
                                    </div>

                                    <!-- /show all bid information -->
                                    <customer-bid :bid="bid">
                                    </customer-bid>
                                    
                                    <div v-if="!bidApproved">
                                        <div class="form-group col-md-6">
                                            <label for="area">City</label>
                                            <input type="text" class="form-control" id="area" v-model="bidForm.area">
                                        </div>
                                        <div class="form-group col-md-6" :class="{'has-error': bidForm.errors.has('agreed_start_date')}">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" v-model="bidForm.agreed_start_date">
                                            <span class="help-block" v-show="bidForm.errors.has('agreed_start_date')">
                                                {{ bidForm.errors.get('agreed_start_date') }}
                                            </span>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <button class="btn btn-success" @click.prevent="approve">Approve</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
              bidApproved: false,
              bid: {},
              bidForm: new SparkForm({
                  id: 0,
                  agreed_start_date: '',
                  end_date: '',
                  area: '',
                  status: '',
              }),
          }
      },
      methods: {
          approve: function () {
              console.log('approve');
              Spark.post('/api/job/approve/' + this.bidForm.id, this.bidForm)
                  .then((response) => {
                      console.log(response);
                      this.$toasted.success('Job Approved');
                  }).catch((error) => {
                      console.log(error);
                      this.bidForm.errors.errors = this.bidForm.errors.errors.errors;
                      this.$toasted.error('Whoops! Something went wrong! Please try again.');
                  });
          },
          status: function (status) {
              return Language.lang()[status].customer;
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

              let status = this.status(bid.status);

              // clone bid
              this.bid = JSON.parse(JSON.stringify(bid));
              this.bid.status = status;

              this.bidApproved = (status === 'Approved' || status === 'Waiting on Contractor to Submit Final Bid');
              
              // set up form inputs
              this.bidForm.id = bid.id;
              this.bidForm.status = status;
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