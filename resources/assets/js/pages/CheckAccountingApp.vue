<template>
    <div class="bg-white">
        <div>
            <h1 class="text-center">WHO ARE YOU?</h1>
            <div class="flex">

                <v-btn
                        class="w-full"
                        color="primary"
                        ref="customer"
                >Customer</v-btn>


                <v-btn
                        class="w-full"
                        color="primary"
                        @click="getAuthURL()"
                        ref="contractor"
                >Contractor</v-btn>

            </div>
        </div>
        <div v-show="isContractor">
            <h1 class="text-center uppercase">Do You Use Accounting Software?</h1>
            <div class="flex">
                <a
                        :href="quickbooks.auth_url"
                        ref="quickbooks"
                        class="flex-1"
                >
                    <img
                            alt="qbo/docs/develop/authentication-and-authorization/C2QB_auth.png"
                            src="https://static.developer.intuit.com/images/C2QB_auth.png">
                </a>

                <v-btn
                        class="w-full"
                        color="primary"
                        ref="dont_use"
                        @click="goToRegistration()"
                >Dont Use</v-btn>
            </div>
        </div>
    </div>
</template>

<script>

  import { mapGetters, mapState } from 'vuex'

  export default {
    name: 'CheckAccountingApp',
    data() {
      return {
        isContractor: false,
        quickbooks: {
          auth_url: '',
          on: 0
        },
      }
    },
    mounted() {
      // TODO: Feature Not Working
      axios.get('/loadFeatures').then((response) => {
          for (let i = 0; i < response.data.length; i++) {
            if (response.data[i].name === 'quickbooks') {
              if (response.data[i].on === 1) {
                this.$router.push('check_accounting')
              } else {
                window.location = '/register';
              }
            }
          }
        }
      )
    },
    computed: {
      ...mapGetters([
        'getQuickBooksState'
      ]),
      ...mapState({
        quickBooks: state => state.features.quickbooks,
      }),
    },
    methods: {
      goToRegistration(){
        window.location = '/register';
      },
      getAuthURL() {
        // only get authUrl once
        this.isContractor = !this.isContractor
        if (this.quickbooks.auth_url === '') {
          axios.get('/quickbooks/getAuthUrl/getCompany').then(function(response) {
            this.quickbooks.auth_url = response.data
          }.bind(this))
        }

      }
    }
  }
</script>

<style>

    .bg-color-red {
        background-color: red;
    }

    .bg-color-blue {
        background-color: blue;
    }

    .bg-color-green {
        background-color: green;
    }

</style>
