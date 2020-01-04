<template>


    <div>

        <div class="container">
            <card class="w-full">
                <div class="row">
                    <h3 class="uppercase m-auto">{{ companyInfo.message.CompanyName }}</h3>
                </div>
                <div class="row" style="flex-direction: column; margin-top: 1.25rem">
                    <vue-element-loading :active="loading" :is-full-screen="true"/>

                    <h5 class="text-center uppercase" style="color:red; display:block" v-show="inputNotValid">Some
                        Fields Are Missing</h5>

                    <div class="text-center uppercase" style="color:red;" v-show="errors.email">email address missing or
                        has
                        incorrect format
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.first_name">Your first name
                        field is missing
                    </div>

                    <div class="text-center uppercase" style="color:red;" v-show="errors.last_name">Your last name field
                        is missing
                    </div>

                    <div class="text-center uppercase" style="color:red;" ref="phoneError" v-if="errors.phone">The phone
                        field
                        is
                        not correct
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.password.error">The password
                        field is
                        missing
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.company_name">The company name
                        field
                        is
                        missing
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.address_line_1">The address
                        line 1
                        field
                        is missing
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.city">The city field is
                        missing
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.state">The state field is
                        missing
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.zip">The zip field is missing
                    </div>
                    <div class="text-center uppercase" style="color:red;" v-show="errors.terms">please accept the terms
                        of
                        agreement
                    </div>

                </div>
                <div class="row">
                    <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('first_name')">-->
                    <label for="firstName" class=" pt-1 pt-2">First Name *</label>
                    <input
                            id="firstName"
                            autocomplete="on"
                            name="fname"
                            type="text"
                            class="form-control "
                            v-model="form.first_name">
                    <!--                <span class="help-block" v-show="form.errors.has('first_name')"></span>-->
                    <span class="help-block" v-show="form.errors.first_name !== ''"></span>
                </div>
                <div class="row">
                    <label for="lastName" class="pt-3 pt-2">Last Name *</label>
                    <input id="lastName"
                           autocomplete="on"
                           name="lname"
                           type="text" class="form-control"
                           v-model="form.last_name">
                    <span class="help-block" v-show="form.errors.last_name !== ''"></span>
                </div>
                <hr>
                <div class="row">
                    <label class="">Password *</label>
                    <div style="color:red;"
                         ref="password_error"
                         class="pl-2"
                         v-show="errors.password.pw_length < 6"
                    >
                        Must Be at least 6 alphanumeric characters
                    </div>
                    <input type="password"
                           class="form-control"
                           name="password"
                           ref="password"
                           @keyup="passwordLength"
                           v-model="form.password">
                </div>
                <div class="row mt-3">
                    <label class="">Confirm Password *</label>
                    <div v-if="!errors.password.match">
                            <span class="has-error-name text-center">
                                {{ errors.password.message }}
                            </span>
                    </div>
                    <input type="password"
                           :class="{'has-error': !errors.password.match}"
                           class="form-control"
                           name="password_confirmation"
                           v-model="form.password_confirmation" @keyup="confirmPassword">
                </div>
                <hr>

                <h4 class="text-center mt-2">General Info</h4>
                <div class="row" style="justify-content: center" v-show="!sections.editGeneralInfo">
                    <div class="flex-1 w-full"></div>
                    <v-btn
                            class="w-40"
                            color="primary"
                            ref="edit_btn"
                            @click="sections.editGeneralInfo = true">Edit
                    </v-btn>
                    <div class="flex-1 w-full"></div>
                </div>
                <div class="row" style="justify-content: space-evenly" v-show="sections.editGeneralInfo">
                    <v-btn
                            class="w-40"
                            color="primary"
                            id="cancel_btn"
                            ref="cancel_btn"
                            @click="cancel()">Cancel
                    </v-btn>
                    <v-btn
                            class="w-40"
                            color="primary"
                            ref="reset_btn"
                            @click="reset()">Reset
                    </v-btn>
                    <v-btn
                            class="w-40"
                            color="primary"
                            ref="save_btn"
                            @click="save()">Save
                    </v-btn>
                </div>

                <hr>

                <div class="content" v-show="!sections.editGeneralInfo">
                    <div class="row" style="justify-content: space-between">
                        <div class="ml-2"
                             :class="companyInfo.message.CompanyName ? '' : 'empty-field-name'"
                        >Company Name *
                        </div>
                        <div class="mr-2">{{ companyInfo.message.CompanyName }}</div>
                    </div>
                    <div class="row" style="justify-content: space-between">
                        <div class="ml-2"
                             :class="companyInfo.message.CompanyAddr.Line1 ? '' : 'empty-field-name'"
                        >Address Line 1
                        </div>
                        <div class="mr-2">{{ companyInfo.message.CompanyAddr.Line1 }}</div>
                    </div>
                    <div v-if="companyInfo.message.CompanyAddr.Line2" class="row"
                         style="justify-content: space-between">
                        <div class="ml-2">Address Line 2</div>
                        <div class="mr-2">{{ companyInfo.message.CompanyAddr.Line2 }}</div>
                    </div>
                    <div class="row" style="justify-content: space-between">
                        <div class="flex">
                            <div class="ml-2"
                                 :class="companyInfo.message.CompanyAddr.City ? '' : 'empty-field-name'"
                            >City
                            </div>

                        </div>
                        <div class="mr-2">{{ companyInfo.message.CompanyAddr.City }}</div>
                    </div>
                    <div class="row" style="justify-content: space-between">
                        <div class="flex">
                            <div class="ml-2"
                                 :class="companyInfo.message.CompanyAddr.CountrySubDivisionCode ? '' : 'empty-field-name'"
                            >State
                            </div>

                        </div>
                        <div class="mr-2">{{ companyInfo.message.CompanyAddr.CountrySubDivisionCode }}</div>
                    </div>
                    <div class="row" style="justify-content: space-between">
                        <div class="flex">
                            <div class="ml-2"
                                 :class="companyInfo.message.CompanyAddr.PostalCode ? '' : 'empty-field-name'"
                            >PostalCode
                            </div>

                        </div>
                        <div class="mr-2">{{ companyInfo.message.CompanyAddr.PostalCode }}</div>
                    </div>
                    <div class="row" style="justify-content: space-between">
                        <div class="flex">
                            <div class="ml-2"
                                 :class="companyInfo.message.Country ? '' : 'empty-field-name'"
                            >Country
                            </div>

                        </div>
                        <div class="mr-2">{{ companyInfo.message.Country }}</div>
                    </div>
                    <div class="row" style="justify-content: space-between">
                        <div class="flex">
                            <div class="flex">
                                <div class="ml-2"
                                     ref="primaryPhone"
                                     :class="checkPhoneErrorsForClass"
                                >Mobile Phone Number
                                    <span
                                            class="j-label ml-2 star"
                                            ref="savedPhoneLabelStar"
                                            :class="checkPhoneErrorsForClass"
                                    >*</span>
                                </div>
                            </div>
                        </div>
                        <div ref="primaryPhoneFromQB"
                             :class="checkPhoneErrorsForClass"
                             class="mr-2">{{ companyInfo.message.PrimaryPhone }}
                        </div>
                    </div>
                    <div class="row" style="justify-content: space-between">
                        <div class="flex">
                            <div class="ml-2"
                                 :class="companyInfo.message.Email.Address ? '' : 'empty-field-name'"
                            >Email Address
                            </div>

                        </div>
                        <div class="mr-2">{{ companyInfo.message.Email.Address }}</div>
                    </div>
                </div>

                <div class="content" v-show="sections.editGeneralInfo">
                    <div class="row pt-2" style="flex-direction: column;">
                        <label class="">Company Name</label>
                        <div>
                            <input type="text" class="form-control" name="password_confirmation"
                                   :class="companyInfoTemporary.CompanyName ? '' : 'empty-field'"
                                   v-model="companyInfoTemporary.CompanyName">
                        </div>
                    </div>

                    <div class="row pt-2" style="flex-direction: column;">
                        <label class="">Address Line 1</label>
                        <div>
                            <input type="text" class="form-control" name="password_confirmation"
                                   :class="companyInfoTemporary.CompanyAddr.Line1 ? '' : 'empty-field'"
                                   v-model="companyInfoTemporary.CompanyAddr.Line1">
                        </div>
                    </div>

                    <div class="row pt-2" style="flex-direction: column;">
                        <label class="">Address Line 2</label>
                        <div>
                            <input type="text" class="form-control" name="password_confirmation"
                                   v-model="companyInfoTemporary.CompanyAddr.Line2">
                        </div>
                    </div>

                    <div class="row pt-2" style="flex-direction: column;">
                        <label class="">City</label>
                        <div>
                            <input type="text" class="form-control" name="password_confirmation"
                                   :class="companyInfoTemporary.CompanyAddr.City ? '' : 'empty-field'"
                                   v-model="companyInfoTemporary.CompanyAddr.City">
                        </div>
                    </div>

                    <div class="row pt-2" style="flex-direction: column;">
                        <label class="">State</label>
                        <div>
                            <input type="text" class="form-control" name="password_confirmation"
                                   :class="companyInfoTemporary.CompanyAddr.CountrySubDivisionCode ? '' : 'empty-field'"
                                   v-model="companyInfoTemporary.CompanyAddr.CountrySubDivisionCode">
                        </div>
                    </div>

                    <div class="row pt-2" style="flex-direction: column;">
                        <label class="">Zip Code</label>
                        <div>
                            <input type="text" class="form-control" name="password_confirmation"
                                   :class="companyInfoTemporary.CompanyAddr.PostalCode ? '' : 'empty-field'"
                                   v-model="companyInfoTemporary.CompanyAddr.PostalCode">
                        </div>
                    </div>

                    <div class="row pt-2" style="flex-direction: column;">
                        <label class="">Country</label>
                        <div>
                            <input type="text" class="form-control" name="password_confirmation"
                                   :class="companyInfoTemporary.Country ? '' : 'empty-field'"
                                   v-model="companyInfoTemporary.Country">
                        </div>
                    </div>


                    <div class="row pt-2" style="flex-direction: column;">
                        <!--<div class="input-section" :class="{'has-error': form.errors.has('phone_number')}">-->
                        <div class="flex justify-between">
                            <div class="flex">
                                <label
                                        class="j-label"
                                        ref="editedPhoneLabel"
                                        :class="checkPhoneErrorsForClass"
                                >Mobile Phone Number
                                    <span
                                            class="j-label ml-2 star"
                                            ref="editedPhoneLabelStar"
                                            :class="checkPhoneErrorsForClass"
                                    >*</span>
                                </label>
                            </div>
                            <div v-if="errors.phone" class="text-center uppercase">
                                Phone Number must be 10 digits
                            </div>
                            <div></div>
                        </div>
                        <div class="">
                            <input type="tel" class="form-control"
                                   ref="phone_number_input"
                                   name="phone_number" maxlength="10"
                                   v-model="companyInfoTemporary.PrimaryPhone"
                                   @blur="validateMobileNumber($event.target.value)"
                                   :class="companyInfoTemporary.PrimaryPhone ? '' : 'empty-field'"
                                   v-on:keyup.delete="makePhoneNumberIntoDigits($event.target.value)"
                                   @keyup="filterPhone">
                            <div v-if="checkThatNumberIsMobile()" style="color: green">
                                {{ this.getMobileValidResponse[1] }}
                            </div>
                            <div v-else-if="checkLandLineNumber()" style="color: red">
                                {{ this.getMobileValidResponse[1] }}
                            </div>
                            <div v-else-if="checkIfNumberIsVirtual()" style="color: green">
                                {{ this.getMobileValidResponse[1] }}
                            </div>
                        </div>
                    </div>

                    <!-- E-Mail Address -->
                    <div class="row pt-2" style="flex-direction: column;">
                        <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('email')}">-->
                        <label class="">E-Mail Address</label>

                        <div>
                            <input
                                    type="email"
                                    class="form-control"
                                    name="email"
                                    @blur="validateEmail()"
                                    :class="companyInfoTemporary.Email.Address ? '' : 'empty-field'"
                                    v-model="companyInfoTemporary.Email.Address">
                            <span class="help-block uppercase" style="color:red;" v-show="!validateEmail()">Your Email Does Not have the correct format</span>
                        </div>
                    </div>

                </div>

                <hr>

                <div class="content">
                    <!-- Terms And Conditions -->
                    <div class="row pt-2" style="flex-direction: column;">
                        <div class="col-md-6 col-md-offset-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="terms" v-model="form.terms">
                                    I Accept The <a href="/terms" target="_blank">Terms Of Service</a>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row pt-2" style="flex-direction: column;">
                        <div class="col-md-6 col-md-offset-4">
                            <v-btn
                                    class="w-40"
                                    color="primary"
                                    id="register" name=register
                                    @click.prevent="register"
                                    ref="register"
                                    :disabled="registerForm.busy">
                                    <span v-if="registerForm.busy">
                                        <i class="fa fa-btn fa-spinner fa-spin"></i>Registering
                                    </span>
                                    <span v-else>
                                        <i class="fa fa-btn fa-check-circle mr-2"></i>Register
                                    </span>
                            </v-btn>
                        </div>
                    </div>
                </div>
            </card>
        </div>
    </div>
