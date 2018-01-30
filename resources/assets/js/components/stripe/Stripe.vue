<template>
      <div class="col-md-12">
        <div class="panel panel-default">
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
      </div>
</template>

<script>
export default {
  computed: {
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
  }
}
</script>