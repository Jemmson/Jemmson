<template>
    <v-content v-if="$vuetify.breakpoint.xs">
        <div class="flex space-between">
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
                    @click="showContent('login')"
            >
                <v-icon>mdi-login</v-icon>
            </v-btn>
        </div>
    </v-content>
</template>

<script>

  export default {
    name: 'AppBarButtons',
    methods: {
      emit(btn) {
        this.$emit('appBtn', btn)
      },
      showContent(btn){
        this.emit(btn)
      }
    }
  }
</script>

<style scoped>

</style>