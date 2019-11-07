<template>
    <div>

        <v-container>
            <v-row>
                <v-col>
                    <h1>Customer Information</h1>
                    <v-card>
                        <v-card-title>
                            {{ customer.user ? customer.user.name : '' }}
                        </v-card-title>
                        <v-img
                                v-if="checkForPhoto()"
                                :src="customer.user.photo_url" aspect-ratio="1.7"></v-img>
                        <v-card-text>
                            <div style="font-weight:bold">Email</div>
                            <v-subheader>{{ customer.user ? customer.user.email : '' }}</v-subheader>
                            <v-divider></v-divider>
                            <div style="font-weight:bold">Phone</div>
                            <v-subheader>{{ customer.user ? customer.user.phone : '' }}</v-subheader>
                            <v-divider></v-divider>
                            <div style="font-weight:bold">Notes</div>
                            <v-divider></v-divider>
                            <v-subheader>{{ customer.user ? customer.user.customer.notes : '' }}</v-subheader>
                        </v-card-text>
                    </v-card>
                    <v-spacer></v-spacer>
                    <v-card class="mt-4">
                        <v-card-title>Address</v-card-title>
                        <v-card-text>
                            <v-subheader>{{ customer.user ? customer.user.location.address_line_1 : '' }}</v-subheader>
                            <v-subheader
                                v-if="checkForAddressLine2()"
                            >{{ customer.user ? customer.user.location.address_line_2 : '' }}
                            </v-subheader>
                            <v-flex>
                                <v-subheader>{{ customer.user ? customer.user.location.city : '' }}</v-subheader>
                                <v-subheader>{{ customer.user ? customer.user.location.state : '' }}</v-subheader>
                                <v-subheader>{{ customer.user ? customer.user.location.zip : '' }}</v-subheader>
                                <v-subheader>{{ customer.user ? customer.user.location.country : '' }}</v-subheader>
                            </v-flex>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
  export default {
    name: 'CustomerInfo',
    props: {
      customerId: Number
    },
    data() {
      return {
        customer: []
      }
    },
    created() {
      this.getCustomer()
      window.location.href = '#'
    },
    methods: {
      checkForPhoto() {
        if (this.customer.user) {
          return this.customer.user.photo_url !== null
        }
      },
      checkForAddressLine2() {
        if (this.customer.location) {
          return this.customer.user.location.address_line_2 !== null
        }
      },
      async getCustomer() {
        try {
          const data = await axios.get('/getCustomer/' + this.customerId)
          this.customer = data.data
        } catch (error) {
          console.log('error')
        }
      }
    }
  }
</script>

<style scoped>

</style>