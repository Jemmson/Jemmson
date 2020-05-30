describe('Initiate Bid Test', function () {
    it('should open up to the initiate bid page', function () {
        cy.get('#newJob').click()
        cy.get("#submit").then(($myElement) => {
            // $myElement.should('have.attr', 'disabled')
            console.log('myelment', $myElement)
        })
    });

    it('should add the name to the customer box and the first name, last name, and job name should auto fill in', function () {
        cy.get('#customerName').type('Shawn Pike')
    });
});