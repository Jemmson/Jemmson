<template>
    <div>
        <div class="flex space-between"
            v-if="!auth"
        >
            <v-btn
                    class="btn-color title"
                    text
                    @click="gotoPage('/')"
            >
                Home
            </v-btn>

            <v-btn
                    class="btn-color title"
                    text
                    @click="emit('features')"
            >
                Features
            </v-btn>

            <v-btn
                    class="btn-color title"
                    text
                    @click="emit('pricing')"
            >
                Pricing
            </v-btn>

            <v-btn
                    class="btn-color title"
                    text
                    @click="routeToDocumentation()"
            >
                Documentation
            </v-btn>
            <v-btn
                    class="btn-color title"
                    text
                    @click="emit('login')"
            >
                Register / Login
            </v-btn>
        </div>
        <div
            v-if="auth"
        >
            <v-btn
                    class="btn-color title"
                    text
                    @click="gotoPage('/home')"
            >
                Home
            </v-btn>
            <v-btn
                    class="btn-color title"
                    text
                    @click="emit('login')"
            >
                Settings
            </v-btn>
        </div>
    </div>
</template>

<script>

  import { mapState } from 'vuex'

  export default {
    name: 'AppBarButtonsSm',
    computed: {
      ...mapState({
        auth: state => state.auth
      })
    },
    methods: {
      goHome() {
        if (this.auth) {
          this.$router.push('/home')
        } else {
          this.$router.push('/')
        }
      },
      gotoPage(page){
        this.$router.push(page)
      },
      emit(btn) {
        this.$emit('appBtn', btn)
      },
      routeToDocumentation() {
        this.$router.push('documentation')
      }
    }
  }
</script>

<style scoped>
    .btn-color {
        color: saddlebrown;
    }
</style>