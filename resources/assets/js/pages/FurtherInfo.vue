<template>
    <div class="col-md-8 col-md-offset-2 styled">
        <!-- <pre> {{ form }}</pre> -->
        <div class="panel panel-default card card-1">
            <div class="panel-heading" v-if="isContractor">Register Your Company</div>
            <div class="panel-heading" v-if="!isContractor">Please Add Additional Information</div>

            <div class="panel-body">
                <form class="form-horizontal" role="form">
                    <input type="hidden" name="street_number" id="street_number">
                    <input type="hidden" name="country" id="country">
                    <!-- email -->
                    <div class="form-group" :class="{'has-error': form.errors.has('email')}">
                        <label class="col-md-3 control-label">Update Login Email</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="email" v-model="form.email"
                                   autofocus required>
                            <span class="help-block" v-show="form.errors.has('email')">
                                    {{ form.errors.get('email') }}
                                </span>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="form-group" :class="{'has-error': form.errors.has('name')}">
                        <label class="col-md-3 control-label">Full Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" v-model="form.name" required>
                            <span class="help-block" v-show="form.errors.has('name')">
                                    {{ form.errors.get('name') }}
                                </span>
                        </div>
                    </div>

                    <!-- Company Name -->
                    <div class="form-group" :class="{'has-error': form.errors.has('company_name')}"
                         v-if="isContractor">
                        <label class="col-md-3 control-label">Company Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="company_name" v-model="form.company_name">
                            <span class="help-block" v-show="form.errors.has('company_name')">
                                    {{ form.errors.get('company_name') }}
                                </span>
                        </div>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group" :class="{'has-error': form.errors.has('phone_number')}">
                        <label class="col-md-3 control-label">Mobile Phone Number</label>
                        <div class="col-md-8">
                            <input type="tel" class="form-control" name="phone_number" maxlength="10"
                                   v-model="form.phone_number"
                                   @blur="validateMobileNumber($event.target.value)"
                                   @keyup="filterPhone">
                            <div v-if="checkThatNumberIsMobile()" style="color: green">{{ this.getMobileValidResponse[1]
                                }}
                            </div>
                            <div v-if="checkLandLineNumber()" style="color: red">{{ this.getMobileValidResponse[1] }}
                            </div>
                            <span class="help-block" v-show="form.errors.has('phone_number')">
                                    {{ form.errors.get('phone_number') }}
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
                                    {{ form.errors.get('address_line_1') }}
                                </span>
                        </div>
                    </div>

                    <!-- Address Line 2 -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Address Line 2</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name=""
                                   v-model="form.address_line_2">
                        </div>
                    </div>

                    <!-- City -->
                    <div class="form-group" :class="{'has-error': form.errors.has('city')}">
                        <label class="col-md-3 control-label">City</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="city" id="administrative_area_level_1"
                                   v-model="form.city">
                            <span class="help-block" v-show="form.errors.has('city')">
                                    {{ form.errors.get('city') }}
                                </span>
                        </div>
                    </div>

                    <!-- State -->
                    <div class="form-group" :class="{'has-error': form.errors.has('state')}">
                        <label class="col-md-3 control-label">State</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="state" id="locality" v-model="form.state">
                            <span class="help-block" v-show="form.errors.has('state')">
                                    {{ form.errors.get('state') }}
                                </span>
                        </div>
                    </div>

                    <!-- Zip Code -->
                    <div class="form-group" :class="{'has-error': form.errors.has('zip')}">
                        <label class="col-md-3 control-label">ZipCode</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="zip" id="postal_code" v-model="form.zip">
                            <span class="help-block" v-show="form.errors.has('zip')">
                                    {{ form.errors.get('zip') }}
                                </span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="form-group" v-if="!isContractor">
                        <label class="col-md-3 control-label">Contractor Instructions</label>
                        <div class="col-md-8">
                                <textarea name="notes" id="notes" v-model="form.notes" cols="30" rows="10"
                                          class="form-control"
                                ></textarea>
                        </div>
                    </div>

                    <div v-if="!passwordUpdated">
                        <h3>Create Password</h3>
                        <div class="update_password" style="border: solid thin black">
                            <div class="update_password_inputs"
                                 style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                                <!-- Update password -->
                                <div class="form-group p-r-8" :class="{'has-error': form.errors.has('password')}">
                                    <label class="col-md-3 control-label">Password</label>

                                    <div class="col-md-8">
                                        <input class="form-control" type="password" name="password" ref="password"
                                               v-model="form.password">
                                        <span class="help-block" v-show="form.errors.has('password')">
                                                {{ form.errors.get('password') }}
                                            </span>
                                    </div>
                                </div>

                                <div class="form-group p-r-8"
                                     :class="{'has-error': form.errors.has('password_confirmation')}">
                                    <label class="col-md-3 control-label">Confirm Password</label>

                                    <div class="col-md-8">
                                        <input class="form-control" type="password" name="password_confirmation" ref="password_confirmation"
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
                        <!-- <div v-if="isContractor"> -->
                        <!-- <div class="form-group">
                            <label class="col-md-4 control-label">&nbsp;</label>

                            <div class="col-md-6">
                                <img :src="logoUrl">
                            </div>
                        </div> -->

                        <!-- Update Button -->
                        <!-- <div class="form-group">
                            <label class="col-md-4 control-label">&nbsp;</label>

                            <div class="col-md-6">
                                <label type="button" class="btn btn-primary btn-upload" :disabled="form.busy">
                                    <span>Select New Logo</span>

                                    <input ref="photo" type="file" class="form-control" name="photo"
                                           @change="update">
                                </label>
                            </div>
                        </div> -->
                        <!-- </div> -->
                        <button type="submit" name="submit" class=""
                                style="margin-top: 1rem; background-color: black" @click.prevent="submitFurtherInfo()"
                                :disabled="checkValidData()">
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
</template>

