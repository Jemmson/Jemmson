describe('Initiate Bid Test', function () {

    beforeEach(() => {

        // cy.exec('php artisan migrate:refresh --force;')

        // && composer dump-autoload && php artisan db:seed --class=UserTableSeeder
        // cy.request('/refreshDatabase')

        cy.viewport('macbook-15')

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

        cy.get('#jobName').should('contain.text', '2020-105-Shawn-Pike')

    });

    // Support Methods

    function beforeVisit() {
        cy.server()
        cy.route({
            method: 'GET',
            url: '/checkAuth',
            response: 'fixture:checkAuth.json'
        }).as('checkAuth')

        // cy.wait('@checkAuth').then((xhr) => {
        //     assert.isNotNull(xhr.response.body.auth, 'true')
        // })

        cy.route({
            method: 'GET',
            url: '/notifications/recent',
            response: 'fixture:recentNotifications.json'
        })
        cy.route({
            method: 'POST',
            url: '/broadcasting/auth',
            response: 'fixture:broadcastingAuth.json'
        })
        cy.route({
            method: 'GET',
            url: '/getJobs',
            response: 'fixture:getJobs/getJobs.json'
        })
        cy.route({
            method: 'GET',
            url: '/getJobs',
            response: 'fixture:getJobs/getJobs.json'
        })
    }


});