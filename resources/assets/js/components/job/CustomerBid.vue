<template>
    <!-- /all details of a bid as a customer should see it -->
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <label>Job Name: </label>
                <label class="label label-primary">
                    {{ bid.job_name }}
                </label>
            </div>
            <div class="col-md-6">
                <label>Status: </label>
                <label class="label label-success">
                    {{ bid.status }}
                </label>
            </div>
            <!-- /end detail header -->
            <div style="padding-top: 40px"></div>
            <div class="col-md-6">
                <p>
                    <label>
                        Location:
                    </label>
                </p>
                <p>
                    {{ bid.address_line_1 }}
                </p>
                <p>
                    {{ bid.city }}, {{ bid.state }} {{ bid.zip }}
                </p>
            </div>
            <div class="col-md-6">
                <p><label for="price">Prices:</label></p>
                <p>
                    Total Job Price: <label class="label label-info">${{ bid.bid_price }}</label>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
      props: {
          bid: Object
      },
      data() {
          return {
          }
      },
      methods: {
      },
      created: function () {
          console.log('created');
      }
  }
</script>