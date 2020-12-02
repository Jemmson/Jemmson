<template>
  <div>
    <v-app-bar
        style="border: thin solid silver"
        color="#1F2937"
        fixed
        dense
        dark
    >
<!--      <v-app-bar-nav-icon></v-app-bar-nav-icon>-->

      <v-toolbar-title style="color: white">{{ pageTitle }}</v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn
          icon
          @click="openDialog()"
      >
        <v-icon>mdi-login</v-icon>
      </v-btn>

      <v-btn
          @click="settings()"
          icon
          color="white"
      >
        <v-icon>mdi-settings</v-icon>
      </v-btn>

      <v-dialog
          v-model="logoutDialog"
          width="500"
      >
        <v-card>
          <v-card-title class="w-break uppercase">Do you wish to logout?</v-card-title>
          <v-card-actions>
            <v-btn
                id="cancel"
                ref="cancel"
                @click="cancelDialog()"
                color="red"
                text="">CANCEL
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
                id="logout"
                ref="logout"
                @click="logout()"
                color="primary"
                text="">LOGOUT
            </v-btn>
          </v-card-actions>
        </v-card>

      </v-dialog>

<!--      <v-menu-->
<!--          left-->
<!--          bottom-->
<!--      >-->
<!--        <template v-slot:activator="{ on, attrs }">-->
<!--          <v-btn-->
<!--              icon-->
<!--              v-bind="attrs"-->
<!--              v-on="on"-->
<!--          >-->
<!--            <v-icon>mdi-dots-vertical</v-icon>-->
<!--          </v-btn>-->
<!--        </template>-->

<!--        <v-list>-->
<!--          <v-list-item-->
<!--              v-for="n in 5"-->
<!--              :key="n"-->
<!--              @click="() => {}"-->
<!--          >-->
<!--            <v-list-item-title>Option {{ n }}</v-list-item-title>-->
<!--          </v-list-item>-->
<!--        </v-list>-->
<!--      </v-menu>-->
    </v-app-bar>
  </div>
</template>

<script>

export default {
  name: 'HeaderBio',
  props: {
    pageTitle: String,
    name: String
  },
  data() {
    return {
      logoutDialog: false
    }
  },
  methods: {
    logout() {
      localStorage.setItem('auth', false)
      window.location.href = '/logout';
    },
    openDialog() {
      this.logoutDialog = true
    },
    cancelDialog() {
      this.logoutDialog = false
    },
    settings() {
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

.justify-content-around {
  justify-content: space-around !important
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
  width: 100%;
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