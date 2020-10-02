<template>
  <div>
    <v-container>
      <v-row>
        <v-col>
          <h1>Contractor Information</h1>
          <v-card>
            <v-card-title class="w-break">
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
              <a
                  v-if="getAddress() !== 'Address Not Available'"
                  target="_blank"
                  :href="'https://www.google.com/maps/search/?api=1&query=' + getAddress()">
                <!--                                    <i class="fas fa-map-marker icon"></i>-->
                <div class="flex flex-col location" v-if="jobLocationHasBeenSet">
                  <div>
                    {{ getAddressLine1 }}
                  </div>
                  <div>
                    {{ getCity }}, {{ getLocationState }} {{ getZip }}
                  </div>
                </div>
              </a>

            </v-card-text>
          </v-card>
          <v-spacer></v-spacer>
          <v-card class="mt-4">
            <v-card-title>Licensed and Bonded</v-card-title>
            <hr>
            <v-card-subtitle>Licenses</v-card-subtitle>
            <v-card-text v-if="!hasLicenses()">
              <v-card-subtitle class="error_widget error--text">This Contractor Is Not Licensed or Bonded</v-card-subtitle>
            </v-card-text>
            <v-card-text v-else>
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                  <tr>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>State</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(license, i) in getLicenses()" :key="i">
                    <td>{{ license.type }}</td>
                    <td>{{ license.name }}</td>
                    <td>{{ license.number }}</td>
                    <td>{{ license.state }}</td>
                  </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <feedback
        page="ContractorInfo"
    ></feedback>
  </div>
</template>

<script>

import Feedback from '../components/shared/Feedback'

export default {
  components: {
    Feedback
  },
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
    document.body.scrollTop = 0 // For Safari
    document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
  },
  computed: {
    jobLocationHasBeenSet() {
      if (this.contractor && this.contractor.user && this.contractor.user.location) {
        return true
      } else {
        return false
      }
    },
    getAddressLine1() {
      console.log('contractor', this.contractor)
      if (this.contractor && this.contractor.user && this.contractor.user.location) {
        return this.contractor.user.location.address_line_1
      }
      return ''
    },
    getCity() {
      if (this.contractor && this.contractor.user && this.contractor.user.location) {
        return this.contractor.user.location.city
      }
      return ''
    },
    getLocationState() {
      if (this.contractor && this.contractor.user && this.contractor.user.location) {
        return this.contractor.user.location.state
      }
      return ''
    },
    getZip() {
      if (this.contractor && this.contractor.user && this.contractor.user.location) {
        return this.contractor.user.location.zip
      }
      return ''
    }
  },
  methods: {

    hasLicenses() {
      return this.contractor
          && this.contractor.user
          && this.contractor.user.licenses.length > 0
    },

    checkForPhoto() {
      if (this.contractor && this.contractor.user) {
        return this.contractor.user.photo_url !== null
      }
    },
    checkForAddressLine2() {
      if (this.contractor.user.location) {
        return this.contractor.user.location.address_line_2
      }
    },
    getAddress() {
      if (this.contractor && this.contractor.user && this.contractor.user.location) {
        console.log(JSON.stringify(this.contractor.user.location))
        if (this.contractor.user.location !== null) {
          return this.contractor.user.location.address_line_1 + ' ' +
              this.contractor.user.location.address_line_2 + ' ' +
              this.contractor.user.location.city + ' ' +
              this.contractor.user.location.state + ' ' +
              this.contractor.user.location.zip
        } else {
          return 'Address Not Available'
        }
      }
    },
    getLicenses() {
      if (this.contractor && this.contractor.user) {
        return this.contractor.user.licenses
      }
    },
    async getContractor() {

      let contractorId = this.contractorId

      if (!contractorId) {
        contractorId = localStorage.getItem('currentContractor');
        this.$store.commit('setCurrentPage', '/contractor-info');
      } else {
        localStorage.setItem('currentContractor', contractorId);
      }

      try {
        const data = await axios.get('/getContractor/' + contractorId);
        this.contractor = data.data
      } catch (error) {
        console.log(error)
      }
    }
  }
}
</script>

<style scoped>

</style>