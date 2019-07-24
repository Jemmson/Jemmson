<template>
    <div class="bg-white">
        <div>
            <h1 class="text-center">WHO ARE YOU?</h1>
            <div class="flex">
                <button class="btn default w-full m-2 bg-color-red"
                        ref="customer">Customer
                </button>
                <button class="btn default w-full m-2 bg-color-green" @click="getAuthURL()"
                        ref="contractor">Contractor
                </button>
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
                <button class="flex-1 btn default w-full m-2 bg-color-green"
                        ref="dont_use" @click="goToRegistration()">Dont Use
                </button>
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
          console.log(JSON.stringify(response.data))
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
