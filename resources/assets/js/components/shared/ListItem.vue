<template>
    <div class="flex space-between">
        <p class="ml-1" style="color:grey">{{ left }}</p>
        <p class="mr-1" style="color:grey" v-if="right !== undefined">{{ right }}</p>
    </div>
</template>

<script>
  export default {
    props: ['right', 'left', 'noDivider']
  }
</script>

<style lang="less">
    p {
        margin-bottom: 0rem;
    }
</style>
