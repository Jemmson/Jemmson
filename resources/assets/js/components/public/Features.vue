<template>

    <v-container class="spacing" fluid>
        <v-row
                v-if="$vuetify.breakpoint.mdAndUp"
        >
            <v-col
                    cols="3"
            >
                <h5 class="text-center">Communication</h5>
            </v-col>
            <v-spacer></v-spacer>
            <v-col
                    cols="3"
            >
                <h5 class="text-center">Analytics</h5>
            </v-col>
            <v-spacer></v-spacer>
            <v-col
                    cols="3"
            >
                <h5 class="text-center">Efficiency</h5>
            </v-col>
            <v-spacer></v-spacer>
            <v-col
                    cols="3"
            >
                <h5 class="text-center">Security</h5>
            </v-col>
        </v-row>
        <div v-if="$vuetify.breakpoint.smAndDown">
            <v-row>
                <v-col
                        cols="12"
                >
                    <h5 class="text-center">Communication</h5>
                </v-col>
            </v-row>
            <v-row>
                <v-col
                        cols="12"
                >

                    <h5 class="text-center">Analytics</h5>
                </v-col>
            </v-row>
            <v-row>
                <v-col
                        cols="12"
                >
                    <h5 class="text-center">Efficiency</h5>
                </v-col>
            </v-row>
            <v-row>
                <v-col
                        cols="12"
                >
                    <h5 class="text-center">Security</h5>
                </v-col>
            </v-row>
        </div>
    </v-container>

</template>

<script>
  export default {
    name: 'Features',
    data() {
      return {}
    }
  }
</script>

<style scoped>
    .spacing {
        margin-top: 6rem
    }
</style>