// / <reference types="cypress" />
//
// This recipe expands on the previous 'Logging in' examples
// and shows you how to use cy.request when your backend
// validates POSTs against a CSRF token

describe('Logging In - CSRF Tokens', function () {
    const username = 'jemmsoninc@gmail.com'
    const password = 'asdasd'

    // Cypress.Commands.add('loginByCSRF', (csrfToken) => {
    //     cy.request({
    //         method: 'POST',
    //         url: '/login',
    //         failOnStatusCode: false, // dont fail so we can make assertions
    //         form: true, // we are submitting a regular form body
    //         body: {
    //             username,
    //             password
    //         },
    //         headers: {
    //             'x-csrf-token': csrfToken
    //
    //         }
    //     })
    // })

    /**
     * A utility function to check that we are seeing the dashboard page
     */
    const inPage = () => {
        // cy.location('href').should('match', /#/)
        // cy.contains('h2', 'Bid')
    }

    /**
     * A utility function to confirm we can visit a protected page
     */
    const visitInitiateBid = () => {
        cy.visit('/#/initiate-bid')
        inPage()
    }

    beforeEach(function () {
        cy.viewport('macbook-15')
    })

    // it('redirects to /#/', () => {
    //     cy.visit('/#/home')
    //     cy.location('href').should('match', /#/)
    // })
    //
    // it('419 status without a valid CSRF token', function () {
    //     // first show that by not providing a valid CSRF token
    //     // that we will get a 419 status code
    //     cy.loginByCSRF('invalid-token')
    //         .its('status')
    //         .should('eq', 419)
    // })

    it('strategy #1: parse token from HTML', function () {
        // if we cannot change our server code to make it easier
        // to parse out the CSRF token, we can simply use cy.request
        // to fetch the login page, and then parse the HTML contents
        // to find the CSRF token embedded in the page

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
                cy.loginByCSRF(csrf)
                    .then((resp) => {
                        console.log('resp', resp)
                        expect(resp.status).to.eq(200)
                        // expect(resp.body.name).to.include('General Contractor')
                    })
            })

        cy.visit('/#/bid/1')
        // visitInitiateBid()
    })

            // it('strategy #2: parse token from response headers', function () {
            //     // if we embed our csrf-token in response headers
            //     // it makes it much easier for us to pluck it out
            //     // without having to dig into the resulting HTML
            //     cy.request('/login')
            //         .its('headers')
            //         .then((headers) => {
            //
            //             console.log('headers', headers)
            //             console.log('headers', JSON.stringify(headers))
            //             console.log('headers', headers[0])
            //             console.log('headers', headers.date)
            //             console.log('headers', headers["date"])
            //             console.log('headers', headers["set-cookie"][0])
            //
            //             let csrfArray = headers["set-cookie"][0].split("=")
            //             console.log('csrf', csrfArray[1])
            //             let csrfArray2 = csrfArray[1].split("; ")
            //             console.log('csrf', csrfArray2[0])
            //
            //             const csrf = csrfArray2[0]
            //             cy.loginByCSRF(csrf)
            //                 .then((resp) => {
            //                     expect(resp.status).to.eq(200)
            //                     expect(resp.body).to.include('General Contractor')
            //                 })
            //         })
            //
            //     // visitInitiateBid()
            // })

    // it('strategy #3: expose CSRF via a route when not in production', function () {
    //     // since we are not running in production we have exposed
    //     // a simple /csrf route which returns us the token directly
    //     // as json
    //     cy.request('/csrf')
    //         .its('body.csrfToken')
    //         .then((csrf) => {
    //             cy.loginByCSRF(csrf)
    //                 .then((resp) => {
    //                     expect(resp.status).to.eq(200)
    //                     expect(resp.body).to.include('<h2>dashboard.html</h2>')
    //                 })
    //         })
    //
    //     visitDashboard()
    // })

    // it('strategy #4: slow login via UI', () => {
    //     // Not recommended: log into the application like a user
    //     // by typing into the form and clicking Submit
    //     // While this works, it is slow and exercises the login form
    //     // and NOT the feature you are trying to test.
    //     cy.visit('/login')
    //     cy.get('input[name=username]').type(username)
    //     cy.get('input[name=password]').type(password)
    //     cy.get('form').submit()
    //     visitInitiateBid()
    // })
})


// describe('Login Test', function () {

// beforeEach(() => {
//     cy.visit('http://localhost:9500/logout')

// cy.request('POST', 'login',
//     {"username": "jemmsoninc@gmail.com", "password": "asdasd", "remember": null, "error": null, "busy": true}
// );
//
// cy.server()
// console.log('visit', cy.visit("/"))

// cy.get("head meta[name=X-CSRF-TOKEN]")

// console.log('token', cy.get("head meta[name=X-CSRF-TOKEN]"))

//     .then((meta) => {
//         const csrf = meta[0].content
//         cy.request({
//             method: 'POST',
//             url: '/login',
//             form: true,
//             body: {
//                 _token: csrf,
//                 email: username,
//                 password: password
//             }
//         })
//     })
//
// cy.get('[data-cy=loginModalButton]').click()
// cy.get('[data-cy=username]').type('jemmsoninc@gmail.com')
// cy.get('[data-cy=password]').type('asdasd')
// cy.get('[data-cy=submit]').click()
//
// cy.server(x)

//     const username = 'cypress'
//     const password = 'password123'
//
//     Cypress.Commands.add('loginByCSRF', (csrfToken) => {
//         cy.request({
//             method: 'POST',
//             url: '/login',
//             failOnStatusCode: false, // dont fail so we can make assertions
//             form: true, // we are submitting a regular form body
//             body: {
//                 username,
//                 password,
//                 _csrf: csrfToken, // insert this as part of form body
//             },
//         })
//     })
//
//     const inDashboard = () => {
//         cy.location('href').should('match', /home$/)
//         cy.contains('h2', 'hello')
//     }
//
//
// })


// it('should login to the home screen', function () {
// Arrange
// cy.visit('https://gemsub.com/logout')


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
// });

// it('should open up initiate bid page', function () {
//     cy.wait(10000)
//     cy.visit('http://localhost:9500/#/initiate-bid')
//     // cy.visit('https://gemsub.com/#/initiate-bid')
//     cy.get('[data-cy=customerName]').type('Shawn').wait(5000);
//     cy.get('.v-list-item__title').click()
//     // console.log('phone', cy.get('#phone').valueOf())
//     // cy.get('[data-cy=customerName]').wait(5000).should('have.text', 'Shawn')
//     // cy.contains('2020-103--');
//     // cy.wait(10000)
//     cy.get('[data-cy=paymentTypeLabel]').should('have.text', 'Select Payment Type For Job')
//     // cy.get('[data-cy=jobName]').should('have.text', '2020-103--')
//     // cy.get('[data-cy=phone]').should('have.text', '(480)-703-4902')
//     // cy.get('[data-cy=phone]').should('contain', '(480)-703-4902')
//     // console.log('elem', cy.get('#customerName').type('{downarrow}'))
// });
// });