<template>
    <div>
        <div v-for="(value, name) in data">
            <div class="flex space-between">
                <div>{{ name }}</div>
                <div>{{ value }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
      name: 'HorizontalTable',
      props: {
        data: {
          type: Object
        }
      },
      computed: {

      }
    }
</script>

<style>

</style>
