<template>
    <div>
        <v-app-bar
                color="#95ca97"
                fixed
                height="100px"
                :extended="extended"
        >
            <v-toolbar-title
            class="display-1"
            ><strong style="color: white !important;">Jemmson</strong></v-toolbar-title>
            <v-spacer></v-spacer>
            <app-bar-buttons-sm
                    @appBtn="showContent($event)">
            </app-bar-buttons-sm>
            <app-bar-buttons
                    @appBtn="showContent($event)"
            >
            </app-bar-buttons>
        </v-app-bar>

        <features
                v-if="this.show.features"
        ></features>

        <login
                v-if="this.show.login"
        >
        </login>

    </div>
</template>

<script>

  import Features from '../public/Features'
  import Login from '../public/Login'
  import AppBarButtons from './AppBarButtons'
  import AppBarButtonsSm from './AppBarButtonsSm'

  export default {
    name: 'AppBar',
    data() {
      return {
        extended: false,
        extendedHeight: 0,
        show: {
          features: false,
          pricing: false,
          documentation: false,
          login: false
        }
      }
    },
    components: {
      Features,
      AppBarButtons,
      AppBarButtonsSm,
      Login
    },
    methods: {
      showContent(content) {
        if (content === 'features') {
          if (!this.show.features) {
            this.hideAllContent()
            this.show.features = true
          } else {
            this.show.features = false
          }
        } else if (content === 'login') {
          if (!this.show.login) {
            this.hideAllContent()
            this.show.login = true
          } else {
            this.show.login = false
          }
        }
      },
      hideAllContent() {
        this.show.features = false
        this.show.pricing = false
        this.show.documentation = false
      }
    }
  }
</script>

<style scoped>

</style>