import Echo from "laravel-echo";
import Stripe from 'stripe'

describe('customer open job page', function () {

    beforeEach(() => {

        // cy.exec('php artisan migrate:refresh --force;')

        // && composer dump-autoload && php artisan db:seed --class=UserTableSeeder
        // cy.request('/refreshDatabase')

        cy.viewport('macbook-15')

        // cy.stub(window.Echo, 'private').callsFake(() => 'foo');

        cy.request('/#/')
            .its('body')
            .then((body) => {
                const $html = Cypress.$(body)
                let csrf = '';
                for (let i = 0; i < $html.length; i++) {
                    if ($html[i].name === 'csrf-token') {
                        csrf = $html[i].content
                    }
                }
                cy.loginByCSRF(csrf, 'pike.shawn@gmail.com', 'asdasd')
                    .then((resp) => {
                        console.log('resp', resp)
                        expect(resp.status).to.eq(200)
                    })
            })
        beforeVisit();
        cy.visit('/#/bid/1')

    })

    it('should open up the job page', function () {

    });

    // Support Methods

    function beforeVisit() {
        cy.server()
        cy.checkAuth();
        cy.recents();
        cy.broadAuth();
        cy.getJobs();
        cy.images();
        cy.current();
        cy.job1();

    }


});