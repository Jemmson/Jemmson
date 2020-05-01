<template>

    <v-container>
        <v-card>
            <v-card-title v-if="isContractor">Register Your Company</v-card-title>
            <v-card-title v-if="!isContractor">Additional Information</v-card-title>
            <v-card-subtitle>Additional information needed</v-card-subtitle>

            <v-card-text>
                <v-text-field
                        id="email"
                        ref="email"
                        v-model="form.email"
                        :class="{'has-error': form.errors.has('email')}"
                        label="Update Login Email: *"
                >
                </v-text-field>
                <span
                        ref="emailError"
                        class="help-block"
                        v-show="form.errors.has('email')">
                  {{ form.errors.get('email') }}
                </span>

                <v-text-field
                        id="first_name"
                        ref="first_name"
                        v-model="form.first_name"
                        :class="{'has-error': form.errors.has('name')}"
                        label="First Name: *"
                >
                </v-text-field>
                <span ref="emailError" class="help-block" v-show="form.errors.has('name')">
                  {{ form.errors.get('name') }}
                </span>

                <v-text-field
                        id="last_name"
                        ref="last_name"
                        v-model="form.last_name"
                        :class="{'has-error': form.errors.has('last_name')}"
                        label="Last Name: *"
                >
                </v-text-field>
                <span ref="emailError" class="help-block" v-show="form.errors.has('last_name')">
                  {{ form.errors.get('last_name') }}
                </span>

                <v-text-field
                        v-if="isContractor"
                        id="company_name"
                        ref="company_name"
                        v-model="form.company_name"
                        :class="{'has-error': form.errors.has('company_name')}"
                        label="Company Name: *"
                >
                </v-text-field>
                <span v-if="isContractor" ref="emailError" class="help-block" v-show="form.errors.has('company_name')">
                  {{ form.errors.get('company_name') }}
                </span>

                <v-text-field
                        id="phone_number"
                        ref="phone_number"
                        maxlength="10"
                        v-model="form.phone_number"
                        :class="{'has-error': form.errors.has('phone_number')}"
                        label="Mobile Phone Number: *"
                        @blur="validateMobileNumber($event.target.value)"
                        @keyup="filterPhone"
                >
                </v-text-field>
                <span
                        ref="emailError"
                        class="help-block"
                        v-show="form.errors.has('phone_number')">
                    {{ form.errors.get('phone_number') }}
                </span>
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


                <input type="hidden" name="street_number" id="street_number">
                <v-text-field
                        id="route"
                        :class="{'has-error': form.errors.has('address_line_1')}"
                        v-model="form.address_line_1"
                        label="Address Line 1 *"
                >
                </v-text-field>
                <span ref="addressLine1Error" class="help-block" v-show="form.errors.has('address_line_1')">{{ form.errors.get('address_line_1') }}</span>

                <v-text-field
                        id="addressLine2"
                        v-model="form.address_line_2"
                        label="Address Line 2 *"
                >
                </v-text-field>

                <v-text-field
                        id="administrative_area_level_1"
                        :class="{'has-error': form.errors.has('city')}"
                        v-model="form.city"
                        label="City *"
                >
                </v-text-field>
                <span ref="cityError" class="help-block"
                      v-show="form.errors.has('city')">{{ form.errors.get('city') }}</span>

                <v-text-field
                        id="locality"
                        :class="{'has-error': form.errors.has('state')}"
                        v-model="form.state"
                        label="State *"
                >
                </v-text-field>
                <span ref="stateError" class="help-block" v-show="form.errors.has('state')">{{ form.errors.get('state') }}</span>

                <v-text-field
                        id="postal_code"
                        :class="{'has-error': form.errors.has('zip')}"
                        :rules="[
                            this.zipMustHaveAtleast5characters()
                        ]"
                        v-model="form.zip"
                        v-mask="'#####-####'"
                        label="Zip Code *"
                >
                </v-text-field>
                <span ref="zipError" class="help-block"
                      v-show="form.errors.has('zip')">{{ form.errors.get('zip') }}</span>

                <v-textarea
                        v-if="!isContractor"
                        v-model="form.notes"
                        :auto-grow="true"
                        :clearable="true"
                        label="General Instructions For All Contractor"
                ></v-textarea>

                <div v-if="isContractor">

                    <add-license-box
                            @add="addLicenses($event)"
                    >
                    </add-license-box>

                </div>


                <v-text-field
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        label="Password *"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showPassword = !showPassword"
                        :class="{'has-error': form.errors.has('password')}"
                >
                </v-text-field>
                <span class="help-block" v-show="form.errors.has('password')">{{ form.errors.get('password') }}</span>

                <v-text-field
                        id="password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        v-model="form.password_confirmation"
                        :class="{'has-error': form.errors.has('password_confirmation')}"
                        label="Confirm Password *"
                        :append-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="showConfirmPassword = !showConfirmPassword"
                        @keyup="confirmPassword"
                >
                </v-text-field>
                <span class="help-block" v-show="form.errors.has('password_confirmation')">{{ form.errors.get('password_confirmation') }}</span>


                <hr style="margin-top: 3rem">

                <v-switch
                        v-model="form.terms"
                        :class="{'has-error': form.errors.has('terms')}"
                        label="I Accept The Terms Of Service *"
                ></v-switch>
                <v-btn
                        id="terms"
                        ref="terms"
                        label="Terms"
                        @click="$router.push('/terms')"
                        color="blue"
                        text="">Terms
                </v-btn>
                <span class="help-block" v-show="form.errors.has('terms')">{{ form.errors.get('terms') }}</span>

                <v-card-actions>
                    <v-btn
                            class="w-full"
                            color="primary"
                            text
                            type="submit"
                            name="submit"
                            @click.prevent="submitFurtherInfo()"
                            :disabled="checkValidData()"
                            :loading="overlay"
                    >
                        Register
                    </v-btn>
                </v-card-actions>


            </v-card-text>

        </v-card>
    </v-container>

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
                showConfirmPassword: false,
                showPassword: false,
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
                'setMobileResponse',
                'setCurrentPage'
            ]),
            ...mapActions([
                'checkMobileNumber',
            ]),

            zipMustHaveAtleast5characters() {
                return this.form.zip.length > 4 || 'Zip Code Must Be At Least 5 Characters'
            },

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
                this.form.zip = this.formatZip()
                this.getFurtherInfo()
            },

            formatZip() {

                let zip = this.form.zip.split('-')
                
                if (zip.length === 1) {
                    return this.form.zip
                }

                if (zip.length === 2) {

                    if (zip[zip.length - 1] === '') {
                        return zip[0]
                    } else {
                        return this.form.zip
                    }
                }

            },

            async getFurtherInfo() {
                this.form.phone_number = Format.numbersOnly(this.form.phone_number)
                try {
                    const data = await Spark.post('/home', this.form)
                    Vue.toasted.success('info updated')
                    Bus.$emit('updateUser');
                    this.$store.commit('setCurrentPage', data);
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

    .help-block {
        display: block;
        margin-bottom: 10px;
        color: red;
        /*color: lighten(@text-color, 25%); // lighten the text some for contrast*/
    }


</style>
