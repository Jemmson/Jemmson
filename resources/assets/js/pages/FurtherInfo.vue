<template>
    <div class="flex flex-col further-info-main text-center">
        <div ref="furtherInfoHeading" v-if="isContractor" class="main-header p-4 uppercase">
            Please register your company
        </div>
        <div v-if="!isContractor" class="main-header p-4 uppercase">
            Please Add Additional Information
        </div>
        <div class="box border flex flex-col section">
            <div class="content">
                <input type="hidden" name="street_number" id="street_number">
                <input type="hidden" name="country" id="country">

                <!-- email -->
                <div class="input-section" :class="{'has-error': form.errors.has('email')}">
                    <label class="j-label">Update Login Email: *</label>
                    <div>
                        <input type="text" class="border input" name="email" v-model="form.email"
                               autofocus required>
                        <span class="help-block" v-show="form.errors.has('email')">
                                    {{ form.errors.get('email') }}
                        </span>
                    </div>
                </div>

                <!-- Name -->
                <div class="input-section" :class="{'has-error': form.errors.has('name')}">
                    <label class="j-label">First Name *</label>
                    <div>
                        <input type="text" class="border input" name="name" v-model="form.first_name" required>
                        <span class="help-block" v-show="form.errors.has('name')">
                                    {{ form.errors.get('first_name') }}
                                </span>
                    </div>
                    <label class="j-label">Last Name *</label>
                    <div>
                        <input type="text" class="border input" name="name" v-model="form.last_name" required>
                        <span class="help-block" v-show="form.errors.has('name')">
                                    {{ form.errors.get('last_name') }}
                                </span>
                    </div>
                </div>

                <!-- Company Name -->
                <div class="input-section" :class="{'has-error': form.errors.has('company_name')}"
                     v-if="isContractor">
                    <label class="j-label">Company Name *</label>
                    <div class="">
                        <input type="text" class="border input" name="company_name" v-model="form.company_name">
                        <span class="help-block" v-show="form.errors.has('company_name')">
                                    {{ form.errors.get('company_name') }}
                                </span>
                    </div>
                </div>

                <!-- Phone Number -->
                <div class="input-section" :class="{'has-error': form.errors.has('phone_number')}">
                    <label class="j-label">Mobile Phone Number *</label>
                    <div class="">
                        <input type="tel" class="border input" name="phone_number" maxlength="10"
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
                <div class="input-section" :class="{'has-error': form.errors.has('address_line_1')}">
                    <label class="j-label">Address Line 1 *</label>
                    <div class="col-md-8">
                        <input type="text" class="border input" name="address_line_1" id="route"
                               v-model="form.address_line_1">
                        <span class="help-block" v-show="form.errors.has('address_line_1')">
                                    {{ form.errors.get('address_line_1') }}
                                </span>
                    </div>
                </div>

                <!-- Address Line 2 -->
                <div class="input-section">
                    <label class="j-label">Address Line 2</label>
                    <div class="col-md-8">
                        <input type="text" class="border input" name=""
                               v-model="form.address_line_2">
                    </div>
                </div>

                <!-- City -->
                <div class="input-section" :class="{'has-error': form.errors.has('city')}">
                    <label class="j-label">City *</label>
                    <div class="col-md-8">
                        <input type="text" class="border input" name="city" id="administrative_area_level_1"
                               v-model="form.city">
                        <span class="help-block" v-show="form.errors.has('city')">
                                    {{ form.errors.get('city') }}
                                </span>
                    </div>
                </div>

                <div class="flex items-center">

                    <!-- State -->
                    <div class="w-1/3 mr-2" :class="{'has-error': form.errors.has('state')}">
                        <label class="j-label" style="font-size: 1rem">State *</label>
                        <div style="height: .75rem"></div>
                        <select v-model="form.state" class="form-control form-control-lg">
                            <option v-for="state in states" :value="state">{{ state }}</option>
                        </select>
                    </div>

                    <!-- Zip Code -->
                    <div class="input-section w-full ml-2" :class="{'has-error': form.errors.has('zip')}">
                        <label class="j-label">ZipCode *</label>

                        <input type="text" class="border input" name="zip" id="postal_code" v-model="form.zip">
                        <span class="help-block" v-show="form.errors.has('zip')">
                                    {{ form.errors.get('zip') }}
                                </span>
                    </div>
                </div>

                <!-- Notes -->
                <div class="input-section" v-if="!isContractor">
                    <label class="j-label">Contractor Instructions</label>
                    <div class="col-md-8">
                                <textarea name="notes" id="notes" v-model="form.notes" cols="30" rows="10"
                                          class="border input"
                                ></textarea>
                    </div>
                </div>

                <div v-if="!passwordUpdated">
                    <h3>Create Password</h3>
                    <div class="update_password" style="border: solid thin black">
                        <div class="update_password_inputs"
                             style="margin-top: 2rem; margin-bottom: 2rem; margin-left: 2rem">
                            <!-- Update password -->
                            <div class="input-section p-r-8" :class="{'has-error': form.errors.has('password')}">
                                <label class="j-label">Password *</label>

                                <div class="col-md-8">
                                    <input class="border input" type="password" name="password" ref="password"
                                           v-model="form.password">
                                    <span class="help-block" v-show="form.errors.has('password')">
                                                {{ form.errors.get('password') }}
                                            </span>
                                </div>
                            </div>

                            <div class="input-section p-r-8"
                                 :class="{'has-error': form.errors.has('password_confirmation')}">
                                <label class="j-label">Confirm Password *</label>

                                <div class="col-md-8">
                                    <input class="border input" type="password" name="password_confirmation"
                                           ref="password_confirmation"
                                           v-model="form.password_confirmation" @keyup="confirmPassword">
                                    <span class="help-block" v-show="form.errors.has('password_confirmation')">
                                                {{ form.errors.get('password_confirmation') }}
                                            </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" name="submit" class="register text-center border shadow uppercase"
                        @click.prevent="submitFurtherInfo()"
                        :disabled="checkValidData()">
                                <span v-if="disabled.submit">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                                </span>
                    Register
                </button>


            </div>
        </div>
        <jemmson-footer></jemmson-footer>
    </div>
