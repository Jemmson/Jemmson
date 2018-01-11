<template>
    <div>
        <h1 class="text-center">Please Initiate a Bid With A Customer</h1>
        <label for="customerName">Customer Name *</label>

        <input
                name="customerName"
                id="customerName"
                :value="name"
                :placeholder="name"
                type="text"
                v-model="query"
                v-on:keyup="autoComplete"
                class="form-control" required>
        <div class="panel-footer" v-if="results.length">
            <ul class="list-group">
                <button class="list-group-item" v-for="result in results" :name="result.phone"
                        @click="fillFields(result)">
                    {{ result.name }}
                </button>
            </ul>
        </div>

        <label for="job-name">Job Name
            <input name="jobName" type="text" id="job-name" class="form-control">
        </label>

        <label for="email">Email
            <input name="email" type="email" id="email" v-model="email" class="form-control">
        </label>
        <label for="phone">Phone
            <input name="phone" type="text" id="phone" v-model="phone" class="form-control">
        </label>
        <br>
        <br>
    </div>
</template>
<script>
  import axios from 'axios'

  export default {
    data () {
      return {
        query: '',
        results: [],
        email: '',
        phone: '',
        name: ''
      }
    },
    methods: {
      autoComplete () {
        this.results = [];
        if (this.query.length > 2) {
          axios.get ('/api/search', {
            params: {
              query: this.query
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
        this.email = result.email;
        this.phone = result.phone;
        this.name = result.name;
      }
    }
  }
</script>