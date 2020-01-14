<template>
    <v-btn
            v-if="isACustomer !== 'customer'"
    >Image Association
    </v-btn>
</template>

<script>
  export default {
    name: 'ImageAssociation',
    data() {
      return {
        isACustomer: Spark.state.user.usertype
      }
    },
    props: {
      isCustomer: {
        type: Boolean
      }
    },
    mounted() {
      if (this.isACustomer === 'customer') {
        this.$router.push('/home')
      } else {
        this.$store.commit('setCurrentPage', this.$router.history.current.path)
      }
    }
  }
</script>

<style scoped>

</style>