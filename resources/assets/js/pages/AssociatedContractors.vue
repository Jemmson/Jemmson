<template>
    <v-card
        :loading="loading"
        >

        <v-card-title>My Subs</v-card-title>
        <v-data-table
                :headers="headers"
                :items="subs"
                class="elevation-1"
        >
            <template v-slot:item.id="{ item }">
                <v-btn
                    text
                    color="primary"
                    @click="viewContractorInfo(item.id)"
                >
                    Select
                </v-btn>
            </template>
        </v-data-table>
    </v-card>

</template>

<script>
    export default {
        name: "AssociatedContractors",
        data() {
            return {
                selected: [],
                error: {
                    exists: false,
                    message: null
                },
                headers: [
                    {
                        text: 'Company Name',
                        align: 'start',
                        sortable: false,
                        value: 'company_name',
                    },
                    { text: 'Name', value: 'name' },
                    { text: '', value: 'id' },
                ],
                item: {
                    id: null
                },
                subs: [],
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
                    this.subs = this.dataTransformed(data);
                }
                this.loading = false;
            },
            dataTransformed(subs){
                let transformed = [];
                for (let i = 0; i < subs.length; i++) {
                    let company = {};
                    company.id = subs[i].id;
                    company.name = subs[i].name;
                    company.company_name = subs[i].contractor.company_name;
                    transformed.push(company)
                }
                return transformed;
            },
            viewContractorInfo(event) {
                this.$router.push({name: 'contractor-info', params: {contractorId: event}})
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