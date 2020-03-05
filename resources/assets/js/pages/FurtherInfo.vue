<template>

    <div class="container">
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <div class="row">
            <div class="col-12 mb-2">
                <icon-header v-if="isContractor" icon="settings"
                             mainHeader="Register Your Company"
                             subHeader="Additional information needed">
                </icon-header>
                <icon-header v-if="!isContractor" icon="settings"
                             mainHeader="Additional Information"
                             subHeader="Additional information needed">
                </icon-header>
            </div>
            <div class="col-12">
                <card class="mb-2">
                    <form>
                        <div class="form-group" :class="{'has-error': form.errors.has('email')}">
                            <label for="email">Update Login Email: *</label>
                            <div>
                                <input id="email" type="text" class="form-control" name="email" v-model="form.email"
                                       autofocus required>
                                <span ref="emailError" class="help-block" v-show="form.errors.has('email')">
                  {{ form.errors.get('email') }}
                </span>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="form-group"
                             :class="{'has-error': (form.errors.has('first_name') || form.errors.has('last_name'))}">
                            <label for="first_name">First Name *</label>
                            <div>
                                <input id="first_name" type="text" class="form-control" name="name"
                                       v-model="form.first_name" required>
                                <span ref="first_name_error" class="help-block" v-show="form.errors.has('name')">
                  {{ form.errors.get('first_name') }}
                </span>
                            </div>
                            <label for="last_name">Last Name *</label>
                            <div>
                                <input id="last_name" type="text" class="form-control" name="name"
                                       v-model="form.last_name" required>
                                <span ref="last_name_error" class="help-block" v-show="form.errors.has('name')">
                  {{ form.errors.get('last_name') }}
                </span>
                            </div>
                        </div>

                        <!-- Company Name -->
                        <div class="form-group" :class="{'has-error': form.errors.has('company_name')}"
                             v-if="isContractor">
                            <label for="company_name">Company Name *</label>
                            <div class="">
                                <input id="company_name" type="text" class="form-control" name="company_name"
                                       v-model="form.company_name">
                                <span ref="companyNameError" class="help-block"
                                      v-show="form.errors.has('company_name')">
                  {{ form.errors.get('company_name') }}
                </span>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group" :class="{'has-error': form.errors.has('phone_number')}">
                            <label for="phone">Mobile Phone Number *</label>
                            <input
                                    type="tel" id="phone"
                                    :disabled="loading"
                                    class="form-control"
                                    name="phone_number"
                                    maxlength="10"
                                    v-model="form.phone_number"
                                    @blur="validateMobileNumber($event.target.value)"
                                    @keyup="filterPhone">
                            <div v-if="checkThatNumberIsMobile()"
                                 style="color: green">{{ this.getMobileValidResponse[1] }}
                            </div>
                            <div v-if="checkLandLineNumber()" style="color: red">
                                {{ this.getMobileValidResponse[1] }}
                            </div>
                            <span ref="phoneNumberError" class="help-block"
                                  v-show="form.errors.has('phone_number')">
                                        {{ form.errors.get('phone_number') }}
                                </span>
                            <v-progress-linear
                                    :active="loading"
                                    :indeterminate="loading"
                                    absolute
                                    bottom
                                    color="deep-purple accent-4"
                            ></v-progress-linear>
                        </div>


                        <!-- Address Line 1 -->
                        <div class="form-group" :class="{'has-error': form.errors.has('address_line_1')}">
                            <input type="hidden" name="street_number" id="street_number">
                            <label for="route">Address Line 1 *</label>
                            <div class="">
                                <input id="route"
                                       name="addressLine1"
                                       autocomplete="on"
                                       type="text" class="form-control"
                                       v-model="form.address_line_1"
                                >
                                <span ref="addressLine1Error" class="help-block"
                                      v-show="form.errors.has('address_line_1')">
                  {{ form.errors.get('address_line_1') }}
                </span>
                            </div>
                        </div>

                        <!-- Address Line 2 -->
                        <div class="form-group">
                            <label for="addressLine2">Address Line 2</label>
                            <div class="">
                                <input
                                        id="addressLine2"
                                        name="addressLine2"
                                        autocomplete="on"
                                        type="text" class="form-control"
                                        v-model="form.address_line_2">
                            </div>
                        </div>

                        <!-- City -->
                        <div class="form-group" :class="{'has-error': form.errors.has('city')}">
                            <label for="administrative_area_level_1">City *</label>
                            <div class="">
                                <input
                                        id="administrative_area_level_1"
                                        name="city"
                                        autocomplete="on"
                                        type="text" class="form-control "
                                        v-model="form.city">
                                <span ref="cityError" class="help-block" v-show="form.errors.has('city')">
                  {{ form.errors.get('city') }}
                </span>
                            </div>
                        </div>

                        <!-- State -->

                        <div class="form-group" :class="{'has-error': form.errors.has('state')}">
                            <label for="locality" style="font-size: 1rem">State *</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    name="state"
                                    id="locality"
                                    v-model="form.state">
                            <span ref="stateError" class="help-block" v-show="form.errors.has('state')">
                {{ form.errors.get('state') }}
              </span>
                        </div>

                        <!-- Zip Code -->
                        <div class="form-group" :class="{'has-error': form.errors.has('zip')}">
                            <label for="postal_code">ZipCode *</label>

                            <input
                                    id="postal_code"
                                    name="zip"
                                    autocomplete="on"
                                    type="text" class="form-control "
                                    v-model="form.zip">
                            <span ref="zipError" class="help-block" v-show="form.errors.has('zip')">
                {{ form.errors.get('zip') }}
              </span>
                        </div>

                        <!-- Notes -->
                        <div class="form-group" v-if="!isContractor">
                            <label for="notes">General Instructions For All Contractors</label>
                            <div class="">
                <textarea name="notes" id="notes" v-model="form.notes" cols="30" rows="10"
                          class="form-control"></textarea>
                            </div>
                        </div>


                        <div v-if="isContractor">

                            <add-license-box
                                    @add="addLicenses($event)"
                            >
                            </add-license-box>

                        </div>


                        <h3>Create Password</h3>
                        <div class="update_password" style="">
                            <!-- Update password -->
                            <div class="form-group" :class="{'has-error': form.errors.has('password')}">
                                <label for="password">Password *</label>

                                <div class="">
                                    <input id="password" class="form-control" type="password" name="password"
                                           ref="password"
                                           v-model="form.password">
                                    <span class="help-block" v-show="form.errors.has('password')">
                      {{ form.errors.get('password') }}
                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="{'has-error': form.errors.has('password_confirmation')}">
                                <label for="password_confirmation">Confirm Password *</label>

                                <div class="">
                                    <input id="password_confirmation" class="form-control" type="password"
                                           name="password_confirmation"
                                           ref="password_confirmation" v-model="form.password_confirmation"
                                           @keyup="confirmPassword">
                                    <span class="help-block" v-show="form.errors.has('password_confirmation')">
                      {{ form.errors.get('password_confirmation') }}
                    </span>
                                </div>
                            </div>
                        </div>
                        <hr style="margin-top: 2rem;">
                        <div class="row mb-1rem"
                             style="margin-left: 0rem; margin-right: 0rem;"
                             :class="{'has-error': form.errors.has('terms')}">
                            <input
                                    type="checkbox"
                                    class="mr-2"
                                    name="terms"
                                    style="align-self: center;"
                                    @change="terms()">I Accept The
                            <a href="/terms"
                               style="margin-left: .2rem;"
                               target="_blank"> Terms Of Service *</a>
                            <span class="help-block"
                                  v-show="form.errors.has('terms')">
                                {{ form.errors.get('terms') }}</span>
                        </div>


                        <v-btn
                                class="w-full"
                                color="primary"
                                type="submit"
                                name="submit"
                                @click.prevent="submitFurtherInfo()"
                                :disabled="checkValidData()"
                                :loading="overlay"
                        >
                            Register
                        </v-btn>
                    </form>
                </card>
            </div>
        </div>
    </div>

