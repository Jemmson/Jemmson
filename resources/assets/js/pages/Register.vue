<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <card>
                    <h3 class="text-center">Who Are You?</h3>
                    <div class="row w-full justify-content-between">
                        <v-btn
                                class="w-1/2"
                                color="primary"
                                ref="customerButton"
                                :class="userTypeSelected === 'customer' ? 'selected-button' : ''"
                                v-on:click="userSelected('customer')">Customer
                        </v-btn>

                        <v-btn
                                class="w-1/2"
                                color="primary"
                                ref="contractorButton"
                                :class="userTypeSelected === 'contractor' ? 'selected-button' : ''"
                                v-on:click="userSelected('contractor')">Contractor
                        </v-btn>
                    </div>

                    <div v-show="showRegistration">

                        <hr style="margin-top: 3rem">

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('first_name')">-->
                            <label for="firstName" class="pt-1 pt-2">First Name *</label>
                            <input
                                    id="firstName"
                                    type="text"
                                    name="fname"
                                    autocomplete="on"
                                    class="form-control"
                                    ref="first_name"
                                    v-model="registerForm.first_name">
                            <!--                <span class="help-block" v-show="registerForm.errors.has('first_name')"></span>-->
                            <span class="help-block"
                                  v-show="registerForm.errors ? registerForm.errors.first_name !== '' : ''">{{ registerForm.errors ? registerForm.errors.first_name : ''}}</span>
                        </div>

                        <div class="row">
                            <label for="lastName" class=" pt-3 pt-2">Last Name *</label>
                            <input id="lastName"
                                   name="lname"
                                   autocomplete="on"
                                   type="text" class="form-control "
                                   v-model="registerForm.last_name">
                            <span class="help-block" v-show="registerForm.errors.last_name !== ''">{{registerForm.errors.last_name}}</span>
                        </div>

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="email" class=" pt-3 pt-2">E-Mail Address *</label>
                            <input id="email"
                                   name="email"
                                   autocomplete="on"
                                   type="text" class="form-control "
                                   v-model="registerForm.email">
                            <span class="help-block" v-show="registerForm.errors.email !== ''">{{registerForm.errors.email}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>

                        <div class="row" v-if="registerForm.usertype === 'contractor'">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="companyName" class=" pt-3 pt-2">Company Name *</label>
                            <input id="companyName"
                                   name="companyname"
                                   autocomplete="on"
                                   type="text" class="form-control "
                                   v-model="registerForm.companyName">
                            <span class="help-block" v-show="registerForm.errors.companyName !== ''">{{registerForm.errors.companyName}}</span>
                            <add-license-box
                                    width="100%"
                                    @add="addLicenses($event)"
                            ></add-license-box>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="phoneNumber" class=" pt-3 pt-2">Mobile Phone Number *</label>
                            <div v-show="phoneFormatError" class="formatErrorLabel">The phone number must be 10
                                numbers
                            </div>
                            <input id="phoneNumber"
                                   type="text"
                                   name="phone"
                                   autocomplete="on"
                                   :disabled="loading"
                                   class="form-control "
                                   @blur="validateMobileNumber($event.target.value)"
                                   @keyup="filterPhone"
                                   v-model="registerForm.phoneNumber">
                            <v-progress-linear
                                    :active="loading"
                                    :indeterminate="loading"
                                    absolute
                                    bottom
                                    color="deep-purple accent-4"
                            ></v-progress-linear>
                            <div v-if="getMobileValidResponse ? getMobileValidResponse.length > 0 : false">
                                <div class="hidden">{{ registerButtonIsDisabled() }}</div>
                                <div v-if="getMobileValidResponse[1] === 'mobile'" class="mt-2">
                                    <div style="color: green">{{ getMobileValidResponse[1] }}</div>
                                </div>
                                <div class="mt-2" v-else style="color: red">{{ getMobileValidResponse[1] }}</div>
                            </div>
                            <span class="help-block" v-show="registerForm.errors.phoneNumber !== ''">{{registerForm.errors.phoneNumber}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>


                        <div class="row">
                            <input type="hidden" name="street_number" id="street_number">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="route" class=" pt-3 pt-2">Address Line 1 *</label>
                            <input id="route"
                                   name="addressLine1"
                                   autocomplete="on"
                                   type="text" class="form-control"
                                   v-model="registerForm.addressLine1">
                            <span class="help-block" v-show="registerForm.errors.addressLine1 !== ''">{{registerForm.errors.addressLine1}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>
                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="addressLine2" class=" pt-3 pt-2">Address Line 2</label>
                            <input id="addressLine2"
                                   name="addressLine2"
                                   autocomplete="on"
                                   type="text" class="form-control"
                                   v-model="registerForm.addressLine2">
                            <span class="help-block" v-show="registerForm.errors.addressLine2 !== ''">{{registerForm.errors.addressLine2}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>
                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="administrative_area_level_1" class=" pt-3 pt-2">City *</label>
                            <input id="administrative_area_level_1"
                                   name="city"
                                   autocomplete="on"
                                   type="text" class="form-control "
                                   v-model="registerForm.city">
                            <span class="help-block" v-show="registerForm.errors.city !== ''">{{registerForm.errors.city}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>
                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="locality" class=" pt-3 pt-2">State *</label>
                            <input
                                    type="text"
                                    class="form-control"
                                    name="state"
                                    id="locality"
                                    v-model="registerForm.state">
                            <span class="help-block" v-show="registerForm.errors.state !== ''">{{registerForm.errors.state}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>
                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="postal_code" class=" pt-3 pt-2">Zip Code *</label>
                            <input id="postal_code"
                                   v-mask="'#####-####'"
                                   name="zip"
                                   autocomplete="on"
                                   type="text" class="form-control "
                                   v-model="registerForm.zip">
                            <span class="help-block"
                                  v-show="registerForm.errors.zip !== ''">{{registerForm.errors.zip}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="password"
                                   class=" pt-3 pt-2">Password *</label>
                            <input id="password"
                                   type="password" class="form-control "
                                   v-model="registerForm.password">
                            <span class="help-block" v-show="registerForm.errors.password !== ''">{{registerForm.errors.password}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>

                        <div class="row">
                            <label for="password_confirmation" class=" pt-3 pt-2">Confirm
                                Password *</label>
                            <input id="password_confirmation"
                                   @keyup="verifyPassword()"
                                   type="password" class="form-control "
                                   v-model="registerForm.password_confirmation">
                            <span class="help-block" v-show="registerForm.errors.password_confirmation !== ''">{{registerForm.errors.password_confirmation}}</span>
                        </div>

                        <hr style="margin-top: 3rem">

                        <div class="row pt-3 pt-2 mb-2">
                            <input
                                    type="checkbox"

                                    class="mr-2"
                                    name="terms"
                                    style="align-self: center;"
                                    v-model="registerForm.terms">I Accept The
                            <a href="/terms" target="_blank">Terms Of Service *</a>
                            <span class="help-block" v-show="registerForm.errors.terms">You Must Accept The Terms Of Service</span>
                        </div>

                        <v-btn
                                class="w-full"
                                color="primary"
                                id="register" name=register
                                @click.prevent="register"
                                :disabled="registerForm.disabled">
                            <span v-if="registerForm.busy">
                                <i class="fa fa-btn fa-spinner fa-spin mr-2"></i>Registering
                            </span>
                            <span v-else>
                                <i class="fa fa-btn fa-check-circle mr-2"></i>Register
                            </span>
                        </v-btn>

                        <div style="height: 10rem; width: 100%">

                        </div>


                    </div>

                </card>
            </div>
        </div>

        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>

    </div>

</template>

<script>
  import { mapGetters, mapState } from 'vuex'
  import Card from '../components/shared/Card'
  import AddLicenseBox from '../components/user/AddLicenseBox'
  import Phone from '../components/mixins/Phone'

  export default {
    name: 'Register',
    components: {
      Card,
      AddLicenseBox
    },
    data() {
      return {
        registerForm: {
          first_name: '',
          last_name: '',
          email: '',
          companyName: '',
          phoneNumber: '',
          addressLine1: '',
          addressLine2: '',
          licenses: [],
          city: '',
          state: '',
          zip: '',
          country: '',
          password: '',
          password_confirmation: '',
          terms: false,
          errors: {
            first_name: '',
            last_name: '',
            email: '',
            companyName: '',
            phoneNumber: '',
            addressLine1: '',
            addressLine2: '',
            city: '',
            state: '',
            zip: '',
            country: '',
            password: '',
            password_confirmation: '',
            terms: false,
          },
          usertype: '',
          busy: false,
          disabled: true
        },
        overlay: false,
        boxArray: [],
        userTypeSelected: '',
        usesQuickbooks: false,
        showRegistration: false,
        stateObjects:
          [
            {'name': 'Alabama', 'code': 'AL'},
            {'name': 'Alaska', 'code': 'AK'},
            {'name': 'American Samoa', 'code': 'AS'},
            {'name': 'Arizona', 'code': 'AZ'},
            {'name': 'Arkansas', 'code': 'AR'},
            {'name': 'California', 'code': 'CA'},
            {'name': 'Colorado', 'code': 'CO'},
            {'name': 'Connecticut', 'code': 'CT'},
            {'name': 'Delaware', 'code': 'DE'},
            {'name': 'District Of Columbia', 'code': 'DC'},
            {'name': 'Federated States Of Micronesia', 'code': 'FM'},
            {'name': 'Florida', 'code': 'FL'},
            {'name': 'Georgia', 'code': 'GA'},
            {'name': 'Guam', 'code': 'GU'},
            {'name': 'Hawaii', 'code': 'HI'},
            {'name': 'Idaho', 'code': 'ID'},
            {'name': 'Illinois', 'code': 'IL'},
            {'name': 'Indiana', 'code': 'IN'},
            {'name': 'Iowa', 'code': 'IA'},
            {'name': 'Kansas', 'code': 'KS'},
            {'name': 'Kentucky', 'code': 'KY'},
            {'name': 'Louisiana', 'code': 'LA'},
            {'name': 'Maine', 'code': 'ME'},
            {'name': 'Marshall Islands', 'code': 'MH'},
            {'name': 'Maryland', 'code': 'MD'},
            {'name': 'Massachusetts', 'code': 'MA'},
            {'name': 'Michigan', 'code': 'MI'},
            {'name': 'Minnesota', 'code': 'MN'},
            {'name': 'Mississippi', 'code': 'MS'},
            {'name': 'Missouri', 'code': 'MO'},
            {'name': 'Montana', 'code': 'MT'},
            {'name': 'Nebraska', 'code': 'NE'},
            {'name': 'Nevada', 'code': 'NV'},
            {'name': 'New Hampshire', 'code': 'NH'},
            {'name': 'New Jersey', 'code': 'NJ'},
            {'name': 'New Mexico', 'code': 'NM'},
            {'name': 'New York', 'code': 'NY'},
            {'name': 'North Carolina', 'code': 'NC'},
            {'name': 'North Dakota', 'code': 'ND'},
            {'name': 'Northern Mariana Islands', 'code': 'MP'},
            {'name': 'Ohio', 'code': 'OH'},
            {'name': 'Oklahoma', 'code': 'OK'},
            {'name': 'Oregon', 'code': 'OR'},
            {'name': 'Palau', 'code': 'PW'},
            {'name': 'Pennsylvania', 'code': 'PA'},
            {'name': 'Puerto Rico', 'code': 'PR'},
            {'name': 'Rhode Island', 'code': 'RI'},
            {'name': 'South Carolina', 'code': 'SC'},
            {'name': 'South Dakota', 'code': 'SD'},
            {'name': 'Tennessee', 'code': 'TN'},
            {'name': 'Texas', 'code': 'TX'},
            {'name': 'Utah', 'code': 'UT'},
            {'name': 'Vermont', 'code': 'VT'},
            {'name': 'Virgin Islands', 'code': 'VI'},
            {'name': 'Virginia', 'code': 'VA'},
            {'name': 'Washington', 'code': 'WA'},
            {'name': 'West Virginia', 'code': 'WV'},
            {'name': 'Wisconsin', 'code': 'WI'},
            {'name': 'Wyoming', 'code': 'WY'}
          ],
        countries: [
          {'name': 'United States', 'code': 'US'},
          {'name': 'United States Minor Outlying Islands', 'code': 'UM'},
          {'name': 'Afghanistan', 'code': 'AF'},
          {'name': 'Åland Islands', 'code': 'AX'},
          {'name': 'Albania', 'code': 'AL'},
          {'name': 'Algeria', 'code': 'DZ'},
          {'name': 'American Samoa', 'code': 'AS'},
          {'name': 'AndorrA', 'code': 'AD'},
          {'name': 'Angola', 'code': 'AO'},
          {'name': 'Anguilla', 'code': 'AI'},
          {'name': 'Antarctica', 'code': 'AQ'},
          {'name': 'Antigua and Barbuda', 'code': 'AG'},
          {'name': 'Argentina', 'code': 'AR'},
          {'name': 'Armenia', 'code': 'AM'},
          {'name': 'Aruba', 'code': 'AW'},
          {'name': 'Australia', 'code': 'AU'},
          {'name': 'Austria', 'code': 'AT'},
          {'name': 'Azerbaijan', 'code': 'AZ'},
          {'name': 'Bahamas', 'code': 'BS'},
          {'name': 'Bahrain', 'code': 'BH'},
          {'name': 'Bangladesh', 'code': 'BD'},
          {'name': 'Barbados', 'code': 'BB'},
          {'name': 'Belarus', 'code': 'BY'},
          {'name': 'Belgium', 'code': 'BE'},
          {'name': 'Belize', 'code': 'BZ'},
          {'name': 'Benin', 'code': 'BJ'},
          {'name': 'Bermuda', 'code': 'BM'},
          {'name': 'Bhutan', 'code': 'BT'},
          {'name': 'Bolivia', 'code': 'BO'},
          {'name': 'Bosnia and Herzegovina', 'code': 'BA'},
          {'name': 'Botswana', 'code': 'BW'},
          {'name': 'Bouvet Island', 'code': 'BV'},
          {'name': 'Brazil', 'code': 'BR'},
          {'name': 'British Indian Ocean Territory', 'code': 'IO'},
          {'name': 'Brunei Darussalam', 'code': 'BN'},
          {'name': 'Bulgaria', 'code': 'BG'},
          {'name': 'Burkina Faso', 'code': 'BF'},
          {'name': 'Burundi', 'code': 'BI'},
          {'name': 'Cambodia', 'code': 'KH'},
          {'name': 'Cameroon', 'code': 'CM'},
          {'name': 'Canada', 'code': 'CA'},
          {'name': 'Cape Verde', 'code': 'CV'},
          {'name': 'Cayman Islands', 'code': 'KY'},
          {'name': 'Central African Republic', 'code': 'CF'},
          {'name': 'Chad', 'code': 'TD'},
          {'name': 'Chile', 'code': 'CL'},
          {'name': 'China', 'code': 'CN'},
          {'name': 'Christmas Island', 'code': 'CX'},
          {'name': 'Cocos (Keeling) Islands', 'code': 'CC'},
          {'name': 'Colombia', 'code': 'CO'},
          {'name': 'Comoros', 'code': 'KM'},
          {'name': 'Congo', 'code': 'CG'},
          {'name': 'Congo, The Democratic Republic of the', 'code': 'CD'},
          {'name': 'Cook Islands', 'code': 'CK'},
          {'name': 'Costa Rica', 'code': 'CR'},
          {'name': 'Cote D\'Ivoire', 'code': 'CI'},
          {'name': 'Croatia', 'code': 'HR'},
          {'name': 'Cuba', 'code': 'CU'},
          {'name': 'Cyprus', 'code': 'CY'},
          {'name': 'Czech Republic', 'code': 'CZ'},
          {'name': 'Denmark', 'code': 'DK'},
          {'name': 'Djibouti', 'code': 'DJ'},
          {'name': 'Dominica', 'code': 'DM'},
          {'name': 'Dominican Republic', 'code': 'DO'},
          {'name': 'Ecuador', 'code': 'EC'},
          {'name': 'Egypt', 'code': 'EG'},
          {'name': 'El Salvador', 'code': 'SV'},
          {'name': 'Equatorial Guinea', 'code': 'GQ'},
          {'name': 'Eritrea', 'code': 'ER'},
          {'name': 'Estonia', 'code': 'EE'},
          {'name': 'Ethiopia', 'code': 'ET'},
          {'name': 'Falkland Islands (Malvinas)', 'code': 'FK'},
          {'name': 'Faroe Islands', 'code': 'FO'},
          {'name': 'Fiji', 'code': 'FJ'},
          {'name': 'Finland', 'code': 'FI'},
          {'name': 'France', 'code': 'FR'},
          {'name': 'French Guiana', 'code': 'GF'},
          {'name': 'French Polynesia', 'code': 'PF'},
          {'name': 'French Southern Territories', 'code': 'TF'},
          {'name': 'Gabon', 'code': 'GA'},
          {'name': 'Gambia', 'code': 'GM'},
          {'name': 'Georgia', 'code': 'GE'},
          {'name': 'Germany', 'code': 'DE'},
          {'name': 'Ghana', 'code': 'GH'},
          {'name': 'Gibraltar', 'code': 'GI'},
          {'name': 'Greece', 'code': 'GR'},
          {'name': 'Greenland', 'code': 'GL'},
          {'name': 'Grenada', 'code': 'GD'},
          {'name': 'Guadeloupe', 'code': 'GP'},
          {'name': 'Guam', 'code': 'GU'},
          {'name': 'Guatemala', 'code': 'GT'},
          {'name': 'Guernsey', 'code': 'GG'},
          {'name': 'Guinea', 'code': 'GN'},
          {'name': 'Guinea-Bissau', 'code': 'GW'},
          {'name': 'Guyana', 'code': 'GY'},
          {'name': 'Haiti', 'code': 'HT'},
          {'name': 'Heard Island and Mcdonald Islands', 'code': 'HM'},
          {'name': 'Holy See (Vatican City State)', 'code': 'VA'},
          {'name': 'Honduras', 'code': 'HN'},
          {'name': 'Hong Kong', 'code': 'HK'},
          {'name': 'Hungary', 'code': 'HU'},
          {'name': 'Iceland', 'code': 'IS'},
          {'name': 'India', 'code': 'IN'},
          {'name': 'Indonesia', 'code': 'ID'},
          {'name': 'Iran, Islamic Republic Of', 'code': 'IR'},
          {'name': 'Iraq', 'code': 'IQ'},
          {'name': 'Ireland', 'code': 'IE'},
          {'name': 'Isle of Man', 'code': 'IM'},
          {'name': 'Israel', 'code': 'IL'},
          {'name': 'Italy', 'code': 'IT'},
          {'name': 'Jamaica', 'code': 'JM'},
          {'name': 'Japan', 'code': 'JP'},
          {'name': 'Jersey', 'code': 'JE'},
          {'name': 'Jordan', 'code': 'JO'},
          {'name': 'Kazakhstan', 'code': 'KZ'},
          {'name': 'Kenya', 'code': 'KE'},
          {'name': 'Kiribati', 'code': 'KI'},
          {'name': 'Korea, Democratic People\'s Republic of', 'code': 'KP'},
          {'name': 'Korea, Republic of', 'code': 'KR'},
          {'name': 'Kuwait', 'code': 'KW'},
          {'name': 'Kyrgyzstan', 'code': 'KG'},
          {'name': 'Lao People\'s Democratic Republic', 'code': 'LA'},
          {'name': 'Latvia', 'code': 'LV'},
          {'name': 'Lebanon', 'code': 'LB'},
          {'name': 'Lesotho', 'code': 'LS'},
          {'name': 'Liberia', 'code': 'LR'},
          {'name': 'Libyan Arab Jamahiriya', 'code': 'LY'},
          {'name': 'Liechtenstein', 'code': 'LI'},
          {'name': 'Lithuania', 'code': 'LT'},
          {'name': 'Luxembourg', 'code': 'LU'},
          {'name': 'Macao', 'code': 'MO'},
          {'name': 'Macedonia, The Former Yugoslav Republic of', 'code': 'MK'},
          {'name': 'Madagascar', 'code': 'MG'},
          {'name': 'Malawi', 'code': 'MW'},
          {'name': 'Malaysia', 'code': 'MY'},
          {'name': 'Maldives', 'code': 'MV'},
          {'name': 'Mali', 'code': 'ML'},
          {'name': 'Malta', 'code': 'MT'},
          {'name': 'Marshall Islands', 'code': 'MH'},
          {'name': 'Martinique', 'code': 'MQ'},
          {'name': 'Mauritania', 'code': 'MR'},
          {'name': 'Mauritius', 'code': 'MU'},
          {'name': 'Mayotte', 'code': 'YT'},
          {'name': 'Mexico', 'code': 'MX'},
          {'name': 'Micronesia, Federated States of', 'code': 'FM'},
          {'name': 'Moldova, Republic of', 'code': 'MD'},
          {'name': 'Monaco', 'code': 'MC'},
          {'name': 'Mongolia', 'code': 'MN'},
          {'name': 'Montserrat', 'code': 'MS'},
          {'name': 'Morocco', 'code': 'MA'},
          {'name': 'Mozambique', 'code': 'MZ'},
          {'name': 'Myanmar', 'code': 'MM'},
          {'name': 'Namibia', 'code': 'NA'},
          {'name': 'Nauru', 'code': 'NR'},
          {'name': 'Nepal', 'code': 'NP'},
          {'name': 'Netherlands', 'code': 'NL'},
          {'name': 'Netherlands Antilles', 'code': 'AN'},
          {'name': 'New Caledonia', 'code': 'NC'},
          {'name': 'New Zealand', 'code': 'NZ'},
          {'name': 'Nicaragua', 'code': 'NI'},
          {'name': 'Niger', 'code': 'NE'},
          {'name': 'Nigeria', 'code': 'NG'},
          {'name': 'Niue', 'code': 'NU'},
          {'name': 'Norfolk Island', 'code': 'NF'},
          {'name': 'Northern Mariana Islands', 'code': 'MP'},
          {'name': 'Norway', 'code': 'NO'},
          {'name': 'Oman', 'code': 'OM'},
          {'name': 'Pakistan', 'code': 'PK'},
          {'name': 'Palau', 'code': 'PW'},
          {'name': 'Palestinian Territory, Occupied', 'code': 'PS'},
          {'name': 'Panama', 'code': 'PA'},
          {'name': 'Papua New Guinea', 'code': 'PG'},
          {'name': 'Paraguay', 'code': 'PY'},
          {'name': 'Peru', 'code': 'PE'},
          {'name': 'Philippines', 'code': 'PH'},
          {'name': 'Pitcairn', 'code': 'PN'},
          {'name': 'Poland', 'code': 'PL'},
          {'name': 'Portugal', 'code': 'PT'},
          {'name': 'Puerto Rico', 'code': 'PR'},
          {'name': 'Qatar', 'code': 'QA'},
          {'name': 'Reunion', 'code': 'RE'},
          {'name': 'Romania', 'code': 'RO'},
          {'name': 'Russian Federation', 'code': 'RU'},
          {'name': 'RWANDA', 'code': 'RW'},
          {'name': 'Saint Helena', 'code': 'SH'},
          {'name': 'Saint Kitts and Nevis', 'code': 'KN'},
          {'name': 'Saint Lucia', 'code': 'LC'},
          {'name': 'Saint Pierre and Miquelon', 'code': 'PM'},
          {'name': 'Saint Vincent and the Grenadines', 'code': 'VC'},
          {'name': 'Samoa', 'code': 'WS'},
          {'name': 'San Marino', 'code': 'SM'},
          {'name': 'Sao Tome and Principe', 'code': 'ST'},
          {'name': 'Saudi Arabia', 'code': 'SA'},
          {'name': 'Senegal', 'code': 'SN'},
          {'name': 'Serbia and Montenegro', 'code': 'CS'},
          {'name': 'Seychelles', 'code': 'SC'},
          {'name': 'Sierra Leone', 'code': 'SL'},
          {'name': 'Singapore', 'code': 'SG'},
          {'name': 'Slovakia', 'code': 'SK'},
          {'name': 'Slovenia', 'code': 'SI'},
          {'name': 'Solomon Islands', 'code': 'SB'},
          {'name': 'Somalia', 'code': 'SO'},
          {'name': 'South Africa', 'code': 'ZA'},
          {'name': 'South Georgia and the South Sandwich Islands', 'code': 'GS'},
          {'name': 'Spain', 'code': 'ES'},
          {'name': 'Sri Lanka', 'code': 'LK'},
          {'name': 'Sudan', 'code': 'SD'},
          {'name': 'Suriname', 'code': 'SR'},
          {'name': 'Svalbard and Jan Mayen', 'code': 'SJ'},
          {'name': 'Swaziland', 'code': 'SZ'},
          {'name': 'Sweden', 'code': 'SE'},
          {'name': 'Switzerland', 'code': 'CH'},
          {'name': 'Syrian Arab Republic', 'code': 'SY'},
          {'name': 'Taiwan, Province of China', 'code': 'TW'},
          {'name': 'Tajikistan', 'code': 'TJ'},
          {'name': 'Tanzania, United Republic of', 'code': 'TZ'},
          {'name': 'Thailand', 'code': 'TH'},
          {'name': 'Timor-Leste', 'code': 'TL'},
          {'name': 'Togo', 'code': 'TG'},
          {'name': 'Tokelau', 'code': 'TK'},
          {'name': 'Tonga', 'code': 'TO'},
          {'name': 'Trinidad and Tobago', 'code': 'TT'},
          {'name': 'Tunisia', 'code': 'TN'},
          {'name': 'Turkey', 'code': 'TR'},
          {'name': 'Turkmenistan', 'code': 'TM'},
          {'name': 'Turks and Caicos Islands', 'code': 'TC'},
          {'name': 'Tuvalu', 'code': 'TV'},
          {'name': 'Uganda', 'code': 'UG'},
          {'name': 'Ukraine', 'code': 'UA'},
          {'name': 'United Arab Emirates', 'code': 'AE'},
          {'name': 'United Kingdom', 'code': 'GB'},
          {'name': 'Uruguay', 'code': 'UY'},
          {'name': 'Uzbekistan', 'code': 'UZ'},
          {'name': 'Vanuatu', 'code': 'VU'},
          {'name': 'Venezuela', 'code': 'VE'},
          {'name': 'Viet Nam', 'code': 'VN'},
          {'name': 'Virgin Islands, British', 'code': 'VG'},
          {'name': 'Virgin Islands, U.S.', 'code': 'VI'},
          {'name': 'Wallis and Futuna', 'code': 'WF'},
          {'name': 'Western Sahara', 'code': 'EH'},
          {'name': 'Yemen', 'code': 'YE'},
          {'name': 'Zambia', 'code': 'ZM'},
          {'name': 'Zimbabwe', 'code': 'ZW'}
        ],
        quickbooks: {
          auth_url: '',
          on: 0
        },
      }
    },
    mounted() {
      Bus.$on('updateFormLocation', (payload) => {
        this.updateFormLocation(payload)
      })
    },
    mixins: [Phone],
    computed: {
      ...mapGetters([
        'getQuickBooksState'
      ]),
      ...mapState({
        quickBooks: state => state.features.quickbooks,
      })
    },
    created() {
      document.body.scrollTop = 0 // For Safari
      document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
    },
    methods: {

      addLicenses(licenses) {
        this.registerForm.licenses = []
        this.registerForm.licenses[this.registerForm.licenses.length] = licenses
      },

      initAutocomplete() {
        User.initAutocomplete('route')
      },

      registerButtonIsDisabled() {
        if (this.getMobileValidResponse[1] === 'mobile'
          || this.getMobileValidResponse[1] === 'virtual'
        ) {
          this.registerForm.disabled = false
        } else {
          this.registerForm.disabled = true
        }
      },
      updateFormLocation(location) {
        this.registerForm.addressLine1 = location.route
        this.registerForm.city = location.locality
        this.registerForm.state = location.administrative_area_level_1
        this.registerForm.zip = location.postal_code
      },
      goToCheckAccounting() {
        // have to check quickbooks is turned on
        // have to check if this page has been loaded from the check_accounting page
        // these buttons should only load this page was routed to directly and not from the check accounting page
        // these buttons have to display that they were clicked
        // these buttons need to load with the registerForm
      },
      doesNotUseQuickbooks() {
        this.usesQuickbooks = false
        this.showRegistration = true
      },
      verifyPassword() {
        if (this.registerForm.password !== this.registerForm.password_confirmation) {
          this.registerForm.errors.password_confirmation = 'The password fields must match'
        } else {
          this.registerForm.errors.password_confirmation = ''
        }
      },
      userSelected(userType) {

        console.log('userSelected have been clicked')

        if (userType === 'customer') {
          this.userTypeSelected = 'customer'
          this.usesQuickbooks = false
          this.registerForm.usertype = 'customer'
          this.showRegistration = true
          this.initAutocomplete()
        } else {
          this.userTypeSelected = 'contractor'
          this.usesQuickbooks = true
          this.getAuthURL()
          this.registerForm.usertype = 'contractor'
          this.doesNotUseQuickbooks()
          this.initAutocomplete()
        }
      },
      goToRegistration() {
        window.location = '/register'
      },
      getAuthURL() {
        // only get authUrl once
        if (this.quickbooks.auth_url === '') {
          axios.get('/quickbooks/getAuthUrl/getCompany').then(function(response) {
            this.quickbooks.auth_url = response.data
          }.bind(this))
        }
      },

      async register() {

        if (!this.registerForm.terms) {
          this.registerForm.errors.terms = true
        } else {
          this.registerForm.busy = true
          this.overlay = true
          if (
            this.getMobileValidResponse[1] === 'mobile'
            || this.getMobileValidResponse[1] === 'virtual'
          ) {
            if (this.registerForm.usertype === 'contractor') {
              try {
                let {data} = await axios.post('/registerContractor', this.registerForm)
                console.log(data)
                this.registerForm.busy = false
                this.$store.commit('setUser', data.user)
                Bus.$emit('updateUser')
                this.$router.push('/home')
                this.overlay = false
              } catch (error) {
                console.log('errors', error)
                let {errors} = error.response.data
                this.setErrors(errors)
                this.registerForm.busy = false
                this.overlay = false
              }
            } else {
              try {
                let {data} = await axios.post('/registerCustomer', this.registerForm)
                console.log(data)
                this.registerForm.busy = false
                this.$store.commit('setUser', data.user)
                Bus.$emit('updateUser')
                this.$router.push('/home')
                this.overlay = false
              } catch (error) {
                let {errors} = error.response.data
                this.setErrors(errors)
                this.registerForm.disabled = false
                this.overlay = false
              }
            }
          } else {
            this.registerForm.busy = true
          }
        }
      },
      setErrors(errors) {
        if (errors && errors.first_name !== undefined) {
          this.registerForm.errors.first_name = errors.first_name[0]
        } else {
          this.registerForm.errors.first_name = ''
        }

        if (errors && errors.last_name !== undefined) {
          this.registerForm.errors.last_name = errors.last_name[0]
        } else {
          this.registerForm.errors.last_name = ''
        }

        if (errors && errors.email !== undefined) {
          this.registerForm.errors.email = errors.email[0]
        } else {
          this.registerForm.errors.email = ''
        }

        if (errors && errors.password !== undefined) {
          this.registerForm.errors.password = errors.password[0]
        } else {
          this.registerForm.errors.password = ''
        }

        if (errors && errors.terms) {
          this.registerForm.errors.terms = errors.terms[0]
        } else {
          this.registerForm.errors.terms = false
        }

        if (errors && errors.companyName !== undefined) {
          this.registerForm.errors.companyName = errors.companyName[0]
        } else {
          this.registerForm.errors.companyName = ''
        }

        if (errors && errors.phoneNumber !== undefined) {
          this.registerForm.errors.phoneNumber = errors.phoneNumber[0]
        } else {
          this.registerForm.errors.phoneNumber = ''
        }

        if (errors && errors.addressLine1 !== undefined) {
          this.registerForm.errors.addressLine1 = errors.addressLine1[0]
        } else {
          this.registerForm.errors.addressLine1 = ''
        }

        if (errors && errors.city !== undefined) {
          this.registerForm.errors.city = errors.city[0]
        } else {
          this.registerForm.errors.city = ''
        }

        if (errors && errors.state !== undefined) {
          this.registerForm.errors.state = errors.state[0]
        } else {
          this.registerForm.errors.state = ''
        }

        if (errors && errors.zip !== undefined) {
          this.registerForm.errors.zip = errors.zip[0]
        } else {
          this.registerForm.errors.zip = ''
        }
      }
    },
  }
</script>

<style scoped>

    .row {
        margin-left: 0;
        margin-right: 0;
    }

    .jem-width {
        width: 100%
    }

    .register {
        width: 100%;
        padding: .5rem 0;
        margin-top: 1rem;
        border-width: .15rem;
        border-color: rgba(0, 0, 255, .59);
        color: rgba(0, 0, 255, .59);
    }

    .border {
        border-color: #e6e6e6;
        border-style: solid;
        border-width: 1px;
        border-radius: .25rem;
    }

    .paginate-links li.active a, .register {
        font-weight: 700;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .shadow {
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .1);
    }

    .help-block {
        display: block;
        margin-bottom: 10px;
        color: red;
        /*color: lighten(@text-color, 25%); // lighten the text some for contrast*/
    }

    .selected-button {
        border: solid thick red;
    }

    .formatError {
        border-color: red;
        background-color: yellow;
    }

    .formatErrorLabel {
        color: red;
    }

    label {
        font-weight: bolder;
    }

    input {
    }

</style>