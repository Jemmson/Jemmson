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


/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
global.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': 'UtdusVdhK7OgJwiNwhj4K4Cf0cJ1m9TZmK1WaQGa'
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