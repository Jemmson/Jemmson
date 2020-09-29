<template>
  <!-- Modal -->

  <div
      v-if="isContractor()" class="h-100 modal modal-background-gray pl-4 pr-4 pt-10 show"
      :id="'sub-invite-modal_' + id" tabindex="-1"
      role="dialog"
      aria-labelledby="stripe-modal"
      aria-hidden="false"
  >
    <v-card>
      <v-overlay :value="overlay">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>

      <button type="button" class="w-break close mr-3 pt-2" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>

      <v-card-title class="flex flex-column pt-6 w-break"
                    v-if="initiateBidForSubForm.counter <= 0">Invite A Subcontractor - {{
        taskForSubInvite === undefined ? '' : jobTaskNameForSubInvite.toUpperCase()
        }}
      </v-card-title>

      <hr>

      <v-tabs
          fixed-tabs
          background-color="indigo"
          dark
      >
        <v-tab @change="switchTab('new')">
          New
        </v-tab>
        <v-tab @change="switchTab('existing')">
          Existing
        </v-tab>
      </v-tabs>

      <v-card-text v-if="newSub">
        <v-form v-model="valid">
          <v-container>
            <v-combobox
                v-model="selected"
                label="Company Name *"
                :search-input.sync="search"
                :items="comboResults"
                :value="initiateBidForSubForm.companyName"
            >
            </v-combobox>

            <v-text-field
                v-model="initiateBidForSubForm.firstName"
                required
                :rules="nameRules()"
                :counter="20"
                label="First Name *"
            >
            </v-text-field>

            <v-text-field
                v-model="initiateBidForSubForm.lastName"
                required
                :rules="nameRules()"
                :counter="20"
                label="Last Name *"
            >
            </v-text-field>

            <v-text-field
                v-model="initiateBidForSubForm.phone"
                required
                v-mask="phoneMask"
                :rules="phoneRules()"
                :counter="14"
                label="Mobile Phone Number *"
                :error="phoneError()"
                :error-messages="phoneErrorMessages()"
                :loading="loading"
                :disabled="loading"
                :messages="phoneMessages()"
            >
            </v-text-field>
            <!--              <v-spacer></v-spacer>-->
            <!--              <v-checkbox-->
            <!--                  id="isMobile"-->
            <!--                  ref="isMobile"-->
            <!--                  v-model="isMobile"-->
            <!--                  label="Is Mobile *"-->
            <!--              ></v-checkbox>-->

            <v-card-actions>
              <v-btn
                  id="submit"
                  class="w-full"
                  text
                  color="primary"
                  @click="sendSubInviteToBidOnTask" type="submit"
                  :disabled="enableSubmit()"
                  :loading="disabled.invite"
                  ref="submit">
                Submit
              </v-btn>
            </v-card-actions>

          </v-container>
        </v-form>
      </v-card-text>
      <v-card-text v-if="existingSub" class="flex flex-column">
        <div v-if="associatedSubs.length > 0">
          <!--          <v-checkbox v-for="sub in associatedSubs"-->
          <!--                      style="margin-top: 4px"-->
          <!--                      color="primary"-->
          <!--                      v-model="sub.selected"-->
          <!--                      :label="sub.companyName + ': ' + sub.firstName + ' ' + sub.lastName"-->
          <!--                      :key="sub.id"-->
          <!--          >-->
          <!--          </v-checkbox>-->

          <v-data-table
              v-model="selectedSubs"
              :headers="headers"
              :items="associatedSubs"
              :single-select="singleSelect"
              :item-key="associatedSubs.id"
              show-select
              class="elevation-1"
          >
            <!--            <template>-->
            <!--              <td class="v-data-table__mobile-row">-->
            <!--                <div class="v-data-table__checkbox v-simple-checkbox">-->
            <!--                  <div class="v-input&#45;&#45;selection-controls__ripple"></div>-->
            <!--                  <i aria-hidden="true" class="v-icon notranslate mdi mdi-checkbox-blank-outline theme&#45;&#45;light"></i>-->
            <!--                </div>-->
            <!--                <div class="v-data-table__mobile-row__cell">Sue the Tax Lady</div>-->
            <!--              </td>-->
            <!--            </template>-->
            <template
                v-slot:header.data-table-select="{}"
            >
              <thead>
              </thead>
            </template>

            <template
                v-slot:body="{ items }"
            >
              <tbody>
              <tr
                  v-for="item in associatedSubs"
                  :key="item.id"
              >
                <td class="flex align-center">
                  <v-simple-checkbox
                    color="green"
                    :value="item.selected"
                    @input="selectSub($event, item.id)"
                    ></v-simple-checkbox>
                  <v-spacer></v-spacer>
                  {{ item.companyName }}
                </td>
              </tr>
              </tbody>
            </template>


          </v-data-table>

          <v-btn
              text
              color="primary"
              @click="sendTaskToSubs()"
          >
            Invite Subs
          </v-btn>
        </div>
        <div v-else>
          All of your subs have been assigned to this task
        </div>
      </v-card-text>

    </v-card>
  </div>

