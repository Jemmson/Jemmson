<template>
    <v-container>
        <v-card>
            <v-card-title>Reset Password</v-card-title>
            <v-card-subtitle></v-card-subtitle>
            <v-card-text>

                <v-form>

                    <v-text-field
                            id="username"
                            label="Email"
                            v-model="form.username"
                            prepend-icon="mdi-account-circle"
                    />
                    <v-text-field
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            label="Password"
                            prepend-icon="mdi-lock"
                            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showPassword = !showPassword"
                    />

                    <v-text-field
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            v-model="form.password"
                            label="Password"
                            prepend-icon="mdi-lock"
                            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showPassword = !showPassword"
                    />

                </v-form>
                
            </v-card-text>
            <v-card-actions>
                
            </v-card-actions>
        </v-card>
        
    </v-container>
</template>

<script>
    export default {
        name: "PasswordReset",
        data() {
            return {
                showLogin: true,
                showPassword: false,
                form: {
                    username: '',
                    password: '',
                    remember: null,
                    error: null,
                    busy: false
                },
            }
        },
    }
</script>

<style scoped>

</style>