</template>

<script>

  import JemmsonFooter from '../components/shared/JemmsonFooter'

  import { mapGetters, mapMutations, mapActions } from 'vuex'

  export default {
    props: {
      user: Object
    },
    components: {
      JemmsonFooter
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
          first_name: '',
          last_name: '',
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
        states: [
          'AS',
          'AL',
          'AK',
          'AZ',
          'AR',
          'CA',
          'CO',
          'CT',
          'DE',
          'DC',
          'FL',
          'GA',
          'HI',
          'ID',
          'IL',
          'IN',
          'IA',
          'FM',
          'GU',
          'KS',
          'KY',
          'LA',
          'MH',
          'ME',
          'MD',
          'MP',
          'MA',
          'MI',
          'MN',
          'MS',
          'MO',
          'MT',
          'NE',
          'NV',
          'NC',
          'ND',
          'OH',
          'OK',
          'OR',
          'PW',
          'PA',
          'PR',
          'RI',
          'SC',
          'SD',
          'TN',
          'TX',
          'UT',
          'VT',
          'VI',
          'VA',
          'WA',
          'WV',
          'WI',
          'WY'
        ]
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
        if (phone !== '') {
          this.checkMobileNumber(phone)
        }
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

        this.form.email = this.form.email.trim()

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
      },
    },
    mounted() {
      this.initAutocomplete()
      this.form.phone_number = this.user.phone != null ? this.user.phone : ''
      this.form.email = this.user.email != null ? this.user.email : ''
      this.form.name = this.user.name != null ? this.user.name : ''
      if (this.user.customer) {
        if (this.user.customer.location) {
          this.form.address_line_1 = this.user.customer.location.address_line_1 != null ?
            this.user.customer.location.address_line_1 : ''
          this.form.address_line_2 = this.user.customer.location.address_line_2 != null &&
          this.user.customer.location.address_line_2 != 'NULL' ?
            this.user.customer.location.address_line_2 : ''
          this.form.city = this.user.customer.location.city != null ?
            this.user.customer.location.city : ''
          this.form.state = this.user.customer.location.state != null ?
            this.user.customer.location.state : ''
          this.form.zip = this.user.customer.location.zip != null ?
            this.user.customer.location.zip : ''
        }
      } else if (this.user.contractor.location) {
        this.form.address_line_1 = this.user.contractor.location.address_line_1 != null ?
            this.user.contractor.location.address_line_1 : ''
        this.form.address_line_2 = this.user.contractor.location.address_line_2 != null &&
            this.user.contractor.location.address_line_2 != 'NULL' ?
            this.user.contractor.location.address_line_2 : ''
        this.form.city = this.user.contractor.location.city != null ?
            this.user.contractor.location.city : ''
        this.form.state = this.user.contractor.location.state != null ?
          this.user.contractor.location.state : ''
        this.form.zip = this.user.contractor.location.zip != null ?
          this.user.contractor.location.zip : ''
      }

      this.form.first_name = this.user.first_name
      this.form.last_name = this.user.last_name

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

    .qb {
        background-color: green;
        margin-bottom: .5rem;
    }

</style>
