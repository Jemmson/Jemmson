<template>
    <div class="w-full">
        <div class="flex flex-col">
            <hr>
            <v-btn
                    class="w-full"
                    color="primary"
                    @click="showLicenseBox()"
                    id="addContractorLicenseButton"
                    ref="add_contractor_license_button">Add A
                License
            </v-btn>
        </div>

        <div
                class="flex space-between mt-1rem"
                v-if="licenses.length > 0"
                v-for="license in licenses"
                :key="license.number">
            <div class="uppercase"><strong>{{ license.type }}</strong></div>
            <div>{{ license.number }}</div>
            <v-btn
                    class="uppercase"
                    color="red"
                    @click="removeLicense(license)"
            >
                delete
            </v-btn>
        </div>
        <v-dialog
                width="500"
                v-model="showLicense"
                id="licenseDialog"
        >

            <v-card>
                <v-card-title>Add A License</v-card-title>

                <v-card-text>
                    <v-autocomplete
                            label="State *"
                            id="state"
                            :items="states"
                            v-model="license.state"
                            :error="licenseErrors.state"
                            @change="checkLicenseError()"
                            :rules="[
                                              licenseStateError()
                                              ]"
                    ></v-autocomplete>
                    <v-autocomplete
                            v-if="license.state === 'Arizona'"
                            id="type"
                            label="License Type *"
                            :items="licenseTypes.arizona"
                            v-model="license.type"
                    ></v-autocomplete>
                    <v-text-field
                            label="License Name"
                            id="name"
                            v-model="license.name"
                    ></v-text-field>
                    <v-text-field
                            id="value"
                            label="License Number *"
                            v-model="license.number"
                    ></v-text-field>
                </v-card-text>

                <v-card-actions>
                    <v-btn
                            color="red"
                            @click="showLicense = false"
                    >Cancel
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="red"
                            id="addLicense"
                            @click="addLicense()"
                    >Add
                    </v-btn>
                </v-card-actions>

            </v-card>
        </v-dialog>
        <hr>

    </div>
</template>

