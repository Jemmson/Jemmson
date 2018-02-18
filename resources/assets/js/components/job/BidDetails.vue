<template>
    <!-- /all details of a bid -->
    <div class="">

        <!--<pre>{{ bid }}</pre>-->

        <div v-if="showArea()" class="form-group col-md-6">
            <label for="area">Locality</label>
            <input type="text" class="form-control" id="area" name="area" v-model="reactiveData.area"
                   v-on:mouseleave="updateArea">
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
        reactiveData: {area: ''},
        areaError: '',
        locationExists: false
      }
    },
    computed: {
      status () {
        return User.status(this.bid.status, this.bid);
      }
    },
    methods: {
      updateArea() {
        Customer.updateArea(this.reactiveData.area, this.bid.id);
      },
      showArea() {
        console.log('user type: ' + User.isContractor())
        return this.reactiveData.area !== '' && User.isContractor();
      }
    },
    mounted: function () {
      Customer.getArea(this.bid.id, this.reactiveData)
    }
  }
</script>