</template>

<script>

    import JemmsonFooter from '../components/shared/JemmsonFooter'
    import Card from '../components/shared/Card'
    import IconHeader from '../components/shared/IconHeader'
    import AddLicenseBox from '../components/user/AddLicenseBox'
    import Phone from '../components/mixins/Phone'

    import {mapMutations, mapActions} from 'vuex'

    export default {
        props: {
            user: Object
        },
        components: {
            JemmsonFooter,
            Card,
            AddLicenseBox,
            IconHeader
        },
        data() {
            return {
                overlay: false,
                boxArray: [],
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
                    licenses: [],
                    state: '',
                    zip: '',
                    notes: '',
                    password: '',
                    password_confirmation: '',
                    email_contact: true,
                    phone_contact: false,
                    sms_text: false,
                    terms: null
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
            boxes() {
                return this.boxArray
            },
            passwordUpdated() {
                return this.user.password_updated
            },
            isContractor() {
                return this.user.usertype === 'contractor'
            },
            logoUrl() {
                return this.user.logo_url
            }
        },
        created() {
            document.body.scrollTop = 0 // For Safari
            document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
        },
        mixins: [Phone],
        methods: {
            ...mapMutations([
                'setMobileResponse'
            ]),
            ...mapActions([
                'checkMobileNumber',
            ]),

            terms() {
                if (this.form.terms) {
                    this.form.terms = null
                } else {
                    this.form.terms = true
                }
            },

            addLicenses(licenses) {
                this.form.licenses = []
                this.form.licenses[this.form.licenses.length] = licenses
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
                this.overlay = true;
                if (!this.passwordsMatch) {
                    return false
                }
                this.form.email = this.form.email.trim()
                this.getFurtherInfo()
            },

            async getFurtherInfo() {
                this.form.phone_number = Format.numbersOnly(this.form.phone_number)
                try {
                    const data = await Spark.post('/home', this.form)
                    Vue.toasted.success('info updated')
                    Bus.$emit('updateUser');
                    this.$router.push(data);
                } catch (error) {
                    console.log(error)
                    this.form.errors.errors = error.errors
                    Vue.toasted.error(error.message)
                    this.overlay = false;
                }
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

            Bus.$on('updateFormLocation', (payload) => {
                this.updateFormLocation(payload)
            })

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
        }
    }
</script>

<style scoped>

    .qb {
        background-color: green;
        margin-bottom: .5rem;
    }

</style>