<script>
  export default {
    name: 'AddLicenseBox',
    data() {
      return {
        showLicense: false,
        license: {
          name: '',
          number: '',
          state: '',
          type: '',
        },
        licenses: [],
        licenseTypes: {
          arizona: [
            'A-',
            'A-4',
            'A-5',
            'A-7',
            'A-9',
            'A-11',
            'A-12',
            'A-14',
            'A-15',
            'A-16',
            'A-17',
            'A-19',
            'B-',
            'B-1',
            'B-2',
            'B-3',
            'B-4',
            'B-5',
            'B-6',
            'B-10',
            'C-',
            'C-1',
            'C-2',
            'C-3',
            'C-4',
            'C-5',
            'C-6',
            'C-7',
            'C-8',
            'C-9',
            'C-10',
            'C-11',
            'C-12',
            'C-13',
            'C-14',
            'C-15',
            'C-16',
            'C-21',
            'C-24',
            'C-27',
            'C-31',
            'C-34',
            'C-36',
            'C-37',
            'C-38',
            'C-39',
            'C-40',
            'C-41',
            'C-42',
            'C-24',
            'C-45',
            'C-48',
            'C-49',
            'C-53',
            'C-54',
            'C-56',
            'C-57',
            'C-58',
            'C-60',
            'C-61',
            'C-63',
            'C-65',
            'C-67',
            'C-70',
            'C-74',
            'C-77',
            'C-78',
            'C-79',
            'CR-',
            'CR-1',
            'CR-2',
            'CR-3',
            'CR-4',
            'CR-5',
            'CR-6',
            'CR-7',
            'CR-8',
            'CR-9',
            'CR-10',
            'CR-11',
            'CR-12',
            'CR-13',
            'CR-14',
            'CR-15',
            'CR-16',
            'CR-17',
            'CR-21',
            'CR-24',
            'CR-29',
            'CR-31',
            'CR-34',
            'CR-36',
            'CR-37',
            'CR-38',
            'CR-39',
            'CR-40',
            'CR-41',
            'CR-42',
            'CR-45',
            'CR-48',
            'CR-53',
            'CR-54',
            'CR-56',
            'CR-57',
            'CR-58',
            'CR-60',
            'CR-61',
            'CR-62',
            'CR-63',
            'CR-65',
            'CR-66',
            'CR-67',
            'CR-69',
            'CR-70',
            'CR-74',
            'CR-77',
            'CR-78',
            'CR-79',
            'CR-80',
            'KA-',
            'KA-5',
            'KA-6',
            'KE-',
            'KB-1',
            'KB-2',
            'KO-',
            'R-',
            'R-1',
            'R-2',
            'R-3',
            'R-4',
            'R-6',
            'R-7',
            'R-8',
            'R-9',
            'R-10',
            'R-11',
            'R-12',
            'R-13',
            'R-14',
            'R-15',
            'R-16',
            'R-17',
            'R-21',
            'R-22',
            'R-24',
            'R-31',
            'R-34',
            'R-36',
            'R-37',
            'R-38',
            'R-39',
            'R-40',
            'R-41',
            'R-42',
            'R-45',
            'R-48',
            'R-53',
            'R-54',
            'R-56',
            'R-57',
            'R-60',
            'R-61',
            'R-62',
            'R-63',
            'R-65',
            'R-67',
            'R-70',
          ],
          idaho: []
        },
        licenseErrors: {
          state: false
        },
        state: '',
        states:
          [
            'Alabama',
            'Alaska',
            'American Samoa',
            'Arizona',
            'Arkansas',
            'California',
            'Colorado',
            'Connecticut',
            'Delaware',
            'District Of Columbia',
            'Federated States Of Micronesia',
            'Florida',
            'Georgia',
            'Guam',
            'Hawaii',
            'Idaho',
            'Illinois',
            'Indiana',
            'Iowa',
            'Kansas',
            'Kentucky',
            'Louisiana',
            'Maine',
            'Marshall Islands',
            'Maryland',
            'Massachusetts',
            'Michigan',
            'Minnesota',
            'Mississippi',
            'Missouri',
            'Montana',
            'Nebraska',
            'Nevada',
            'New Hampshire',
            'New Jersey',
            'New Mexico',
            'New York',
            'North Carolina',
            'North Dakota',
            'Northern Mariana Islands',
            'Ohio',
            'Oklahoma',
            'Oregon',
            'Palau',
            'Pennsylvania',
            'Puerto Rico',
            'Rhode Island',
            'South Carolina',
            'South Dakota',
            'Tennessee',
            'Texas',
            'Utah',
            'Vermont',
            'Virgin Islands',
            'Virginia',
            'Washington',
            'West Virginia',
            'Wisconsin',
            'Wyoming'
          ]
      }
    },
    methods: {
      removeLicense(license) {
        for (let i = 0; i < this.licenses.length; i++) {
          if (this.licenses[i].number === license.number) {
            this.licenses.splice(i, 1)
            return
          }
        }
      },

      addLicense() {

        if (
          this.license.state !== ''
        ) {
          const license = {}
          if (this.license.state === 'Arizona') {
            license.name = this.getArizonaLicenseName();
          } else {
            license.name = this.license.name
          }
          license.type = this.license.type
          license.number = this.license.number
          license.state = this.license.state
          this.licenses[this.licenses.length] = license
          this.showLicense = false
          this.$emit('add', this.licenses)
        } else {
          this.licenseErrors.state = true
        }
      },

      licenseStateError() {

        if (this.license.state === '') {
          return 'State is Required'
        } else {
          return ''
        }
      },

      getArizonaLicenseName(){
        // licenseNames = {
        //   A-0: '',
        // A-4: '',
        // A-5: '',
        // A-7: '',
        // A-9: '',
        // A-11: '',
        // A-12: '',
        // A-14: '',
        // A-15: '',
        // A-16: '',
        // A-17: '',
        // A-19: ''
        // }

      },

      checkLicenseError() {
        if (this.license.state === '') {
          this.licenseErrors.state = true
        } else {
          this.licenseErrors.state = false
        }
      },

      showLicenseBox() {
        this.showLicense = true
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
