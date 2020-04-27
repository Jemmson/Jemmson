<template>
    <!-- Modal -->

    <div
            v-if="isContractor()" class="h-100 modal modal-background-gray pl-4 pr-4 pt-10 show" :id="'sub-invite-modal_' + id" tabindex="-1"
            role="dialog"
            aria-labelledby="stripe-modal"
            aria-hidden="false"
    >
        <v-card >

            <button type="button" class="close mr-3 pt-2" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <v-card-title class="flex flex-column pt-6"
                          v-if="initiateBidForSubForm.counter <= 0">Invite A Subcontractor - {{
                taskForSubInvite === undefined ? '' : jobTaskNameForSubInvite.toUpperCase() }}
            </v-card-title>

            <hr>

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
                comboResults: [],
                valid: true,
                selected: null,
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

            phone(val){
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
                        console.log(error)
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
                    console.log('data', JSON.stringify(data.data))
                    this.results = this.returnContractorsNotAlreadyAssignedToTask(data.data)
                    this.comboResults = this.transformDataForComboBox(this.results)
                } catch (error) {
                    console.log(error)
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

            // autoComplete() {
            //   this.results = []
            //   let query = this.initiateBidForSubForm.companyName
            //   console.log('checking for names')
            //   if (query.length > 2) {
            //     axios.get('/search/' + query).then(function(response) {
            //       console.log('autocomplete', response.data)
            //       this.results = this.returnContractorsNotAlreadyAssignedToTask(response.data)
            //     }.bind(this))
            //   }
            // },

            enableSubmit() {

                return !(
                    this.hasMobileNumber()
                    && this.hasFirstName()
                    && this.hasLastName()
                    && this.hasCompanyName()
                );
            },

            hasMobileNumber(){
                return this.getMobileValidResponse[1] === 'mobile'
                    || this.getMobileValidResponse[1] === 'virtual'
            },

            hasFirstName() {
                return this.initiateBidForSubForm.firstName !== ''
            },

            hasLastName() {
                return this.initiateBidForSubForm.lastName !== ''
            },

            hasCompanyName(){
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
