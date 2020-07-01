<template>
    <v-container>



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