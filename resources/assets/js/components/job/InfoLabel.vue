<template>
    <label>{{ label }}:
        <span>{{ value }}</span>
    </label>
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
