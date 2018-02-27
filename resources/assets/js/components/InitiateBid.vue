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

              <label for="job-name">Job Name
                <input name="jobName" type="text" id="job-name" class="form-control" v-model="form.jobName">
              </label>

              <label for="email">Email
                <input name="email" type="email" id="email" class="form-control" v-model="form.email">
              </label>
              <label for="phone">Phone
                <input name="phone" type="text" id="phone" class="form-control" maxlength="10" v-model="form.phone" @keyup="filterPhone">
              </label>
              <button class="btn btn-default btn-primary" @click.prevent="submit" :disabled="disabled.submit">
                <span v-if="disabled.submit">
                  <i class="fa fa-btn fa-spinner fa-spin"></i>
                </span>
                Submit
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <hr>
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
        console.log (result);
        console.log (result.email);
        console.log (result.phone);
        console.log (result.name);
        this.form.email = result.email;
        this.form.phone = result.phone;
        this.form.customerName = result.name;
      }
    }
  }
</script>