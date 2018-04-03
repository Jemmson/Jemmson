<template>
    <!-- Modal -->
    <div class="modal fade" id="update-task-location-modal" tabindex="-1" role="dialog" aria-labelledby="stripe-modal" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Task Location</h4>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <!-- Address Line 1 -->
                        <div class="form-group" :class="{'has-error': form.errors.has('address_line_1')}">
                            <label class="col-md-3 control-label">Address Line 1</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="address_line_1" v-model="form.address_line_1" autofocus>
                                <span class="help-block" v-show="form.errors.has('address_line_1')">
                                    {{ form.errors.get('address_line_1') }}
                                </span>
                            </div>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">Address Line 2</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="address_line_2" v-model="form.address_line_2">
                            </div>
                        </div>

                        <!-- City -->
                        <div class="form-group" :class="{'has-error': form.errors.has('city')}">
                            <label class="col-md-3 control-label">City</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="city" v-model="form.city">
                                <span class="help-block" v-show="form.errors.has('city')">
                                    {{ form.errors.get('city') }}
                                </span>
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group" :class="{'has-error': form.errors.has('state')}">
                            <label class="col-md-3 control-label">State</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="state" v-model="form.state">
                                <span class="help-block" v-show="form.errors.has('state')">
                                    {{ form.errors.get('state') }}
                                </span>
                            </div>
                        </div>

                        <!-- Zip Code -->
                        <div class="form-group" :class="{'has-error': form.errors.has('zip')}">
                            <label class="col-md-3 control-label">ZipCode</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="zip" v-model="form.zip">
                                <span class="help-block" v-show="form.errors.has('zip')">
                                    {{ form.errors.get('zip') }}
                                </span>
                            </div>
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
            update() {
                this.form.id = this.jobTask.id;
                this.form.location_id = this.jobTask.location_id;
                User.updateTaskLocation(this.form, this.disabled);
            }
        },
        computed: {
        },
        mounted: function () {
            this.form.address_line_1 = this.jobTask.address_line_1;
            this.form.address_line_2 = this.jobTask.address_line_2;
            this.form.city = this.jobTask.city;
            this.form.state = this.jobTask.state;
            this.form.zip = this.jobTask.zip;
        }
    }
</script>