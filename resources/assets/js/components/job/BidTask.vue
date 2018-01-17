<template>
    <div class="panel panel-default" v-if="show">
        <!-- <div class="panel-heading">Dashboard</div> -->
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="initiateBid">
                        <div class="addBidTask">
                            <h3 class="text-center">Task: {{ task.name }}</h3>
                            <div class="form-group">
                                <label for="contractorName">Contractor Name *</label>
                                <span class="validationError" v-show="hasNameError">Please Enter A Name</span>
                                <input type="text" class="form-control" id="contractorName" name="contractorName" :placeholder="contractorName" v-model="query"
                                    v-bind:class="{ 'text-danger': hasNameError }" required v-on:keyup="autoComplete" @blur="mouseLeave('notNow')">
                                <div class="panel-footer" v-if="results.length">
                                    <ul class="list-group">
                                        <button class="list-group-item" v-for="result in results" :name="result.phone" @click="fillFields(result)">
                                            {{ result.name }}
                                        </button>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone *</label>
                                <span class="validationError" v-show="hasPhoneError">Please Enter A Valid Phone Number - xxx-xxx-xxxx</span>
                                <input type="tel" class="form-control" id="phone" name="phone" required v-bind:class="{ 'text-danger': hasPhoneError }" v-model="phone"
                                    @blur="mouseLeave('phone')">
                            </div>
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <span class="validationError" v-show="hasEmailError">Please Enter A Valid Email Address</span>
                                <input type="email" class="form-control" id="email" name="email" required v-bind:class="{ 'text-danger': hasEmailError }"
                                    v-model="email" @blur="mouseLeave('email')">
                            </div>
                            <div class="form-group">
                                <button @click="sendNotificationToSubForParticularTask()" class="btn btn-sm btn-success" type="submit">Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /end col-md-6 -->

                <div class="col-md-6">
                    <h3 class="text-center">Sub Details: {{ task.name }}</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sub</th>
                                <th>Price</th>
                                <th>Accept</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table" v-for="bid in task.bid_contractor_job_tasks" :key="bid.id">
                                <td>{{ bid.contractor_id }}</td>
                                <td>{{ bid.bid_price }}</td>
                                <td>
                                    <button @click="acceptSub(bid.id, task.id, bid.bid_price, bid.contractor_id)" class="button btn btn-sm btn-success">Accept
                                    </button>
                                </td>
                                <td>
                                    <button @click="notify(bid.id)" class="button btn btn-sm btn-warning">Notify
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /end col-md-6 -->
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            task: Object,
            show: Boolean,
        },
        data() {
            return {
                acceptSubForm: new SparkForm({
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                }),
                initiateBidForSubForm: new SparkForm({
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                }),
                userType: '',

                hasNameError: false,
                hasEmailError: false,
                hasPhoneError: false,
                query: null,
                phone: '',
                email: '',
                contractorName: '',
                results: [],

            }
        },
        methods: {
            acceptSub() {
                this.$emit('acceptSub');
            },
            notify() {

            },
            fillFields() {

            },
            sendNotificationToSubForParticularTask() {

            },
            mouseLeave() {

            },
            autoComplete() {

            }
        },
        mounted: function () {
            this.userType = Spark.state.user.usertype;
        }
    }
</script>