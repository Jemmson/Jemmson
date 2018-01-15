<template>
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
            <div class="col-md-8 col-md-offset-2">
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
                                    <td>{{ bid.status }}</td>
                                    <td>{{ bid.bid_price }}</td>
                                    <td>
                                        <button class="btn btn-primary" @click="openBid(bid)">Edit</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <transition name="slide-fade">
                <div class="col-md-8 col-md-offset-2" v-if="!showBidList">
                    <div class="panel panel-default">
                        <!-- <div class="panel-heading">Dashboard</div> -->
                        <div class="panel-body">
                            <form role="form">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="area">City</label>
                                        <input type="text" class="form-control" id="area" name="area" required v-model="bidForm.area">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="area">Start Date</label>
                                        <input type="text" class="form-control" id="area" name="area" required v-model="bidForm.start_date">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="area">End Date</label>
                                        <input type="text" class="form-control" id="area" name="area" required v-model="bidForm.end_date">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </transition>
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
          bidForm: new SparkForm({
              start_date: '',
              end_date: '',
              area: ''
          }),
      }
    },
    methods: {
        prettyDate: function (date) {
        if (date == null)
          return '';
        // return the date and ignore the time
        date = date.split(' ');
        return date[0];
      },
      openBid: function (bid) {
          this.showBidList = this.showBidList ? false : true;
          console.log(bid);
      }
    },
    created: function () {
      console.log('created');
    }
  }
</script>