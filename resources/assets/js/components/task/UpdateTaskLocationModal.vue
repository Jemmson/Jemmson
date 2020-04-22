<template>
    <!-- Modal -->
    <div class="modal h-100 modal-background-gray" :id="'update-task-location-modal_'+id" tabindex="-1" role="dialog"
         aria-labelledby="stripe-modal" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <v-card>
                        <v-card-title>Update Task Location</v-card-title>
                        <v-card-text>
                            <input type="hidden" name="street_number" id="street_number">
                            <v-text-field
                                    id="route"
                                    v-model="form.address_line_1"
                                    label="Address Line 1 *"
                            >
                            </v-text-field>
                            <span class="help-block" v-show="form.errors.address_line_1 !== ''">{{form.errors.address_line_1}}</span>

                            <v-text-field
                                    id="addressLine2"
                                    v-model="form.address_line_2"
                                    label="Address Line 2 *"
                            >
                            </v-text-field>
                            <span class="help-block" v-show="form.errors.address_line_2 !== ''">{{form.errors.address_line_2}}</span>

                            <v-text-field
                                    id="administrative_area_level_1"
                                    v-model="form.city"
                                    label="City *"
                            >
                            </v-text-field>
                            <span class="help-block"
                                  v-show="form.errors.city !== ''">{{form.errors.city}}</span>

                            <v-text-field
                                    id="locality"
                                    v-model="form.state"
                                    label="State *"
                            >
                            </v-text-field>
                            <span class="help-block"
                                  v-show="form.errors.state !== ''">{{form.errors.state}}</span>

                            <v-text-field
                                    id="postal_code"
                                    v-model="form.zip"
                                    label="Zip Code *"
                            >
                            </v-text-field>
                            <span class="help-block"
                                  v-show="form.errors.zip !== ''">{{form.errors.zip}}</span>
                        </v-card-text>

                        <v-card-actions>
                            <v-btn
                                    class="w-full"
                                    text
                                    color="primary"
                                    @click="update" type="submit" :loading="disabled.update">
                                Submit
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import User from '../../classes/User'

    export default {
        props: {
            jobTask: Object,
            id: Number
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
                },
                authUser: {}
            }
        },
        methods: {
            updateFormLocation(location) {
                this.form.address_line_1 = location.route
                this.form.city = location.locality
                this.form.state = location.administrative_area_level_1
                this.form.zip = location.postal_code
            },
            update() {
                if (this.jobTask) {
                    this.form.id = this.jobTask.id
                    this.form.location_id = this.jobTask.location_id
                    this.authUser.updateTaskLocation(this.form, this.disabled)
                    $('#update-task-location-modal_' + this.id).modal('hide')
                }
            },
            initAutocomplete() {
                this.authUser.initAutocomplete('route')
            }
        },
        computed: {},
        mounted: function () {
            this.initAutocomplete()
            Bus.$on('updateFormLocation', (payload) => {
                this.updateFormLocation(payload)
            })
        },
        created() {
            this.authUser = new User()
        }
    }
</script>

<style scoped>
    .styled {
        margin-top: 10rem;
        margin-bottom: 10rem;
    }
</style>