<template>
    <!-- /all details of a bid -->
    <div class="col-md-12">
        <section class="col-xs-12 col-md-6">
            <h3 for="job_name">{{ bid.job_name }}</h3>
            <address v-if="bid.location !== undefined && bid.location !== null">
                <br> {{ bid.location.address_line_1 }}
                <br> {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}
            </address>
        </section>
        <section class="col-xs-12 col-md-6">
            <div class="label-details">
                <span>Status: </span>
                <label class="label label-warning">
                    {{ status }}
                </label>
            </div>
            <div class="label-details">
                <span>Total Job Price: </span>
                <label class="label label-info">
                    ${{ bid.bid_price }}
                </label>
            </div>
        </section>
        <!-- /end detail header -->
    </div>
</template>

<script>
    export default {
        props: {
            bid: Object
        },
        data() {
            return {
                area: {
                    area: ''
                },
                areaError: '',
                locationExists: false
            }
        },
        computed: {
            status() {
                return User.status(this.bid.status, this.bid);
            }
        },
        methods: {
            updateArea() {
                Customer.updateArea(this.area.area, this.bid.id);
            },
            showArea() {
                console.log('user type: ' + User.isContractor())
                return this.area.area !== '' && User.isContractor();
            }
        },
        mounted: function () {
            // Customer.getArea(this.bid.id, this.area)
            Customer.getArea(1, this.area)
        }
    }
</script>