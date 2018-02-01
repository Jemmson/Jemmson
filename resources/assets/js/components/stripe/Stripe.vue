<template>
<div class="col-md-12">
    <div class="panel panel-default" v-if="isContractor">
        <!-- <div class="panel-heading">Dashboard</div> -->
        <div class="panel-body">
            <span>stripe stuff test</span>
            <br>

            <connect-with-stripe v-if="showConnectBtn">
            </connect-with-stripe>

            <express-dashboard-stripe v-if="showDashboardLoginBtn">
            </express-dashboard-stripe>
        </div>
    </div>
    <div class="panel panel-default" v-if="isCustomer && notSignedUp">
        <div class="panel-body">
            <signup-with-stripe>
            </signup-with-stripe>
        </div>
    </div>
</div>
</template>

<script>
export default {
  computed: {
      notSignedUp() {
          return !User.hasStripeId();
      },
      isContractor() {
          return User.isContractor();
      },
      isCustomer() {
          return User.isCustomer();
      },
      showConnectBtn() {
          if (Spark.state.user.contractor === null)
            return false;

          return Spark.state.user.contractor.stripe_express === null;
      },
      showDashboardLoginBtn() {
          if (Spark.state.user.contractor === null)
            return false;

          return Spark.state.user.contractor.stripe_express !== null;
      }
  },
  methods: {
  }
}
</script>