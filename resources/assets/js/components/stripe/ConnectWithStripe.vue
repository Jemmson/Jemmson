<template>
      <button type="button" class="btn-normal stripe-connect" @click="connectWithStripe"><span>Connect With Stripe</span></button>
</template>

<script>
export default {
  // /Set up an express account with our stripe platform
  methods: {
    connectWithStripe() {
      let connectLink = "https://connect.stripe.com/express/oauth/authorize?client_id=" 
      + Spark.stripeClientId +"&state="+ this.$route.path
      + "&stripe_user[email]=" + Spark.state.user.email
      + "&stripe_user[country]=US"
      + "&stripe_user[phone_number]=" + Spark.state.user.phone;

      window.location = connectLink;
    }
  }
}
</script>
