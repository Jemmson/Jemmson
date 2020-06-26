// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })

Cypress.Commands.add('loginByCSRF', (csrfToken, username, password) => {
    // debugger
    cy.request({
        method: 'POST',
        url: '/login',
        failOnStatusCode: false, // dont fail so we can make assertions
        form: true, // we are submitting a regular form body
        body: {
            username: username,
            password: password
        },
        headers: {
            'x-csrf-token': csrfToken
        }
    })
})

Cypress.Commands.add('checkAuth', () => {
    cy.route({
        method: 'GET',
        url: '/checkAuth',
        response: 'fixture:checkAuth.json'
    }).as('checkAuth')
})

Cypress.Commands.add('recents', () => {
    cy.route({
        method: 'GET',
        url: '/notifications/recent',
        response: 'fixture:recentNotifications.json'
    })
})

Cypress.Commands.add('broadAuth', () => {
    cy.route({
        method: 'POST',
        url: '/broadcasting/auth',
        response: 'fixture:broadcastingAuth.json'
    })
})

Cypress.Commands.add('getJobs', () => {
    cy.route({
        method: 'GET',
        url: '/getJobs',
        response: 'fixture:getJobs/getJobs.json'
    })
})

// Cypress.Commands.add('validatePhoneNumber', () => {
//     cy.route({
//         method: 'GET',
//         url: '/getJobs',
//         response: 'fixture:getJobs/getJobs.json'
//     })
// })




//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })
