describe('Login Test', function () {
    it('should login to the home screen', function () {
        // Arrange
        cy.visit('https://gemsub.com/logout')
        // cy.visit('http://localhost:9500/logout')
        cy.get('#loginModalButton').click()
        cy.get('#username').type('jemmsoninc@gmail.com')
        cy.get('#password').type('asdasd')
        cy.get('#submit').click()


        // cy.server()
        // cy.route('GET', '/#/initiate-bid')
        // cy.server().should((server) => {
        //     expect(server.method).to.eq('GET')
        //     expect(server.status).to.eq(200)
        // })
        //
        // cy.server({
        //     method: 'GET',
        //     delay: 0,
        //     status: 200,
        //     response: {},
        // })

        // cy.get("#newJob").then(($myElement) => {
        //     // $myElement.should('have.attr', 'disabled')
        //     console.log('myElement', $myElement)
        // })
    });

    it('should open up initiate bid page', function () {
        cy.wait(15000)
        cy.visit('http://localhost:9500/#/initiate-bid')
        cy.get('#customerName').type('Shawn').wait(10000).type('{downarrow}');
        // console.log('elem', cy.get('#customerName').type('{downarrow}'))
    });
});