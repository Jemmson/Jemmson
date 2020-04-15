<template>

  <v-card
          class="margins-1rem"
          v-if="showDashboardLoginBtn"
          @click="login"
          :loading="busy">
    <v-card-actions>
      <v-btn
        color="primary"
        text
      >
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
          return Spark.state.user.contractor !== null
                  && Spark.state.user.contractor.customer_stripe_id !== null
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
