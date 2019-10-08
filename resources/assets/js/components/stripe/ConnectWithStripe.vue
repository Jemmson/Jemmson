<template>
      <button
              style="background-image: url('/img/blue-on-light.png'); width: 12rem; height: 2.15rem;"
              type="button" class="btn-normal " @click="connectWithStripe"></button>
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
