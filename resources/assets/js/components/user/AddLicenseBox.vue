<template>
    <div>
        <v-container>
            <v-row>
                <v-col>
                    <v-text-field
                            label="License Name"
                            v-model="name"
                    ></v-text-field>
                    <v-text-field
                            label="License Number"
                            v-model="value"
                    ></v-text-field>
                    <v-btn
                            color="red"
                            @click="deleteLicense()"
                    >Delete
                    </v-btn>
                    <hr>
                </v-col>
            </v-row>
        </v-container>
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
