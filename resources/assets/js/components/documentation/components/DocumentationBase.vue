<template>
  <v-card-text>
    <v-card-subtitle>{{ title }}</v-card-subtitle>
    <slot style="color: black"></slot>
  </v-card-text>
</template>

<script>
export default {
  props: {
    title: String,
    text: String,
  }
}
</script>

<style scoped>

</style>