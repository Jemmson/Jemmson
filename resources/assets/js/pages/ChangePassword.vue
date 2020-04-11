<template>
    <v-container>
        <v-card>
            <v-card-title
            >Change Password
            </v-card-title>
            <h4 class="text-center" style="color: #ff5252 !important;" v-if="errorMessage">{{ theErrorMessage }}</h4>
            <h4 class="text-center" style="color: mediumseagreen !important;" v-if="successMessage">{{ theSuccessMessage }}</h4>
            <v-card-subtitle class="text-center" style="color: #ff5252 !important;" v-if="errorMessageSubTitle">Fill In All Fields</v-card-subtitle>
            <v-form v-model="valid">
                <v-container>

                    <v-text-field
                            :type="showCurrentPassword ? 'text' : 'password'"
                            id="currentPassword"
                            v-model="currentPassword"
                            required
                            :rules="[
                                currentPasswordFieldIsRequired(currentPassword),
                                currentPasswordMustBeGreaterThan5Characters(currentPassword)
                            ]"
                            label="Current Password"
                            prepend-icon="mdi-lock"
                            :append-icon="showCurrentPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showCurrentPassword = !showCurrentPassword"
                    >
                    </v-text-field>

                    <v-text-field
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            v-model="password"
                            required
                            :rules="[
                                passwordFieldIsRequired(password),
                                passwordMustBeGreaterThan5Characters(password)
                            ]"
                            label="Password"
                            prepend-icon="mdi-lock"
                            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showPassword = !showPassword"
                    >
                    </v-text-field>

                    <v-text-field
                            :type="showConfirmPassword ? 'text' : 'password'"
                            id="confirmPassword"
                            v-model="confirmPassword"
                            required
                            :rules="[
                                confirmPasswordFieldIsRequired(confirmPassword),
                                confirmPasswordMustBeGreaterThan5Characters(confirmPassword),
                                newPasswordsMustMatch()
                            ]"
                            label="Confirm Password"
                            prepend-icon="mdi-lock"
                            :append-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            @click:append="showConfirmPassword = !showConfirmPassword"
                    >
                    </v-text-field>

                    <v-card-actions>
                        <v-btn
                                class="w-full"
                                color="primary"
                                name="submit"
                                id="submit"
                                dusk="submitBid"
                                @click.prevent="submit()"
                                :loading="loading"
                        >
                            Submit
                        </v-btn>
                    </v-card-actions>

                    <br>
                    <feedback
                            page="initiateBid"
                    ></feedback>
                </v-container>
            </v-form>
        </v-card>
    </v-container>
</template>

<script>

    import Feedback from "../components/shared/Feedback";

    export default {
        name: 'ChangePassword',
        components: {
            Feedback
        },
        data() {
            return {
                theErrorMessage: '',
                theSuccessMessage: '',
                showCurrentPassword: false,
                showPassword: false,
                showConfirmPassword: false,
                errorMessage: false,
                successMessage: false,
                errorMessageSubTitle: false,
                valid: false,
                loading: false,
                currentPassword: null,
                password: null,
                confirmPassword: null,
                beingSubmitted: false,
                errors: {
                    currentPassword: {
                        fieldRequired: false,
                        tooShort: false
                    },
                    password: {
                        fieldRequired: false,
                        tooShort: false
                    },
                    confirmPassword: {
                        fieldRequired: false,
                        mustMatch: false,
                        tooShort: false
                    }
                }
            }
        },
        methods: {

            validInput() {

                this.noEmptyFields()

            },

            currentPasswordFieldIsRequired(field) {

                if (field === null) {
                    this.errors.currentPassword.fieldRequired = true
                } else {
                    this.errors.currentPassword.fieldRequired = false
                }
                return field !== null || 'This field is required'
            },

            currentPasswordMustBeGreaterThan5Characters(password) {

                if (password !== null && password.length > 5) {
                    this.errors.currentPassword.tooShort = false
                } else {
                    this.errors.currentPassword.tooShort = true
                }
                return password !== null && password.length > 5 || 'Password must be at least 6 characters'
            },

            passwordFieldIsRequired(field) {

                if (field === null) {
                    this.errors.password.fieldRequired = true
                } else {
                    this.errors.password.fieldRequired = false
                }
                return field !== null || 'This field is required'
            },

            passwordMustBeGreaterThan5Characters(password) {

                if (password !== null && password.length > 5) {
                    this.errors.password.tooShort = false
                } else {
                    this.errors.password.tooShort = true
                }
                return password !== null && password.length > 5 || 'Password must be at least 6 characters'
            },

            confirmPasswordFieldIsRequired(field) {

                if (field === null) {
                    this.errors.confirmPassword.fieldRequired = true
                } else {
                    this.errors.confirmPassword.fieldRequired = false
                }
                return field !== null || 'This field is required'
            },

            confirmPasswordMustBeGreaterThan5Characters(password) {

                if (password !== null && password.length > 5) {
                    this.errors.confirmPassword.tooShort = false
                } else {
                    this.errors.confirmPassword.tooShort = true
                }
                return password !== null && password.length > 5 || 'Password must be at least 6 characters'
            },

            newPasswordsMustMatch() {

                if (this.password === this.confirmPassword) {
                    this.errors.confirmPassword.mustMatch = false
                } else {
                    this.errors.confirmPassword.mustMatch = true
                }
                return this.password === this.confirmPassword || 'Passwords must match'
            },



            noEmptyFields() {
                return !this.currentPassword && !this.password && !this.confirmPassword
            },

            validate() {

                return !this.errors.currentPassword.fieldRequired
                    && !this.errors.currentPassword.tooShort
                    && !this.errors.password.fieldRequired
                    && !this.errors.password.tooShort
                    && !this.errors.confirmPassword.fieldRequired
                    && !this.errors.confirmPassword.mustMatch
                    && !this.errors.confirmPassword.tooShort;
            },

            async submit() {

                if (this.validate()) {

                    this.errorMessage = false;
                    this.successMessage = false;
                    this.errorMessageSubTitle = false;
                    this.loading = true;

                    const {data} = await axios.post('/user/changePassword', {
                        currentPassword: this.currentPassword,
                        password: this.password,
                        confirmPassword: this.confirmPassword
                    })

                    if (data.error) {
                        this.errorMessage = true;
                        this.theErrorMessage = data.error
                    } else {
                        this.successMessage = true;
                        this.theSuccessMessage = data.success
                    }

                    this.loading = false;
                } else {
                    this.errorMessage = true;
                    this.errorMessageSubTitle = true;
                    this.theErrorMessage = 'Please Review Errors'
                }

            }

        }
    }
</script>

<style scoped>

</style>