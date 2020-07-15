<template>
    <v-container>

        <video width="320" height="240" controls>
            <source src="https://res.cloudinary.com/jemmson-inc/video/upload/v1593611192/RPReplay_Final1593610350_yzoxz9.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <h1>Help</h1>
        <h1>{{ this.$route.params.page }}</h1>
    </v-container>
</template>

<script>
    export default {
        methods: {
            setPage() {
                this.$store.commit('setCurrentPage', '/help');
            },
        },
        created() {
            this.setPage();
        }
    }
</script>

<style scoped>

</style>