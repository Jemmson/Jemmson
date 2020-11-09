import { mapGetters, mapState, mapMutations, mapActions } from 'vuex'
import Format from '../../classes/Format'

export default {
  mounted() {
    this.$store.commit('setPhoneLoadingValue')
    if (this.user && this.user.phone != null) {
      this.validateMobileNumber(this.user.phone)
    }
  },
  data() {
    return {
      phoneFormatError: false,
      validMobileNumber: '',
      networkType: {
        success: '',
        originalCarrier: '',
        currentCarrier: '',
        exists: ''
      }
    }
  },
  computed: {
    ...mapGetters(['getMobileValidResponse']),
    ...mapState({
      loading: state => state.busy,
      busy: state => state.busy
    }),
  },
  methods: {
    ...mapMutations(['setMobileResponse']),
    ...mapActions(['checkMobileNumber']),
    validateMobileNumber(phone) {
      if (this.initiateBidForSubForm) {
        if (this.unformatNumber(this.initiateBidForSubForm.phone) === 10) {
          this.checkMobileNumber(phone)
          this.checkValidData()
        } else {
          this.phoneFormatError = true
        }
      } else if (this.registerForm) {
        if (this.unformatNumber(this.registerForm.phoneNumber) === 10) {
          this.checkMobileNumber(phone)
          this.checkValidData()
        } else {
          this.registerForm.errors.phoneNumber = ''
        }
      } else if (this.form) {
        if (this.form.phone) {
          if (this.unformatNumber(this.form.phone) === 10) {
            this.checkMobileNumber(phone)
            this.checkValidData()
          }
        } else if (this.form.phone_number) {
          if (this.unformatNumber(this.form.phone_number) === 10) {
            this.checkMobileNumber(phone)
            this.checkValidData()
          }
        }
      }

      localStorage.setItem('mobile', phone);

    },
    unformatNumber(number) {
      let unformattedNumber = ''
      if (number !== '') {
        if (number) {
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
          return numberLength
        }
      } else {
        return 0
      }
    },
    getPhoneLength() {

      // subinvitemodal
      if (this.initiateBidForSubForm) {
        return this.unformatNumber(this.initiateBidForSubForm.phone)
      }
      // register.vue
      if (this.registerForm) {
        return this.unformatNumber(this.registerForm.phoneNumber)
      }

      // initiatebid.vue
      if (this.form) {
        return this.unformatNumber(this.form.phone)
      }
    },

    checkValidData() {

      let phoneLength = this.getPhoneLength()

      if (
        (
          this.getMobileValidResponse[1] === 'mobile' ||
          this.getMobileValidResponse[2] === 'mobile')
        && phoneLength === 10
      ) {
        if (this.initiateBidForSubForm) {
          if (this.initiateBidForSubForm.name !== '') {
            this.phoneFormatError = false
          } else {
            this.phoneFormatError = true
          }
        } else if (this.form) {
          if (this.form.first_name !== '' && this.form.last_name !== '') {
            this.phoneFormatError = false
          } else {
            this.phoneFormatError = true
            this.phoneError = true
            this.phoneErrorMessages = []
          }
        }
      }
    },
    checkThatNumberIsMobile() {
      if (
        (this.getMobileValidResponse[1] === 'mobile' ||
          this.getMobileValidResponse[2] === 'mobile')
        || (this.getMobileValidResponse[1] === 'virtual' ||
        this.getMobileValidResponse[2] === 'virtual')
      ) {
        this.validResponse()
        return true
      } else {
        return false
      }
    },
    checkLandLineNumber() {
      if (
        this.getMobileValidResponse[1] === 'landline' ||
        this.getMobileValidResponse[2] === 'landline'
      ) {
        this.validResponse()
        return true
      } else {
        return false
      }
    },
    filterPhone() {
      if (this.getPhoneLength() > 9) {
        if (this.form) {
          this.form.phone = Format.phone(this.form.phone)
        }
        if (this.registerForm) {
          this.registerForm.phoneNumber = Format.phone(this.registerForm.phoneNumber)
        }
      }
    },
    validResponse() {
      this.networkType.success = this.getMobileValidResponse[0]
      this.networkType.originalCarrier = this.getMobileValidResponse[1]
      this.networkType.currentCarrier = this.getMobileValidResponse[2]
      this.networkType.exists = this.getMobileValidResponse[3]
    }
  }
}