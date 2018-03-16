var base = require('auth/register-stripe');

Vue.component('spark-register-stripe', {
    mixins: [base],
    methods: {
        confirmPassword() {
            if (this.registerForm.password !== this.registerForm.password_confirmation) {
                this.registerForm.errors.errors = {
                    password_confirmation: ['Passwords need to match.']
                };
                this.passwordsMatch = false;
            } else {
                this.registerForm.errors.errors = {};
            }
            this.passwordsMatch = true;
        },
    }
});
