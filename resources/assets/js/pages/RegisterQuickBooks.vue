<template>

    <div class="flex flex-col further-info-main text-center">
        <div class="main-header p-4 uppercase">
            Please enter and review the below information
        </div>
        <h3 class="text-center m-4">{{ companyInfo.message.CompanyName }}</h3>
        <div class="box border flex flex-col section">
            <div class="content">


                <h2 class="text-center text-red uppercase" v-show="inputNotValid">Please Check That All Mandatory Fields
                    Are Setup Correctly</h2>

                <div class="flex flex-col m-4">
                    <h5 class="text-center text-red uppercase" v-show="errors.email">email address missing or has
                        incorrect format</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.name">The name field is missing</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.phone">The phone field is not correct</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.password.error">The password field is missing</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.company_name">The company name field is missing</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.address_line_1">The address line 1 field is missing</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.city">The city field is missing</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.state">The state field is missing</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.zip">The zip field is missing</h5>
                    <h5 class="text-center text-red uppercase" v-show="errors.terms">please accept the terms of agreement</h5>
                </div>

                <!-- Name -->
                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('name')}">-->
                    <label class="j-label">First and Last Name</label><span class="j-label ml-2 star">*</span>
                    <div class="">
                        <input type="text" class="border input" name="name" v-model="form.name" autofocus>
                    </div>
                </div>

                <!--&lt;!&ndash; E-Mail Address &ndash;&gt;-->
                <!--<div class="input-section">-->
                <!--&lt;!&ndash;<div class="input-section" :class="{'has-error': registerForm.errors.has('email')}">&ndash;&gt;-->
                <!--<label class="j-label">E-Mail Address</label>-->

                <!--<div>-->
                <!--<input-->
                <!--type="email"-->
                <!--class="border input"-->
                <!--name="email"-->
                <!--v-model="companyInfo.message.Email.Address">-->
                <!--&lt;!&ndash;<span class="help-block" v-show="registerForm.errors.has('email')"></span>&ndash;&gt;-->
                <!--</div>-->
                <!--</div>-->

                <hr>

                <!-- Password -->
                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('password')}">-->
                    <div class="flex justify-between">
                        <div class="flex">
                            <label class="j-label">Password</label><span class="j-label ml-2 star">*</span>
                        </div>
                        <div style="color:red;"
                             ref="password_error"
                             v-show="errors.password.pw_length < 6"
                        >
                            Must Be at least 6 alphanumeric characters
                        </div>
                    </div>

                    <div>
                        <input type="password"
                               class="border input"
                               name="password"
                               ref="password"
                               @keyup="passwordLength"
                               v-model="form.password">
                        <!--<span class="help-block" v-show="registerForm.errors.has('password')"></span>-->
                    </div>
                </div>

                <!-- Password Confirmation -->
                <!--<div class="input-section">-->
                <div class="input-section">
                    <div class="flex justify-between">
                        <div>
                            <label class="j-label">Confirm Password</label><span class="j-label ml-2 star">*</span>
                        </div>
                        <div v-if="!errors.password.match">
                            <span class="has-error-name text-center">
                                {{ errors.password.message }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <input type="password"
                               :class="{'has-error': !errors.password.match}"
                               class="border input"
                               name="password_confirmation"
                               v-model="form.password_confirmation" @keyup="confirmPassword">

                        <!--<span class="help-block" v-show="registerForm.errors.has('password_confirmation')"></span>-->
                    </div>
                </div>
            </div>
        </div>


        <div class="box border flex flex-col section">
            <h4 class="text-center mt-2">General Info</h4>
            <div class="flex justify-around items-center m-2" v-show="!sections.editGeneralInfo">
                <div class="flex-1 w-full"></div>
                <button class="flex-1 w-full btn bg-blue"
                        ref="edit_btn"
                        @click="sections.editGeneralInfo = true">Edit
                </button>
                <div class="flex-1 w-full"></div>
            </div>
            <div class="flex justify-around items-center m-2" v-show="sections.editGeneralInfo">
                <button class="w-full flex-1 btn bg-blue mr-2"
                        id="cancel_btn"
                        ref="cancel_btn"
                        @click="cancel()">Cancel
                </button>
                <button class="w-full flex-1 btn bg-blue mr-2"
                        ref="reset_btn"
                        @click="reset()">Reset
                </button>
                <button class="flex-1 btn bg-blue w-full ml-2"
                        ref="save_btn"
                        @click="save()">Save
                </button>
            </div>
            <div class="content" v-show="!sections.editGeneralInfo">
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="flex">
                        <div class="ml-2"
                             :class="companyInfo.message.CompanyName ? '' : 'empty-field-name'"
                        >Company Name
                        </div>
                        <span class="j-label ml-2 star">*</span>
                    </div>
                    <div class="mr-2">{{ companyInfo.message.CompanyName }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="flex">
                        <div class="ml-2"
                             :class="companyInfo.message.CompanyAddr.Line1 ? '' : 'empty-field-name'"
                        >Address Line 1
                        </div>
                        <span class="j-label ml-2 star">*</span>
                    </div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.Line1 }}</div>
                </div>
                <div v-if="companyInfo.message.CompanyAddr.Line2" class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">Address Line 2</div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.Line2 }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="flex">
                        <div class="ml-2"
                             :class="companyInfo.message.CompanyAddr.City ? '' : 'empty-field-name'"
                        >City
                        </div>
                        <span class="j-label ml-2 star">*</span>
                    </div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.City }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="flex">
                        <div class="ml-2"
                             :class="companyInfo.message.CompanyAddr.CountrySubDivisionCode ? '' : 'empty-field-name'"
                        >State
                        </div>
                        <span class="j-label ml-2 star">*</span>
                    </div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.CountrySubDivisionCode }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="flex">
                        <div class="ml-2"
                             :class="companyInfo.message.CompanyAddr.PostalCode ? '' : 'empty-field-name'"
                        >PostalCode
                        </div>
                        <span class="j-label ml-2 star">*</span>
                    </div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.PostalCode }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="flex">
                        <div class="ml-2"
                             ref="primaryPhone"
                             :class="companyInfo.message.PrimaryPhone ? '' : 'empty-field-name'"
                        >Mobile Phone Number
                        </div>
                        <span class="j-label ml-2 star">*</span>
                    </div>
                    <div class="mr-2">{{ companyInfo.message.PrimaryPhone }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="flex">
                        <div class="ml-2"
                             :class="companyInfo.message.Email.Address ? '' : 'empty-field-name'"
                        >Email Address
                        </div>
                        <span class="j-label ml-2 star">*</span>
                    </div>
                    <div class="mr-2">{{ companyInfo.message.Email.Address }}</div>
                </div>
            </div>
            <div class="content" v-show="sections.editGeneralInfo">
                <div class="input-section">
                    <label class="j-label">Company Name</label><span class="j-label ml-2 star">*</span>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               :class="companyInfoTemporary.CompanyName ? '' : 'empty-field'"
                               v-model="companyInfoTemporary.CompanyName">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">Address Line 1</label><span class="j-label ml-2 star">*</span>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               :class="companyInfoTemporary.CompanyAddr.Line1 ? '' : 'empty-field'"
                               v-model="companyInfoTemporary.CompanyAddr.Line1">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">Address Line 2</label>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               v-model="companyInfoTemporary.CompanyAddr.Line2">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">City</label><span class="j-label ml-2 star">*</span>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               :class="companyInfoTemporary.CompanyAddr.City ? '' : 'empty-field'"
                               v-model="companyInfoTemporary.CompanyAddr.City">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">State</label><span class="j-label ml-2 star">*</span>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               :class="companyInfoTemporary.CompanyAddr.CountrySubDivisionCode ? '' : 'empty-field'"
                               v-model="companyInfoTemporary.CompanyAddr.CountrySubDivisionCode">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">Zip Code</label><span class="j-label ml-2 star">*</span>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               :class="companyInfoTemporary.CompanyAddr.PostalCode ? '' : 'empty-field'"
                               v-model="companyInfoTemporary.CompanyAddr.PostalCode">
                    </div>
                </div>


                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': form.errors.has('phone_number')}">-->
                    <label class="j-label">Mobile Phone Number</label><span class="j-label ml-2 star">*</span>
                    <div class="">
                        <input type="tel" class="border input" name="phone_number" maxlength="10"
                               v-model="companyInfoTemporary.PrimaryPhone"
                               @blur="validateMobileNumber($event.target.value)"
                               :class="companyInfoTemporary.PrimaryPhone ? '' : 'empty-field'"
                               @keyup="filterPhone">
                        <div v-if="checkThatNumberIsMobile()" style="color: green">
                            {{ this.getMobileValidResponse[1] }}
                        </div>
                        <div v-else-if="checkLandLineNumber()" style="color: red">
                            {{ this.getMobileValidResponse[1] }}
                        </div>
                        <div v-else-if="checkIfNumberIsVirtual()" style="color: red">
                            {{ this.getMobileValidResponse[1] }}
                        </div>
                        <span class="help-block" v-show="form.errors.has('phone_number')">
                                    {{ form.errors.get('phone_number') }}
                        </span>
                    </div>
                </div>

                <!-- E-Mail Address -->
                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('email')}">-->
                    <label class="j-label">E-Mail Address</label><span class="j-label ml-2 star">*</span>

                    <div>
                        <input
                                type="email"
                                class="border input"
                                name="email"
                                @blur="validateEmail()"
                                :class="companyInfoTemporary.Email.Address ? '' : 'empty-field'"
                                v-model="companyInfoTemporary.Email.Address">
                        <span class="help-block uppercase" v-show="!validateEmail()">Your Email Does Not have the correct format</span>
                    </div>
                </div>

            </div>
        </div>


        <div class="box border flex flex-col section">
            <div class="content">
                <!-- Terms And Conditions -->
                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('terms')}">-->
                    <div class="col-md-6 col-md-offset-4">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="terms" v-model="form.terms">
                                I Accept The <a href="/terms" target="_blank">Terms Of Service</a>
                            </label>

                            <!--<span class="help-block" v-show="registerForm.errors.has('terms')"></span>-->
                        </div>
                    </div>
                </div>


                <!--<pre>{{ registerForm.terms }}</pre>-->


                <!--TODO check if checkValidData can be used-->
                <!--<button type="submit" name="submit" class="register text-center border shadow uppercase"-->
                <!--@click.prevent="submitFurtherInfo()"-->
                <!--:disabled="checkValidData()">-->
                <!--<span v-if="disabled.submit">-->
                <!--<i class="fa fa-btn fa-spinner fa-spin"></i>-->
                <!--</span>-->
                <!--Register-->
                <!--</button>-->


                <div class="input-section">
                    <div class="col-md-6 col-md-offset-4">
                        <button id="register" name=register
                                class="register text-center border shadow uppercase"
                                @click.prevent="register"
                                :disabled="registerForm.busy">
                    <span v-if="registerForm.busy">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>Registering
                    </span>
                            <span v-else>
                        <i class="fa fa-btn fa-check-circle mr-2"></i>Register
                    </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--<pre>{{ companyInfo }}</pre>-->

    </div>

