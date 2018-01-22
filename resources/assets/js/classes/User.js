export default class User {
    constructor(user) {
        this.user = user;
    }

    status(status, bid) {
        status = Language.lang()[status];
        if (status === undefined) {
            return '';
        }
        if (this.isContractor) {
            if (bid !== null && this.isGeneral(bid))
                return status.general;

            return status.sub;
        }

        return status.customer;
    }

    isContractor() {
        return this.user.usertype === 'contractor';
    }

    isGeneral(bid) {
        if (bid !== null)
            return bid.contractor_id === this.user.id;
    }
}