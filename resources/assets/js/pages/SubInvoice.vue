<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="card card-1">
                    <div class="panel-body">
                        <h3>Invoice Page</h3>
                    </div>
                </div>
            </div>
            <div v-if="subInvoice !== null">
                <div class="col-md-12">
                    <div class="card card-1">
                        <div class="panel-body">
                            <section class="col-xs-12 col-md-6">
                                <h3 for="company_name" v-if="isContractor">{{ user.contractor.company_name }}</h3>
                                <h3 for="company_name" v-if="!isContractor">{{ user.name }}</h3>
                                <address v-if="subInvoice.location !== null">
                                    <br> {{ subInvoice.location.address_line_1 }}
                                    <br> {{ subInvoice.location.city }}, {{ subInvoice.location.state }} {{
                                    subInvoice.location.zip }}
                                </address>
                            </section>
                            <section class="col-xs-12 col-md-6">
                                <label>
                                    Job Name:
                                </label>
                                <p>
                                    {{ subInvoice.task.name }}
                                </p>

                                <label>
                                    Date Completed:
                                </label>
                                <p>
                                    {{ subInvoice.updated_at }}
                                </p>
                            </section>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card card-1">
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Task Name</th>
                                    <th scope="col">QTY</th>
                                    <th scope="col">Task Price</th>

                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ subInvoice.task.name }}</td>
                                    <td>{{ subInvoice.qty }}</td>
                                    <td>${{ subInvoice.sub_final_price }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <feedback></feedback>
    </div>
</template>

<script>
  import Feedback from '../components/shared/Feedback'

  export default {
    components: {
      Feedback
    },
    props: {
      user: Object,
    },
    data() {
      return {
        subInvoice: null
      }
    },
    computed: {
      isContractor() {
        return User.isContractor()
      },
    },
    methods: {},
    mounted: function() {
      const id = this.$route.params.id
      axios.get('/sub/invoice/' + id).then((data) => {
        this.subInvoice = data.data
      })
    }
  }
</script>