<template>
    <div>

        <pre>{{ contractor }}</pre>

<!--        <section ref="job_address" class="col-12">-->
<!--            <h1 class="card-title mt-4">Company Address</h1>-->
<!--            <card>-->

<!--                <div class="flex flex-col">-->
<!--                    <div>-->
<!--                        {{ bid.location.address_line_1 }}-->
<!--                    </div>-->
<!--                    <div>-->
<!--                        {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}-->
<!--                    </div>-->
<!--                </div>-->

<!--                <hr>-->

<!--                <main class="map-responsive">-->
<!--                    <iframe-->
<!--                            width="450"-->
<!--                            height="250"-->
<!--                            frameborder="0" style="border:0"-->
<!--                            :src="'https://www.google.com/maps/embed/v1/search?key=AIzaSyBAQZB-zS1HVbyNe2JEk1IgNVl0Pm2xsno&q=' +-->
<!--                            bid.location.address_line_1 + ' ' +-->
<!--                            bid.location.city + ' ' +-->
<!--                            bid.location.state + ' ' +-->
<!--                            bid.location.zip-->
<!--                            " allowfullscreen>-->
<!--                    </iframe>-->
<!--                </main>-->
<!--            </card>-->
<!--        </section>-->

    </div>
</template>

<script>
  export default {
    name: 'ContractorInfo',
    props: {
      contractorId: Number
    },
    data () {
      return {
        contractor: []
      }
    },
    created() {
      this.getContractor()
    },
    methods: {
      async getContractor() {
        try {
          const data = await axios.get('/getContractor/' + this.contractorId)
          this.contractor = data.data
        } catch (error) {
          console.log('error')
        }
      }
    }
  }
</script>

<style scoped>

</style>