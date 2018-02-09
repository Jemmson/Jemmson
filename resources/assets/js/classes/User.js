export default class User {
    constructor(user) {
        this.user = user;
    }

    status(status, bid) {
        status = Language.lang()[status];
        if (status === undefined) {
            return '';
        }
        if (this.isContractor()) {
            if (bid !== null && this.isGeneral(bid))
                return status.general;

            return status.sub;
        }

        return status.customer;
    }

    isCustomer() {
        return this.user.usertype === 'customer';
    }

    isContractor() {
        return this.user.usertype === 'contractor';
    }

    isGeneral(bid) {
        if (bid !== null)
            return bid.contractor_id === this.user.id;
    }

    isSignedUpWithStripe() {
        this.user.stripe_id === undefined && this.user.stripe_id === null;
    }

    payWithStripe() {
        return true;
    }

    /**
     * User has a stripe express account connected
     */
    stripeExpressConnected() {
        if (!this.isContractor) {
            return false; 
        }
        return this.user.contractor.stripe_express !== undefined && this.user.contractor.stripe_express !== null;
    }

    recievePaymentsWithStripe() {
        return false;
    }

    emitChange(emit) {
        switch (emit) {
            case 'bidUpdated':
                Bus.$emit('bidUpdated');
                break;
            case 'taskUpdated':
                Bus.$emit('taskUpdated');
                break;
        }
    }

    findTaskBid(id, bids) {
        return bids.filter(function (bid) {
            return id === bid.id;
        });
    }

    hasStripeId() {
        return this.user.stripe_id !== null && this.user.stripe_id !== undefined;
    }

    // /stripe functions 
    // /NOTICE: not used just incase we need them later as functions need to fix the error
    // /NOTICE: need to fix the error handling since it doesn't work
    async createToken() {
        // create stripe token
        const {
            token,
            error
        } = await this.stripe.createToken(this.card);

        if (error) {
            // Inform the customer that there was an error
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            return false;
        } else {
            return token;
        }
    }

    async saveCustomer(token) {
        if (!token) {
            return false;
        }
        // create stripe customer with token
        const {
            customer,
            error
        } = await axios.post('/stripe/customer', token);

        if (error) {
            Vue.toasted.error(error.message);
            return false;
        } else {
            this.stripe_id = customer.id;
            return true;
        }
    }

    async chargeCustomer() {
        // charge customer
        const {
            charge,
            error
        } = await axios.post('/stripe/customer/charge', {
            amount: 1
        });

        if (error) {
            Vue.toasted.error(error.message);
        } else {
            console.log(charge);
            Vue.toasted.success('Payment Sent!');
        }
    }
    getBidIndex(id, bid) {
        for (let index = 0; index < bid.length; index++) {
            if (bid[index].id == id) {
                return index;
            }
        }
        return null;
    }
    getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
}