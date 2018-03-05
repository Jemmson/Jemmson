<template>
  <button v-if="showDashboardLoginBtn" type="button" class="btn btn-primary" @click="login" :disabled="busy">
    <span v-if="busy">
      <i class="fa fa-btn fa-spinner fa-spin"></i>
    </span>
    <span>Login into Stripe Dashboard</span>
  </button>
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
