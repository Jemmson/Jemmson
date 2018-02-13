<template>
    <!-- /customer approve bid form -->
    <form role="form">
        <div class="form-group col-sm-12 col-md-6">
            <label for="job_same_as_home_location">Job Location Same as Home Location</label>
            <input type="checkbox" class="form-control" id="job_same_as_home_location" v-model="form.job_same_as_home_location">
        </div>

        <div class="form-group col-sm-12 col-md-6" :class="{'has-error': form.errors.has('agreed_start_date')}">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date" v-model="form.agreed_start_date">
            <span class="help-block" v-show="form.errors.has('agreed_start_date')">
                {{ form.errors.get('agreed_start_date') }}
            </span>
        </div>

        <!-- /job location -->
        <div v-if="!form.job_same_as_home_location">
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
                <input type="text" class="form-control" name="address_line_2" v-model="form.address_line_2" autofocus>
            </div>

            <!-- City -->
            <div class="form-group col-md-6" :class="{'has-error': form.errors.has('city')}">
                <label class=" ">City</label>
                <input type="text" class="form-control" name="city" v-model="form.city" autofocus>
                <span class="help-block" v-show="form.errors.has('city')">
                    {{ form.errors.get('city') }}
                </span>
            </div>

            <!-- State -->
            <div class="form-group col-md-6" :class="{'has-error': form.errors.has('state')}">
                <label for="">State</label>
                <input type="text" class="form-control" name="state" v-model="form.state" autofocus>
                <span class="help-block" v-show="form.errors.has('state')">
                    {{ form.errors.get('state') }}
                </span>
            </div>

            <!-- Zip Code -->
            <div class="form-group col-md-12" :class="{'has-error': form.errors.has('zip')}">
                <label for="">ZipCode</label>
                <input type="text" class="form-control" name="zip" v-model="form.zip" autofocus>
                <span class="help-block" v-show="form.errors.has('zip')">
                    {{ form.errors.get('zip') }}
                </span>
            </div>
        </div>
        <!-- / buttons -->
        <div class="form-group col-md-12">
            <button class="btn btn-success" @click.prevent="approve" :disabled="disabled.approve">
                <span v-if="disabled.approve">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Approve
            </button>
            <button class="btn btn-danger" @click.prevent="declineBid" :disabled="disabled.declineBid">
                <span v-if="disabled.declineBid">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Decline
            </button>
        </div>
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
                    id: 0,
                    agreed_start_date: '',
                    end_date: '',
                    area: '',
                    status: '',
                    job_same_as_home_location: true,
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zip: '',
                }),
                user: '',
                disabled: {
                    approve: false,
                    declineBid: false
                },
            }
        },
        methods: {
            approve() {
                Customer.approveBid(this.form, this.disabled);
            },
        },
        mounted: function () {
            // set up init data
            this.form.id = this.bid.id;
            this.form.status = this.bid.status;
            this.user = Spark.state.user;
        }
    }
</script>