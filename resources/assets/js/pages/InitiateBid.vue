<template>
  <div class="container" v-if="isContractor()">
    <v-card>
      <div class="flex justify-content-between">
        <v-card-title class="w-break">
          <img :src="'/img/jobs.svg'" alt="" srcset="" class="pr-1 float-left">
          <div class="ml-2">Create A Job</div>
        </v-card-title>
        <v-icon
            color="primary"
            @click="showModal('createJob')"
            class="mr-5">mdi-information
        </v-icon>
      </div>
      <hr>
      <v-form v-model="valid">
        <v-container>

          <v-combobox
              id="customerName"
              ref="customerName"
              data-cy="customerName"
              v-model="selected"
              label="Customer Name"
              :search-input.sync="search"
              :items="comboResults"
              :value="form.firstName"
          >
          </v-combobox>

          <!--                    <v-text-field-->
          <!--                            id="firstName"-->
          <!--                            v-model="form.firstName"-->
          <!--                            required-->
          <!--                            :rules="nameRules()"-->
          <!--                            :counter="20"-->
          <!--                            label="First Name *"-->
          <!--                    >-->
          <!--                    </v-text-field>-->

          <!--                    <v-text-field-->
          <!--                            id="lastName"-->
          <!--                            v-model="form.lastName"-->
          <!--                            @change="lastName()"-->
          <!--                            required-->
          <!--                            :rules="nameRules()"-->
          <!--                            :counter="20"-->
          <!--                            label="Last Name *"-->
          <!--                    >-->
          <!--                    </v-text-field>-->

          <div class="flex align-center">
            <v-text-field
                class="1rem"
                style="margin-right: 1rem;"
                id="phone"
                ref="phone"
                data-cy="phone"
                v-model="form.phone"
                required
                v-mask="phoneMask"
                :rules="phoneRules()"
                @change="validateMobileNumber($event)"
                :counter="14"
                label="Mobile Phone Number *"
                :error="phoneError()"
                :error-messages="phoneErrorMessages()"
                :loading="loading"
                :disabled="loading"
                :messages="phoneMessages()"
            >
            </v-text-field>

            <!--                        <input ref="isMobile"-->
            <!--                               v-model="form.isMobile"-->
            <!--                               aria-checked="false"-->
            <!--                               id="isMobile"-->
            <!--                               role="checkbox"-->
            <!--                               type="checkbox"-->
            <!--                               value="">-->

            <v-checkbox
                id="isMobile"
                ref="isMobile"
                v-model="form.isMobile"
                label="Is Mobile *"
            ></v-checkbox>

          </div>

          <v-text-field
              id="jobName"
              data-cy="jobName"
              label="Job Name"
              v-model="form.jobName"
          >
          </v-text-field>

          <v-row class="flex-col payment-section">
            <div class="align-baseline flex justify-center">
              <h5 data-cy="paymentTypeLabel" class="text-center">Select Payment Type For Job</h5>
            </div>
            <v-radio-group
                v-model="form.paymentType"
                row
                style="margin: 1rem auto 0 auto;"
            >
              <v-radio
                  label="Cash"
                  value="cash"
              ></v-radio>
              <v-spacer></v-spacer>
              <v-radio
                  :disabled="needsStripeForCreditCardPayments()"
                  label="Credit Card"
                  value="creditCard"
              ></v-radio>
            </v-radio-group>
            <!--                        <v-checkbox-->
            <!--                                v-model="form.paymentTypeDefault"-->
            <!--                                label="Set As Default"-->
            <!--                                :value=true-->
            <!--                                hide-details-->
            <!--                                style="margin: 0 auto 0 auto;"-->
            <!--                        ></v-checkbox>-->

            <v-btn
                @click="connectWithStripe($route.path)"
                class="margins-1rem"
                color="aliceblue"
                elevation="2"
                text
                single-line
                sticky
                style="background-color: cornflowerblue; font-size: 9pt; color: white"
                v-show="needsStripeForCreditCardPayments()"
            >
              Click To Accept Credit Cards
            </v-btn>

          </v-row>

          <v-card-actions>
            <v-btn
                ref="submit"
                class="w-full"
                color="primary"
                name="submit" id="submit" dusk="submitBid"
                @click.prevent="submit"
                text
                :loading="disabled.submit"
                :class="dataMustBeValid() ? '': 'border-color'"
                :disabled="dataMustBeValid()"
            >
              Submit
            </v-btn>
          </v-card-actions>

          <br>
          <feedback
              page="initiateBid"
          ></feedback>
        </v-container>
      </v-form>
    </v-card>

    <info-modal-generic
        :text="modalText.paymentTypeText"
        title="Payment Types"
        :open-dialog="modal.paymentTypeInfoDialog"
        modal="paymentType"
        @close-modal="closeModal($event)">
    </info-modal-generic>

    <info-modal-generic
        :text="modalText.createJobText"
        title="Create A New Job"
        :open-dialog="modal.createJobDialog"
        modal="createJob"
        @close-modal="closeModal($event)">
    </info-modal-generic>

  </div>
