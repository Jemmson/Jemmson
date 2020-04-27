<template>

    <div>
        <v-card-title class="w-break">{{ title }}</v-card-title>
        <v-card-text>
            <div v-for="(section, index) in sections" :key="index">
                <h4>{{ section.title }}</h4>
                <p style="color:black">{{ section.description }}</p>
            </div>
        </v-card-text>
    </div>

</template>

<script>
    export default {
        name: "InformationCard",
        props: {
          title: String,
          sections: Array
        },
        data() {
            return {
                showDialog: true
            }
        }
    }
</script>

<style scoped>

</style>