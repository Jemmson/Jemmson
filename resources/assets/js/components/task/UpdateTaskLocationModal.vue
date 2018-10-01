<template>
    <!-- Modal -->
    <div class="modal fade" id="update-task-location-modal" tabindex="-1" role="dialog" aria-labelledby="stripe-modal" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content styled">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Task Location</h4>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <input type="hidden" name="street_number" id="street_number">
                        <input type="hidden" name="country" id="country">
                        <!-- Address Line 1 -->
                        <div class="form-group" :class="{'has-error': form.errors.has('address_line_1')}">
                            <label class="control-label">Address Line 1</label>
                            <input type="text" class="form-control" name="address_line_1" id="route2" v-model="form.address_line_1">
                            <span class="help-block" v-show="form.errors.has('address_line_1')">
                                {{ form.errors.get('address_line_1') }}
                            </span>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="form-group">
                            <label class="control-label">Address Line 2</label>
                            <input type="text" class="form-control" name="address_line_2" v-model="form.address_line_2">
                        </div>

                        <!-- City -->
                        <div class="form-group" :class="{'has-error': form.errors.has('city')}">
                            <label class="control-label">City</label>
                            <input type="text" class="form-control" name="city" id="administrative_area_level_1" v-model="form.city">
                            <span class="help-block" v-show="form.errors.has('city')">
                                {{ form.errors.get('city') }}
                            </span>
                        </div>

                        <!-- State -->
                        <div class="form-group" :class="{'has-error': form.errors.has('state')}">
                            <label class="control-label">State</label>
                            <input type="text" class="form-control" name="state" id="locality" v-model="form.state">
                            <span class="help-block" v-show="form.errors.has('state')">
                                {{ form.errors.get('state') }}
                            </span>
                        </div>

                        <!-- Zip Code -->
                        <div class="form-group" :class="{'has-error': form.errors.has('zip')}">
                            <label class="control-label">ZipCode</label>
                            <input type="text" class="form-control" name="zip" id="postal_code" v-model="form.zip">
                            <span class="help-block" v-show="form.errors.has('zip')">
                                {{ form.errors.get('zip') }}
                            </span>
                        </div>
                    </form>
                    <!-- /end col-md6ss -->
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button @click="update" class="btn btn-sm btn-success" type="submit" :disabled="disabled.update">
                            <span v-if="disabled.update">
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
                form: new SparkForm({
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zip: '',
                    id: '',
                    location_id: ''
                }),
                disabled: {
                    update: false
                }
            }
        },
        methods: {
            updateFormLocation(location) {
                this.form.address_line_1 = location.route;
                this.form.city = location.locality;
                this.form.state = location.administrative_area_level_1;
                this.form.zip = location.postal_code;
            },
            update() {
                this.form.id = this.jobTask.id;
                this.form.location_id = this.jobTask.location_id;
                User.updateTaskLocation(this.form, this.disabled);
            },
            initAutocomplete () {
                User.initAutocomplete('route2');
            }
        },
        computed: {},
        mounted: function () {
            this.initAutocomplete();
            this.form.address_line_1 = this.jobTask.address_line_1;
            this.form.address_line_2 = this.jobTask.address_line_2;
            this.form.city = this.jobTask.city;
            this.form.state = this.jobTask.state;
            this.form.zip = this.jobTask.zip;
            Bus.$on('updateFormLocation', (payload) => {
                this.updateFormLocation(payload);
            });
        }
    }
</script>

<style scoped>
    .styled {
        margin-top: 10rem;
        margin-bottom: 10rem;
    }
</style>