<template>
  <div class="container">
    <!-- Application Dashboard -->
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">Initiate Bid</div>

          <div class="panel-body">
            <form method="post">
              <h1 class="text-center">Please Initiate a Bid With A Customer</h1>

              <div class="form-group col-md-6" :class="{'has-error': form.errors.has('customerName')}">
                <label for="customerName">Customer Name *</label>

                <input name="customerName" id="customerName" type="text" v-model="form.customerName" v-on:keyup="autoComplete" class="form-control"
                  required>
                <div class="panel-footer" v-if="results.length">
                  <ul class="list-group">
                    <button class="list-group-item" v-for="result in results" v-bind:key="result.id" :name="result.phone" @click.prevent="fillFields(result)">
                      {{ result.name }}
                    </button>
                  </ul>
                </div>
              </div>

              <div class="form-group col-md-6" :class="{'has-error': form.errors.has('jobName')}">
                <label for="job-name">Job Name</label>
                <input name="jobName" type="text" id="job-name" class="form-control" v-model="form.jobName">
                <span class="help-block" v-show="form.errors.has('jobName')">
                  {{ form.errors.get('jobName') }}
                </span>
              </div>

              <div class="form-group col-md-6" :class="{'has-error': form.errors.has('email')}">
                <label for="email">Email</label>
                <input name="email" type="email" id="email" class="form-control" v-model="form.email">
                <span class="help-block" v-show="form.errors.has('email')">
                  {{ form.errors.get('email') }}
                </span>
              </div>

              <div class="form-group col-md-6" :class="{'has-error': form.errors.has('phone')}">
                <label for="phone">Phone</label>
                <input name="phone" type="tel" id="phone" class="form-control" maxlength="10" v-model="form.phone" @keyup="filterPhone">
                <span class="help-block" v-show="form.errors.has('phone')">
                  {{ form.errors.get('phone') }}
                </span>
              </div>

              <div class="form-group col-md-12">
                <button  name="submit" class="btn btn-default btn-primary" @click.prevent="submit" :disabled="disabled.submit">
                  <span v-if="disabled.submit">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                  Submit
                </button>
              </div>
            </form>
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
        form: new SparkForm({
          email: '',
          phone: '',
          customerName: '',
          jobName: ''
        }),
        disabled: {
          submit: false
        }
      }
    },
    methods: {
      submit() {
        console.log('submit');
        GeneralContractor.initiateBid(this.form, this.disabled);
      },
      filterPhone () {
        this.form.phone = Format.phone(this.form.phone);
      },
      autoComplete () {
        this.results = [];
        if (this.form.customerName.length > 2) {
          axios.get ('/api/search', {
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