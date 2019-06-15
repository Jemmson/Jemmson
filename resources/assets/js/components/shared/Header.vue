<template>
    <div class="container-fluid">
        <div v-if="page === '/home' || page === '/home/'" class="row bg-primary home-row">
            <div class="col-12 pt-3" style="height: 40px;">
                <i class="fas fa-search text-white float-left sm-icon"></i>
                <i class="far fa-bell text-white float-right sm-icon"></i>
            </div>
            <div class="col-12">
                <img class="profile-pic float-left ml-2"
                    src="http://infinisolutionslk.com/wp-content/uploads/2018/09/profilephotocircle.png"
                    alt="profile pic">
                <div class="profile-details">
                    <h4>{{ user.first_name }}  {{ user.last_name }}</h4>
                    <div class="row">
                        <div class="col pr-0">
                            <img class="float-left" src="/img/edit.svg" alt="">
                            <p>Edit Profile</p>
                        </div>
                        <div class="col pl-0">
                            <a href="/logout">
                            <img class="float-left" src="/img/Logout.svg" alt="">
                            <p>Log Out</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="page === '/bids' || page === '/home/'" class="row bg-white bids-row ">
            <div class="col-12 pt-3" style="height: 40px;">
                <i class="fas fa-search text-primary float-left sm-icon"></i>
                <i class="fas fa-plus text-primary float-right sm-icon"></i>
            </div>
            <div @click.prevent="toggleBidsContractor(true)" class="col pr-0 pl-0 text-center text-uppercase align-self-end" :class="bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">
                <p class="bids-toggle text-primary">
                    Contractor
                </p>
            </div>
            <div @click.prevent="toggleBidsContractor(false)" class="col pr-0 pl-0 text-center text-uppercase align-self-end" :class="!bidsContractorSectionPicked ? 'border-bottom border-primary' : ''">
                <p class="bids-toggle text-primary">
                    Subcontractor
                </p>
            </div>
        </div>
        <div v-else-if="page.split('/')[1] === 'bid'" class="row bg-white bid-row mb-4">
            <div class="col-12 d-flex align-items-center">
                <i class="fas fa-chevron-left text-primary float-left sm-icon align-self-center"></i>
                <span class="page-header-title mx-auto align-middle" style="font-weight: 700;">
                    {{$store.state.job.model !== null ? $store.state.job.model.job_name : 'No Job Name'}}
                </span>
            </div>
        </div>
        <div v-else class="row bg-white default-row mb-4">
            <div class="col-12 d-flex align-items-center">
                <i class="fas fa-tree text-primary float-left sm-icon align-self-center"></i>
                <h3 class="page-header-title font-weight-bold mx-auto">{{ getCompanyName }}</h3>
                <i class="fas fa-search text-primary float-right sm-icon"></i>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapState, mapMutations } from 'vuex'
    export default {
        props: ['user'],
        computed: {
            ...mapState({
                page: state => state.page,
                bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
            }),
          getCompanyName() {
              if (this.user.contractor) {
                return this.user.contractor.company_name
              }
          }
        },
        methods: {
            ...mapMutations([
                'toggleBidsContractor'
            ]),
        },
        mounted() {
            if (this.user !== undefined && this.user !== null) {
                this.$store.commit('setUser', this.user);
            }
            console.log('header', this.page);
            
        },
    }
</script>

<style lang="less" scoped>
.home-row {
    height: 152px;
    margin-bottom: 4rem;
}

.bids-row {
    height: 112px;
}

.bid-row {
    height: 55px;
}

.default-row {
    height: 70px;
}


.profile-pic {
    margin-top: 57px;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
    border: 5px solid rgba(255,255,255,0.5);
}
.profile-details {
    margin-top: 52px;
    padding-left: 8rem;
}
p {
    font-size: 12px;
    font-weight: 600px;
    margin-top: -3px;
    color: #ffffff;
    padding-left: 1rem;
}
h4 {
    font-size: 23px;
    color: #ffffff;
    font-weight: 600;
}

.bids-toggle {
    font-size: 12px;
}

</style>

