<spark-update-contact-information :user="user" inline-template>
    <div class="panel panel-default">
        <div class="panel-heading">Contact Information</div>

        <div class="panel-body">
            <!-- Success Message -->
            <div class="alert alert-success" v-if="form.successful">
                Your contact information has been updated!
            </div>

            <form class="form-horizontal" role="form">
                <!-- Name -->
                <div class="form-group" :class="{'has-error': form.errors.has('name')}">
                    <label class="col-md-4 control-label">Name</label>

                    <div class="col-md-6">
                        <input type="text" class="form-control" name="name" v-model="form.name">

                        <span class="help-block" v-show="form.errors.has('name')">
                            @{{ form.errors.get('name') }}
                        </span>
                    </div>
                </div>

                <!-- E-Mail Address -->
                <div class="form-group" :class="{'has-error': form.errors.has('email')}">
                    <label class="col-md-4 control-label">E-Mail Address</label>

                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" v-model="form.email">

                        <span class="help-block" v-show="form.errors.has('email')">
                            @{{ form.errors.get('email') }}
                        </span>
                    </div>
                </div>

                <!-- Address Line 1 -->
                <div class="form-group" :class="{'has-error': form.errors.has('address_line_1')}">
                    <label class="col-md-3 control-label">Address Line 1</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="address_line_1" id="route"
                               v-model="form.address_line_1">
                        <span class="help-block" v-show="form.errors.has('address_line_1')">
                                    @{{ form.errors.get('address_line_1') }}
                                </span>
                    </div>
                </div>

                <!-- Address Line 2 -->
                <div class="form-group">
                    <label class="col-md-3 control-label">Address Line 2</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="address_line_2"
                               v-model="form.address_line_2">
                    </div>
                </div>

                <!-- City -->
                <div class="form-group" :class="{'has-error': form.errors.has('city')}">
                    <label class="col-md-3 control-label">City</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="city" id="administrative_area_level_1" v-model="form.city">
                        <span class="help-block" v-show="form.errors.has('city')">
                                    @{{ form.errors.get('city') }}
                                </span>
                    </div>
                </div>

                <!-- State -->
                <div class="form-group" :class="{'has-error': form.errors.has('state')}">
                    <label class="col-md-3 control-label">State</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="state" id="locality" v-model="form.state">
                        <span class="help-block" v-show="form.errors.has('state')">
                                    @{{ form.errors.get('state') }}
                                </span>
                    </div>
                </div>

                <!-- Zip Code -->
                <div class="form-group" :class="{'has-error': form.errors.has('zip')}">
                    <label class="col-md-3 control-label">ZipCode</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="zip" id="postal_code" v-model="form.zip">
                        <span class="help-block" v-show="form.errors.has('zip')">
                                    @{{ form.errors.get('zip') }}
                                </span>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="form-group">
                    <div class="col-md-offset-4 col-md-6">
                        <button type="submit" class="btn btn-primary"
                                @click.prevent="update"
                                :disabled="form.busy">

                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</spark-update-contact-information>
