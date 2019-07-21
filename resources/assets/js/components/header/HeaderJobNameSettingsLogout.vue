<template>
    <div class="container-fluid">
        <div class="row bg-primary home-row">
            <div class="col-12 pt-3" style="height: 40px;">
                <i class="fas fa-search text-white float-left sm-icon"></i>
                <i class="far fa-bell text-white float-right sm-icon"></i>
            </div>
            <div class="col-12" ref="biographical_information">
                <div class="profile-details">
                    <h4 ref="name">{{ jobName }}</h4>
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
    </div>




</template>

<script>
  export default {
    name: 'HeaderJobNameSettingsLogout',
    props: {
      jobName: String
    }
  }
</script>

<style scoped>

</style>