</template>

<script>

  import { mapGetters, mapMutations, mapActions } from 'vuex'

  export default {
    name: 'RegisterQuickBooks',
    data() {
      return {
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
          terms: false,
          notes: '',
          password: '',
          password_confirmation: '',
          email_contact: true,
          phone_contact: false,
          sms_text: false,
          qbCompanyInfo: {}
        }),
        errors: {
          password: {
            match: true,
            message: '',
            pw_length: 0,
            error:false
          },
          email: false,
          name: false,
          company_name: false,
          phone: false,
          address_line_1: false,
          city: false,
          state: false,
          zip: false,
          terms: false
        },
        qbCompanyInfoWasUpdated: false,
        companyInfoTemporary: {
          CompanyName: '',
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
          name: '',
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
      ])
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
      getTheCompanyInfo() {
        axios.get('/quickbooks/getCachedCompanyInfo')
          .then(function(response) {
            console.log('no error')
            // debugger
            this.companyInfo = response.data

            this.companyInfoTemporary.CompanyName = response.data.message.CompanyName
            this.companyInfoTemporary.CompanyAddr.Line1 = response.data.message.CompanyAddr.Line1
            this.companyInfoTemporary.CompanyAddr.Line2 = response.data.message.CompanyAddr.Line2
            this.companyInfoTemporary.CompanyAddr.City = response.data.message.CompanyAddr.City
            this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode = response.data.message.CompanyAddr.CountrySubDivisionCode
            this.companyInfoTemporary.CompanyAddr.PostalCode = response.data.message.CompanyAddr.PostalCode
            this.companyInfoTemporary.PrimaryPhone = response.data.message.PrimaryPhone
            this.companyInfoTemporary.Email.Address = response.data.message.Email.Address

            this.companyInfoOriginal.CompanyName = response.data.message.CompanyName
            this.companyInfoOriginal.CompanyAddr.Line1 = response.data.message.CompanyAddr.Line1
            this.companyInfoOriginal.CompanyAddr.Line2 = response.data.message.CompanyAddr.Line2
            this.companyInfoOriginal.CompanyAddr.City = response.data.message.CompanyAddr.City
            this.companyInfoOriginal.CompanyAddr.CountrySubDivisionCode = response.data.message.CompanyAddr.CountrySubDivisionCode
            this.companyInfoOriginal.CompanyAddr.PostalCode = response.data.message.CompanyAddr.PostalCode
            this.companyInfoOriginal.PrimaryPhone = response.data.message.PrimaryPhone
            this.companyInfoOriginal.Email.Address = response.data.message.Email.Address

          }.bind(this))
          .catch(function(error) {
            console.log(error)
          })
      },
      confirmPassword() {
        if (this.form.password !== this.form.password_confirmation) {
          this.errors.password.message = 'Passwords need to match.'
          this.errors.password.match = false
        } else {
          this.form.errors.errors = {}
          this.errors.password.match = true
        }
      },
      passwordLength() {
        this.errors.password.pw_length = this.form.password.length
      },
      register() {
        this.inputNotValid = false;
        this.updateFormDataWithQBData();
        if (this.checkValidData()) {
          let updateQBData = false
          if (this.checkIfQBCompanyInfoWasUpdated()) {
            this.addCompanyInfoToFormObject()
            updateQBData = true
          } else {
            this.sendEmptyQBCompanyInfoObject()
          }
          this.registerForm.busy = false

          User.submitFurtherInfo(this.form, this.registerForm, updateQBData)
          this.registerForm.busy = false
        } else {
          this.inputNotValid = true
        }
      },
      unformatNumber(number) {
        let unformattedNumber = ''
        for (let i = 0; i < number.length; i++) {
          if (!isNaN(parseInt(number[i]))) {
            unformattedNumber = unformattedNumber + number[i]
          }
        }
        let numberLength = unformattedNumber.length
        if (numberLength < 10) {
          this.emptyPhoneNumberInStore()
        }
        // debugger;
        this.phoneNumberLength = numberLength
        return numberLength
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
        let phone = this.unformatNumber(this.form.phone_number)
        if (
          (this.getMobileValidResponse[1] !== 'mobile' ||
            this.getMobileValidResponse[2] !== 'mobile') ||
          phone !== 10
        ) {
          valid = false
          this.errors.phone = true;
        } else {
          this.errors.phone = false;
        }

        if (this.form.email === '' || !this.validateEmail()) {
          this.errors.email = true;
          valid = false;
        } else {
          this.errors.email = false;
        }

        if (this.form.name === '') {
          this.errors.name = true;
          valid = false;
        } else {
          this.errors.name = false;
        }

        if (this.form.company_name === '') {
          this.errors.company_name = true;
          valid = false;
        } else {
          this.errors.company_name = false;
        }

        if (this.form.address_line_1 === '') {
          this.errors.address_line_1 = true;
          valid = false;
        } else {
          this.errors.address_line_1 = false;
        }

        if (this.form.city === '') {
          this.errors.city = true;
          valid = false;
        } else {
          this.errors.city = false;
        }


        if (this.form.state === '') {
          this.errors.state = true;
          valid = false;
        } else {
          this.errors.state = false;
        }


        if (this.form.zip === '') {
          this.errors.zip = true;
          valid = false;
        } else {
          this.errors.zip = false;
        }

        if (this.form.terms === '') {
          this.errors.terms = true;
          valid = false;
        } else {
          this.errors.terms = false;
        }

        // company name
        // does it have a length

        if (
          (this.form.password !== this.form.password_confirmation) ||
          ( this.form.password === '' || this.form.password_confirmation === '')
        ) {
          valid = false
          this.errors.password.error = true
        } else {
          this.errors.password.error = false
        }

        return valid

      },
      validateMobileNumber(phone) {
        if (phone !== '') {
          this.checkMobileNumber(phone)
        }
      },
      validateEmail() {
        var re = /^(?:[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&amp;'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/
        return re.test(this.companyInfoTemporary.Email.Address)
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
      checkIfNumberIsVirtual() {
        if (this.getMobileValidResponse[1] === 'virtual' ||
          this.getMobileValidResponse[2] === 'virtual') {
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
