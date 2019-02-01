<template>

    <div style="background-color: white;">
        <h1 style="color: black;">Register QuickBooks</h1>
        <pre>{{ companyInfo }}</pre>
    </div>

</template>

<script>
  export default {
    name: 'RegisterQuickBooks',
    data() {
      return {
        companyInfo: {}
      }
    },
    mounted: function () {
      axios.get('/quickbooks/getCachedCompanyInfo')
        .then(function(response) {
          console.log('no error')
          // debugger
          this.companyInfo = response.data;
        }.bind(this))
        .catch(function(error) {
          console.log(error)
        });
    },
    methods: {}
  }
</script>

<style>

</style>
