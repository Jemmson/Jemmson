var base = require('settings/profile/update-contact-information');

Vue.component('spark-update-contact-information', {
    mixins: [base],
    mounted () {
        // this.form.address_line_1 = Spark.state.user.location.address_line_1;
        // this.form.address_line_2 = Spark.state.user.location.address_line_2;
        // this.form.city = Spark.state.user.location.city;
        // this.form.state = Spark.state.user.location.state;
        // this.form.zip  = Spark.state.user.location.zip;
        if(this.user.contractor !== null) {
          this.form.address_line_1 = this.user.contractor.location.address_line_1;
          this.form.address_line_2 = this.user.contractor.location.address_line_2;
          this.form.city = this.user.contractor.location.city;
          this.form.state = this.user.contractor.location.state;
          this.form.zip  = this.user.contractor.location.zip;
        } else if(this.user.customer !== null) {
          this.form.address_line_1 = this.user.customer.location.address_line_1;
          this.form.address_line_2 = this.user.customer.location.address_line_2;
          this.form.city = this.user.customer.location.city;
          this.form.state = this.user.customer.location.state;
          this.form.zip  = this.user.customer.location.zip;
        }

        this.form.phone = this.user.phone;
    }
});
