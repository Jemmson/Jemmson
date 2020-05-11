<template>
   <div>
       <v-app-bar
               app
               color="#95ca97"
               height="100"
       >
           <v-avatar
                   class="mr-3"
                   color="#95ca97"
                   style="height: 66px;
                            min-width: 100px;
                            width: 216px;"
           >
               <v-img
                       src="img/premiumlogo/jemmson-logo.png"
               ></v-img>
           </v-avatar>
       </v-app-bar>
       <v-container class="mt-11">
           <v-card>
               <v-card-title>Please Verify Your Email</v-card-title>
               <v-card-subtitle></v-card-subtitle>
               <v-card-text>
                   <v-text-field
                           id="username"
                           label="Email"
                           v-model="email"
                           prepend-icon="mdi-account-circle"
                           :rules="[
                            emailIsValid()
                        ]"
                   />
                   <div v-if="error" class="uppercase error--text">
                       {{ this.errorMessage }}
                   </div>
                   <div v-if="success" class="uppercase green--text">
                       {{ this.successMessage }}
                   </div>
               </v-card-text>
               <v-card-actions>
                   <v-btn
                           text
                           color="primary"
                           @click="resetPasswordLink()"
                           :loading="loading"
                   >Send Password Reset Link
                   </v-btn>
               </v-card-actions>
           </v-card>

       </v-container>
   </div>
</template>

<script>
    export default {
        name: "PasswordEmailVerification",
        data() {
            return {
                email: null,
                error: false,
                errorMessage: '',
                loading: false,
                success: false,
                successMessage: ''
            }
        },
        methods: {
            emailIsValid() {
                if (this.email) {
                    const mailFormat = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/;
                    return mailFormat.test(this.email) || 'Email Must Be A Valid Email Address';
                } else {
                    return false
                }
            },
            async resetPasswordLink() {

                if (this.emailIsValid() !== 'Email Must Be A Valid Email Address') {
                    this.loading = true
                    const {data} = await axios.post('/password/email', {
                        'email': this.email
                    });

                    if (data.error) {
                        this.error = true;
                        this.errorMessage = data.error;
                        this.success = false;
                        this.successMessage = '';
                    } else {
                        this.error = false;
                        this.errorMessage = '';
                        this.success = true;
                        this.successMessage = 'We have e-mailed your password reset link!';
                    }
                    this.loading = false
                }
            },
            mounted(){
                this.$store.commit('setCurrentPage', '/password-email-verification');
            }
        }
    }
</script>

<style scoped>

</style>