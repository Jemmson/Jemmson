<template>
    <div>
        <div v-for="(value, name) in data" :key="name">
            <div
                    v-if="name === 'Contractor'"
                    class="flex space-between">
                <div>{{ name }}</div>
                <div class="lookLikeALink capitalize"
                     @click="viewContractorInfo(id)">{{ value }}</div>
            </div>
            <div
                    v-if="name !== 'Contractor'"
                    class="flex space-between">
                <div>{{ name }}</div>
                <div class="capitalize">{{ value }}</div>
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
      },
      id: {
        type: Number
      }
    },
    computed: {},
    methods: {
      viewContractorInfo(id) {
        this.$router.push({name: 'contractor-info', params: {contractorId: id}})
      }
    }
  }
</script>

<style>
    .lookLikeALink {
        text-decoration: underline;
        color: #1976d2 !important;
    }
</style>
