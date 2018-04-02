<template>
    <!-- /all details of a bid -->
    <div class="job-main-wrapper" v-if="bid.job_name !== undefined">
        <div class="job-main-row job-main-header">
            <span class="title">Job Name:</span>
            <span class="title-value text-center">{{ bid.job_name }}</span>
        </div>
        <div class="job-main-row job-main-address">
            <span class="title">Address:</span>
            <a class="text-center" target="_blank" v-if="bid.location !== undefined && bid.location !== null" :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">
                <address>
                    <span>{{ bid.location.address_line_1 }}</span>
                    <br>
                    <span>{{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}</span>
                </address>
            </a>
            <!--<span class="title-value text-center">{{ bid.job_name }}</span>-->
        </div>
        <div class="job-main-row job-main-status">
            <div class="job-status">
                <span class="title job-status-label">Status:</span>
                <span class="title-value text-center  job-status-value">{{ status }}</span>
            </div>
            <div class="job-status" v-if="showBidPrice">
                <span class="title job-status-label">Total Job Price:</span>
                <span class="title-value text-center  job-status-value">${{ bid.bid_price }}</span>
            </div>
        </div>

        <!--<div class="col-md-12">-->
        <!--<section class="col-xs-12 col-md-6">-->
        <!--<div class="label-span">Job Name: </div>-->
        <!--<div class="job-name" for="job_name">{{ bid.job_name }}</div>-->
        <!--<a target="_blank" v-if="bid.location !== undefined && bid.location !== null" :href="'https://www.google.com/maps/search/?api=1&query=' + bid.location.address_line_1">-->
        <!--<address>-->
        <!--<br> {{ bid.location.address_line_1 }}-->
        <!--<br> {{ bid.location.city }}, {{ bid.location.state }} {{ bid.location.zip }}-->
        <!--</address>-->
        <!--</a>-->
        <!--</section>-->
        <!--<section class="col-xs-12 col-md-6">-->
        <!--<div class="label-details">-->
        <!--<span class="label-span">Status: </span>-->
        <!--<br>-->
        <!--<label class="label label-warning">-->
        <!--{{ status }}-->
        <!--</label>-->
        <!--</div>-->
        <!--<div class="label-details">-->
        <!--<span class="label-span">Total Job Price: </span>-->
        <!--<label class="label label-info">-->
        <!--${{ bid.bid_price }}-->
        <!--</label>-->
        <!--</div>-->
        <!--</section>-->
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
            showBidPrice() {
                if (User.isCustomer()) {
                    const status = this.bid.status;
                    if (status !== 'bid.initiated' && status !== 'bid.in_progress') {
                        return true;
                    }
                    return false;
                }
                return true;
            },
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

<style scoped>
    .job-main-wrapper {
        display: grid;
        grid-template-rows: repeat(3, 1fr);
    }

    .job-main-header {
        background-color: #eee;
        display: grid;
    }

    .job-main-address {
        background-color: white;
        display: grid;
    }

    .job-main-row {
        border-radius: 4px;
    }

    .job-main-status {
        background-color: #eee;
        display: grid;
    }

    .title {
        padding-top: 1rem;
        padding-left: 1rem;
    }

    .title-value {
        padding-right: 1rem;
        padding-bottom: 1rem;
        padding-left: 1rem;
        font-size: 2rem;
    }

    .job-status {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .job-status-label {}

    .job-status-value {
        margin-left: auto;
    }
</style>