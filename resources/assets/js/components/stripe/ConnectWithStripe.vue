<template>
      <button type="button" class="stripe-connect" @click="connectWithStripe"><span>Connect With Stripe</span></button>
</template>

<script>
export default {
  // /Set up an express account with our stripe platform
  methods: {
    connectWithStripe() {
      let connectLink = "https://connect.stripe.com/express/oauth/authorize?client_id=" + Spark.stripeClientId +"&state={STATE_VALUE}";
      window.location = connectLink;
    }
  }
}
</script>
