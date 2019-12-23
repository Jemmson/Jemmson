<template>
    <v-content v-if="$vuetify.breakpoint.smAndUp">

        <div class="flex space-between">
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
                Login
            </v-btn>
        </div>
    </v-content>
</template>

<script>

  import Features from '../public/Features'

  export default {
    name: 'AppBarButtonsSm',
    methods: {
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