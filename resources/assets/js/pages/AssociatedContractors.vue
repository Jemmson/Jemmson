<template>
    <v-card
        :loading="loading"
        >
        <v-card-title>Associated Contractors</v-card-title>
        <v-card-text>
            <v-list-item
                    selectable
                    @click="viewContractorInfo(item.id)"
                    v-for="item in associated" :key="item.id">
                <v-list-item-content>Name</v-list-item-content>
                <v-list-item-content v-if="item.contractor.company_name">{{ item.contractor.company_name }}</v-list-item-content>
                <v-list-item-content v-else>{{ item.name }}</v-list-item-content>
            </v-list-item>
        </v-card-text>
    </v-card>

</template>

<script>
    export default {
        name: "AssociatedContractors",
        data() {
            return {
                error: {
                    exists: false,
                    message: null
                },
                associated: null,
                loading: false
            }
        },
        methods: {
            async getSubs() {
                this.loading = true;
                const {data} = await axios.get('/contractors/getSubs', {});

                if (data.error) {
                    this.error.exists = true;
                    this.error.message = data.error.message;
                } else {
                    this.associated = data
                }
                this.loading = false;
            },
            viewContractorInfo(contractorId) {
                this.$router.push({name: 'contractor-info', params: {contractorId: contractorId}})
            },
        },
        mounted() {
            this.$store.commit('setCurrentPage', '/associatedContractors');
            this.getSubs();
        }
    }
</script>

<style scoped>

</style>