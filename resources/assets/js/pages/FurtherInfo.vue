<template>
    <div>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading" v-if="isContractor">Register Your Company</div>
                <div class="panel-heading" v-if="!isContractor">Please Add Additional Information</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="post" role="form">

                        <!-- Company Name -->
                        <div class="form-group" :class="{'has-error': form.errors.has('company_name')}"
                             v-if="isContractor">
                            <label class="col-md-3 control-label">Company Name</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="company_name" v-model="form.company_name"
                                       autofocus>
                                <span class="help-block" v-show="form.errors.has('company_name')">
                                    {{ form.errors.get('company_name') }}
                                </span>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group" :class="{'has-error': form.errors.has('phone_number')}">
                            <label class="col-md-3 control-label">Phone Number</label>
                            <div class="col-md-8">
                                <input type="tel" class="form-control" name="phone_number" maxlength="10" v-model="form.phone_number" @keyup="filterPhone">
                                <span class="help-block" v-show="form.errors.has('phone_number')">
                                    {{ form.errors.get('phone_number') }}
                                </span>
                            </div>
                        </div>

                        <!-- Address Line 1 -->
                        <div class="form-group" :class="{'has-error': form.errors.has('address_line_1')}">
                            <label class="col-md-3 control-label">Address Line 1</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="address_line_1"
                                       v-model="form.address_line_1" autofocus>
                                <span class="help-block" v-show="form.errors.has('address_line_1')">
                                    {{ form.errors.get('address_line_1') }}
                                </span>
                            </div>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="form-group">
                            <label class="col-md-3 control-label">Address Line 2</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="address_line_2"
                                       v-model="form.address_line_2" autofocus>
                            </div>
                        </div>

                        <!-- City -->
                        <div class="form-group" :class="{'has-error': form.errors.has('city')}">
                            <label class="col-md-3 control-label">City</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="city" v-model="form.city" autofocus>
                                <span class="help-block" v-show="form.errors.has('city')">
                                    {{ form.errors.get('city') }}
                                </span>
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group" :class="{'has-error': form.errors.has('state')}">
                            <label class="col-md-3 control-label">State</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="state" v-model="form.state" autofocus>
                                <span class="help-block" v-show="form.errors.has('state')">
                                    {{ form.errors.get('state') }}
                                </span>
                            </div>
                        </div>

                        <!-- Zip Code -->
                        <div class="form-group" :class="{'has-error': form.errors.has('zip')}">
                            <label class="col-md-3 control-label">ZipCode</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="zip" v-model="form.zip" autofocus>
                                <span class="help-block" v-show="form.errors.has('zip')">
                                    {{ form.errors.get('zip') }}
                                </span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group" v-if="!isContractor">
                            <label class="col-md-3 control-label">Any Special Notes</label>
                            <div class="col-md-8">
                                <textarea name="notes" id="notes" cols="30" rows="10" class="form-control"
                                          autofocus></textarea>
                            </div>
                        </div>

                        <div v-if="!passwordUpdated">
                            <h3>Create Password</h3>
                            <div class="update_password" style="border: solid thin black">
                                <div class="update_password_inputs"
                                     style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                                    <!-- Update password -->
                                    <div class="form-group" :class="{'has-error': form.errors.has('password')}">
                                        <label class="col-md-3 control-label">Password</label>

                                        <div class="col-md-8">
                                            <input type="password" name="password" v-model="form.password">
                                            <span class="help-block" v-show="form.errors.has('password')">
                                                {{ form.errors.get('password') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group"
                                         :class="{'has-error': form.errors.has('password_confirmation')}">
                                        <label class="col-md-3 control-label">Confirm Password</label>

                                        <div class="col-md-8">
                                            <input type="password" name="password_confirmation"
                                                   v-model="form.password_confirmation" @keyup="confirmPassword">
                                            <span class="help-block" v-show="form.errors.has('password_confirmation')">
                                                {{ form.errors.get('password_confirmation') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <!-- upload company logo -->
                            <!-- Photo Preview-->
                            <div v-if="isContractor">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">&nbsp;</label>
    
                                    <div class="col-md-6">
                                        <img :src="logoUrl">
                                    </div>
                                </div>
    
                                <!-- Update Button -->
                                <div class="form-group">
                                    <label class="col-md-4 control-label">&nbsp;</label>
    
                                    <div class="col-md-6">
                                        <label type="button" class="btn btn-primary btn-upload" :disabled="form.busy">
                                            <span>Select New Logo</span>
    
                                            <input ref="photo" type="file" class="form-control" name="photo"
                                                   @change="update">
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <h3>Preferred Method of Contact</h3>
                            <div class="preferred_contact" style="border: solid thin black">
                                <div class="preferred_contact_input"
                                     style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                                    <!-- preferred method of contact -->
                                    <div class="form-group" :class="{'has-error': form.errors.has('email_contact')}">
                                        <label class="col-md-3 control-label">Email</label>

                                        <div class="col-md-8">
                                            <input type="checkbox" name="email_contact" v-model="form.email_contact">
                                            <span class="help-block" v-show="form.errors.has('email_contact')">
                                                {{ form.errors.get('email_contact') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group" :class="{'has-error': form.errors.has('phone_contact')}">
                                        <label class="col-md-3 control-label">Phone Call</label>

                                        <div class="col-md-8">
                                            <input type="checkbox" name="phone_contact" v-model="form.phone_contact">
                                            <span class="help-block" v-show="form.errors.has('phone_contact')">
                                                {{ form.errors.get('phone_contact') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group" :class="{'has-error': form.errors.has('sms_text')}">
                                        <label class="col-md-3 control-label">SMS Text</label>

                                        <div class="col-md-8">
                                            <input type="checkbox" name="sms_text" v-model="form.sms_text">
                                            <span class="help-block" v-show="form.errors.has('sms_text')">
                                                {{ form.errors.get('sms_text') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-default btn-primary"
                                    style="margin-top: 1rem" @click.prevent="submitFurtherInfo()"
                                    :disabled="disabled.submit">
                                <span v-if="disabled.submit">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                                </span>
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
    },
    data () {
      return {
        user: {},
        disabled: {
          submit: false
        },
        form: new SparkForm ({
          company_name: '',
          phone_number: '',
          address_line_1: '',
          address_line_2: '',
          city: '',
          state: '',
          zip: '',
          password: '',
          password_confirmation: '',
          email_contact: true,
          phone_contact: false,
          sms_text: false,
        }),
        passwordsMatch: true,
      }
    },
    computed: {
      passwordUpdated () {
        return this.user.password_updated;
      },
      isContractor () {
        return User.isContractor ();
      },
      logoUrl () {
        return this.user.logo_url;
      }
    },
    methods: {
        filterPhone(){
            this.form.phone_number = Format.phone(this.form.phone_number);
        },
        confirmPassword() {
            if (this.form.password !== this.form.password_confirmation) {
                this.form.errors.errors = {
                    password_confirmation: ['Passwords need to match.']
                };
                this.passwordsMatch = false;
            } else {
                this.form.errors.errors = {};
            }
            this.passwordsMatch = true;
        },
      submitFurtherInfo () {
        // debugger
        if (!this.passwordsMatch) {
            return;
        }
        User.submitFurtherInfo(this.form, this.disabled);
      },
      /**
       * Update the user's profile photo.
       */
      update (e) {
        e.preventDefault ();

        var self = this;

        this.form.startProcessing ();

        // We need to gather a fresh FormData instance with the profile photo appended to
        // the data so we can POST it up to the server. This will allow us to do async
        // uploads of the profile photos. We will update the user after this action.
        axios.post ('/settings/logo', this.gatherFormData ())
          .then (
            () => {
              Bus.$emit('updateUser');
              self.form.finishProcessing ();
            },
            (error) => {
              self.form.setErrors(error.response.data);
              Vue.toasted.error('Image needs to be 2MB or less');
            }
          );
      },


      /**
       * Gather the form data for the photo upload.
       */
      gatherFormData () {
        const data = new FormData ();

        data.append('photo', this.$refs.photo.files[0]);

        return data;
      }
    },
    mounted() {
        this.user = Spark.state.user;
    }
  }
</script>
