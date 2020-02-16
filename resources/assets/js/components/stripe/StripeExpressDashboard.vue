<template>

  <v-card
          class="margins-1rem"
          v-if="showDashboardLoginBtn"
          @click="login"
          :disabled="busy">
    <v-card-actions>
      <v-btn
        color="primary"
        text
      >
        <span v-if="busy">
      <i class="fa fa-btn fa-spinner fa-spin"></i>
    </span>
        <span>Login into Stripe Dashboard</span>
      </v-btn>
    </v-card-actions>
  </v-card>

</template>

<script>
export default {
  // /Set up an express account with our stripe platform
  data: function() {
    return {
      busy: false
    }
  },
  computed: {
      showDashboardLoginBtn() {
          if (Spark.state.user.contractor === null)
            return false;
          return Spark.state.user.contractor.stripe_express !== null;
      }
  },
  methods: {
    login() {
      this.busy = true;
      axios.post('/stripe/express/dashboard').then((response) => {
        console.log(response.data);
        this.busy = false;
        window.location = response.data.url;
      });
    }
  }
}
</script>
