<template>

    <div class="flex flex-col further-info-main text-center">
        <div class="main-header p-4 uppercase">
            Please enter and review the below information
        </div>
        <h3 class="text-center m-4">{{ companyInfo.message.CompanyName }}</h3>
        <div class="box border flex flex-col section">
            <div class="content">
                <!-- Name -->
                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('name')}">-->
                    <label class="j-label">Name</label>
                    <div class="">
                        <input type="text" class="border input" name="name" v-model="registerForm.name" autofocus>
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
                        <label class="j-label">Password</label>
                        <div style="color:red;"
                        >Must Be at least 6 alphanumeric characters
                        </div>
                    </div>

                    <div>
                        <input type="password" class="border input" name="password" v-model="registerForm.password">

                        <!--<span class="help-block" v-show="registerForm.errors.has('password')"></span>-->
                    </div>
                </div>

                <!-- Password Confirmation -->
                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('password_confirmation')}">-->
                    <label class="j-label">Confirm Password</label>
                    <div>
                        <input type="password" class="border input" name="password_confirmation"
                               v-model="registerForm.password_confirmation" @keyup="confirmPassword">

                        <!--<span class="help-block" v-show="registerForm.errors.has('password_confirmation')"></span>-->
                    </div>
                </div>
            </div>
        </div>


        <div class="box border flex flex-col section">
            <h4 class="text-center mt-2">General Info</h4>
            <div class="flex justify-around items-center m-2" v-show="!sections.editGeneralInfo">
                <div class="flex-1 w-full"></div>
                <button class="flex-1 w-full btn bg-blue" @click="sections.editGeneralInfo = true">Edit</button>
                <div class="flex-1 w-full"></div>
            </div>
            <div class="flex justify-around items-center m-2" v-show="sections.editGeneralInfo">
                <button class="w-full flex-1 btn bg-blue mr-2" @click="cancel()">Cancel</button>
                <button class="w-full flex-1 btn bg-blue mr-2" @click="reset()">Reset</button>
                <button class="flex-1 btn bg-blue w-full ml-2" @click="save()">Save</button>
            </div>
            <div class="content" v-show="!sections.editGeneralInfo">
                readonly
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">Company Name</div>
                    <div class="mr-2">{{ companyInfo.message.CompanyName }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">Address Line 1</div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.Line1 }}</div>
                </div>
                <div v-if="companyInfo.message.CompanyAddr.Line2" class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">Address Line 2</div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.Line2 }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">City</div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.City }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">State</div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.CountrySubDivisionCode }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">PostalCode</div>
                    <div class="mr-2">{{ companyInfo.message.CompanyAddr.PostalCode }}</div>
                </div>
                <div class="flex mt-2 mb-2 justify-between">
                    <div class="ml-2">Email Address</div>
                    <div class="mr-2">{{ companyInfo.message.Email.Address }}</div>
                </div>
            </div>
            <div class="content" v-show="sections.editGeneralInfo">
                editable
                <div class="input-section">
                    <label class="j-label">Company Name</label>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               v-model="companyInfoTemporary.CompanyName">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">Address Line 1</label>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               v-model="companyInfoTemporary.CompanyAddr.Line1">
                    </div>
                </div>

                <div v-if="companyInfo.message.CompanyAddr.Line2" class="input-section">
                    <label class="j-label">Address Line 2</label>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               v-model="companyInfoTemporary.CompanyAddr.Line2">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">City</label>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               v-model="companyInfoTemporary.CompanyAddr.City">
                    </div>
                </div>

                <div class="input-section">
                    <label class="j-label">State</label>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               v-model="companyInfoTemporary.CompanyAddr.CountrySubDivisionCode">
                    </div>
                </div>


                <div class="input-section">
                    <label class="j-label">Zip Code</label>
                    <div>
                        <input type="text" class="border input" name="password_confirmation"
                               v-model="companyInfoTemporary.CompanyAddr.PostalCode">
                    </div>
                </div>


                <!-- E-Mail Address -->
                <div class="input-section">
                    <!--<div class="input-section" :class="{'has-error': registerForm.errors.has('email')}">-->
                    <label class="j-label">E-Mail Address</label>

                    <div>
                        <input
                                type="email"
                                class="border input"
                                name="email"
                                v-model="companyInfo.message.Email.Address">
                        <!--<span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
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
                                <input type="checkbox" name="terms" v-model="registerForm.terms">
                                I Accept The <a href="/terms" target="_blank">Terms Of Service</a>
                            </label>

                            <!--<span class="help-block" v-show="registerForm.errors.has('terms')"></span>-->
                        </div>
                    </div>
                </div>

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


        <pre>{{ companyInfo }}</pre>

    </div>

</template>