<script>

  import { mapGetters, mapMutations, mapActions } from 'vuex'

  export default {
    props: {
      user: Object
    },
    data() {
      return {
        disabled: {
          submit: false,
          validData: true
        },
        form: new SparkForm({
          email: '',
          name: '',
          company_name: '',
          phone_number: '',
          address_line_1: '',
          address_line_2: '',
          city: '',
          state: '',
          zip: '',
          notes: '',
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
      ...mapGetters([
        'getMobileValidResponse'
      ]),
      passwordUpdated() {
        return this.user.password_updated
      },
      isContractor() {
        return User.isContractor()
      },
      logoUrl() {
        return this.user.logo_url
      }
    },
    methods: {
      ...mapMutations([
        'setMobileResponse'
      ]),
      ...mapActions([
        'checkMobileNumber',
      ]),
      unformatNumber(number) {
        let unformattedNumber = ''
        for (let i = 0; i < number.length; i++) {
          if (!isNaN(parseInt(number[i]))) {
            unformattedNumber = unformattedNumber + number[i]
          }
        }
        let numberLength = unformattedNumber.length
        if (numberLength < 10) {
          if (this.getMobileValidResponse[1] !== '') {
            this.$store.commit('setTheMobileResponse', ['', '', ''])
          }
        }
        // debugger;
        return numberLength
      },
      checkValidData() {
        // debugger
        let phone = this.unformatNumber(this.form.phone_number)
        if ((this.getMobileValidResponse[1] === 'mobile' ||
          this.getMobileValidResponse[2] === 'mobile') &&
          this.form.customerName !== '' && (phone === 10)
        ) {
          return false
        } else {
          return true
        }
      },
      validateMobileNumber(phone) {
        this.checkMobileNumber(phone)
      },
      checkThatNumberIsMobile() {
        if (this.getMobileValidResponse[1] === 'mobile' ||
          this.getMobileValidResponse[2] === 'mobile') {
          return true
        } else {
          return false
        }
      },
      checkLandLineNumber() {
        if (this.getMobileValidResponse[1] === 'landline' ||
          this.getMobileValidResponse[2] === 'landline') {
          return true
        } else {
          return false
        }
      },
      updateFormLocation(location) {
        this.form.address_line_1 = location.route
        this.form.city = location.locality
        this.form.state = location.administrative_area_level_1
        this.form.zip = location.postal_code
      },
      filterPhone() {
        this.form.phone_number = Format.phone(this.form.phone_number)
      },
      confirmPassword() {
        if (this.form.password !== this.form.password_confirmation) {
          this.form.errors.errors = {
            password_confirmation: ['Passwords need to match.']
          }
          this.passwordsMatch = false
        } else {
          this.form.errors.errors = {}
        }
        this.passwordsMatch = true
      },
      submitFurtherInfo() {
        // debugger
        if (!this.passwordsMatch) {
          return
        }
        User.submitFurtherInfo(this.form, this.disabled)
      },
      initAutocomplete() {
        User.initAutocomplete('route')
      },
      /**
       * Update the user's profile photo.
       */
      update(e) {
        e.preventDefault()

        var self = this

        this.form.startProcessing()

        // We need to gather a fresh FormData instance with the profile photo appended to
        // the data so we can POST it up to the server. This will allow us to do async
        // uploads of the profile photos. We will update the user after this action.
        axios.post('/settings/logo', this.gatherFormData())
          .then(
            (data) => {
              this.user.logo_url = data.data
              self.form.finishProcessing()
            },
            (error) => {
              self.form.setErrors(error.response.data)
              Vue.toasted.error('Image needs to be 2MB or less')
            }
          )
      },

      /**
       * Gather the form data for the photo upload.
       */
      gatherFormData() {
        const data = new FormData()

        data.append('photo', this.$refs.photo.files[0])

        return data
      }
    },
    mounted() {
      this.initAutocomplete();
      this.form.phone_number = this.user.phone != null ? this.user.phone : ''
      this.form.email = this.user.email != null ? this.user.email : ''
      this.form.name = this.user.name != null ? this.user.name : ''

      this.form.company_name = this.user.contractor !== null ? this.user.contractor.company_name : ''

      if (this.user.phone != null) {
        this.validateMobileNumber(this.user.phone)
      }

      Bus.$on('updateFormLocation', (payload) => {
        this.updateFormLocation(payload)
      })
    }
  }
</script>

<style scoped>
    .styled {
        margin-top: 10rem;
        margin-bottom: 10rem;
    }
</style>
