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
                       class="borderR input"
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
                <label for="phone">Phone *</label>
                <input class="borderR input"
                       id="phone"
                       @keyup="filterPhone"
                       maxlength="10"
                       name="phone"
                       type="tel"
                       v-model="form.phone">
                <span class="help-block"
                      v-show="form.errors.has('phone')">
                  {{ form.errors.get('phone') }}
                </span>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button name="submit" id="submit" class="btn btn-default btn-primary"
                                @click.prevent="submit" :disabled="disabled.submit">
                  <span v-if="disabled.submit">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    data () {
      return {
        query: '',
        results: [],
        form: new SparkForm ({
          phone: '',
          customerName: ''
        }),
        disabled: {
          submit: false
        }
      }
    },
    methods: {
      submit () {
        console.log ('submit');
        GeneralContractor.initiateBid (this.form, this.disabled);
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

    @media (min-width: 500px) {
        .customer-inputs {
            flex-direction: row;
        }

        .customer-input {
            width: 50%;
        }
    }
</style>