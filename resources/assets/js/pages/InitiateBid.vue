<template>
    <div class="container">
        <div class="header text-center">
            Please Initiate a Bid With A Customer
        </div>
        <div class="customer-inputs">
            <div class="flex flex-col w-50 pady padx customer-input">
                <label for="customerName">Customer Name *</label>

                <input name="customer"
                       id="customerName"
                       type="text"
                       v-model="form.customerName"
                       v-on:keyup="autoComplete"
                       class="form-control"
                       required>
                <div class="customer-name-results"
                     v-if="results.length">
                    <button class="customer-name-result"
                            v-for="result in results"
                            v-bind:key="result.id"
                            :name="result.phone"
                            @click.prevent="fillFields(result)">
                        {{ result.name }}
                    </button>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="flex flex-col w-50 pady padx customer-input"
                 :class="{'has-error': form.errors.has('phone')}">
                <label for="phone">Mobile Phone *</label>
                <input class="form-control"
                       id="phone"
                       @keyup="filterPhone"
                       maxlength="10"
                       name="phone"
                       type="tel"
                       @blur="validateMobileNumber($event.target.value)"
                       v-model="form.phone">
                <div v-if="checkThatNumberIsMobile()" style="color: green">{{ this.getMobileValidResponse[1] }}</div>
                <div v-if="checkLandLineNumber()" style="color: red">{{ this.getMobileValidResponse[1] }}</div>
                <span class="help-block"
                      v-show="form.errors.has('phone')">
                  {{ form.errors.get('phone') }}
                </span>
            </div>

        </div>

        <div class="customer-inputs">
            <div class="flex flex-col w-50 pady padx customer-input"
                 :class="{'has-error': form.errors.has('jobName')}">
                <label for="jobName">Job Name</label>
                <input class="form-control"
                       id="jobName"
                       name="jobName"
                       type="text"
                       v-model="form.jobName">
                <span class="help-block"
                      v-show="form.errors.has('jobName')">
                  {{ form.errors.get('jobName') }}
                </span>
            </div>

            <button name="submit" id="submit" class="btn btn-default btn-primary"
                    @click.prevent="submit" :disabled="checkValidData()">
                  <span v-if="disabled.submit">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                Submit
            </button>
        </div>

        <pre>{{ this.getMobileValidResponse[1] }}</pre>
        <pre>{{ this.getMobileValidResponse[2] }}</pre>
        <pre>{{ this.form.customerName }}</pre>
        <pre>{{ this.form.phone }}</pre>

    </div>
</template>
<script>

  import {mapGetters, mapMutations, mapActions} from 'vuex'

  export default {
    data () {
      return {
        query: '',
        validMobileNumber: '',
        results: [],
        form: new SparkForm ({
          phone: '',
          customerName: '',
          jobName: ''
        }),
        disabled: {
          submit: false,
          validData: true
        },
        numberType: ''
      }
    },
    computed: {
      ...mapGetters ([
        'getMobileValidResponse'
      ])
    },
    methods: {
      ...mapMutations ([
        'setMobileResponse'
      ]),
      ...mapActions ([
        'checkMobileNumber',
      ]),
      submit () {
        console.log ('submit');
        GeneralContractor.initiateBid (this.form, this.disabled);
      },
      unformatNumber (number) {
        let unformattedNumber = '';
        for (let i = 0; i < number.length; i++) {
          if (!isNaN (parseInt (number[i]))) {
            unformattedNumber = unformattedNumber + number[i];
          }
        }
        let numberLength = unformattedNumber.length;
        if (numberLength < 10) {
          if (this.getMobileValidResponse[1] !== '') {
            this.$store.commit ('setTheMobileResponse', ['', '', '']);
          }
        }
        // debugger;
        return numberLength;
      },
      checkValidData () {
        // debugger
        let phone = this.unformatNumber (this.form.phone);
        if ((this.getMobileValidResponse[1] === 'mobile' ||
          this.getMobileValidResponse[2] === 'mobile') &&
          this.form.customerName !== '' && (phone === 10)
        ) {
          return false;
        } else {
          return true;
        }
      },
      validateMobileNumber (phone) {
        this.checkMobileNumber (phone);
      },
      checkThatNumberIsMobile () {
        if (this.getMobileValidResponse[1] === 'mobile' ||
          this.getMobileValidResponse[2] === 'mobile') {
          return true;
        } else {
          return false;
        }
      },
      checkLandLineNumber () {
        if (this.getMobileValidResponse[1] === 'landline' ||
          this.getMobileValidResponse[2] === 'landline') {
          return true;
        } else {
          return false;
        }
      },
      filterPhone () {
        this.form.phone = Format.phone (this.form.phone);
      },
      autoComplete () {
        this.results = [];
        if (this.form.customerName.length > 2) {
          axios.get ('/api/customer/search', {
            params: {
              query: this.form.customerName
            }
          }).then (response => {
            console.log (response.data);
            this.results = response.data;
          });
        }
      },
      fillFields (result) {
        this.form.email = result.email;
        this.form.phone = result.phone;
        this.form.customerName = result.name;
        this.results = [];
      }
    }
  }
</script>

<style scoped>
    .container {
        background-color: white;
        width: 90%;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        border-radius: 10px;
    }

    .input {
        height: 4rem;
        background-color: #80808014;
        width: 100%;
    }

    .borderR {
        border-radius: 10px;
    }

    .pady {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .padx {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .header {
        font-weight: bold;
        font-size: 3rem;
    }

    .flex {
        display: flex;
    }

    .flex-col {
        flex-direction: column;
    }

    .customer-inputs {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .w-50 {
    }

    button {
        height: 5rem;
        margin-top: auto;
        margin-bottom: 1rem;
        margin-right: 1rem;
    }

    @media (min-width: 500px) {
        .customer-inputs {
            flex-direction: row;
        }

        .customer-input {
            width: 50%;
        }
    }
</style>