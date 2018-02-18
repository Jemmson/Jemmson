<template>
    <!-- /all details of a bid -->
    <div class="">

        <!--<pre>{{ bid }}</pre>-->

        <div v-if="locationExists" class="form-group col-md-6">
            <label for="area">Locality</label>
            <input type="text" class="form-control" id="area" name="area" v-model="area"
                   v-on:mouseleave="updateLocality">
        </div>
        <div class="col-md-6">
            <label>Job Name: </label>
            <label class="label label-primary">
                {{ bid.job_name }}
            </label>
        </div>
        <div class="col-md-6">
            <label>Status: </label>
            <label class="label label-warning">
                {{ status }}
            </label>
        </div>
        <!-- /end detail header -->
        <div style="padding-top: 40px"></div>
        <div class="col-md-6">
            <p>
                <label>
                    Location :
                </label>
            </p>
            <p>
                {{ bid.address_line_1 }}
            </p>
            <p>
                {{ bid.city }}, {{ bid.state }} {{ bid.zip }}
            </p>
        </div>
        <div class="col-md-6">
            <p>
                <label for="price">Prices:</label>
            </p>
            <p>
                Total Job Price:
                <label class="label label-info">${{ bid.bid_price }}</label>
            </p>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      bid: Object
    },
    data () {
      return {
        area: '',
        locationExists: false
      }
    },
    computed: {
      status () {
        return User.status (this.bid.status, this.bid);
      }
    },
    methods: {
      updateLocality () {
        console.log ('customer_id: ' + this.bid.customer_id)
        console.log ('area: ' + this.area)
        axios.post ('/api/customer/updateLocality', {
          customer_id: this.bid.customer_id,
          area: this.area
        }).then (function (response) {
          console.log (response.data)
          if (!response.data.error) {
          } else {
          }
        }.bind (this))
      }
    },
    mounted: function () {
      console.log (this.bid.customer_id)
      axios.post ('/api/customer/getAddress', {
        customer_id: this.bid.customer_id,
      }).then (function (response) {
        console.log (response.data)
        if (response.data !== 'location not set') {
          this.area = response.data
          this.locationExists = true
        }
      }.bind (this))
    }
  }
</script>