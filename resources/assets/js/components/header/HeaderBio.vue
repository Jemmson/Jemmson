<template>

    <v-app-bar
            color="#95ca97"
            fixed
            height="62px"
            class="mb-1rem"
    >
        <img src="img/premiumlogo/onlinelogomaker-031420-0923-4133-2000-transparent.png"
             style="max-height:50px"
        >
        <v-spacer></v-spacer>

        <div>{{ name }}</div>

        <v-spacer></v-spacer>

        <v-btn
                icon
                @click="logout()"
        >
            <v-icon>mdi-login</v-icon>
        </v-btn>

        <v-btn
                @click="settings()"
                icon
        >
            <v-icon>mdi-settings</v-icon>
        </v-btn>

    </v-app-bar>


<!--    <div class="container-fluid mb-1rem">-->
<!--        <div class="row bg-light home-row">-->
<!--            &lt;!&ndash;            <div class="col-12 pt-3" style="height: 40px;">&ndash;&gt;-->
<!--            &lt;!&ndash;                <i class="fas fa-search text-white float-left sm-icon"></i>&ndash;&gt;-->
<!--            &lt;!&ndash;                <i class="far fa-bell text-white float-right sm-icon"></i>&ndash;&gt;-->
<!--            &lt;!&ndash;            </div>&ndash;&gt;-->
<!--            <div class="col-12" ref="biographical_information">-->
<!--                &lt;!&ndash;                <img class="profile-pic float-left ml-2"&ndash;&gt;-->
<!--                &lt;!&ndash;                     src="https://www.skylightsearch.co.uk/wp-content/uploads/2017/01/Hadie-profile-pic-circle-1.png"&ndash;&gt;-->
<!--                &lt;!&ndash;                     alt="profile pic">&ndash;&gt;-->
<!--                <div class="profile-details">-->
<!--                    <h4 class="text-center mt-half-rem" ref="name">{{ name }}</h4>-->

<!--                    <div>-->

<!--                    </div>-->


<!--                    <nav class="flex justify-content-around header-border">-->
<!--                        <section class="" @click="settings()">-->
<!--&lt;!&ndash;                            <img class="float-left img" src="/img/edit.svg" alt="">&ndash;&gt;-->
<!--                            <label>Edit Profile</label>-->
<!--                        </section>-->
<!--                        <section class="" @click="logout()">-->
<!--&lt;!&ndash;                            <img id="logout" class="float-left img" src="/img/Logout.svg" alt="">&ndash;&gt;-->
<!--                            <label for="logout">Log Out</label>-->
<!--                        </section>-->

<!--                    </nav>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
</template>

<script>

  export default {
    name: 'HeaderBio',
    props: {
      name: String
    },
    methods:{
      logout(){
        window.location.href = '/logout';
      },
      settings(){
        // window.location.href = '/settings';
          this.$router.push('/settings')
      }
    }
  }
</script>

<style scoped>

    .mt-half-rem {
        margin-top: .5rem;
    }

    .mb-1rem {
        margin-bottom: 1rem;
    }

    .flex {
        display: flex;
    }

    .justify-content-around{
        justify-content:space-around!important
    }

    /*.flex-1{-webkit-box-flex:1;-ms-flex:1;flex:1}*/

    .header-link {
        justify-content: space-evenly;
    }

    section {
        margin-top: .5rem;
    }

    .header-border {
        border: solid thin black;
    }

    .header-row {
        margin-top: .65rem;
        width:100%;
    }

    .img {
        margin: auto;
        width: 50%;
    }

    .justify-center {
        justify-items: center;
    }

    .justify-content-center {
        justify-content: center;
    }

    .align-center {
        align-items: center;
    }
</style>