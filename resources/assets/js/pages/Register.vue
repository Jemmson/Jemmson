<template>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <card>
                    <h3 class="text-center">Who Are You?</h3>
                    <div class="row">
                        <div class="col-6">
                            <button
                                    class="btn btn-lg btn-primary jem-width"
                                    ref="customerButton"
                                    style="margin-left: -.65rem;"
                                    :class="userTypeSelected === 'customer' ? 'selected-button' : ''"
                                    v-on:click="userSelected('customer')">Customer
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-lg btn-primary jem-width"
                                    ref="contractorButton"
                                    :class="userTypeSelected === 'contractor' ? 'selected-button' : ''"
                                    v-on:click="userSelected('contractor')">Contractor
                            </button>
                        </div>
                    </div>


                    <h4 data-v-24a44010=""
                        class="text-center"
                        v-if="usesQuickbooks"
                        style="margin-top: 1.5rem">Do You Use Quickbooks?</h4>

                    <div class="row" v-if="usesQuickbooks">
                        <div class="col-9">
                            <a
                                    :href="quickbooks.auth_url"
                                    ref="quickbooks"
                            >
                                <img
                                        alt="qbo/docs/develop/authentication-and-authorization/C2QB_auth.png"
                                        src="https://static.developer.intuit.com/images/C2QB_auth.png"
                                        style="height: 100%; width: 100%"
                                        class="w-full"
                                >
                            </a>
                        </div>

                        <button class="btn btn-lg btn-primary col-3"
                                v-on:click="doesNotUseQuickbooks()"><span class="uppercase">no</span>
                        </button>
                    </div>

                    <div v-if="showRegistration">

                        <hr style="margin-top: 3rem">

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('first_name')">-->
                            <label for="firstName" class="m-auto pt-1 pt-2" style="">First Name *</label>
                            <input
                                    id="firstName"
                                    type="text"
                                    class="form-control m-auto"
                                    v-model="registerForm.first_name">
                            <!--                <span class="help-block" v-show="registerForm.errors.has('first_name')"></span>-->
                            <span class="help-block" v-show="registerForm.errors.first_name !== ''">{{registerForm.errors.first_name}}</span>
                        </div>

                        <div class="row">
                            <div class="flex flex-col mt-2 mb-2">
                                <!--                                 :class="{'has-error': registerForm.errors.has('last_name')">-->
                                <label for="lastName" class="m-auto pt-3 pt-2">Last Name *</label>
                                <input id="lastName"
                                       type="text" class="form-control m-auto"
                                       v-model="registerForm.last_name">
                                <span class="help-block" v-show="registerForm.errors.last_name !== ''">{{registerForm.errors.last_name}}</span>
                            </div>
                        </div>

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="email" class="m-auto pt-3 pt-2">E-Mail Address *</label>
                            <input id="email"
                                   type="text" class="form-control m-auto"
                                   v-model="registerForm.email">
                            <span class="help-block" v-show="registerForm.errors.email !== ''">{{registerForm.errors.email}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="password"
                                   class="m-auto pt-3 pt-2">Password *</label>
                            <input id="password"
                                   type="password" class="form-control m-auto"
                                   v-model="registerForm.password">
                            <span class="help-block" v-show="registerForm.errors.password !== ''">{{registerForm.errors.password}}</span>
                            <!--                <span class="help-block" v-show="registerForm.errors.has('email')"></span>-->
                        </div>

                        <div class="row">
                            <!--            <div class="flex flex-col mt-2 mb-2" :class="{'has-error': registerForm.errors.has('email')">-->
                            <label for="password_confirmation" class="m-auto pt-3 pt-2">Confirm
                                Password *</label>
                            <input id="password_confirmation"
                                   @keyup="verifyPassword()"
                                   type="password" class="form-control m-auto"
                                   v-model="registerForm.password_confirmation">
                            <span class="help-block" v-show="registerForm.errors.password_confirmation !== ''">{{registerForm.errors.password_confirmation}}</span>
                        </div>


                        <!-- Terms And Conditions -->

                        <!--            <div class="input-section" :class="{'has-error': registerForm.errors.has('terms')}">-->

                        <hr style="margin-top: 3rem">

                        <div class="row pt-3 pt-2 m-auto">
                            <input
                                    type="checkbox"

                                    class="mr-2"
                                    name="terms"
                                    style="align-self: center;"
                                    v-model="registerForm.terms">I Accept The
                            <a href="/terms" target="_blank">Terms Of Service *</a>
                            <span class="help-block" v-show="registerForm.errors.terms !== ''">{{registerForm.errors.terms}}</span>
                        </div>

                        <button id="register" name=register
                                class="register border shadow uppercase"
                                @click.prevent="register"
                                :disabled="registerForm.busy">
                            <span v-if="registerForm.busy">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>Registering
                            </span>
                            <span v-else>
                                <i class="fa fa-btn fa-check-circle mr-2"></i>Register
                            </span>
                        </button>


                        <div style="height: 10rem; width: 100%">

                        </div>


                    </div>

                </card>
            </div>
        </div>
    </div>

</template>

<script>
  import { mapGetters, mapState } from 'vuex'

  export default {
    name: 'Register',
    data() {
      return {
        registerForm: {
          first_name: '',
          last_name: '',
          email: '',
          password: '',
          password_confirmation: '',
          terms: '',
          errors: {
            first_name: '',
            last_name: '',
            email: '',
            password: '',
            password_confirmation: '',
            terms: '',
          },
          usertype: '',
          busy: false
        },
        userTypeSelected: '',
        usesQuickbooks: false,
        showRegistration: false,
        quickbooks: {
          auth_url: '',
          on: 0
        },
      }
    },
    mounted() {
      axios.get('/loadFeatures').then((response) => {
          console.log(JSON.stringify(response.data))
          for (let i = 0; i < response.data.length; i++) {
            if (response.data[i].name === 'quickbooks') {
              if (response.data[i].on === 1) {
                this.$router.push('check_accounting')
              } else {
                window.location = '/register'
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
      goToCheckAccounting() {
        // have to check quickbooks is turned on
        // have to check if this page has been loaded from the check_accounting page
        // these buttons should only load this page was routed to directly and not from the check accounting page
        // these buttons have to display that they were clicked
        // these buttons need to load with the registerForm
      },
      doesNotUseQuickbooks() {
        this.usesQuickbooks = false
        this.showRegistration = true
      },
      verifyPassword(){
        if (this.registerForm.password !== this.registerForm.password_confirmation) {
          this.registerForm.errors.password_confirmation = 'The password fields must match';
        } else {
          this.registerForm.errors.password_confirmation = '';
        }
      },
      checkValidData() {
        if (
          this.registerForm.first_name !== '' &&
          this.registerForm.last_name !== '' &&
          this.registerForm.email !== '' &&
          this.registerForm.password !== '' &&
          this.registerForm.password_confirmation !== '' &&
          this.registerForm.terms === true
        ) {
          this.registerForm.busy = true
        } else {
          this.registerForm.busy = false
        }
      },
      userSelected(userType) {

        console.log('userSelected have been clicked')

        if (userType === 'customer') {
          this.userTypeSelected = 'customer'
          this.usesQuickbooks = false
          this.registerForm.usertype = 'customer'
          this.showRegistration = true
        } else {
          this.userTypeSelected = 'contractor'
          this.usesQuickbooks = true
          this.getAuthURL()
          this.registerForm.usertype = 'contractor'
        }
      },
      goToRegistration() {
        window.location = '/register'
      },
      getAuthURL() {
        // only get authUrl once
        if (this.quickbooks.auth_url === '') {
          axios.get('/quickbooks/getAuthUrl/getCompany').then(function(response) {
            this.quickbooks.auth_url = response.data
          }.bind(this))
        }
      },
      register() {
        if (this.registerForm.usertype === 'contractor') {
          axios.post('/registerContractor', this.registerForm)
            .then(function(response) {
              console.log(response.data)

              this.$store.commit('setUser', response.data.user);

              window.location = response.data.redirect
            }.bind(this))
            .catch(function(error) {

              if (error.response.data.errors.first_name !== undefined) {
                this.registerForm.errors.first_name = error.response.data.errors.first_name[0]
              } else {
                this.registerForm.errors.first_name = ''
              }

              if (error.response.data.errors.last_name !== undefined) {
                this.registerForm.errors.last_name = error.response.data.errors.last_name[0]
              } else {
                this.registerForm.errors.last_name = ''
              }

              if (error.response.data.errors.email !== undefined) {
                this.registerForm.errors.email = error.response.data.errors.email[0]
              } else {
                this.registerForm.errors.email = ''
              }

              if (error.response.data.errors.password !== undefined) {
                this.registerForm.errors.password = error.response.data.errors.password[0]
              } else {
                this.registerForm.errors.password = ''
              }

              if (error.response.data.errors.terms !== undefined) {
                this.registerForm.errors.terms = error.response.data.errors.terms[0]
              } else {
                this.registerForm.errors.terms = ''
              }

              console.log(error)
            }.bind(this))
        } else {
          axios.post('/registerCustomer', this.registerForm)
            .then(function(response) {
              console.log(response)
              window.location = response.redirect
            })
            .catch(function(error) {

              if (error.response.data.errors.first_name !== undefined) {
                this.registerForm.errors.first_name = error.response.data.errors.first_name[0]
              } else {
                this.registerForm.errors.first_name = ''
              }

              if (error.response.data.errors.last_name !== undefined) {
                this.registerForm.errors.last_name = error.response.data.errors.last_name[0]
              } else {
                this.registerForm.errors.last_name = ''
              }

              if (error.response.data.errors.email !== undefined) {
                this.registerForm.errors.email = error.response.data.errors.email[0]
              } else {
                this.registerForm.errors.email = ''
              }

              if (error.response.data.errors.password !== undefined) {
                this.registerForm.errors.password = error.response.data.errors.password[0]
              } else {
                this.registerForm.errors.password = ''
              }

              if (error.response.data.errors.terms !== undefined) {
                this.registerForm.errors.terms = error.response.data.errors.terms[0]
              } else {
                this.registerForm.errors.terms = ''
              }

              console.log(error)
            })
        }
      }
    },
  }
</script>

<style scoped>

    .jem-width {
        width: 106%
    }

    .register {
        width: 100%;
        padding: .5rem 0;
        margin-top: 1rem;
        border-width: .15rem;
        border-color: rgba(0, 0, 255, .59);
        color: rgba(0, 0, 255, .59);
    }

    .border {
        border-color: #e6e6e6;
        border-style: solid;
        border-width: 1px;
        border-radius: .25rem;
    }

    .paginate-links li.active a, .register {
        font-weight: 700;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .shadow {
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, .1);
    }

    .help-block {
        display: block;
        margin-bottom: 10px;
        color:red;
        /*color: lighten(@text-color, 25%); // lighten the text some for contrast*/
    }

    .selected-button {
        border: solid thick red;
    }

</style>