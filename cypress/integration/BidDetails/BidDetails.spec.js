describe('Bid Details Test', function () {

    beforeEach(() => {

        // cy.exec('php artisan migrate:refresh --force;')

        // && composer dump-autoload && php artisan db:seed --class=UserTableSeeder

        cy.request('/#/')
            .its('body')
            .then((body) => {
                // debugger
                const $html = Cypress.$(body)
                let csrf = '';
                for (let i = 0; i < $html.length; i++) {
                    if ($html[i].name === 'csrf-token') {
                        csrf = $html[i].content
                    }
                }
                cy.loginByCSRF(csrf, 'jemmsoninc@drfirst.com', 'asdasd')
            })


    })

    it('should be able to see the assessor page', function (){
        cy.visit('/#/bid/1');
    });

});