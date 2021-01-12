<template>
  <div class="w-full">
    <div class="flex flex-col">
      <hr>

      <h4 class="text-center">License and Bonding</h4>

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
        :key="license.name">
      <div class="uppercase"><strong>{{ license.type }}</strong></div>
      <div>{{ license.number }}</div>
      <v-btn
          class="uppercase"
          color="red"
          text
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
        <v-card-title class="w-break">Add A License</v-card-title>

        <v-card-text>
          <v-autocomplete
              label="State *"
              id="state"
              :items="states"
              v-model="license.state"
              :error="licenseErrors.state"
              @change="checkLicenseError()"
              :rules="[licenseStateError()]"
          ></v-autocomplete>
          <v-autocomplete
              v-if="license.state === 'Arizona'"
              id="type"
              label="License Type *"
              :items="licenseTypes.arizona"
              @change="fillLicense()"
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
              class="w-40"
              text
              @click="showLicense = false"
          >Cancel
          </v-btn>
          <v-spacer></v-spacer>
          <v-btn
              color="blue"
              class="w-40"
              text
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
import {mapGetters, mapState} from 'vuex'
export default {
  name: 'AddLicenseBox',


  mounted(){
    console.log('mounted Licenses')
    if (this.getRegisterInfo && this.getLicenses) {
      this.licenses = this.getLicenses[0]
    }
  },

  created(){
    console.log('created Licenses')
  },

  beforeCreate(){
    console.log('beforeCreate Licenses')
  },

  beforeMount(){
    console.log('beforeMount Licenses')
  },

  updated(){
    console.log('updated Licenses')
  },

  deactivated(){
    console.log('deactivated Licenses')
  },

  beforeUnmount(){
    console.log('beforeUnmount Licenses')
  },

  unmounted(){
    console.log('unmounted Licenses')
  },

  errorCaptured(){
    console.log('errorCaptured Licenses')
  },

  renderTracked(){
    console.log('renderTracked Licenses')
  },

  renderTriggered(){
    console.log('renderTriggered Licenses')
  },



  beforeUpdate(){
    console.log('beforeUpdateLicenses')
  },
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
          'C-1',
          'C-3',
          'C-4',
          'C-6',
          'C-7',
          'C-8',
          'C-9',
          'C-10',
          'C-11',
          'C-12',
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
      licenseKeyValue:
          {
            'Arizona':
                {
                  'A-': 'GENERAL ENGINEERING',
                  'A-4': 'DRILLING',
                  'A-5': 'EXCAVATING, GRADING AND OIL SURFACING',
                  'A-7': 'PIERS AND FOUNDATIONS',
                  'A-9': 'SWIMMING POOLS',
                  'A-11': 'STEEL AND ALUMINUM ERECTION',
                  'A-12': 'SEWERS, DRAINS AND PIPE LAYING',
                  'A-14': 'ASPHALT PAVING',
                  'A-15': 'SEAL COATING',
                  'A-16': 'WATERWORKS',
                  'A-17': 'ELECTRICAL AND TRANSMISSION LINES',
                  'A-19': 'SWIMMING POOLS, INCLUDING SOLAR',
                  'B-': 'GENERAL RESIDENTIAL CONTRACTOR',
                  'B-1': 'GENERAL COMMERCIAL CONTRACTOR',
                  'B-2': 'GENERAL SMALL COMMERCIAL CONTRACTOR',
                  'B-3': 'GENERAL REMODELING AND REPAIR CONTRACTOR',
                  'B-4': 'GENERAL RESIDENTIAL ENGINEERING CONTRACTOR',
                  'B-5': 'GENERAL SWIMMING POOL CONTRACTOR',
                  'B-6': 'GENERAL SWIMMING POOL CONTRACTOR, INCLUDING SOLAR',
                  'B-10': 'PRE-MANUFACTURED SPAS AND HOT TUBS',
                  'C-1': 'ACOUSTICAL SYSTEMS',
                  'C-3': 'AWNINGS, CANOPIES, CARPORTS AND PATIO COVERS',
                  'C-4': 'BOILERS, STEAMFITTING AND PROCESS PIPING',
                  'C-6': 'SWIMMING POOL SERVICE AND REPAIR',
                  'C-7': 'CARPENTRY',
                  'C-8': 'FLOOR COVERING',
                  'C-9': 'CONCRETE',
                  'C-10': 'DRYWALL',
                  'C-11': 'ELECTRICAL',
                  'C-12': 'ELEVATORS',
                  'C-14': 'FENCING',
                  'C-15': 'BLASTING',
                  'C-16': 'FIRE PROTECTION SYSTEMS',
                  'C-21': 'HARDSCAPING AND IRRIGATION SYSTEMS',
                  'C-24': 'ORNAMENTAL METALS',
                  'C-27': 'LIGHTWEIGHT PARTITIONS',
                  'C-31': 'MASONRY',
                  'C-34': 'PAINTING AND WALL COVERING',
                  'C-36': 'PLASTERING',
                  'C-37': 'PLUMBING',
                  'C-38': 'SIGNS',
                  'C-39': 'AIR CONDITIONING AND REFRIGERATION',
                  'C-40': 'INSULATION',
                  'C-41': 'SEPTIC TANKS AND SYSTEMS',
                  'C-42': 'ROOFING',
                  'C-45': 'SHEET METAL',
                  'C-48': 'CERAMIC, PLASTIC AND METAL TILE',
                  'C-49': 'COMMERCIAL, INDUSTRIAL REFRIGERATION',
                  'C-53': 'WATER WELL DRILLING',
                  'C-54': 'WATER CONDITIONING EQUIPMENT',
                  'C-56': 'WELDING',
                  'C-57': 'WRECKING',
                  'C-58': 'COMFORT HEATING, VENTILATING, EVAPORATIVE COOLING',
                  'C-60': 'FINISH CARPENTRY',
                  'C-61': 'CARPENTRY, REMODELING AND REPAIRS',
                  'C-63': 'APPLIANCES',
                  'C-65': 'GLAZING',
                  'C-67': 'LOW VOLTAGE COMMUNICATION SYSTEMS',
                  'C-70': 'REINFORCING BAR AND WIRE MESH',
                  'C-74': 'BOILERS, STEAMFITTING AND PROCESS PIPING, INCLUDING SOLAR',
                  'C-77': 'PLUMBING INCLUDING SOLAR',
                  'C-78': 'SOLAR PLUMBING LIQUID SYSTEMS ONLY',
                  'C-79': 'AIR CONDITIONING AND REFRIGERATION INCLUDING SOLAR',
                  'CR-1': 'ACOUSTICAL SYSTEMS',
                  'CR-2': 'EXCAVATING, GRADING AND OIL SURFACING',
                  'CR-3': 'AWNINGS, CANOPIES, CARPORT AND PATIO COVERS',
                  'CR-4': 'BOILERS, STEAMFITTING AND PROCESS PIPING',
                  'CR-5': '',
                  'CR-6': 'SWIMMING POOL SERVICE AND REPAIR',
                  'CR-7': 'CARPENTRY',
                  'CR-8': 'FLOOR COVERING',
                  'CR-9': 'CONCRETE',
                  'CR-10': 'DRYWALL',
                  'CR-11': 'ELECTRICAL',
                  'CR-12': 'ELEVATORS',
                  'CR-14': 'FENCING',
                  'CR-15': 'BLASTING',
                  'CR-16': 'FIRE PROTECTION SYSTEMS',
                  'CR-17': 'STEEL AND ALUMINUM ERECTION',
                  'CR-21': 'HARDSCAPING AND IRRIGATION SYSTEMS',
                  'CR-24': 'ORNAMENTAL METALS',
                  'CR-29': 'MACHINERY',
                  'CR-31': 'MASONRY',
                  'CR-34': 'PAINTING AND WALL COVERING',
                  'CR-36': 'PLASTERING',
                  'CR-37': 'PLUMBING',
                  'CR-38': 'SIGNS',
                  'CR-39': 'AIR CONDITIONING AND REFRIGERATION',
                  'CR-40': 'INSULATION',
                  'CR-41': 'SEPTIC TANKS AND SYSTEMS',
                  'CR-42': 'ROOFING',
                  'CR-45': 'SHEET METAL',
                  'CR-48': 'CERAMIC, PLASTIC AND METAL TILE',
                  'CR-53': 'WATER WELL DRILLING',
                  'CR-54': 'WATER CONDITIONING EQUIPMENT',
                  'CR-56': 'WELDING',
                  'CR-57': 'WRECKING',
                  'CR-58': 'COMFORT HEATING, VENTILATING, EVAPORATIVE COOLING',
                  'CR-60': 'FINISH CARPENTRY',
                  'CR-61': 'CARPENTRY, REMODELING AND REPAIRS',
                  'CR-62': 'REINFORCING BAR AND WIRE MESH',
                  'CR-63': 'APPLIANCES',
                  'CR-65': 'GLAZING',
                  'CR-66': 'SEAL COATING',
                  'CR-67': 'LOW VOLTAGE COMMUNICATION SYSTEMS',
                  'CR-69': 'ASPHALT PAVING',
                  'CR-70': 'REINFORCING BAR AND WIRE MESH',
                  'CR-74': 'BOILERS, STEAMFITTING AND PROCESSPIPING, INCLUDING SOLAR',
                  'CR-77': 'PLUMBING INCLUDING SOLAR',
                  'CR-78': 'SOLAR PLUMBING LIQUID SYSTEMS ONLY',
                  'CR-79': 'AIR CONDITIONING AND REFRIGERATION INCLUDING SOLAR',
                  'CR-80': 'SEWERS, DRAINS AND PIPE LAYING',
                  'KA-': 'DUAL ENGINEERING',
                  'KA-5': 'DUAL SWIMMING POOL CONTRACTOR',
                  'KA-6': 'DUAL SWIMMING POOL CONTRACTOR INCLUDING SOLAR',
                  'KE-': 'AIR CONDITIONING AND REFRIGERATION INCLUDING SOLAR',
                  'KB-1': 'DUAL BUILDING CONTRACTOR',
                  'KB-2': 'DUAL RESIDENTIAL AND SMALL COMMERCIAL',
                  'KO-': 'BOILERS, STEAMFITTING AND PROCESS PIPING, INCLUDING SOLAR',
                  'R-1': 'ACOUSTICAL SYSTEMS',
                  'R-2': 'EXCAVATING, GRADING AND OIL SURFACING',
                  'R-3': 'AWNINGS, CANOPIES, CARPORTS AND PATIO COVERS',
                  'R-4': 'BOILERS, STEAMFITTING AND PROCESS PIPING',
                  'R-6': 'SWIMMING POOL SERVICE AND REPAIR',
                  'R-7': 'CARPENTRY',
                  'R-8': 'FLOOR COVERING',
                  'R-9': 'CONCRETE',
                  'R-10': 'DRYWALL',
                  'R-11': 'ELECTRICAL',
                  'R-12': 'ELEVATORS',
                  'R-13': 'ASPHALT PAVING',
                  'R-14': 'FENCING',
                  'R-15': 'BLASTING',
                  'R-16': 'FIRE PROTECTION SYSTEMS',
                  'R-17': 'STRUCTURAL STEEL AND ALUMINUM',
                  'R-21': 'HARDSCAPING AND IRRIGATION SYSTEMS',
                  'R-22': 'HOUSE MOVING',
                  'R-24': 'ORNAMENTAL METALS',
                  'R-31': 'MASONRY',
                  'R-34': 'PAINTING AND WALL COVERING',
                  'R-36': 'PLASTERING',
                  'R-37': 'PLUMBING, INCLUDING SOLAR',
                  'R-38': 'SIGNS',
                  'R-39': 'AIR CONDITIONING AND REFRIGERATION, INCLUDING SOLAR',
                  'R-40': 'INSULATION',
                  'R-41': 'SEPTIC TANKS AND SYSTEMS',
                  'R-42': 'ROOFING',
                  'R-45': 'SHEET METAL',
                  'R-48': 'CERAMIC, PLASTIC AND METAL TILE',
                  'R-53': 'DRILLING',
                  'R-54': 'WATER CONDITIONING EQUIPMENT',
                  'R-56': 'WELDING',
                  'R-57': 'WRECKING',
                  'R-60': 'FINISH CARPENTRY',
                  'R-61': 'CARPENTRY, REMODELING AND REPAIRS',
                  'R-62': 'MINOR HOME IMPROVEMENTS',
                  'R-63': 'APPLIANCES',
                  'R-65': 'GLAZING',
                  'R-67': 'LOW VOLTAGE COMMUNICATION SYSTEMS',
                  'R-70': 'REINFORCING BAR AND WIRE MESH'
                }
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
  computed: {
    ...mapGetters([
      'getLicenses',
      'getRegisterInfo'
    ])
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

    fillLicense() {
      if (this.license.state === 'Arizona') {
        this.license.name = this.licenseKeyValue[this.license.state][this.license.type]
      }
    },

    addLicense() {

      if (
          this.license.state !== ''
      ) {
        const license = {}
        if (this.license.state === 'Arizona') {
          // license.name = this.getArizonaLicenseName()
          license.name = this.license.name
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

    getArizonaLicenseName() {
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
