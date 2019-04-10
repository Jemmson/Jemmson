<template>
<div class="container-fluid">
    <div v-if="page === '/home'" class="row bg-primary home-row">
        <div class="col-12 pt-3" style="height: 40px;">
            <i class="fas fa-search text-white float-left sm-icon"></i>
            <i class="far fa-bell text-white float-right sm-icon"></i>
        </div>
        <div class="col-12">
            <img class="profile-pic float-left ml-2" src="http://infinisolutionslk.com/wp-content/uploads/2018/09/profilephotocircle.png" alt="profile pic">
            <div class="profile-details">
                <h4>Jane Doe</h4>
                <div class="row">
                    <div class="col pr-0">
                        <img class="float-left" src="/img/edit.svg" alt="">
                        <p>Edit Profile</p>
                    </div>
                    <div class="col pl-0">
                        <img class="float-left" src="/img/Logout.svg" alt="">
                        <p>Log Out</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="page === '/bids'" class="row bg-white bids-row ">
        <div class="col-12 pt-3" style="height: 40px;">
            <i class="fas fa-search blue float-left sm-icon"></i>
            <i class="fas fa-plus blue float-right sm-icon"></i>
        </div>
        <div class="col-12 align-items-end">
            <div class="row  text-center">

            <div class="col">
                <h6 class="align-bottom">

                    contractor
                </h6>
            </div>
            <div class="col">
                <h6 class="align-bottom">

                    subcontractor
                </h6>
            </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    import { mapState } from 'vuex'
    export default {
        computed: {
            ...mapState({
                page: state => state.page
            })
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

.blue {
    color: #346aff;
}

h6 {
   color: #346aff; 
}

</style>

