<template>
    <!-- /customer approve bid form -->
    <form role="form">
        <div class="form-group col-sm-12 col-md-6">
            <label for="job_location_same_as_home">Job Location Same as Home Location</label>
            <input type="checkbox" class="form-control" id="job_location_same_as_home" v-model="form.job_location_same_as_home">
        </div>

        <div class="form-group col-sm-12 col-md-6" :class="{'has-error': form.errors.has('agreed_start_date')}">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" v-model="form.agreed_start_date">
            <span class="help-block" v-show="form.errors.has('agreed_start_date')">
                {{ form.errors.get('agreed_start_date') }}
            </span>
        </div>

        <!-- /job location -->
        <div v-if="!form.job_location_same_as_home">
            <!-- Address Line 1 -->
            <div class="form-group col-sm-12" :class="{'has-error': form.errors.has('address_line_1')}">
                <label for="">Address Line 1</label>
                <input type="text" class="form-control" name="address_line_1" v-model="form.address_line_1" autofocus>
                <span class="help-block" v-show="form.errors.has('address_line_1')">
                    {{ form.errors.get('address_line_1') }}
                </span>
            </div>

            <!-- Address Line 2 -->
            <div class="form-group col-sm-12">
                <label for="">Address Line 2</label>
                <input type="text" class="form-control" name="address_line_2" v-model="form.address_line_2">
            </div>

            <!-- City -->
            <div class="form-group col-md-6" :class="{'has-error': form.errors.has('city')}">
                <label class=" ">City</label>
                <input type="text" class="form-control" name="city" v-model="form.city">
                <span class="help-block" v-show="form.errors.has('city')">
                    {{ form.errors.get('city') }}
                </span>
            </div>

            <!-- State -->
            <div class="form-group col-md-6" :class="{'has-error': form.errors.has('state')}">
                <label for="">State</label>
                <input type="text" class="form-control" name="state" v-model="form.state">
                <span class="help-block" v-show="form.errors.has('state')">
                    {{ form.errors.get('state') }}
                </span>
            </div>

            <!-- Zip Code -->
            <div class="form-group col-md-12" :class="{'has-error': form.errors.has('zip')}">
                <label for="">ZipCode</label>
                <input type="text" class="form-control" name="zip" v-model="form.zip">
                <span class="help-block" v-show="form.errors.has('zip')">
                    {{ form.errors.get('zip') }}
                </span>
            </div>
        </div>
        <!-- / buttons -->
        <div class="form-group col-md-12">
            <button class="btn btn-success" @click.prevent="confirm" :disabled="disabled.approve">
                <span v-if="disabled.approve">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Approve
            </button>
            <button class="btn btn-primary" @click.prevent="openDeclineForm">
                Open Decline Form
            </button>
            <button class="btn btn-danger" @click.prevent="cancelBid" :disabled="disabled.cancelBid">
                <span v-if="disabled.cancelBid">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Cancel Job
            </button>
        </div>
        <!-- / decline bid section -->
        <transition name="slide-fade">
            <div v-if="showDeclineForm">
                <!-- deny message -->
                <div class="form-group col-md-12" :class="{'has-error': form.errors.has('message')}">
                    <label for="">Message</label>
                    <input type="text" class="form-control" name="message" v-model="form.message" placeholder="Optional Message">
                    <span class="help-block" v-show="form.errors.has('message')">
                        {{ form.errors.get('message') }}
                    </span>
                </div>
                <div class="form-group col-md-12">
                    <button class="btn btn-danger" @click.prevent="declineBid" :disabled="disabled.declineBid">
                        <span v-if="disabled.declineBid">
                            <i class="fa fa-btn fa-spinner fa-spin"></i>
                        </span>
                        Decline Bid
                    </button>
                </div>
            </div>
        </transition>
        <modal modalId="bidConfirmed" header="Confirmation" :body="modalBody" @modal="approve" no="Review Bid" yes="Submit Bid"></modal>
    </form>
</template>

<script>
    export default {
        props: {
            bid: Object,
        },
        data() {
            return {
                taskIndex: 0,
                form: new SparkForm({
                    id: this.bid.id,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: this.bid.status,
                    job_location_same_as_home: true,
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zip: '',
                    message: '',
                }),
                disabled: {
                    approve: false,
                    declineBid: false,
                    cancelBid: false
                },
                showDeclineForm: false,
                modalBody: Language.lang().modal.reviewBidConfirmationModal
            }
        },
        methods: {
            openDeclineForm() {
                this.showDeclineForm ? this.showDeclineForm = false : this.showDeclineForm = true;
            },
            confirm() {
                $('#modal').modal();
            },
            approve(data) {
                if (data === 'bidConfirmed') {
                    $('#modal').modal('toggle');
                    Customer.approveBid(this.form, this.disabled);
                }
            },
            declineBid() {
                Customer.declineBid(this.form, this.disabled);
            },
            cancelBid() {
                Customer.cancelBid(this.form, this.disabled);
            }
        }
    }
</script>