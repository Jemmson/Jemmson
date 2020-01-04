<template>
    <div class="container" v-if="isContractor()">
        <icon-header icon="jobs" mainHeader="Add New Job" subHeader="Initiate a new job with a customer.">
        </icon-header>
        <card>
            <div class="form-group">
                <div class="flex flex-col" v-if="results.length">
                    <v-btn
                            class="w-full"
                            color="primary"
                            v-for="result in results" v-bind:key="result.id"
                            :name="result.phone"
                            @click.prevent="fillFields(result)">
                        {{ showCustomerName(result) }}
                    </v-btn>
                </div>
            </div>

            <div class="form-group">
                <label for="firstName">First Name *</label>

                <input type="text"
                       name="firstName"
                       ref="firstName"
                       id="firstName"
                       class="form-control"
                       v-on:keyup="autoComplete"
                       required
                       v-model="form.firstName"
                >
            </div>


            <div class="form-group">
                <label for="lastName">Last Name *</label>

                <input type="text"
                       name="lastName"
                       ref="lastName"
                       id="lastName"
                       class="form-control"
                       v-on:keyup="autoComplete"
                       required
                       v-model="form.lastName"
                >
            </div>


            <!-- Phone Number -->
            <div class="form-group" :class="{'has-error': form.errors.has('phone')}">
                <div class="flex justify-between">
                    <label for="phone">Mobile Phone *</label>
                    <div v-show="phoneFormatError" class="formatErrorLabel">The phone number must be 10 numbers</div>
                </div>
                <input class="form-control"
                       :class="{'formatError': phoneFormatError}"
                       id="phone"
                       @keyup="$event.target.value > 9 ? filterPhone : false"
                       maxlength="10"
                       data-dependency="jobName"
                       :disabled="loading"
                       name="phone"
                       dusk="phone"
                       type="tel"
                       @blur="validateMobileNumber($event.target.value)"
                       v-model="form.phone">

                <div v-if="getMobileValidResponse.length > 0">
                    <div v-if="getMobileValidResponse[1] === 'mobile'
                    || getMobileValidResponse[1] === 'virtual'" class="mt-2">
                        <div style="color: green">{{ getMobileValidResponse[1] }}</div>
                    </div>
                    <div class="mt-2" v-else style="color: red">{{ getMobileValidResponse[1] }}</div>
                </div>

                <v-progress-linear
                        :active="loading"
                        :indeterminate="loading"
                        absolute
                        bottom
                        color="deep-purple accent-4"
                ></v-progress-linear>

                <span class="help-block" v-show="form.errors.has('phone')">
                  {{ form.errors.get('phone') }}
                </span>
            </div>


            <div class="form-group" :class="{'has-error': form.errors.has('jobName')}">
                <label for="jobName">Job Name</label>
                <input class="form-control" id="jobName" name="jobName" dusk="jobName" type="text"
                       v-model="form.jobName">
                <span class="help-block" v-show="form.errors.has('jobName')">
          {{ form.errors.get('jobName') }}
        </span>
            </div>

            <div class="form-group pt-4 mb-0">
                <v-btn
                        class="w-full"
                        color="primary"
                        name="submit" id="submit" dusk="submitBid"
                        @click.prevent="submit"
                        :disabled="checkValidData()">
          <span v-if="disabled.submit">
            <i class="fa fa-btn fa-spinner fa-spin"></i>
          </span>
                    Submit
                </v-btn>
            </div>
        </card>
        <br>
        <feedback
                page="initiateBid"
        ></feedback>
    </div>
</template>

<script>
  import Card from '../components/shared/Card'
  import Feedback from '../components/shared/Feedback'
  import IconHeader from '../components/shared/IconHeader'
  import Phone from '../components/mixins/Phone'

  export default {
    components: {
      Card,
      Feedback,
      IconHeader
    },
    data() {
      return {
        query: '',
        results: [],
        form: new SparkForm({
          customerName: '',
          email: '',
          firstName: '',
          lastName: '',
          jobName: '',
          phone: '',
          quickbooks_id: '',
        }),
        disabled: {
          submit: false,
          validData: true,
          searchingMobileNumber: true
        },
        numberType: ''
      }
    },
    mixins: [ Phone ],
    created() {
      document.body.scrollTop = 0; // For Safari
      document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    },
    methods: {
      submit() {
        console.log('submit')
        GeneralContractor.initiateBid(this.form, this.disabled)
      },

      imclicked() {
        console.log('Im clicked')
      },

      isContractor(){
        if (Spark.state.user.usertype === 'contractor') {
          return true
        }
        this.$router.push('/home')
      },
      createName() {
        if (this.form.firstName === "" && this.form.lastName !== "") {
          this.form.customerName = this.form.lastName;
        } else if (this.form.firstName !== "" && this.form.lastName === "") {
          this.form.customerName = this.form.firstName;
        } else if (this.form.firstName === "" && this.form.lastName === "") {
          this.form.customerName = "";
        } else {
          this.form.customerName = this.form.firstName + " " + this.form.lastName;
        }
      },
      autoComplete() {
        // this.results = []
        this.createName()
        if (this.form.customerName.length > 2) {
          axios.get('/customer/search', {
            params: {
              query: this.form.customerName
            }
          })
            .then(response => {
              console.log(response.data)
              console.log(JSON.stringify(response.data))
              this.results = response.data
            })
        }
      },
      showCustomerName(result) {
        if (result.given_name !== undefined) {
          return result.fully_qualified_name
        } else {
          return result.first_name + " " + result.last_name
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
      }
    },
    mounted() {
      this.$store.commit('setCurrentPage', this.$router.history.current.path)
    },
  }
</script>

<style lang="less" scoped>
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
</style>
