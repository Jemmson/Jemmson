<template>
    <v-container>

        <app-bar></app-bar>

        <v-col
                align="center"
                justify="center"
                fluid
                class="header-margin"
        >
            <div class="display-2 header-title">Pricing</div>
            <v-row v-if="$vuetify.breakpoint.mdAndUp"

            >
                <v-card
                        class="card-margin card-size"
                >
                    <div class="title text-center title-height"><strong class="display-1">Monthly Plan</strong></div>
                    <v-card-text>
                        <div class="title text-center">Unlimited Jobs</div>
                        <div class="title text-center">50$ / Month</div>
                    </v-card-text>
                    <v-card-text class="uppercase details">
                        monthly and cancel anytime
                    </v-card-text>
                </v-card>
                <v-spacer></v-spacer>
                <v-card
                        class="card-margin card-size"
                >
                    <div class="title text-center title-height"><strong class="display-1">Yearly Plan</strong></div>
                    <v-card-text>
                        <div class="title text-center">Unlimited Jobs</div>
                        <div class="title text-center">550$ / Year</div>
                    </v-card-text>
                    <v-card-text class="uppercase details">
                        Simplify your billing and
                        Get one month free
                    </v-card-text>
                </v-card>
            </v-row>

            <div
                    v-else
            >

                <v-card
                        class="card-margin"
                >
                    <v-card-title class="title text-center">Monthly Plan</v-card-title>
                    <v-card-text>
                        <div class="title text-center">Unlimited Jobs</div>
                        <div class="title text-center">50$ / Month</div>
                    </v-card-text>
                    <v-card-text class="uppercase details">
                        monthly and cancel anytime
                    </v-card-text>
                </v-card>

                <v-card
                        class="card-margin"
                >
                    <v-card-title class="title text-center">Yearly Plan</v-card-title>
                    <v-card-text>
                        <div class="title text-center">Unlimited Jobs</div>
                        <div class="title text-center">550$ / Year</div>
                    </v-card-text>
                    <v-card-text class="uppercase details">
                        Simplify your billing and
                        Get one month free
                    </v-card-text>
                </v-card>

            </div>
        </v-col>
    </v-container>
</template>

<script>
  import AppBar from '../components/header/AppBar'

  export default {
    name: 'Pricing',
    components: {
      AppBar
    }
  }
</script>

<style scoped>
    .header-margin {
        margin-top: 6rem;
    }

    .header-title {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .card-margin {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .card-size {
        width: 40%;
        height: 200px;
    }

    .title-height {
        margin-top: 1rem;
    }

    .details {
        background-color: antiquewhite;
    }

</style>