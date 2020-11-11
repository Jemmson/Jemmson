<template>
  <v-card>
    <documentation-base
        title="Home Page Help"
    >
      <p>This page shows your basic information which is under the details section in the above bar.</p>
      <h5>Stripe -</h5>
      <p>If you are set up with Stripe then you will also see the stripe logo in the above bar as well. When you click
        on
        this you will be able to click another button that will take you to your Stripe dashboard. On the Stripe
        dashboard,
        you will be able to update your information and also see all of your transactions.</p>

      <h5>Demo -</h5>
      <p>You can go to the settings icon in the top right corner and change your profile pic and also be able to
        change other demographic information.</p>

      <h5>Jobs -</h5>
      <p>At the bottom of the page, you will be able to see the total number of jobs that you currently have.</p>

      <h5>Subs -</h5>
      <p>You can also see the total number of Subs that you have worked with and then be able to see their demographic
        information.</p>

    </documentation-base>
  </v-card>
</template>

<script>

import DocumentationBase from "./DocumentationBase";

export default {
  components: {
    DocumentationBase
  }
}
</script>

<style scoped>

p {
  color: black;
  font-size: 12pt;
}

</style>