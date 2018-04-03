<template>
    <!-- /all bids shown in a list as a customer should see it -->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <!-- <div class="panel-heading">Dashboard</div> -->
                    <div class="panel-body">
                        <center>
                            <h2 class="page-title">Open Bids</h2>
                        </center>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="job-search">Search Jobs</label>
                            <input type="text" id="job-search" class="form-control" placeholder="Search" v-model="searchTerm" @keyup="search">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4" v-for="bid in sBids" v-bind:key="bid.id">
                <div class="panel">
                    <div class="panel-body">
                        <div class="col-xs-12">
                            <h4>
                                <label for="job-stats" class="label" :class="getLabelClass(bid.status)">{{ status(bid) }}</label>
                            </h4>
                            <h4 for="job-name" class="job-name">{{ jobName(bid.job_name) }}</h4>
                        </div>
                        <div class="col-xs-12">
                            <p>
                                <i class="fas fa-clock icon"></i>
                                <label for="start-date" class="start-date">{{ prettyDate(bid.agreed_start_date) }}</label>
                                <span class="right-label">
                                    <i class="fas fa-money-bill-alt icon"></i>
                                    <label for="job-price" class="job-price">${{ bid.bid_price }}</label>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-12">
                                <span class="primary-action-btn">
                                    <!-- <button class="btn btn-primary" name="review" @click="openBid(index)">Review</button> -->
                                    <router-link :to="'/bid/' + bid.id" :name="'reviewBid'+ bid.id" class="btn btn-primary">ReviewBid{{ bid.id }}</router-link>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /end col-md-8 -->
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            //user: Object,
            //pbids: Array
        },
        data() {
            return {
                bids: [],
                sBids: [],
                showBid: false,
                bidIndex: 0,
                searchTerm: ''
            }
        },
        watch: {
            '$route' (to, from) {
                // get the bids
                this.getBids();
            }
        },
        methods: {
            search() {
                this.sBids = this.bids.filter((bid) => {
                    if (this.searchTerm == '') {
                        return true;
                    }
                    return bid.job_name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1;
                })
            },
            getLabelClass(status) {
                return Format.statusLabel(status);
            },
            jobName(name) {
                return Format.jobName(name);
            },
            status(bid) {
                return User.status(bid.status, bid);
            },
            prettyDate(date) {
                if (date == null)
                    return '';
                // return the date and ignore the time
                date = date.split(' ');
                return date[0];
            },
            getBids() {
                console.log('getBids');
                axios.post('/jobs').then((response) => {
                    this.bids = response.data;
                    this.sBids = this.bids;
                });
            },
            previewSubForTask(bidId, jobTaskId, subBidId) {
                console.log(TaskUtil.previewSubForTask(this.bids, bidId, jobTaskId, subBidId));
            }
        },
        created() {
            this.getBids();
            Bus.$on('bidUpdated', (payload) => {
                this.getBids();
            });
            Bus.$on('previewSubForTask', (payload) => {
                this.previewSubForTask(payload[0], payload[1], payload[2]);
            });

        },
    }
</script>