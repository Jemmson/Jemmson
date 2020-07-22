<template>
    <v-container>

        <v-card v-if="jobs">
            <v-card-title>Jobs</v-card-title>
            <v-card-text>
                <v-card-subtitle>Overview</v-card-subtitle>
                <video width="320" height="240" controls>
                    <source src="https://res.cloudinary.com/jemmson-inc/video/upload/v1593611192/RPReplay_Final1593610350_yzoxz9.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </v-card-text>
        </v-card>

    </v-container>
</template>

<script>
    export default {
        data() {
          return {
              jobs: false
          }
        },
        methods: {
            setPage() {
                this.$store.commit('setCurrentPage', '/help');
            },
        },
        created() {
            this.setPage();
        },
        mounted() {
            let page = this.$route.params.page;
            if (page) {
                if (page === 'jobs') {
                    this.jobs = true;
                }
            }
        }
    }
</script>

<style scoped>

</style>