describe('My First Test', function () {
    it('should login to the home screen', function () {
        // Arrange
        cy.visit('http://localhost:9500/logout')
        cy.get('#registerLogin').click()
        cy.get('#username').type('jemmsoninc@gmail.com')
        cy.get('#password').type('asdasd')
        cy.get('#submit').click()
    });

    it('should open up to the initiate bid page', function () {
        cy.wait(1000).get('#newJob').click()
        cy.get("#submit").should('have.attr', 'disabled')
    });

    it('should add the name to the customer box and the first name, last name, and job name should auto fill in', function () {
        cy.get('#customerName').type('Shawn Pike')
        cy.get('#firstName').type( 'Shawn')
        cy.get('#lastName').type('Pike')
        cy.get('#jobName').type( ' ')
    });


});