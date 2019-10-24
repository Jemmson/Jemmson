<template>
    <div>
        <label for="licenseName">License Name</label>
        <input id="licenseName" type="text" v-model="name">
        <label for="licenseNumber">License Number</label>
        <input id="licenseNumber" type="text" v-model="value">
        <button @click="deleteLicense()" class="btn btn-normal btn-sm">Delete</button>
    </div>
</template>

<script>
  export default {
    name: 'AddLicenseBox',
    data() {
      return {
        name: '',
        value: ''
      }
    },
    methods: {
      deleteLicense(){
        this.$emit('delete', {name: this.name, value: this.value})
      }
    }
  }
</script>

<style>

</style>