</template>

<script>

  import { mapGetters, mapMutations, mapActions } from 'vuex'
  import VueElementLoading from 'vue-element-loading'
  import Card from '../components/shared/Card'

  export default {
    name: 'RegisterQuickBooks',
    components: {
      VueElementLoading,
      Card
    },
    data() {
      return {
        loading: false,
        unformattedNumber: '',
        companyInfo: {
          message: {
            CompanyName: null,
            LegalName: null,
            CompanyAddr: {
              Id: null,
              Line1: null,
              Line2: null,
              Line3: null,
              Line4: null,
              Line5: null,
              City: null,
              Country: null,
              CountryCode: null,
              CountrySubDivisionCode: null,
              PostalCode: null,
              PostalCodeSuffix: null,
              Lat: null,
              Long: null,
              Tag: null,
              Note: null
            },
            CustomerCommunicationAddr: {
              Id: null,
              Line1: null,
              Line2: null,
              Line3: null,
              Line4: null,
              Line5: null,
              City: null,
              Country: null,
              CountryCode: null,
              CountrySubDivisionCode: null,
              PostalCode: null,
              PostalCodeSuffix: null,
              Lat: null,
              Long: null,
              Tag: null,
              Note: null
            },
            LegalAddr: {
              Id: null,
              Line1: null,
              Line2: null,
              Line3: null,
              Line4: null,
              Line5: null,
              City: null,
              Country: null,
              CountryCode: null,
              CountrySubDivisionCode: null,
              PostalCode: null,
              PostalCodeSuffix: null,
              Lat: null,
              Long: null,
              Tag: null,
              Note: null
            },
            CompanyEmailAddr: null,
            CustomerCommunicationEmailAddr: null,
            CompanyURL: null,
            PrimaryPhone: null,
            OtherContactInfo: null,
            CompanyFileName: null,
            FlavorStratum: null,
            SampleFile: null,
            CompanyUserId: null,
            CompanyUserAdminEmail: null,
            CompanyStartDate: null,
            EmployerId: null,
            FiscalYearStartMonth: null,
            TaxYearStartMonth: null,
            QBVersion: null,
            Country: null,
            ShipAddr: null,
            OtherAddr: null,
            Mobile: null,
            Fax: null,
            Email: {
              Id: null,
              Address: null,
              Default: null,
              Tag: null
            },
            WebAddr: null,
            LastImportedTime: null,
            LastSyncTime: null,
            SupportedLanguages: null,
            DefaultTimeZone: null,
            MultiByteCharsSupported: null,
            NameValue: [
              {
                Name: 'NeoEnabled',
                Value: null
              },
              {
                Name: 'IsQbdtMigrated',
                Value: null
              },
              {
                Name: 'CompanyType',
                Value: null
              },
              {
                Name: 'SubscriptionStatus',
                Value: null
              },
              {
                Name: 'OfferingSku',
                Value: null
              },
              {
                Name: 'PayrollFeature',
                Value: null
              },
              {
                Name: 'AccountantFeature',
                Value: null
              },
              {
                Name: 'QBOIndustryType',
                Value: null
              },
              {
                Name: 'ItemCategoriesFeature',
                Value: null
              },
              {
                Name: 'AssignedTime',
                Value: null
              }
            ],
            CompanyInfoEx: null,
            Id: null,
            SyncToken: null,
            MetaData: {
              CreatedByRef: null,
              CreateTime: null,
              LastModifiedByRef: null,
              LastUpdatedTime: null,
              LastChangedInQB: null,
              Synchronized: null
            },
            CustomField: null,
            AttachableRef: null,
            domain: null,
            status: null,
            sparse: null
          }
        },
        form: {
          email: '',
          first_name: '',
          last_name: '',
          company_name: '',
          phone_number: '',
          address_line_1: '',
          address_line_2: '',
          city: '',
          state: '',
          zip: '',
          country: '',
          terms: false,
          notes: '',
          password: '',
          password_confirmation: '',
          email_contact: true,
          phone_contact: false,
          sms_text: false,
          qbCompanyInfo: {},
          errors: {
            errors: {}
          }
        },
        errors: {
          password: {
            match: true,
            message: '',
            pw_length: 0,
            error: false
          },
          email: false,
          last_name: false,
          first_name: false,
          company_name: false,
          phone: false,
          address_line_1: false,
          city: false,
          state: false,
          zip: false,
          country: false,
          terms: false
        },
        qbCompanyInfoWasUpdated: false,
        companyInfoTemporary: {
          CompanyName: '',
          Country: '',
          CompanyAddr: {
            Line1: '',
            Line2: '',
            City: '',
            CountrySubDivisionCode: '',
            PostalCode: ''
          },
          Email: {
            Address: ''
          }
        },
        companyInfoOriginal: {
          CompanyName: '',
          Country: '',
          CompanyAddr: {
            Line1: '',
            Line2: '',
            City: '',
            CountrySubDivisionCode: '',
            PostalCode: ''
          },
          Email: {
            Address: ''
          }
        },
        phoneNumberLength: null,
        registerForm: {
          busy: false,
          email: '',
          errors: {},
          first_name: '',
          last_name: '',
          password: '',
          usertype: ''
        },
        sections: {
          editGeneralInfo: false
        },
        inputNotValid: false
      }
    },
    computed: {
      ...mapGetters([
        'getMobileValidResponse'
      ]),
      checkPhoneErrorsForClass() {
        return {
          'empty-field-name':
            this.companyInfo.message.PrimaryPhone === null || this.companyInfo.message.PrimaryPhone === '' || this.errors.phone === true
        }
        // this.companyInfo.message.PrimaryPhone !== '' && !this.errors.phone;
      },
      classObject: function() {
        return {
          active: this.isActive && !this.error,
          'text-danger': this.error && this.error.type === 'fatal'
        }
      }
    },
    mounted: function() {
      this.getTheCompanyInfo()
    },
    methods: {
      ...mapMutations([
        'setMobileResponse'
      ]),
      ...mapActions([
        'checkMobileNumber',
      ]),
      makePhoneNumberIntoDigits(value) {
        this.unformatNumber(value)
        this.companyInfoTemporary.PrimaryPhone = this.unformattedNumber
      },
      getTheCompanyInfo() {
        axios.get('/quickbooks/getCachedCompanyInfo')
          .then(function(response) {
            // debugger
            this.companyInfo = response.data

            this.companyInfoTemporary.CompanyName = response.data.message.CompanyName
            this.companyInfoTemporary.CompanyAddr.Line1 = response.data.message.CompanyAddr.Line1
            this.companyInfoTemporary.CompanyAddr.Line2 = response.data.message.CompanyAddr.Line2
            this.companyInfoTemporary.CompanyAddr.City = response.data.message.CompanyAddr.City
            this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode = response.data.message.CompanyAddr.CountrySubDivisionCode
            this.companyInfoTemporary.CompanyAddr.PostalCode = response.data.message.CompanyAddr.PostalCode
            this.companyInfoTemporary.Country = response.data.message.Country
            this.companyInfoTemporary.PrimaryPhone = response.data.message.PrimaryPhone
            this.companyInfoTemporary.Email.Address = response.data.message.Email.Address

            this.companyInfoOriginal.CompanyName = response.data.message.CompanyName
            this.companyInfoOriginal.CompanyAddr.Line1 = response.data.message.CompanyAddr.Line1
            this.companyInfoOriginal.CompanyAddr.Line2 = response.data.message.CompanyAddr.Line2
            this.companyInfoOriginal.CompanyAddr.City = response.data.message.CompanyAddr.City
            this.companyInfoOriginal.CompanyAddr.CountrySubDivisionCode = response.data.message.CompanyAddr.CountrySubDivisionCode
            this.companyInfoOriginal.CompanyAddr.PostalCode = response.data.message.CompanyAddr.PostalCode
            this.companyInfoOriginal.Country = response.data.message.Country
            this.companyInfoOriginal.PrimaryPhone = response.data.message.PrimaryPhone
            this.companyInfoOriginal.Email.Address = response.data.message.Email.Address

          }.bind(this))
          .catch(function(error) {
            // TODO: do something with this error
          })
      },
      confirmPassword() {
        if (this.form.password !== this.form.password_confirmation) {
          this.errors.password.message = 'Passwords need to match.'
          this.errors.password.match = false
        } else {
          this.form.errors.errors = {}
          this.errors.password.match = true
          this.errors.password.message = ''
        }
      },
      passwordLength() {
        if (this.form.password !== null) {
          this.errors.password.pw_length = this.form.password.length
        }
      },
      checkPasswords() {
        if (
          (this.form.password !== this.form.password_confirmation) ||
          (this.form.password === '' || this.form.password_confirmation === '')
        ) {
          this.errors.password.error = true
          return false
        } else {
          this.errors.password.error = false
          return true
        }
      },
      checkValidPhoneNumber() {
        let phone = this.unformatNumber(this.form.phone_number)

        if (phone === 10) {
          if (
            this.getMobileValidResponse[1] === '' || this.getMobileValidResponse[2] === ''
          ) {
            this.validateMobileNumber(phone)
          }
          if (
            (this.getMobileValidResponse[1] !== 'mobile' || this.getMobileValidResponse[2] !== 'mobile')
            || (this.getMobileValidResponse[1] !== 'virtual' || this.getMobileValidResponse[2] !== 'virtual')
          ) {
            this.errors.phone = true
            return false
          } else {
            this.errors.phone = false
            return true
          }
        } else {
          this.errors.phone = true
          return false
        }
      },
      checkValidEmail() {
        if (this.form.email === '' || !this.validateEmail()) {
          this.errors.email = true
          return false
        } else {
          this.errors.email = false
          return true
        }
      },
      checkName() {
        if (this.form.first_name === '') {
          this.errors.first_name = true
          return false
        } else {
          this.errors.first_name = false
          return true
        }

        // TODO: this code needs to be refactored

        if (this.form.last_name === '') {
          this.errors.last_name = true
          return false
        } else {
          this.errors.last_name = false
          return true
        }
      },
      checkCompanyName() {
        if (this.form.company_name === '') {
          this.errors.company_name = true
          return false
        } else {
          this.errors.company_name = false
          return true
        }
      },
      checkCompanyAddressLine1() {
        if (this.form.address_line_1 === '') {
          this.errors.address_line_1 = true
          return false
        } else {
          this.errors.address_line_1 = false
          return true
        }
      },
      checkCompanyAddressCity() {
        if (this.form.city === '') {
          this.errors.city = true
          return false
        } else {
          this.errors.city = false
          return true
        }
      },
      checkCompanyAddressState() {
        if (this.form.state === '') {
          this.errors.state = true
          return false
        } else {
          this.errors.state = false
          return true
        }
      },
      checkCompanyAddressZip() {
        if (this.form.zip === '') {
          this.errors.zip = true
          return false
        } else {
          this.errors.zip = false
          return true
        }
      },
      checkCountry() {
        if (this.form.country === '') {
          this.errors.country = true
          return false
        } else {
          this.errors.country = false
          return true
        }
      },
      checkTerms() {
        if (this.form.terms === '') {
          this.errors.terms = true
          return false
        } else {
          this.errors.terms = false
          return true
        }
      },
      register() {

        // check if the data is in edit mode and then save it if it is
        if (this.sections.editGeneralInfo) {
          this.save()
        }

        // check that all form elements have been updated
        this.updateFormDataWithQBData()

        // is all the input valid?
        let submit = true

        if (!this.checkPasswords()) {
          submit = false
        }

        if (!this.checkValidPhoneNumber()) {
          submit = false
        }

        if (!this.checkValidEmail()) {
          submit = false
        }

        if (!this.checkName()) {
          submit = false
        }

        if (!this.checkCompanyName()) {
          submit = false
        }

        if (!this.checkCompanyAddressLine1()) {
          submit = false
        }
        if (!this.checkCompanyAddressCity()) {
          submit = false
        }

        if (!this.checkCompanyAddressState()) {
          submit = false
        }

        if (!this.checkCompanyAddressZip()) {
          submit = false
        }

        if (!this.checkCountry()) {
          submit = false
        }

        if (!this.checkTerms()) {
          submit = false
        }

        this.inputNotValid = false
        if (submit) {

          // this is meant to trigger an update request object to quickbooks
          // this feature does not seem available to the php sdk so is not implemented
          let updateQBData = false
          // if (this.checkIfQBCompanyInfoWasUpdated()) {
          //   this.addCompanyInfoToFormObject()
          //   updateQBData = true
          // } else {
          //   this.sendEmptyQBCompanyInfoObject()
          // }
          this.registerForm.busy = false
          // debugger;
          this.loading = true
          User.registerContractor(this.form, 'quickBooks', updateQBData)
          this.registerForm.busy = false
        } else {
          this.inputNotValid = true
        }
      },
      unformatNumber(number) {
        if (number !== undefined && number !== '' && number !== null) {
          let unformattedNumber = ''
          for (let i = 0; i < number.length; i++) {
            if (!isNaN(parseInt(number[i]))) {
              unformattedNumber = unformattedNumber + number[i]
            }
          }
          this.unformattedNumber = unformattedNumber
          let numberLength = unformattedNumber.length
          if (numberLength < 10) {
            this.emptyPhoneNumberInStore()
          }
          this.phoneNumberLength = numberLength
          return numberLength
        }
      },
      emptyPhoneNumberInStore() {
        if (this.getMobileValidResponse[1] !== '') {
          this.$store.commit('setTheMobileResponse', ['', '', ''])
        }
      },
      checkValidData() {

        let valid = true

        // phone number
        // does it have the right format
        // is it the right number of digits
        // is it a mobile number

        // let phone = this.unformatNumber(this.form.phone_number)
        //
        // if (phone === 10) {
        //   if (
        //     this.getMobileValidResponse[1] === '' ||
        //     this.getMobileValidResponse[2] === ''
        //   ) {
        //     this.validateMobileNumber(phone)
        //   }
        //   if (
        //     this.getMobileValidResponse[1] !== 'mobile' ||
        //     this.getMobileValidResponse[2] !== 'mobile') {
        //     valid = false
        //     this.errors.phone = true
        //   } else {
        //     this.errors.phone = false
        //   }
        // } else {
        //   valid = false
        //   this.errors.phone = true
        // }

        // if (
        //   (this.getMobileValidResponse[1] !== 'mobile' ||
        //     this.getMobileValidResponse[2] !== 'mobile') ||
        //   phone !== 10
        // ) {
        //   valid = false
        //   this.errors.phone = true
        // } else {
        //   this.errors.phone = false
        // }

        // if (this.form.email === '' || !this.validateEmail()) {
        //   this.errors.email = true
        //   valid = false
        // } else {
        //   this.errors.email = false
        // }

        // if (this.form.name === '') {
        //   this.errors.name = true
        //   valid = false
        // } else {
        //   this.errors.name = false
        // }

        // if (this.form.company_name === '') {
        //   this.errors.company_name = true
        //   valid = false
        // } else {
        //   this.errors.company_name = false
        // }

        // if (this.form.address_line_1 === '') {
        //   this.errors.address_line_1 = true
        //   valid = false
        // } else {
        //   this.errors.address_line_1 = false
        // }
        //
        // if (this.form.city === '') {
        //   this.errors.city = true
        //   valid = false
        // } else {
        //   this.errors.city = false
        // }
        //
        // if (this.form.state === '') {
        //   this.errors.state = true
        //   valid = false
        // } else {
        //   this.errors.state = false
        // }
        //
        // if (this.form.zip === '') {
        //   this.errors.zip = true
        //   valid = false
        // } else {
        //   this.errors.zip = false
        // }

        // if (this.form.terms === '') {
        //   this.errors.terms = true
        //   valid = false
        // } else {
        //   this.errors.terms = false
        // }

        // company name
        // does it have a length

        // if (
        //   (this.form.password !== this.form.password_confirmation) ||
        //   (this.form.password === '' || this.form.password_confirmation === '')
        // ) {
        //   valid = false
        //   this.errors.password.error = true
        // } else {
        //   this.errors.password.error = false
        // }

        return valid

      },
      validateMobileNumber(phone) {
        if (this.unformatNumber(phone) === 10) {
          this.errors.phone = false
          this.setMobileResponse(['', '', ''])
          this.loading = true
          if (phone !== '') {
            this.checkMobileNumber(phone)
          }
        } else {
          this.errors.phone = true
        }
      },
      validateEmail() {
        var re = /^(?:[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/
        return re.test(this.companyInfoTemporary.Email.Address)
      },
      checkThatNumberIsMobile() {
        if ((this.getMobileValidResponse[1] === 'mobile' ||
          this.getMobileValidResponse[2] === 'mobile')
        || (this.getMobileValidResponse[1] === 'virtual' ||
          this.getMobileValidResponse[2] === 'virtual')) {
          this.loading = false
          return true
        } else {
          return false
        }
      },
      checkLandLineNumber() {
        if (this.getMobileValidResponse[1] === 'landline' ||
          this.getMobileValidResponse[2] === 'landline') {
          this.loading = false
          return true
        } else {
          return false
        }
      },
      checkIfNumberIsVirtual() {
        if (this.getMobileValidResponse[1] === 'virtual' ||
          this.getMobileValidResponse[2] === 'virtual') {
          this.loading = false
          return true
        } else {
          return false
        }
      },
      filterPhone() {
        this.companyInfoTemporary.PrimaryPhone = Format.phone(this.companyInfoTemporary.PrimaryPhone)
      },
      updateFormDataWithQBData() {
        this.form.email = this.companyInfo.message.Email.Address
        this.form.company_name = this.companyInfo.message.CompanyName
        this.form.address_line_1 = this.companyInfo.message.CompanyAddr.Line1
        if (this.companyInfo.message.CompanyAddr.Line2) {
          this.form.address_line_2 = this.companyInfo.message.CompanyAddr.Line2
        } else {
          this.form.address_line_2 = null
        }
        this.form.city = this.companyInfo.message.CompanyAddr.City
        this.form.country = this.companyInfo.message.Country
        this.form.state = this.companyInfo.message.CompanyAddr.CountrySubDivisionCode
        this.form.zip = this.companyInfo.message.CompanyAddr.PostalCode
        this.form.phone_number = this.companyInfo.message.PrimaryPhone
      },
      checkIfQBCompanyInfoWasUpdated() {
        return this.qbCompanyInfoWasUpdated
      },
      addCompanyInfoToFormObject() {
        this.form.qbCompanyInfo = this.companyInfo
      },
      sendEmptyQBCompanyInfoObject() {
        this.form.qbCompanyInfo = {}
      },
      reset() {
        this.sections.editGeneralInfo = false
        this.companyInfo.message.CompanyName = this.companyInfoOriginal.CompanyName
        this.companyInfo.message.CompanyAddr.Line1 = this.companyInfoOriginal.CompanyAddr.Line1
        this.companyInfo.message.CompanyAddr.Line2 = this.companyInfoOriginal.CompanyAddr.Line2
        this.companyInfo.message.CompanyAddr.City = this.companyInfoOriginal.CompanyAddr.City
        this.companyInfo.message.CompanyAddr.CountrySubDivisionCode = this.companyInfoOriginal.CompanyAddr.CountrySubDivisionCode
        this.companyInfo.message.CompanyAddr.PostalCode = this.companyInfoOriginal.CompanyAddr.PostalCode
        this.companyInfo.message.PrimaryPhone = this.companyInfoOriginal.PrimaryPhone
        this.companyInfo.message.Email.Address = this.companyInfoOriginal.Email.Address

        this.companyInfoTemporary.CompanyName = this.companyInfoOriginal.CompanyName
        this.companyInfoTemporary.CompanyAddr.Line1 = this.companyInfoOriginal.CompanyAddr.Line1
        this.companyInfoTemporary.CompanyAddr.Line2 = this.companyInfoOriginal.CompanyAddr.Line2
        this.companyInfoTemporary.CompanyAddr.City = this.companyInfoOriginal.CompanyAddr.City
        this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode = this.companyInfoOriginal.CompanyAddr.CountrySubDivisionCode
        this.companyInfoTemporary.CompanyAddr.PostalCode = this.companyInfoOriginal.CompanyAddr.PostalCode
        this.companyInfoTemporary.PrimaryPhone = this.companyInfoOriginal.PrimaryPhone
        this.companyInfoTemporary.Email.Address = this.companyInfoOriginal.Email.Address

        this.qbCompanyInfoWasUpdated = false

      },
      save() {
        this.sections.editGeneralInfo = false
        this.companyInfo.message.CompanyName = this.companyInfoTemporary.CompanyName
        this.companyInfo.message.CompanyAddr.Line1 = this.companyInfoTemporary.CompanyAddr.Line1
        this.companyInfo.message.CompanyAddr.Line2 = this.companyInfoTemporary.CompanyAddr.Line2
        this.companyInfo.message.CompanyAddr.City = this.companyInfoTemporary.CompanyAddr.City
        this.companyInfo.message.CompanyAddr.CountrySubDivisionCode = this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode
        this.companyInfo.message.CompanyAddr.PostalCode = this.companyInfoTemporary.CompanyAddr.PostalCode
        this.companyInfo.message.PrimaryPhone = this.companyInfoTemporary.PrimaryPhone
        this.companyInfo.message.Email.Address = this.companyInfoTemporary.Email.Address
        this.qbCompanyInfoWasUpdated = true

        this.updateFormDataWithQBData()
        this.checkValidPhoneNumber()

      },
      cancel() {
        this.sections.editGeneralInfo = false
        this.companyInfoTemporary.CompanyName = this.companyInfo.message.CompanyName
        this.companyInfoTemporary.CompanyAddr.Line1 = this.companyInfo.message.CompanyAddr.Line1
        this.companyInfoTemporary.CompanyAddr.Line2 = this.companyInfo.message.CompanyAddr.Line2
        this.companyInfoTemporary.CompanyAddr.City = this.companyInfo.message.CompanyAddr.City
        this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode = this.companyInfo.message.CompanyAddr.CountrySubDivisionCode
        this.companyInfoTemporary.PrimaryPhone = this.companyInfo.message.PrimaryPhone
        this.companyInfoTemporary.Email.Address = this.companyInfo.message.Email.Address
      },
      companyNameIsValid() {

      }
    }
  }
</script>

<style>

    .empty-field {
        border: thin red solid
    }

    .has-error {
        border: thin red solid
    }

    .has-error-name {
        color: red;
    }

    .empty-field-name {
        color: red;
    }

    .star {
        font-size: 1.15rem;
    }

</style>
