<template>
    <div>
        <v-container>
            <v-row>
                <v-col>
                    <h1>Contractor Information</h1>
                    <v-card>
                        <v-card-title>
                            {{ contractor && contractor.user ? contractor.user.contractor.company_name : '' }}
                        </v-card-title>
                        <v-img
                                v-if="checkForPhoto()"
                                :src="contractor.user.photo_url" aspect-ratio="1.7"></v-img>
                        <v-card-text>
                            <div style="font-weight:bold">Name</div>
                            <v-subheader>{{ contractor && contractor.user ? contractor.user.name : '' }}</v-subheader>

                            <div style="font-weight:bold">Email</div>
                            <v-subheader>{{ contractor && contractor.user ? contractor.user.email : '' }}</v-subheader>

                            <div style="font-weight:bold">Phone</div>
                            <v-subheader>{{ contractor && contractor.user ? contractor.user.phone : '' }}</v-subheader>
                        </v-card-text>
                    </v-card>
                    <v-spacer></v-spacer>
                    <v-card class="mt-4">
                        <v-card-title>Address</v-card-title>
                        <v-card-text>
                            <v-subheader>{{ contractor && contractor.user ? contractor.user.location.address_line_1 : ''
                                }}
                            </v-subheader>
                            <v-subheader
                                    v-if="checkForAddressLine2()"
                            >{{ contractor && contractor.user ? contractor.user.location.address_line_2 : '' }}
                            </v-subheader>
                            <v-flex>
                                <v-subheader>{{ contractor && contractor.user ? contractor.user.location.city : '' }}
                                </v-subheader>
                                <v-subheader>{{ contractor && contractor.user ? contractor.user.location.state : '' }}
                                </v-subheader>
                                <v-subheader>{{ contractor && contractor.user ? contractor.user.location.zip : '' }}
                                </v-subheader>
                                <v-subheader>{{ contractor && contractor.user ? contractor.user.location.country : ''
                                    }}
                                </v-subheader>
                            </v-flex>
                        </v-card-text>
                    </v-card>
                    <v-spacer></v-spacer>
                    <v-card class="mt-4">
                        <v-card-title>Licenses</v-card-title>
                        <v-card-text>
                            <v-simple-table>
                                <template v-slot:default>
                                    <thead>
                                    <tr>
                                        <th>License Name</th>
                                        <th>License Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(license, i) in contractor.user.licenses" :key="i">
                                        <td>{{ license.name }}</td>
                                        <td>{{ license.value }}</td>
                                    </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
  export default {
    name: 'ContractorInfo',
    props: {
      contractorId: Number
    },
    data() {
      return {
        contractor: []
      }
    },
    created() {
      this.getContractor()
    },
    methods: {
      checkForPhoto() {
        if (this.contractor && this.contractor.user) {
          return this.contractor.user.photo_url !== null
        }
      },
      checkForAddressLine2() {
        if (this.contractor.location) {
          return this.contractor.user.location.address_line_2
        }
      },
      async getContractor() {
        try {
          const data = await axios.get('/getContractor/' + this.contractorId)
          this.contractor = data.data
        } catch (error) {
          console.log('error')
        }
      }
    }
  }
</script>

<style scoped>

</style>