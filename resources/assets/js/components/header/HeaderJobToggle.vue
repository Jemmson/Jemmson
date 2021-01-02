<template>

  <div class="flex flex-col">
    <header-bio
        ref="homePage1"
        page-title="Jobs"
        name=""
    ></header-bio>

    <v-tabs v-model="activeTab" class="justify-center" style="margin-top: 3.5rem;">
      <v-tab
          style="margin-left: auto; margin-right: auto;"
          class="w-full"
          @click.prevent="toggleBidsContractor(true)"
      >General Jobs
      </v-tab>
      <v-tab
          style="margin-left: auto; margin-right: auto;"
          class="w-full"
          @click.prevent="toggleBidsContractor(false)"
      >Sub Jobs
      </v-tab>
    </v-tabs>

    <!--        <div class="container-fluid" style="margin-top: 40px">-->
    <!--            <div ref="job_toggle"-->
    <!--                 class="row bg-white bids-row spacing">-->
    <!--                <div ref="toggle_contractors" @click.prevent="toggleBidsContractor(true)"-->
    <!--                     class="col pr-0 pl-0 text-center text-uppercase align-self-end"-->
    <!--                     :class="bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">-->
    <!--                    <p class="bids-toggle text-primary f-size-13pt text-center">-->
    <!--                        General Jobs-->
    <!--                    </p>-->
    <!--                </div>-->
    <!--                <div ref="toggle_subContractors" @click.prevent="toggleBidsContractor(false)"-->
    <!--                     class="col pr-0 pl-0 text-center text-uppercase align-self-end"-->
    <!--                     :class="!bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">-->
    <!--                    <p class="bids-toggle text-primary f-size-13pt text-center">-->
    <!--                        Sub Jobs-->
    <!--                    </p>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
  </div>

</template>

<script>
import {mapState, mapMutations} from 'vuex'
import HeaderBio from "./HeaderBio";

export default {
  name: 'HeaderJobToggle',
  components: {
    HeaderBio
  },
  props: {
    name: String
  },
  data() {
    return {
      logoutDialog: false,
      activeTab: 0
    }
  },
  computed: {
    ...mapState({
      bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
    })
  },
  methods: {
    ...mapMutations([
      'toggleBidsContractor'
    ]),
    openDialog() {
      this.logoutDialog = true
    },
    cancelDialog() {
      this.logoutDialog = false
    },
    logout() {
      localStorage.setItem('auth', false);
      window.location.href = '/logout';
    },
    settings() {
      // window.location.href = '/settings';
      this.$router.push('/settings')
    }
  }
}
</script>

<style scoped>
.spacing {
  height: 54px;
  padding-bottom: 4px;
}
</style>