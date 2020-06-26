<template>
    <div>
        {{ $route.params.location }}

        <pre>{{ locationData }}</pre>
    </div>
</template>

<script>
    export default {
        name: 'Assessor',
        data() {
            return {
                locationData: {}
            }
        },
        methods: {
            setPage(){
                this.$store.commit('setCurrentPage', '/assessor');
            },
            async getAssessorData() {
              const {data} = await axios.get('/assessor/' + this.$route.params.location);
              
              console.log('data', data)
              
              if (data.error) {
                  
              } else {
                  this.locationData = data;
              }
            }
        },
        created() {
            this.setPage();
            this.getAssessorData();
        }
    }
</script>

<style scoped>

</style>