</template>

<script>
import Card from '../components/shared/Card'
import Feedback from '../components/shared/Feedback'
import IconHeader from '../components/shared/IconHeader'
import Phone from '../components/mixins/Phone'
import StripeMixin from '../components/mixins/StripeMixin'
import InfoModalGeneric from '../components/documentation/InfoModalGeneric'

export default {
  components: {
    Card,
    Feedback,
    IconHeader,
    InfoModalGeneric
  },
  data() {
    return {
      latestJobNumber: null,
      modal: {
        paymentTypeInfoDialog: false,
        createJobDialog: false,
      },
      modalText: {
        paymentTypeText: `Every job is either a cash job or a credit card job.
                        To handle credit payments you will need to setup with Stripe. Stripe handles all of our
                        credit card processing. You will have an account with stripe and you will collect payments there.
                        If you have a stripe account already then you will connect to your existing stripe account.
                    `,
        createJobText: `Create A New Job Here`
      },
      radioGroup: 1,
      selected: null,
      query: '',
      valid: false,
      results: [],
      search: null,
      phoneMask: '(###)-###-####',
      form: new SparkForm({
        customerName: '',
        email: '',
        firstName: '',
        lastName: '',
        jobName: '',
        phone: '',
        quickbooks_id: '',
        isMobile: false,
        id: '',
        taxRate: 0,
        paymentType: 'cash',
        paymentTypeDefault: null
      }),
      disabled: {
        submit: false,
        validData: true,
        searchingMobileNumber: true
      },
      numberType: '',
      firstName: '',
      comboResults: []
    }
  },
  mixins: [Phone, StripeMixin],
  created() {
    document.body.scrollTop = 0 // For Safari
    document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
  },

  watch: {
    search(val) {
      if (val && val.length > 2) {
        this.autoComplete()
      }
    },
    enteredPhoneNumber(val) {
      if (val && val.length === 14) {
        this.validateMobileNumber(val);
      } else if (val && val.length === 10 && this.allNumbers(val)) {
        this.formatPhone(val);
      }
    },
    selected(val) {
      if (val && val !== null) {
        const filteredComboResult = this.getComboResult(val)
        if (filteredComboResult) {
          this.setFormData(filteredComboResult)
          this.splitName(val);
          this.setJobNameFromResult(filteredComboResult)
        } else {
          this.splitName(val);
          this.setJobName()
        }
      } else {
        this.form.customerName = this.selected;
      }
      this.storeInfoInLocalStorage();
    }
  },

  computed: {
    enteredPhoneNumber: {
      get: function () {
        return this.form.phone
      },
      set: function (newValue) {
        this.form.phone = newValue
      }
    }
  },

  methods: {

    allNumbers(val) {
      if (typeof val !== 'string') {
        val = val + ''
      }
      return this.countNumbers(val);
    },

    countNumbers(val) {
      let onlyNumbers = true
      for (let i = 0; i < val.length; i++) {
        if (isNaN(val[i])) {
          onlyNumbers = false
        }
      }
      return onlyNumbers
    },

    storeInfoInLocalStorage() {
      this.form.customerName = this.removeExtraSpaces(this.form.customerName)
      localStorage.setItem('customerName', this.form.customerName)
      localStorage.setItem('isMobile', this.form.isMobile)
      localStorage.setItem('mobile', this.form.phone)
      localStorage.setItem('jobName', this.form.jobName)
    },

    removeExtraSpaces(value) {
      if (typeof value === 'string') {
        let valArray = value.split(' ');
        let noSpaceArray = []
        for (let i = 0; i < valArray.length; i++) {
          if (valArray[i] !== "") {
            noSpaceArray.push(valArray[i])
          }
        }

        let newString = '';
        for (let i = 0; i < noSpaceArray.length; i++) {
          if (i === noSpaceArray.length - 1) {
            newString = newString + noSpaceArray[i];
          } else {
            newString = newString + noSpaceArray[i] + ' ';
          }
        }

        return newString;
      } else {
        return ''
      }

    },

    retrieveCustomerInfoFromLocalStorage() {
      this.selected = localStorage.getItem('customerName');
      this.form.customerName = localStorage.getItem('customerName');
      if (localStorage.getItem('isMobile') && localStorage.getItem('isMobile') === 'false') {
        this.form.isMobile = false;
      } else {
        this.form.isMobile = true;
      }
      if (localStorage.getItem('mobile') && localStorage.getItem('mobile').length < 10) {
        this.form.phone = ''
        localStorage.setItem('mobile', '');
      } else {
        this.form.phone = this.formatPhone(localStorage.getItem('mobile'));
      }
      this.form.jobName = localStorage.getItem('jobName');
    },

    formatPhone(phone) {

      if (typeof phone !== 'string') {
        phone = phone + ''
      }

      if (phone) {
        let totalDigits = 0;
        let totalDigitString = '(';
        for (let i = 0; i < phone.length; i++) {
          if (!isNaN(phone[i])) {
            totalDigits++;
            if (
                totalDigits < 4
                || (totalDigits > 4 && totalDigits < 7)
                || totalDigits > 7
            ) {
              totalDigitString = totalDigitString + phone[i]
            } else if (4 === totalDigits) {
              totalDigitString = totalDigitString + ")-" + phone[i]
            } else if (totalDigits === 7) {
              totalDigitString = totalDigitString + "-" + phone[i]
            }
          }

        }

        return totalDigitString
      }
    },

    showModal(modal) {
      if (modal === 'paymentType') {
        this.modal.paymentTypeInfoDialog = true;
      } else if (modal === 'createJob') {
        this.modal.createJobDialog = true;
      }
    },
    closeModal(modal) {
      if (modal === 'paymentType') {
        this.modal.paymentTypeInfoDialog = false;
      } else if (modal === 'createJob') {
        this.modal.createJobDialog = false;
      }
    },

    lastName() {
      this.selected = this.form.firstName + ' ' + this.form.lastName;
    },

    setJobName() {
      let n = moment();

      if (this.customerNameLength() > 1) {
        this.form.jobName = n.year() + '-' + 100 + '-' + this.form.lastName + '-' + this.getFirstNameForJobName().trimLeft()
      } else {
        this.form.jobName = n.year() + '-' + 100 + '-' + this.form.customerName;
      }

    },

    customerNameLength() {
      let nameArray = null;
      if (typeof this.form.customerName === 'object' && typeof this.form.customerName !== null) {
        nameArray = this.form.customerName.text.split(' ');
      } else {
        nameArray = this.form.customerName.split(' ');
      }
      return nameArray.length;
    },

    setJobNameFromResult(result) {
      let n = moment();
      if (this.customerNameLength() > 1) {
        this.form.jobName = n.year() + '-' + this.latestJobNumber + '-' + this.form.lastName + '-' + this.getFirstNameForJobName().trimLeft()
      } else {
        this.form.jobName = n.year() + '-' + this.latestJobNumber + '-' + this.form.customerName;
      }
    },

    splitName(val) {

      let nameArray = null;
      if (typeof val === 'object' && typeof val !== null) {
        nameArray = val.text.split(' ');
      } else {
        nameArray = val.split(' ');
      }

      nameArray = this.removeEmptiesInArray(nameArray);

      if (nameArray.length > 1) {
        this.form.lastName = nameArray[nameArray.length - 1]
        let firstName = this.getFirstName(nameArray);
        this.form.firstName = firstName.trimLeft();
      }
      this.form.customerName = val;
    },

    removeEmptiesInArray(array) {
      let subArray = [];
      for (let i = 0; i < array.length; i++) {
        if (array[i] !== '') {
          subArray.push(array[i]);
        }
      }
      return subArray;
    },

    getLastName(nameArray) {
      let lastName = ''
      for (let i = 1; i < nameArray.length; i++) {
        lastName = lastName + ' ' + nameArray[i]
      }
      return lastName
    },

    getFirstName(nameArray) {
      let firstName = ''
      for (let i = 0; i < nameArray.length - 1; i++) {
        firstName = firstName + ' ' + nameArray[i]
      }
      return firstName
    },

    getLastNameForJobName() {

      let lastName = ''
      let lastNameArray = this.form.lastName.split(' ')
      for (let i = 0; i < lastNameArray.length; i++) {

        if (i === 0) {
          lastName = lastName + lastNameArray[i]
        } else {
          lastName = lastName + '-' + lastNameArray[i]
        }

      }
      return lastName.trimLeft()
    },

    getFirstNameForJobName() {

      let firstName = ''
      let firstNameArray = this.form.firstName.split(' ')
      for (let i = 0; i < firstNameArray.length; i++) {

        if (i === 0) {
          firstName = firstName + firstNameArray[i]
        } else {
          firstName = firstName + '-' + firstNameArray[i]
        }

      }
      return firstName.trimLeft()
    },

    dataMustBeValid() {

      // return (
      //         this.form.customerName !== ''
      //     || this.form.customerName.text !== ''
      //     )
      // && this.form.phone !== ''
      // && this.form.phone !== null
      // && this.form.phone.length > 13
      // && (this.getMobileValidResponse[1] === 'mobile'
      //     || this.getMobileValidResponse[1] === 'virtual')
      // && this.form.isMobile

      return !(this.allRequiredFieldsHaveAValue() && this.phoneNumberIsMobile() && this.form.isMobile)
    },

    allRequiredFieldsHaveAValue() {
      return this.form.customerName !== '' && this.form.phone !== '';
    },

    phoneNumberIsMobile() {
      return this.phoneNumberIsValid() && this.isMobile()
    },

    phoneError() {
      if (this.form && this.form.phone && this.form.phone.length > 13) {
        return !(this.getMobileValidResponse[1] === 'mobile'
            || this.getMobileValidResponse[1] === 'virtual')
      }
    },

    isMobile() {
      if (this.phoneNumberIsValid()) {
        return this.getMobileValidResponse[1] === 'mobile'
            || this.getMobileValidResponse[1] === 'virtual'
      }
      return false
    },

    phoneNumberIsValid() {
      return this.phoneDataVariableExists()
          && this.phoneNumberHasProperLength()
    },

    phoneDataVariableExists() {
      return this.form.phone
    },

    phoneNumberHasProperLength() {
      return this.phoneDataVariableExists() && this.form.phone.length > 13
    },

    phoneErrorMessages() {
      if (this.phoneError() && this.getMobileValidResponse[1]) {
        return this.getMobileValidResponse[1]
      }
    },

    phoneMessages() {
      if (!this.phoneError()) {
        return this.getMobileValidResponse[1]
      }
    },

    getComboResult(selected) {
      for (let i = 0; i < this.results.length; i++) {
        if (selected.value === this.results[i].id) {
          return this.results[i]
        }
      }
    },

    setFormData(result) {
      if (result) {
        this.form.phone = this.formatPhone(result.phone)
        this.form.email = result.email
        this.form.isMobile = true
        this.form.taxRate = result.tax_rate
        this.form.quickbooks_id = result.quickbooks_id
        this.form.customerName = result.name
        this.form.id = result.id
        this.validateMobileNumber(result.phone)
        this.storeInfoInLocalStorage();
      }
    },

    getCustomerNames() {
      return [{}]
    },

    nameRules() {
      return []
    },

    phoneRules() {

    },

    submit() {
      if (this.setCustomerName()) {
        console.log('submit')
        GeneralContractor.initiateBid(this.form, this.disabled)
      }
    },

    setCustomerName() {
      if (this.form.customerName !== '') {
        let nameArray = this.form.customerName.split(' ');
        this.form.firstName = nameArray[0];
        if (nameArray.length > 1) {
          let lname = '';
          for (let i = 1; i < nameArray.length; i++) {
            lname = lname + ' ' + nameArray[i];
          }
          this.form.lastName = lname;
        }
        return true;
      }
      return false;
    },

    imclicked() {
      console.log('Im clicked')
    },

    isContractor() {
      if (Spark.state.user.usertype === 'contractor') {
        return true
      }
      this.$router.push('/home')
    },

    createName() {
      if (this.form.firstName === '' && this.form.lastName !== '') {
        this.form.customerName = this.form.lastName
      } else if (this.form.firstName !== '' && this.form.lastName === '') {
        this.form.customerName = this.form.firstName
      } else if (this.form.firstName === '' && this.form.lastName === '') {
        this.form.customerName = ''
      } else {
        this.form.customerName = this.form.firstName + ' ' + this.form.lastName
      }
    },

    async autoComplete() {
      this.createName()
      try {
        const data = await axios.get('/customer/search', {
          params: {
            query: this.form.firstName
          }
        })
        this.results = data.data
        this.comboResults = this.transformDataForComboBox(data.data)
        console.log(' this.comboResults ', this.comboResults);
        console.log(' this.comboResults ', JSON.stringify(this.comboResults));
      } catch (error) {
        console.log(error)
      }
    },

    transformDataForComboBox(data) {
      let customers = []
      for (let i = 0; i < data.length; i++) {
        customers.push(
            {
              text: data[i].name,
              value: data[i].id
            }
        )
      }
      return customers
    },

    showCustomerName(result) {
      if (result.given_name !== undefined) {
        return result.fully_qualified_name
      } else {
        return result.first_name + ' ' + result.last_name
      }
    },

    fillFields(result) {
      if (result.given_name !== undefined) {
        this.form.email = result.primary_email_addr
        this.form.customerName = result.fully_qualified_name
        this.form.firstName = result.given_name
        this.form.lastName = result.family_name
        this.form.quickbooks_id = result.quickbooks_id
        if (result.primary_phone !== null) {
          this.form.phone = result.primary_phone
          this.checkMobileNumber(result.primary_phone)
          this.validateMobileNumber(result.primary_phone)
          this.filterPhone()
        }
      } else {
        this.form.email = result.email
        this.form.customerName = result.name
        this.form.firstName = result.first_name
        this.form.lastName = result.last_name
        if (result.quickbooks_id) {
          this.form.quickbooks_id = result.quickbooks_id
        }
        if (result.phone !== null) {
          this.form.phone = result.phone
          this.checkMobileNumber(result.phone)
          this.validateMobileNumber(result.phone)
          this.filterPhone()
        }
      }
      // this.results = []
    },

    async getLatestJobNumber() {
      const {data} = await axios.get('/job/getLatestJobNumber')

      if (data.error) {

      } else {
        this.latestJobNumber = data.latest + 101;
      }
    }

  },
  mounted() {
    this.$store.commit('setCurrentPage', this.$router.history.current.path)
    this.form.paymentType = Spark.state.user.contractor.payment_type;
    this.retrieveCustomerInfoFromLocalStorage();
    this.getLatestJobNumber();
  },
}
</script>

<style lang="less" scoped>

.border-color {
  border: #0d47a1 solid thin;
}

.formatError {
  border-color: red;
  background-color: yellow;
}

.formatErrorLabel {
  color: red;
}

.btn-format {
  background-color: beige;
  border-bottom: solid thin black;
  padding: .5rem;
}

.payment-section {
  margin: .25rem .25rem 3rem .25rem;
  border: solid thin black;
  padding: 1rem 0 1rem 0;
}

</style>