<script>
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
                Name: "NeoEnabled",
                Value: null
              },
              {
                Name: "IsQbdtMigrated",
                Value: null
              },
              {
                Name: "CompanyType",
                Value: null
              },
              {
                Name: "SubscriptionStatus",
                Value: null
              },
              {
                Name: "OfferingSku",
                Value: null
              },
              {
                Name: "PayrollFeature",
                Value: null
              },
              {
                Name: "AccountantFeature",
                Value: null
              },
              {
                Name: "QBOIndustryType",
                Value: null
              },
              {
                Name: "ItemCategoriesFeature",
                Value: null
              },
              {
                Name: "AssignedTime",
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
        companyInfoEditable: {
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
                Name: "NeoEnabled",
                Value: null
              },
              {
                Name: "IsQbdtMigrated",
                Value: null
              },
              {
                Name: "CompanyType",
                Value: null
              },
              {
                Name: "SubscriptionStatus",
                Value: null
              },
              {
                Name: "OfferingSku",
                Value: null
              },
              {
                Name: "PayrollFeature",
                Value: null
              },
              {
                Name: "AccountantFeature",
                Value: null
              },
              {
                Name: "QBOIndustryType",
                Value: null
              },
              {
                Name: "ItemCategoriesFeature",
                Value: null
              },
              {
                Name: "AssignedTime",
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
        }
      }
    },
    mounted: function() {
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
          this.companyInfoTemporary.Email.Address = response.data.message.Email.Address

          this.companyInfoOriginal.CompanyName = response.data.message.CompanyName
          this.companyInfoOriginal.CompanyAddr.Line1 = response.data.message.CompanyAddr.Line1
          this.companyInfoOriginal.CompanyAddr.Line2 = response.data.message.CompanyAddr.Line2
          this.companyInfoOriginal.CompanyAddr.City = response.data.message.CompanyAddr.City
          this.companyInfoOriginal.CompanyAddr.CountrySubDivisionCode = response.data.message.CompanyAddr.CountrySubDivisionCode
          this.companyInfoOriginal.CompanyAddr.PostalCode = response.data.message.CompanyAddr.PostalCode
          this.companyInfoOriginal.Email.Address = response.data.message.Email.Address


        }.bind(this))
        .catch(function(error) {
          console.log(error)
        })
    },
    methods: {
      confirmPassword() {},
      register() {},
      reset() {

        this.sections.editGeneralInfo = false;

        this.companyInfo.message.CompanyName = this.companyInfoOriginal.CompanyName;
        this.companyInfo.message.CompanyAddr.Line1 = this.companyInfoOriginal.CompanyAddr.Line1;
        this.companyInfo.message.CompanyAddr.Line2 = this.companyInfoOriginal.CompanyAddr.Line2;
        this.companyInfo.message.CompanyAddr.City = this.companyInfoOriginal.CompanyAddr.City;
        this.companyInfo.message.CompanyAddr.CountrySubDivisionCode = this.companyInfoOriginal.CompanyAddr.CountrySubDivisionCode;
        this.companyInfo.message.CompanyAddr.PostalCode = this.companyInfoOriginal.CompanyAddr.PostalCode;
        this.companyInfo.message.Email.Address = this.companyInfoOriginal.Email.Address;

        this.companyInfoTemporary.CompanyName = this.companyInfoOriginal.CompanyName;
        this.companyInfoTemporary.CompanyAddr.Line1 = this.companyInfoOriginal.CompanyAddr.Line1;
        this.companyInfoTemporary.CompanyAddr.Line2 = this.companyInfoOriginal.CompanyAddr.Line2;
        this.companyInfoTemporary.CompanyAddr.City = this.companyInfoOriginal.CompanyAddr.City;
        this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode = this.companyInfoOriginal.CompanyAddr.CountrySubDivisionCode;
        this.companyInfoTemporary.CompanyAddr.PostalCode = this.companyInfoOriginal.CompanyAddr.PostalCode;
        this.companyInfoTemporary.Email.Address = this.companyInfoOriginal.Email.Address;

      },
      save() {
        this.sections.editGeneralInfo = false;

        this.companyInfo.message.CompanyName = this.companyInfoTemporary.CompanyName;
        this.companyInfo.message.CompanyAddr.Line1 = this.companyInfoTemporary.CompanyAddr.Line1;
        this.companyInfo.message.CompanyAddr.Line2 = this.companyInfoTemporary.CompanyAddr.Line2;
        this.companyInfo.message.CompanyAddr.City = this.companyInfoTemporary.CompanyAddr.City;
        this.companyInfo.message.CompanyAddr.CountrySubDivisionCode = this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode;
        this.companyInfo.message.CompanyAddr.PostalCode = this.companyInfoTemporary.CompanyAddr.PostalCode;
        this.companyInfo.message.Email.Address = this.companyInfoTemporary.Email.Address;
      },
      cancel () {
        this.sections.editGeneralInfo = false;
        this.companyInfoTemporary.CompanyName = this.companyInfo.message.CompanyName;
        this.companyInfoTemporary.CompanyAddr.Line1 = this.companyInfo.message.CompanyAddr.Line1;
        this.companyInfoTemporary.CompanyAddr.Line2 = this.companyInfo.message.CompanyAddr.Line2;
        this.companyInfoTemporary.CompanyAddr.City = this.companyInfo.message.CompanyAddr.City;
        this.companyInfoTemporary.CompanyAddr.CountrySubDivisionCode = this.companyInfo.message.CompanyAddr.CountrySubDivisionCode;
        this.companyInfoTemporary.Email.Address = this.companyInfo.message.Email.Address;
      }
    }
  }
</script>

<style>

</style>
