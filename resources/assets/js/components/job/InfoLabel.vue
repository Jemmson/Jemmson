<template>
    <h3>{{ label }}:
        <span>{{ value }}</span>
    </h3>
</template>

<script>
  export default {
    name: 'InfoLabel',
    props: {
      label: {
        type: String
      },
      value: {
        type: String
      }
    }
  }
</script>

<style>

</style>
