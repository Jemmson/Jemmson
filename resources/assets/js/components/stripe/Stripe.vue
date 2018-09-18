<template>
    <!-- Modal -->
    <div class="modal fade" id="stripe-modal" tabindex="-1" role="dialog" aria-labelledby="stripe-modal" aria-hidden="false" style="z-index: 1000000000;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{ header }}</h4>
                </div>
                <div class="modal-body">
                    <div v-if="showStripeExpress">
                        Before you can continue you will need to connect with Stripe Express. 
                        This is allows you to get paid with a credit or debit card easily.
                    </div>
                    <div v-if="isCustomer && notSignedUp">
                        Before You can pay with stripe you will need to complete the form below.
                    </div>
                </div>
                <div class="modal-footer">
                    <span>
                        <div v-if="isContractor">
                            <connect-with-stripe v-if="showStripeExpress">
                            </connect-with-stripe>
                        </div>
                        <div v-if="isCustomer && notSignedUp">
                            <signup-with-stripe>
                            </signup-with-stripe>
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        user: Object
    },
  computed: {
      header() {
          return 'Stripe';
      },
      notSignedUp() {
          return !User.hasStripeId();
      },
      isContractor() {
          return User.isContractor();
      },
      isCustomer() {
          return User.isCustomer();
      },
      showStripeExpress() {
          if (this.user.contractor === null)
            return false;

          return this.user.contractor.stripe_express === null;
      }
  },
  methods: {
  }
}
</script>