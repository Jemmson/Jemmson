<template>
  <!-- Modal -->
  <div class="modal" :id="'sub-invite-modal_' + id" tabindex="-1" role="dialog" aria-labelledby="stripe-modal"
    aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 v-if="initiateBidForSubForm.counter <= 0" class="modal-title">Invite A Subcontractor - {{
                        taskForSubInvite === undefined ? '' : jobTaskNameForSubInvite.toUpperCase() }}</h6>
          <h6 v-else>Sent Invite - {{ taskForSubInvite === undefined ? '' :
                        jobTaskNameForSubInvite.toUpperCase() }} -
            would you like to invite another sub to bid on this task?</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
          <form role="form">
            <div class="form-group">
              <label for="contractorName">Company Name *</label>
              <span class="validationError" v-show="initiateBidForSubForm.errors.has('name')" ref="name">Please Enter A
                Name
              </span>
              <input type="text" class="form-control" id="contractorName" name="contractorName" autocomplete="off"
                v-model="initiateBidForSubForm.companyName" placeholder="Name"
                v-bind:class="{ 'text-danger': initiateBidForSubForm.errors.has('name')}" autofocus required
                v-on:keyup="autoComplete">
              <div class="panel-footer" v-if="aResults.length > 0">
                <div class="flex flex-col" ref="buttons">
                  <button class="flex-1 m-2 btn-format" v-for="(result, index) in aResults" v-bind:key="result.id"
                    :name="result.phone" @click.prevent="fillFields(result)">
                    <span :id="'result' + index">
                      {{ result.name }} - {{ result.contractor.company_name }}
                    </span>
                  </button>
                </div>
              </div>

              <div class="flex flex-col">
                <div class="flex-1">
                  <label for="firstName">First Name</label>
                  <input type="text" class="form-control" id="firstName" name="contractorName"
                    v-model="initiateBidForSubForm.firstName">
                </div>
                <div class="flex-1">
                  <label for="lastName">Last Name</label>
                  <input type="text" class="form-control" id="lastName" name="contractorName"
                    v-model="initiateBidForSubForm.lastName">
                </div>
              </div>

            </div>
            <div class="form-group" :class="{'has-error': initiateBidForSubForm.errors.has('phone')}">
              <label for="phone">Mobile Phone *</label>
              <input type="tel" placeholder="Phone Number" class="form-control" id="phone" name="phone" maxlength="10"
                required v-model="initiateBidForSubForm.phone" @blur="validateMobileNumber($event.target.value)"
                @keyup="filterPhone">
              <span class="help-block" v-show="initiateBidForSubForm.errors.has('phone')">
                {{ initiateBidForSubForm.errors.get('phone') }}
              </span>
              <div v-if="getMobileValidResponse.length > 0">
                <div v-if="getMobileValidResponse[1] === 'mobile'" class="mt-2">
                  <div style="color: green">{{ getMobileValidResponse[1] }}</div>
                </div>
                <div class="mt-2" v-else style="color: red">{{ getMobileValidResponse[1] }}</div>
              </div>
            </div>
            <div class="form-group" :class="{'has-error': initiateBidForSubForm.errors.has('email')}">
              <label for="email">Email</label>
              <input type="email" placeholder="Email" class="form-control" id="email" name="email"
                v-model="initiateBidForSubForm.email">
              <span class="help-block" v-show="initiateBidForSubForm.errors.has('email')">
                {{ initiateBidForSubForm.errors.get('email') }}
              </span>
            </div>

            <div class="flex flex-col" id="preferredPaymentMethod">
              <label class="text-center mb-3">Preferred Payment Method</label>
              <div class="flex justify-between">
                <div class="flex">
                  <label for="cash" class="mr-6">Cash</label>
                  <input type="checkbox" :checked="paymentTypeCash" @click="paymentMethod('cash')" id="cash">
                </div>
                <div class="flex">
                  <label for="stripe" class="mr-6">Credit Card</label>
                  <input type="checkbox" :checked="paymentTypeStripe" @click="paymentMethod('stripe')" id="stripe">
                </div>
              </div>
            </div>
          </form>
          <!-- /end col-md6ss -->
        </div>
        <div class="modal-footer">
            <button @click="sendSubInviteToBidOnTask" class="btn btn-success" type="submit"
              :disabled="getMobileValidResponse[1] !== 'mobile'" ref="submit">
              <span v-if="disabled.invite">
                <i class="fa fa-btn fa-spinner fa-spin"></i>
              </span>
              Submit
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import { mapGetters, mapMutations, mapActions } from 'vuex'

  export default {
    props: {
      jobTask: Object,
      jobTaskTask: Object,
      jobTaskName: String,
      id: Number,
      bidId: Number
    },
    data() {
      return {
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
          paymentType: 'stripe'
        }),
        paymentTypeCash: false,
        paymentTypeStripe: true,
        phoneFormatError: true,
        companyName: '',
        user: '',
        results: [],
        disabled: {
          accept: false,
          invite: false
        }
      }
    },
    methods: {
      ...mapMutations(['setMobileResponse']),
      ...mapActions(['checkMobileNumber']),
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
      sendSubInviteToBidOnTask() {
        GeneralContractor.sendSubInviteToBidOnTask(this.jobTask, this.initiateBidForSubForm, this.disabled, this.id)
        this.companyName = ''
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

        this.clearFields()

        this.initiateBidForSubForm.id = result.id
        this.initiateBidForSubForm.email = result.email
        this.initiateBidForSubForm.phone = result.phone
        this.initiateBidForSubForm.name = result.name
        if (result.first_name !== null && result.last_name !== null) {
          this.initiateBidForSubForm.firstName = result.given_name
          this.initiateBidForSubForm.lastName = result.family_name
          this.initiateBidForSubForm.givenName = result.given_name
          this.initiateBidForSubForm.familyName = result.family_name
        }
        if (this.initiateBidForSubForm.quickbooksId !== null) {
          this.initiateBidForSubForm.quickbooksId = result.quickbooks_id
        }
        this.initiateBidForSubForm.companyName = result.contractor.company_name
        this.companyName = result.contractor.company_name
        this.results = ''
        this.validateMobileNumber(this.initiateBidForSubForm.phone)

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
      autoComplete() {
        this.results = []
        let query = this.initiateBidForSubForm.companyName;
        // let query = this.initiateBidForSubForm.name;
        // let query = this.initiateBidForSubForm.name;
        console.log('checking for names')
        if (query.length > 2) {
          axios.get('/search/' + query).then(function(response) {
            console.log('autocomplete', response.data)
            this.results = this.returnContractorsNotAlreadyAssignedToTask(response.data)
          }.bind(this))
        }
      },
      validateMobileNumber(phone) {
        // this.phoneFormatError = false
        if (this.unformatNumber(this.initiateBidForSubForm.phone) === 10) {
          this.checkMobileNumber(phone)
          this.checkValidData()
        } else {
          this.phoneFormatError = true
        }
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
      checkValidData() {
        let phoneLength = this.unformatNumber(this.initiateBidForSubForm.phone)
        if (
          (this.getMobileValidResponse[1] === 'mobile' ||
            this.getMobileValidResponse[2] === 'mobile') &&
          this.initiateBidForSubForm.name !== '' &&
          phoneLength === 10
        ) {
          this.phoneFormatError = false
        } else {
          this.phoneFormatError = true
        }
      }
    },
    computed: {
      ...mapGetters(['getMobileValidResponse']),
      taskForSubInvite() {
        // debugger;
        return this.jobTaskTask
      },
      jobTaskNameForSubInvite() {
        // debugger;
        return this.jobTaskName
      },
      aResults() {
        // if (this.results.length > 0) {
        //     return this.results.filter((sub) => {
        //         for (let bid of this.jobTask.bid_contractor_job_tasks) {
        //             // if invited to bid do not show in dropdown list
        //             if (bid.contractor_id !== sub.id && sub.id !== this.user.id) {
        //                 return true;
        //             }
        //         }
        //         // do not show self in dropdown list
        //         // return sub.id !== this.user.id;
        //     });
        // }
        // return [];
        if (this.results.length > 0) {
          // return this.results.filter((sub) => {
          // }
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
    mounted: function() {
      this.user = Spark.state.user
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