</template>

<script>

import {mapMutations, mapActions} from 'vuex'
import Phone from '../../components/mixins/Phone'

export default {
  name: 'SubInviteModal',
  props: {
    bidPaymentType: String,
    jobTask: Object,
    jobTaskTask: Object,
    jobTaskName: String,
    id: Number,
    bidId: Number
  },
  data() {
    return {
      singleSelect: false,
      headers: [
        {
          align: 'start',
          sortable: false,
          mobile: false,
          options: {
            sortBy: [
              'companyName'
            ]
          },
          width: 0,
          value: 'companyName',
        },
      ],

      overlay: false,
      newSub: false,
      existingSub: true,
      associatedSubs: [],
      comboResults: [],
      valid: true,
      selected: null,
      selectedSubs: [],
      initiateBidForSubForm: new SparkForm({
        task_id: 0,
        email: '',
        phone: '',
        counter: 0,
        name: '',
        firstName: '',
        lastName: '',
        givenName: '',
        familyName: '',
        quickbooksId: '',
        companyName: '',
        paymentType: ''
      }),
      isMobile: false,
      paymentTypeCash: false,
      paymentTypeStripe: true,
      phoneFormatError: true,
      duplicateError: false,
      companyName: '',
      user: '',
      subInvited: false,
      phoneMask: '(###)-###-####',
      search: null,
      results: [],
      disabled: {
        accept: false,
        invite: false
      }
    }
  },
  mixins: [Phone],

  watch: {
    search(subVal) {
      if (subVal.length > 2) {
        this.autoComplete()
      }
    },

    phone(val) {
      if (val.length === 14) {
        this.validateMobileNumber(val)
      }
    },

    selected(val) {
      if (val && val !== null) {
        const filteredComboResult = this.getComboResult(val)
        if (filteredComboResult) {
          this.fillFields(filteredComboResult)
        } else {
          this.initiateBidForSubForm.companyName = val;
        }
      }
    }
  },

  methods: {
    ...mapMutations(['setMobileResponse']),
    ...mapActions(['checkMobileNumber']),

    nameRules() {
      return []
    },

    selectSub(selectedSub, subId){
      for (let i = 0; i < this.associatedSubs.length; i++) {
        if (this.associatedSubs[i].id === subId) {
          this.associatedSubs[i].selected = selectedSub;
        }
      }
    },

    addSubs () {
      let subArray = [];
      for (let i = 0; i < this.associatedSubs.length; i++) {
        if (this.associatedSubs[i].selected) {
          subArray.push(this.associatedSubs[i]);
        }
      }
      return subArray;
    },

    async sendTaskToSubs() {
      this.overlay = true;
      let invitedSubs = this.addSubs();
      const {data} = await axios.post('/task/inviteSubs', {
        subs: invitedSubs,
        task_id: this.jobTask.task.id,
        job_id: this.jobTask.job.id
      })

      if (data.error) {

      } else {
        User.emitChange('bidUpdated');
        Vue.toasted.success('Invite Sent!');
        Bus.$emit('clearAndCloseForm')
      }
      this.overlay = false;
    },

    switchTab(tab) {
      if (tab === 'new') {
        this.newSub = true
        this.existingSub = false
      } else {
        this.getAssociatedSubs();
      }
    },

    async getAssociatedSubs() {
      this.overlay = true
      const jobTaskId = this.jobTask.id
      const {data} = await axios.get('/task/getAssociatedSubs/' + jobTaskId)
      if (data.error) {

      } else {
        this.associatedSubs = data[0];
        this.newSub = false
        this.existingSub = true
      }
      this.overlay = false
    },

    phoneRules() {
      return []
    },

    phoneNumberMustBeMobile() {
      return !this.phoneError()
    },

    phoneError() {

      if (this.initiateBidForSubForm.phone.length > 13) {
        return !(this.getMobileValidResponse[1] === 'mobile'
            || this.getMobileValidResponse[1] === 'virtual')
      }
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

    async checkForDuplicateEmail(email) {
      this.duplicateError = false
      if (this.emailIsCorrectlyFormatted(email)) {
        try {
          const data = await axios.get('/email/duplicate/' + email)
          if (data.data.exists) {
            this.duplicateError = true
          }
        } catch (error) {
        }
      }
    },

    isContractor() {
      if (Spark.state.user.usertype === 'contractor') {
        return true
      }
      this.$router.push('/home')
    },

    emailIsCorrectlyFormatted(email) {
      const mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
      return email.match(mailformat) !== null
    },
    needsNewTask() {
      this.subInvited = false
      this.clearFields()
    },
    returnContractorsNotAlreadyAssignedToTask(subs) {

      let filteredSubs = []
      let assignedSubs = this.jobTask.bid_contractor_job_tasks

      // TODO: need to filter on something more unique than name comparison but it is a start
      for (let i = 0; i < subs.length; i++) {
        let subExists = false
        for (let j = 0; j < assignedSubs.length; j++) {
          if (subs[i].name == assignedSubs[j].contractor.name) {
            subExists = true
          }
        }
        if (!subExists) {
          filteredSubs.push(subs[i])
        }
      }

      return filteredSubs

    },
    filterPhone() {
      this.initiateBidForSubForm.phone = Format.phone(this.initiateBidForSubForm.phone)
    },
    async sendSubInviteToBidOnTask() {
      await GeneralContractor.sendSubInviteToBidOnTask(this.jobTask, this.initiateBidForSubForm, this.disabled, this.id)
    },
    clearAndCloseForm() {
      this.clearFields()
      this.companyName = ''
      $('#sub-invite-modal_' + this.id).modal('hide')
    },
    clearFields() {

      this.initiateBidForSubForm.id = ''
      this.initiateBidForSubForm.email = ''
      this.initiateBidForSubForm.phone = ''
      this.initiateBidForSubForm.name = ''
      this.initiateBidForSubForm.firstName = ''
      this.initiateBidForSubForm.lastName = ''
      this.initiateBidForSubForm.givenName = ''
      this.initiateBidForSubForm.familyName = ''
      this.initiateBidForSubForm.quickbooksId = ''
      this.initiateBidForSubForm.companyName = ''
      this.companyName = ''

    },

    fillFields(result) {

      if (result) {
        this.clearFields()

        this.initiateBidForSubForm.id = result.id
        this.initiateBidForSubForm.email = result.email
        this.initiateBidForSubForm.phone = result.phone
        this.initiateBidForSubForm.name = result.name
        if (result.first_name !== null && result.last_name !== null) {

          if (result.given_name) {
            this.initiateBidForSubForm.firstName = result.given_name
            this.initiateBidForSubForm.lastName = result.family_name
            this.initiateBidForSubForm.givenName = result.given_name
            this.initiateBidForSubForm.familyName = result.family_name
          } else if (result.first_name) {
            this.initiateBidForSubForm.firstName = result.first_name
            this.initiateBidForSubForm.lastName = result.last_name
            this.initiateBidForSubForm.givenName = result.first_name
            this.initiateBidForSubForm.familyName = result.last_name
          }

        }
        if (this.initiateBidForSubForm.quickbooksId !== null) {
          this.initiateBidForSubForm.quickbooksId = result.quickbooks_id
        }
        this.initiateBidForSubForm.companyName = result.contractor.company_name
        this.companyName = result.contractor.company_name
        this.initiateBidForSubForm.paymentType = this.bidPaymentType
        this.results = ''
        this.validateMobileNumber(this.initiateBidForSubForm.phone)
      }

    },

    paymentMethod(paymentType) {
      if (paymentType === 'cash') {
        this.initiateBidForSubForm.paymentType = 'cash'
        this.paymentTypeCash = true
        this.paymentTypeStripe = false
      } else {
        this.initiateBidForSubForm.paymentType = 'stripe'
        this.paymentTypeCash = false
        this.paymentTypeStripe = true
      }
    },

    async autoComplete() {
      this.results = []
      try {
        const data = await axios.get('/search/' + this.search)
        this.results = this.returnContractorsNotAlreadyAssignedToTask(data.data)
        this.comboResults = this.transformDataForComboBox(this.results)
      } catch (error) {
      }
    },

    transformDataForComboBox(data) {
      let customers = []
      for (let i = 0; i < data.length; i++) {
        customers.push(
            {
              text: data[i].contractor.company_name,
              value: data[i].id
            }
        )
      }
      return customers
    },

    enableSubmit() {

      return !(
          this.hasMobileNumber()
          && this.hasFirstName()
          && this.hasLastName()
          && this.hasCompanyName()
      );
    },

    hasMobileNumber() {
      return this.getMobileValidResponse[1] === 'mobile'
          || this.getMobileValidResponse[1] === 'virtual'
    },

    hasFirstName() {
      return this.initiateBidForSubForm.firstName !== ''
    },

    hasLastName() {
      return this.initiateBidForSubForm.lastName !== ''
    },

    hasCompanyName() {
      return this.initiateBidForSubForm.companyName !== ''
    }

  },
  computed: {

    phone() {
      return this.initiateBidForSubForm.phone
    },

    taskForSubInvite() {
      // debugger;
      if (this.jobTask) {
        return this.jobTaskTask
      }
    },
    jobTaskNameForSubInvite() {
      // debugger;
      if (this.jobTask) {
        return this.jobTaskName
      }
    },
    aResults() {
      if (this.results.length > 0) {
        return this.results
      } else {
        return []
      }
    },
    isGeneralContractor() {
      // General contractor is the one who created the bid
      return this.jobTask.task.contractor_id === this.user.id
    }
  },
  mounted: function () {
    this.$store.commit('setPhoneLoadingValue')
    this.user = Spark.state.user
    this.getAssociatedSubs();
    Bus.$on('clearAndCloseForm', () => {
      this.clearAndCloseForm()
    })
  }
}
</script>

<style scoped>
.styled {
  margin-top: 10rem;
  margin-bottom: 10rem;
}

.btn-format {
  background-color: beige;
  border-bottom: solid thin black;
  padding: .5rem;
}
</style>
