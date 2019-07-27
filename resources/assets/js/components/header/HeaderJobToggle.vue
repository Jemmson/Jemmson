<template>
    <div class="container-fluid">
        <div ref="job_toggle"
             class="row bg-white bids-row ">
<!--            <div class="col-12 pt-3" style="height: 40px;">-->
<!--                <i class="fas fa-search text-primary float-left sm-icon"></i>-->
<!--                <i class="fas fa-plus text-primary float-right sm-icon"></i>-->
<!--            </div>-->
            <div ref="toggle_contractors" @click.prevent="toggleBidsContractor(true)"
                 class="col pr-0 pl-0 text-center text-uppercase align-self-end"
                 :class="bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">
                <p class="bids-toggle text-primary">
                    Contractor
                </p>
            </div>
            <div ref="toggle_subContractors" @click.prevent="toggleBidsContractor(false)"
                 class="col pr-0 pl-0 text-center text-uppercase align-self-end"
                 :class="!bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">
                <p class="bids-toggle text-primary">
                    Subcontractor
                </p>
            </div>
        </div>
    </div>

</template>

<script>
  import { mapState, mapMutations } from 'vuex'

  export default {
    name: 'HeaderJobToggle',
    computed: {
      ...mapState({
        // userFromState: state => state.user.user,
        // userType: (state) => {
        //   if (state.user.user !== undefined && state.user.user !== null) {
        //     return state.user.user.usertype
        //   }
        //   return null
        // },
        bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
      })
    },
    methods: {
      ...mapMutations([
        'toggleBidsContractor'
      ]),
    }
  }
</script>

<style scoped>

</style>