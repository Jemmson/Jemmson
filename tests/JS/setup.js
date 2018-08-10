require('jsdom-global')()

/*
 * Load various JavaScript modules that assist Spark.
 */
global.URI = require('urijs');
global.axios = require('axios');
global._ = require('underscore');
global.moment = require('moment');
global.Promise = require('promise');
global.Cookies = require('js-cookie');


/*
 * Define Moment locales
 */
global.moment.defineLocale('en-short', {
    parentLocale: 'en',
    relativeTime: {
        future: "in %s",
        past: "%s",
        s: "1s",
        m: "1m",
        mm: "%dm",
        h: "1h",
        hh: "%dh",
        d: "1d",
        dd: "%dd",
        M: "1 month ago",
        MM: "%d months ago",
        y: "1y",
        yy: "%dy"
    }
});
global.moment.locale('en');

/*
 * Load jQuery and Bootstrap jQuery, used for front-end interaction.
 */
if (global.$ === undefined || global.jQuery === undefined) {
    global.$ = global.jQuery = require('jquery');
}

global.Spark = {
    "braintreeMerchantId": null,
    "braintreeToken": null,
    "cardUpFront": false,
    "collectsBillingAddress": false,
    "collectsEuropeanVat": false,
    "createsAdditionalTeams": true,
    "csrfToken": "UtdusVdhK7OgJwiNwhj4K4Cf0cJ1m9TZmK1WaQGa",
    "currencySymbol": "$",
    "env": "local",
    "roles": [],
    "state": {
        "user": {
            "id": 2,
            "location_id": null,
            "name": "John Smith",
            "email": "john@example.com",
            "usertype": "contractor",
            "password_updated": 1,
            "photo_url": "https:\/\/www.gravatar.com\/avatar\/64823ac710ad3108143fd0eeda8d32cc.jpg?s=200&d=mm",
            "logo_url": null,
            "uses_two_factor_auth": false,
            "phone": "4901113333",
            "two_factor_reset_code": null,
            "current_team_id": null,
            "stripe_id": null,
            "current_billing_plan": null,
            "billing_state": null,
            "trial_ends_at": null,
            "last_read_announcements_at": null,
            "created_at": "2018-08-08 11:48:10",
            "updated_at": "2018-08-08 11:48:10",
            "subscriptions": [],
            "contractor": {
                "id": 2,
                "user_id": 2,
                "location_id": 2,
                "free_jobs": 5,
                "company_name": "autem",
                "company_logo_name": "inventore",
                "email_method_of_contact": 1,
                "sms_method_of_contact": 1,
                "phone_method_of_contact": 1,
                "created_at": "2018-08-08 11:48:10",
                "updated_at": "2018-08-08 11:48:10",
                "stripe_express": {
                    "id": 2,
                    "contractor_id": 2,
                    "access_token": "sk_test_2DL5LXhimtvvVfbhZNOaEYOG",
                    "refresh_token": "rt_CMkY01KB2aW0XM0Q2XCKw8fNbH8kI3y1EnqfJ2mQ8LzfbbgC",
                    "stripe_user_id": "acct_1By13dFOSJzZ3wkC",
                    "created_at": "2018-08-08 11:48:10",
                    "updated_at": "2018-08-08 11:48:10"
                },
                "location": null
            },
            "customer": null,
            "tax_rate": 0
        },
        "teams": [],
        "currentTeam": null
    },
    "stripeKey": "pk_test_XoNo7nhrt0YIuNCO8LQO9TBM",
    "stripeClientId": "ca_CEwm3Rx977zh3SbKTRmewKg40tMEserh",
    "teamString": "team",
    "pluralTeamString": "teams",
    "userId": 2,
    "usesApi": true,
    "usesBraintree": false,
    "usesTeams": false,
    "usesStripe": true
};

/**
 * Initialize the Spark form extension points.
 */
global.Spark.forms = {
    register: {},
    updateContactInformation: {},
    updateTeamMember: {}
};

/**
 * Spark form error collection class.
 */
global.SparkFormErrors = function () {
    this.errors = {};

    /**
     * Determine if the collection has any errors.
     */
    this.hasErrors = function () {
        return !_.isEmpty(this.errors);
    };


    /**
     * Determine if the collection has errors for a given field.
     */
    this.has = function (field) {
        return _.indexOf(_.keys(this.errors), field) > -1;
    };


    /**
     * Get all of the raw errors for the collection.
     */
    this.all = function () {
        return this.errors;
    };


    /**
     * Get all of the errors for the collection in a flat array.
     */
    this.flatten = function () {
        return _.flatten(_.toArray(this.errors));
    };


    /**
     * Get the first error message for a given field.
     */
    this.get = function (field) {
        if (this.has(field)) {
            return this.errors[field][0];
        }
    };


    /**
     * Set the raw errors for the collection.
     */
    this.set = function (errors) {
        if (typeof errors === 'object') {
            this.errors = errors;
        } else {
            this.errors = {
                'form': ['Something went wrong. Please try again or contact customer support.']
            };
        }
    };


    /**
     * Remove errors from the collection.
     */
    this.forget = function (field) {
        if (typeof field === 'undefined') {
            this.errors = {};
        } else {
            Vue.delete(this.errors, field);
        }
    };
};


/**
 * Load the SparkForm helper class.
 */
global.SparkForm = function (data) {
    var form = this;

    $.extend(this, data);

    /**
     * Create the form error helper instance.
     */
    this.errors = new SparkFormErrors();

    this.busy = false;
    this.successful = false;

    /**
     * Start processing the form.
     */
    this.startProcessing = function () {
        form.errors.forget();
        form.busy = true;
        form.successful = false;
    };

    /**
     * Finish processing the form.
     */
    this.finishProcessing = function () {
        form.busy = false;
        form.successful = true;
    };

    /**
     * Reset the errors and other state for the form.
     */
    this.resetStatus = function () {
        form.errors.forget();
        form.busy = false;
        form.successful = false;
    };


    /**
     * Set the errors on the form.
     */
    this.setErrors = function (errors) {
        form.busy = false;
        form.errors.set(errors);
    };
};


/**
 * Add additional HTTP / form helpers to the Spark object.
 */
$.extend(Spark, require('../../spark/resources/assets/js/forms/http'));


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
global.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': Spark.csrfToken
};

/**
 * Intercept the incoming responses.
 *
 * Handle any unexpected HTTP errors and pop up modals, etc.
 */
global.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    switch (error.response.status) {
        case 401:
            global.axios.get('/logout');
            $('#modal-session-expired').modal('show');
            break;

        case 402:
            global.location = '/settings#/subscription';
            break;
    }

    return Promise.reject(error);
});