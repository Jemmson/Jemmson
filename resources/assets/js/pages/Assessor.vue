<template>
  <div>

    <v-overlay :value="overlay">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-container v-if="parcelFound">

      <h1 class="text-center">Assessor Information</h1>

      <v-card class="mb-1rem">
        <v-card-actions
            class="flex flex-col"
        >
          <div class="flex justify-content-around w-full">

            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="show.owner ? 'success': ''"
                  class="nav-btn-position"
                  @click="showSection('owner')"

              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="show.owner ? 'nav-icon-label-selected': ''">
                Owner
              </div>
            </div>

            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="show.valuations ? 'success': ''"
                  class="nav-btn-position"
                  @click="showSection('valuations')"

              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="show.valuations ? 'nav-icon-label-selected': ''">
                Valuations
              </div>
            </div>

            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="show.propertyData ? 'success': ''"
                  class="nav-btn-position"
                  @click="showSection('propertyData')"

              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="show.propertyData ? 'nav-icon-label-selected': ''">
                General Data
              </div>
            </div>

            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="show.similarParcels ? 'success': ''"
                  class="nav-btn-position"
                  @click="showSection('similarParcels')"

              >mdi-details
              </v-icon>
              <div class="nav-icon-label" :class="show.similarParcels ? 'nav-icon-label-selected': ''">
                Similar
              </div>
            </div>

            <div class="flex flex-col nav-icon-spacing">
              <v-icon
                  :color="show.residentialPropertyData ? 'success': ''"
                  class="nav-btn-position"
                  @click="showSection('residentialPropertyData')"

              >mdi-details
              </v-icon>
              <div class="nav-icon-label"
                   :class="show.residentialPropertyData ? 'nav-icon-label-selected': ''">
                Property Data
              </div>
            </div>

          </div>
        </v-card-actions>
      </v-card>

      <v-card v-if="show.valuations">
        <v-card-title>Valuations</v-card-title>
        <v-card-text>

          <v-data-iterator
              :items="locationData.Valuations"
              hide-default-footer
          >
            <template v-slot:default="props">
              <v-row>
                <v-col
                    v-for="item in props.items"
                    :key="item.name"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                >
                  <v-card>
                    <v-card-title class="subheading font-weight-bold">{{ item.TaxYear }}
                    </v-card-title>

                    <v-divider></v-divider>

                    <v-list dense>
                      <v-list-item v-for="(value, name) in item" :key="name">
                        <v-list-item-content>{{ name }}:</v-list-item-content>
                        <v-list-item-content class="align-end">{{ value }}
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>

                  </v-card>
                </v-col>
              </v-row>
            </template>
          </v-data-iterator>

        </v-card-text>
        <v-card-actions>

        </v-card-actions>
      </v-card>

      <v-card v-if="show.residentialPropertyData">
        <v-card-title>Residential Property Data</v-card-title>
        <v-card-text>
          <div>

            <v-divider></v-divider>

            <v-list dense>

              <v-list dense>
                <v-list-item v-if="name !== 'ParkingDetails'" v-for="(value, name) in locationData.ResidentialPropertyData" :key="name">
                  <v-list-item-content>{{ name }}:</v-list-item-content>
                  <v-list-item-content class="align-end">{{ value }}
                  </v-list-item-content>
                </v-list-item>
                <v-list-item v-else="name !== 'ParkingDetails'" v-for="(value, name) in locationData.ResidentialPropertyData.ParkingDetails" :key="name">
                  <v-list-item-content>{{ name }}:</v-list-item-content>
                  <v-list-item-content class="align-end">{{ value }}
                  </v-list-item-content>
                </v-list-item>
              </v-list>

            </v-list>
          </div>

        </v-card-text>
        <v-card-actions>

        </v-card-actions>
      </v-card>

      <v-card v-if="show.similarParcels">
        <v-card-title>Similar Parcels</v-card-title>
        <v-card-text>

          <v-data-iterator
              :items="locationData.SimilarParcels"
              hide-default-footer
          >
            <template v-slot:default="props">
              <v-row>
                <v-col
                    v-for="item in props.items"
                    :key="item.name"
                    cols="12"
                    sm="6"
                    md="4"
                    lg="3"
                >
                  <v-card>

                    <v-card-title class="subheading font-weight-bold">
                      <a :href="'https://preview.mcassessor.maricopa.gov/mcs/?q=' + item.Parcel">{{
                          item.Parcel
                        }}</a>
                    </v-card-title>

                    <v-divider></v-divider>

                    <v-list dense>
                      <v-list-item v-for="(value, name) in item" :key="name">
                        <v-list-item-content>{{ name }}:</v-list-item-content>
                        <v-list-item-content class="align-end">{{ value }}
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>

                  </v-card>
                </v-col>
              </v-row>
            </template>
          </v-data-iterator>

        </v-card-text>
        <v-card-actions>

        </v-card-actions>
      </v-card>

      <v-card v-if="show.owner && dataReturned">
        <v-card-title>Owner Information</v-card-title>
        <v-card-text>
          <div>

            <v-divider></v-divider>

            <v-list dense>
              <v-list-item v-for="(value, name) in locationData.Owner" :key="name">
                <v-list-item-content>{{ name }}:</v-list-item-content>
                <v-list-item-content class="align-end">{{ value }}
                </v-list-item-content>
              </v-list-item>
            </v-list>

          </div>

        </v-card-text>
        <v-card-actions>

        </v-card-actions>
      </v-card>

      <v-card v-if="show.propertyData">
        <v-card-title>General Data</v-card-title>
        <v-card-text>
          <div>

            <v-divider></v-divider>

            <v-list dense>
              <v-list-item

                  v-if="name !== 'ParcelStatus'
                    && name !== 'Owner'
                    && name !== 'Geo'
                    && name !== 'McrTransitionUrl'
                    && name !== 'TreasurersTransitionUrl'
                    && name !== 'DeedTransitionUrl'
                    && name !== 'Valuations'
                    && name !== 'ResidentialPropertyData'
                    && name !== 'SimilarParcels'
                    && name !== 'Sketches'
                    && name !== 'MapIDs'"

                  v-for="(value, name) in locationData" :key="name">
                <v-list-item-content>{{ name }}:</v-list-item-content>
                <v-list-item-content class="align-end">{{ value }}
                </v-list-item-content>
              </v-list-item>
            </v-list>

          </div>

        </v-card-text>
        <v-card-actions>

        </v-card-actions>
      </v-card>

    </v-container>

    <v-container v-if="showResults">


      <v-card>
        <v-card-title>Please Pick an Address</v-card-title>
        <v-card-text>
          <div>

            <v-divider></v-divider>

            <v-list dense>
              <v-list-item
                  @click="getParcel(value.APN)"
                  v-for="(value, name) in multipleResults"
                  :key="name">
                <v-list-item-content class="align-end">
                  {{ value.SitusAddress }}
                </v-list-item-content>
              </v-list-item>
            </v-list>

          </div>
        </v-card-text>
      </v-card>

    </v-container>

    <feedback
        page="assessor"
    ></feedback>
  </div>
