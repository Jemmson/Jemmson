<template>
    <div>
        <div class="flex space-between" style="margin-bottom: .5rem">
            <label for="licenseName">License Name</label>
            <input id="licenseName" class="form-control-sm box-size" type="text" v-model="name">
        </div>
        <div class="flex space-between" style="margin-bottom: .5rem">
            <label for="licenseNumber">License Number</label>
            <input id="licenseNumber" class="form-control-sm box-size" type="text" v-model="value">
        </div>
        <button @click="deleteLicense()" class="btn btn-normal-red btn-sm w-full">Delete</button>
        <hr>
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
      deleteLicense() {
        this.$emit('delete', {name: this.name, value: this.value})
      }
    }
  }
</script>

<style scoped>
    .box-size {
        height: 35px;
        width: 50%;
    }
</style>
