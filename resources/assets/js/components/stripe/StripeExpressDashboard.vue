<template>
      <button type="button" class="btn btn-primary" @click="login"><span>Login into Stripe Dashboard</span></button>
</template>

<script>
export default {
  // /Set up an express account with our stripe platform
  methods: {
    login() {
      axios.post('/stripe/express/dashboard').then((response) => {
        console.log(response.data);
        window.location = response.data.url;
      });
    }
  }
}
</script>
