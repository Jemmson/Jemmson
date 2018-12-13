<template>
    <!-- Modal -->
    <div class="modal fade" id="sub-invite-modal" tabindex="-1" role="dialog" aria-labelledby="stripe-modal" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content styled">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 v-if="initiateBidForSubForm.counter <= 0" class="modal-title">Invite A Subcontractor - {{ jobTask.task === undefined ? '' : jobTask.task.name.toUpperCase() }}</h4>
                    <h4 v-else>Sent Invite - {{ jobTask.task === undefined ? '' : jobTask.task.name.toUpperCase() }} - would you like to invite another sub to bid on this task?</h4>
                </div>
                <div class="modal-body">
                        <form role="form">
                                <div class="form-group">
                                    <label for="contractorName">Company Name *</label>
                                    <span class="validationError" 
                                          v-show="initiateBidForSubForm.errors.has('name')" 
                                          ref="name">Please Enter A Name
                                    </span>
                                    <input 
                                       type="text"
                                       class="form-control"
                                       id="contractorName" 
                                       name="contractorName" 
                                       v-model="name"
                                       placeholder="Name"
                                       v-bind:class="{ 'text-danger': initiateBidForSubForm.errors.has('name')}" 
                                       autofocus 
                                       required 
                                       v-on:keyup="autoComplete"
                                    >
                                     <div 
                                        class="panel-footer" 
                                        v-if="aResults.length">
                                        <ul class="list-group">
                                            <button
                                                class="list-group-item" 
                                                v-for="result in aResults" 
                                                v-bind:key="result.id" 
                                                :name="result.phone" 
                                                @click.prevent="fillFields(result)"
                                            >
                                                {{ result.name }} - {{ result.contractor.company_name }}
                                            </button>
                                        </ul>        
                                    </div>
                                    <!-- <input 
                                        type="text" 
                                        class="form-control" 
                                        id="contractorName" 
                                        name="contractorName" 
                                        placeholder="Name" 
                                        v-model="initiateBidForSubForm.name"
                                        v-bind:class="{ 'text-danger': initiateBidForSubForm.errors.has('name')}" 
                                        autofocus 
                                        required 
                                        v-on:keyup="autoComplete">
                                    <div 
                                        class="panel-footer" 
                                        v-if="aResults.length">
                                        <ul class="list-group">
                                            <button 
                                                class="list-group-item" 
                                                v-for="result in aResults" 
                                                v-bind:key="result.id" 
                                                :name="result.phone" 
                                                @click.prevent="fillFields(result)">
                                                {{ result.name }} - {{ result.contractor.company_name }}
                                            </button>
                                        </ul>
                                    </div> -->
                                </div>
                                <div class="form-group" :class="{'has-error': initiateBidForSubForm.errors.has('phone')}">
                                    <label for="phone">Phone *</label>
                                    <input type="tel" 
                                           placeholder="Phone Number"
                                           class="form-control" id="phone" name="phone" maxlength="10" required v-model="initiateBidForSubForm.phone"
                                        @keyup="filterPhone">
                                    <span class="help-block" v-show="initiateBidForSubForm.errors.has('phone')">
                                        {{ initiateBidForSubForm.errors.get('phone') }}
                                    </span>
                                </div>
                                <div class="form-group" :class="{'has-error': initiateBidForSubForm.errors.has('email')}">
                                    <label for="email">Email</label>
                                    <input type="email" 
                                           placeholder="Email"
                                           class="form-control" id="email" name="email" v-model="initiateBidForSubForm.email">
                                    <span class="help-block" v-show="initiateBidForSubForm.errors.has('email')">
                                        {{ initiateBidForSubForm.errors.get('email') }}
                                    </span>
                                </div>
                        </form>
                    <!-- /end col-md6ss -->
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button @click="sendSubInviteToBidOnTask" class="btn btn-green" type="submit" :disabled="disabled.invite" ref="submit">
                            <span v-if="disabled.invite">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>
                            </span>
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            jobTask: Object,
        },
        data() {
            return {
                initiateBidForSubForm: new SparkForm({
                    task_id: 0,
                    email: '',
                    phone: '',
                    counter: 0,
                }),
                name: '',
                user: '',
                results: [],
                disabled: {
                    accept: false,
                    invite: false
                }
            }
        },
        methods: {
            filterPhone() {
                this.initiateBidForSubForm.phone = Format.phone(this.initiateBidForSubForm.phone);
            },
            sendSubInviteToBidOnTask() {
                GeneralContractor.sendSubInviteToBidOnTask(this.jobTask, this.initiateBidForSubForm, this.disabled);
            },
            fillFields(result) {
                this.initiateBidForSubForm.email = result.email;
                this.initiateBidForSubForm.phone = result.phone;
                this.initiateBidForSubForm.name = result.name;
                this.results = '';
            },
            autoComplete() {
                this.results = [];
                let query = this.name;
                // let query = this.initiateBidForSubForm.name;
                // let query = this.initiateBidForSubForm.name;
                console.log("checking for names");
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
            aResults() {
                // if (this.results.length > 0) {
                //     return this.results.filter((sub) => {
                //         for (let bid of this.jobTask.bid_contractor_job_tasks) {
                //             // if invited to bid do not show in dropdown list
                //             if (bid.contractor_id === sub.id) {
                //                 return false;
                //             }
                //         }
                //         // do not show self in dropdown list
                //         return sub.id !== this.user.id;
                //     });
                // }
                // return [];
                if (this.results.length > 0) {
                    // return this.results.filter((sub) => {
                    // }
                    return this.results;
                } else {
                    return [];
                }
            },
            isGeneralContractor() {
                // General contractor is the one who created the bid
                return this.jobTask.task.contractor_id === this.user.id;
            }
        },
        mounted: function () {
            this.user = Spark.state.user;
        }
    }
</script>

<style scoped>
    .styled {
        margin-top: 10rem;
        margin-bottom: 10rem;
    }
</style>
