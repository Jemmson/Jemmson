<template>
    <div>
        <v-app-bar
                color="#95ca97"
                fixed
                height="100px"
                :extended="extended"
        >


            <v-content v-if="$vuetify.breakpoint.xs">
                <template class="flex flex-column">
                    <div class="flex space-between">
                        <v-app-bar-nav-icon
                                @click.stop="toggleDrawer()"
                        >
                        </v-app-bar-nav-icon>
                        <v-toolbar-title
                                class="display-1"
                        ><strong style="color: white !important;">Jemmson</strong></v-toolbar-title>
                        <div></div>
                    </div>
                    <app-bar-buttons
                            @appBtn="showContent($event)"
                    >
                    </app-bar-buttons>
                </template>
            </v-content>
            <v-content
                    v-else
            >
                <v-toolbar-title
                        class="display-1"
                ><strong style="color: white !important;">Jemmson</strong></v-toolbar-title>
                <v-spacer></v-spacer>
                <app-bar-buttons-sm
                        @appBtn="showContent($event)">
                </app-bar-buttons-sm>
            </v-content>
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
        drawer: false,
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
      toggleDrawer() {
        this.$emit('drawer', true)
      },
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
        } else if (content === 'documentation') {
          this.$router.push('documentation')
        } else if (content === 'pricing') {
          this.$router.push('pricing')
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