<template>
    <div class="panel panel-default" v-if="show">
        <!-- <div class="panel-heading">Dashboard</div> -->
        <div class="panel-body">
            <!-- /customer and contractor section -->
            <div class="row">
                <pre>
                    {{ JSON.stringify(task) }}
                </pre>
            </div>

            <!-- / general contractor only section -->
            <div class="row" v-if="isGeneralContractor && !taskApproved">
                <div class="col-md-6">
                    <div class="initiateBid">
                        <div class="addBidTask">
                            <h3 class="text-center">Send Bid To Sub</h3>
                            <div class="form-group">
                                <label for="contractorName">Contractor Name *</label>
                                <span class="validationError" v-show="initiateBidForSubForm.errors.has('name')">Please Enter A Name</span>
                                <input type="text" class="form-control" id="contractorName" name="contractorName" placeholder="Name" v-model="initiateBidForSubForm.name"
                                    v-bind:class="{ 'text-danger': initiateBidForSubForm.errors.has('name')}" required v-on:keyup="autoComplete" @blur="mouseLeave('notNow')">
                                <div class="panel-footer" v-if="aResults.length">
                                    <ul class="list-group">
                                        <button class="list-group-item" v-for="result in aResults" v-bind:key="result.id" :name="result.phone" @click="fillFields(result)">
                                            {{ result.name }}
                                        </button>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group" :class="{'has-error': initiateBidForSubForm.errors.has('phone')}">
                                <label for="phone">Phone *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required v-model="initiateBidForSubForm.phone" @blur="mouseLeave('phone')">
                                <span class="help-block" v-show="initiateBidForSubForm.errors.has('phone')">
                                    {{ initiateBidForSubForm.errors.get('phone') }}
                                </span>
                            </div>
                            <div class="form-group" :class="{'has-error': initiateBidForSubForm.errors.has('email')}">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required v-model="initiateBidForSubForm.email" @blur="mouseLeave('email')">
                                <span class="help-block" v-show="initiateBidForSubForm.errors.has('email')">
                                    {{ initiateBidForSubForm.errors.get('email') }}
                                </span>
                            </div>
                            <div class="form-group">
                                <button @click="sendSubInviteToBidOnTask" class="btn btn-sm btn-success" type="submit" :disabled="disabled.invite">
                                    <span v-if="disabled.invite">
                                        <i class="fa fa-btn fa-spinner fa-spin"></i>
                                    </span>
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /end col-md-6 -->

                <div class="col-md-6">
                    <h3 class="text-center">Accept Sub Bid</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sub</th>
                                <th>Price</th>
                                <th>Privew Price</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table" v-for="bid in task.bid_contractor_job_tasks" :key="bid.id">
                                <td>{{ bid.contractor.name }}</td>
                                <td>{{ bid.bid_price }}</td>
                                <td>
                                    <button @click="preview(bid.id)" class="button btn btn-sm btn-info">Preview
                                    </button>
                                </td>
                                <td>
                                    <button v-if="showAcceptBtn" @click="acceptSubBidForTask(bid)" class="button btn btn-sm btn-success" :disabled="disabled.accept">
                                        <span v-if="disabled.accept">
                                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                                        </span>
                                        Accept
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
                    task_id: 0,
                    email: '',
                    phone: '',
                }),
                user: '',
                results: [],
                disabled: {
                    accept: false,
                    invite: false
                }
            }
        },
        methods: {
            acceptSubBidForTask(bid) {
                GeneralContractor.acceptSubBidForTask(this.task, bid, this.disabled);
            },
            sendSubInviteToBidOnTask() {
                GeneralContractor.sendSubInviteToBidOnTask(this.task, this.initiateBidForSubForm, this.disabled);
            },
            notify() {

            },
            fillFields(result) {
                this.initiateBidForSubForm.email = result.email;
                this.initiateBidForSubForm.phone = result.phone;
                this.initiateBidForSubForm.name = result.name;
                this.results = '';
            },
            mouseLeave() {

            },
            autoComplete() {
                this.results = [];
                let query = this.initiateBidForSubForm.name;
                if (query.length > 2) {
                    axios.get('/api/search', {
                        params: {
                            query: query
                        }
                    }).then(function (response) {
                        console.log('autocomplete', response.data)
                        this.results = response.data
                    }.bind(this))
                }
            }
        },
        computed: {
            showAcceptBtn() {
                return this.task.job_task.status === 'bid_task.bid_sent';
            },
            taskApproved() {
                return this.task.job_task.status === 'bid_task.approved_by_customer';
            },
            aResults() {
                if (this.results.length > 0) {
                    return this.results.filter((sub) => {
                        for (let bid of this.task.bid_contractor_job_tasks) {
                            // if invited to bid do not show in dropdown list
                            if (bid.contractor_id === sub.id) {
                                return false;                                
                            }
                        } 
                        // do not show self in dropdown list
                        return sub.id !== this.user.id;
                    });
                }
                return [];
            },
            isGeneralContractor() {
                // General contractor is the one who created the bid
                return this.task.contractor_id === this.user.id;
            }
        },
        mounted: function () {
            this.user = Spark.state.user;
        }
    }
</script>