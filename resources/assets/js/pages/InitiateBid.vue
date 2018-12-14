<template>
    <div class="flex flex-row">
        <card header="true">
            <template slot="card-header">
                <h2 for="header">
                    Initiate A Bid With A Customer
                </h2>
            </template>

            <div class="form-group">
                <label for="customerName">Customer Name *</label>

                <input name="customer" 
                       id="customerName" 
                       dusk="customerName"
                       data-dependency="phone"
                       type="text" 
                       v-model="form.customerName"
                       v-on:keyup="autoComplete"
                       class="form-control" required>
                <div class="flex flex-col" v-if="results.length">
                    <button class="flex-1 m-2 btn-format" v-for="result in results" v-bind:key="result.id"
                            :name="result.phone" @click.prevent="fillFields(result)">
                        {{ result.name }}
                    </button>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="form-group" :class="{'has-error': form.errors.has('phone')}">
                <div class="flex justify-between"> 
                  <label for="phone">Mobile Phone *</label>
                  <div v-show="phoneFormatError" class="formatErrorLabel">The phone number must be 10 numbers</div>
                </div>
                <input class="form-control" 
                       :class="{'formatError': phoneFormatError}" 
                       id="phone" @keyup="filterPhone" 
                       maxlength="10" 
                       data-dependency="jobName"
                       name="phone" 
                       dusk="phone"
                       type="tel" @blur="validateMobileNumber($event.target.value)"
                       v-model="form.phone">
                <div dusk="networkType" class="mt-2" id="#mobileNetworktype" v-show="checkThatNumberIsMobile()" style="color: green">
                    {{ networkType.originalCarrier }}
                </div>
                <div dusk="networkType" class="mt-2" v-show="checkLandLineNumber()" style="color: red">{{ networkType.originalCarrier
                    }}
                </div>
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

            <div class="">
                <button name="submit" id="submit" dusk="submitBid" class="btn btn-green btn-full"
                        @click.prevent="submit" :disabled="checkValidData()">
          <span v-if="disabled.submit">
            <i class="fa fa-btn fa-spinner fa-spin"></i>
          </span>
                    Submit
                </button>
            </div>
        </card>
        <feedback></feedback>
    </div>
</template>

<script>
import Card from "../components/shared/Card";
import { mapGetters, mapMutations, mapActions } from "vuex";

export default {
  components: {
    Card
  },
  data() {
    return {
      query: "",
      phoneFormatError: false,
      validMobileNumber: "",
      results: [],
      form: new SparkForm({
        phone: "",
        customerName: "",
        jobName: ""
      }),
      networkType: {
        success: "",
        originalCarrier: "",
        currentCarrier: "",
        exists: ""
      },
      disabled: {
        submit: false,
        validData: true
      },
      numberType: ""
    };
  },
  computed: {
    ...mapGetters(["getMobileValidResponse"])
  },
  methods: {
    ...mapMutations(["setMobileResponse"]),
    ...mapActions(["checkMobileNumber"]),
    submit() {
      console.log("submit");
      GeneralContractor.initiateBid(this.form, this.disabled);
    },
    unformatNumber(number) {
      let unformattedNumber = "";
      for (let i = 0; i < number.length; i++) {
        if (!isNaN(parseInt(number[i]))) {
          unformattedNumber = unformattedNumber + number[i];
        }
      }
      let numberLength = unformattedNumber.length;
      if (numberLength < 10) {
        if (this.getMobileValidResponse[1] !== "") {
          this.$store.commit("setTheMobileResponse", ["", "", ""]);
        }
      }
      // debugger;
      return numberLength;
    },
    checkValidData() {
      // debugger
      let phoneLength = this.unformatNumber(this.form.phone);
      if (
        (this.getMobileValidResponse[1] === "mobile" ||
          this.getMobileValidResponse[2] === "mobile") &&
        this.form.customerName !== "" &&
        phoneLength === 10
      ) {
        return false;
      } else {
        return true;
      }
    },
    validateMobileNumber(phone) {
      this.phoneFormatError = false;
      if (this.unformatNumber(this.form.phone) === 10) {
        this.checkMobileNumber(phone);
      } else {
        this.phoneFormatError = true;
      }
    },
    validResponse() {
      this.networkType.success = this.getMobileValidResponse[0];
      this.networkType.originalCarrier = this.getMobileValidResponse[1];
      this.networkType.currentCarrier = this.getMobileValidResponse[2];
      this.networkType.exists = this.getMobileValidResponse[3];
    },
    checkThatNumberIsMobile() {
      // debugger;
      if (
        this.getMobileValidResponse[1] === "mobile" ||
        this.getMobileValidResponse[2] === "mobile"
      ) {
        this.validResponse();
        return true;
      } else {
        return false;
      }
    },
    checkLandLineNumber() {
      if (
        this.getMobileValidResponse[1] === "landline" ||
        this.getMobileValidResponse[2] === "landline"
      ) {
        this.validResponse();
        return true;
      } else {
        return false;
      }
    },
    filterPhone() {
      this.form.phone = Format.phone(this.form.phone);
    },
    autoComplete() {
      this.results = [];
      if (this.form.customerName.length > 2) {
        axios
          .get("/api/customer/search", {
            params: {
              query: this.form.customerName
            }
          })
          .then(response => {
            console.log(response.data);
            this.results = response.data;
          });
      }
    },
    fillFields(result) {
      this.form.email = result.email;
      this.form.phone = result.phone;
      this.form.customerName = result.name;
      this.results = [];
      this.validateMobileNumber(result.phone);
    }
  }
};
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