</template>

<script>

import Feedback from '../components/shared/Feedback'

export default {
  name: 'Assessor',
  components: {
    Feedback
  },
  data() {
    return {
      locationData: null,
      show: {
        details: false,
        valuations: false,
        propertyData: false,
        similarParcels: false,
        residentialPropertyData: false,
        owner: true
      },
      showResults: false,
      parcelFound: true,
      multipleResults: null,
      overlay: true,
      dataReturned: false
    }
  },
  methods: {
    showSection(section) {
      this.hideAllSections();
      if (section === 'valuations') {
        this.show.valuations = true;
      } else if (section === 'details') {
        this.show.details = true;
      } else if (section === 'propertyData') {
        this.show.propertyData = true;
      } else if (section === 'similarParcels') {
        this.show.similarParcels = true;
      } else if (section === 'residentialPropertyData') {
        this.show.residentialPropertyData = true;
      } else if (section === 'owner') {
        this.show.owner = true;
      }
    },
    hideAllSections() {
      this.show.details = false;
      this.show.valuations = false;
      this.show.propertyData = false;
      this.show.similarParcels = false;
      this.show.residentialPropertyData = false;
      this.show.owner = false;
    },
    parcel(parcel) {
      let parcelArray = parcel.split('-');
      return parcelArray[0] + parcelArray[1] + parcelArray[2];
    },
    setPage() {
      this.$store.commit('setCurrentPage', '/assessor');
    },
    async getAssessorData() {
      const {data} = await axios.get('/assessor/' + this.$route.params.location);
      if (data.error) {
      } else {

        if (data.length > 1) {
          this.parcelFound = false;
          this.showResults = true;
          this.multipleResults = data;
        }

        if (data.length === 1) {
          this.parcelFound = true;
          this.showResults = false;
        }

      }
      this.overlay = false;
    },
    async getParcel(apn) {
      this.overlay = true;
      const {data} = await axios.get('/parcel/' + apn);
      if (data.error) {
      } else {
        this.parcelFound = true;
        this.showResults = false;
        this.locationData = data;
        this.dataReturned = true;
      }
      this.overlay = false;
    }
  },
  mounted() {
    this.setPage();
    this.overlay = true;
    this.getAssessorData();
  }
}
</script>

<style scoped>

</style>