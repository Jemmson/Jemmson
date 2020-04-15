<template>
    <section>

        <v-tabs>
            <v-tab>Jobs</v-tab>
            <v-tab>Subs</v-tab>
            <v-tab>Contractors</v-tab>
            <v-tab v-show="isContractor()">Customers</v-tab>
            <v-tab>Invoices</v-tab>
            <v-tab v-show="isContractor()">Stripe Dashboard</v-tab>
        </v-tabs>

    </section>
</template>

<script>
    export default {
        name: 'home',
        methods: {
            isContractor() {
                return true
            }
        }
    }
</script>

<style scoped>

</style>