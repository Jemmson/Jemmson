import Echo from "laravel-echo";
import Stripe from 'stripe'

describe('Initiate Bid Test', function () {

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
                cy.loginByCSRF(csrf, 'jemmsoninc@gmail.com', 'asdasd')
                    .then((resp) => {
                        console.log('resp', resp)
                        expect(resp.status).to.eq(200)
                    })
            })
        beforeVisit();
        cy.visit('/#/initiate-bid')

    })

    it('should open up to the initiate bid page', function () {


        // window.Echo = new Echo({
        //     broadcaster: '',
        //     key: '',
        //     cluster: '',
        //     encrypted: true
        // })

        // cy.stub(window.Echo)
        cy.stub(Stripe)


        // disabled button should be disabled upon load with empty fields
        cy.get("#submit").then(($myElement) => {
            // $myElement.should('have.attr', 'disabled')
            console.log('myelment', $myElement)
        })


        // typing a name should give me responses back
        cy.route({
            method: 'GET',
            url: '/customer/search?query=',
            response: 'fixture:searchShawn.json'
        });

        cy.get('#customerName').then(($dropdown) => {
            // return $dropdown;
        }).type('Shawn Pike')

        cy.contains('span', 'Shawn Pike').click()

        cy.get('[data-cy=jobName]').should('contain.text', '2020-105-Pike-Shawn')

    });

    // Support Methods

    function beforeVisit() {
        cy.server()
        cy.checkAuth();
        cy.recents();
        cy.broadAuth();
        cy.getJobs();

    }


});