var base = require('settings/payment-method/update-payment-method-stripe');

Vue.component('spark-update-payment-method-stripe', {
    mixins: [base],
    methods: {
        async deleteCard() {
            this.form.busy = true;
            this.form.errors.forget();
            this.form.successful = false;
            this.cardForm.errors.forget();
            try {
                let {data} = await axios.delete('/stripe/customer/card', {});
                console.log(data);
                this.form.busy = false;
                Bus.$emit('updateUser');
                Bus.$emit('updateTeam');
                Vue.toasted.success('Card Deleted');
                this.form.successful = true;
            } catch (error) {
                this.form.busy = false;
                Vue.toasted.error('Error Deleting Card');
            }
        }
    }
});
