<template>
    <div>
        <div class="flex space-between"
             v-if="!auth"
        >
            <v-btn
                    @click="gotoPage('/')"
                    icon
            >
                <v-icon>mdi-home-outline</v-icon>
            </v-btn>

            <v-btn
                    @click="emit('features')"
                    icon
            >
                <v-icon>mdi-earth</v-icon>
            </v-btn>

            <v-btn
                    @click="emit('pricing')"
                    icon
            >
                <v-icon>mdi-account-cash</v-icon>
            </v-btn>

            <v-btn
                    @click="emit('documentation')"
                    icon
            >
                <v-icon>mdi-file-document</v-icon>
            </v-btn>

            <v-btn
                    icon
                    @click="emit('login')"
            >
                <v-icon>mdi-login</v-icon>
            </v-btn>
        </div>
        <div
            v-if="auth"
        >
            <v-btn
                    @click="gotoPage('/home')"
                    icon
            >
                <v-icon>mdi-home-outline</v-icon>
            </v-btn>

            <v-btn
                    @click="gotoSettings()"
                    icon
            >
                <v-icon>mdi-settings</v-icon>
            </v-btn>
        </div>
    </div>
</template>

<script>

  import { mapState } from 'vuex'

  export default {
    name: 'AppBarButtons',
    computed: {
      ...mapState({
        auth: state => state.auth
      })
    },
    methods: {
      goHome(){
        if (this.auth) {
          this.$router.push('/home')
        } else {
          this.$router.push('/')
        }
      },
      gotoSettings(){
        window.location.href = '/settings'
      },
      gotoPage(page){
        this.$router.push(page)
      },
      emit(btn) {
        this.$emit('appBtn', btn)
      },
      showContent(btn) {
        this.emit(btn)
      }
    }
  }
</script>

<style scoped>

</style>