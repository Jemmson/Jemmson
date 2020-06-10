describe('Initiate Bid Test', function () {

    beforeEach(() => {

        // cy.exec('php artisan migrate:refresh --force;')

        // && composer dump-autoload && php artisan db:seed --class=UserTableSeeder

    })

    it('should be able to register a contractor', function () {
        cy.request('/refreshDatabase');
        cy.visit('/#/register');
        clickContractor()
        addContractor('Registration/Requests/full.json')
        addLicense()
        submit()
        // cy.location().should((loc)=>{
        //     expect(loc.pathname).to.eq('/#/home')
        // })
    });


    it('should show no company name if submit is hit and company name is not filled in', function () {
        cy.visit('/#/register');
        clickContractor()
        addContractor('Registration/Requests/full_noCompanyName.json')

        cy.server()
        cy.route({
            method: 'POST',
            status: '422',
            url: '/registerContractor',
            response: 'fixture:Registration/Responses/noCompanyEntered.json'
        })

        submit()
    });


    // Support Methods

    function submit () {
        cy.get('#term_switch').check({force: true});
        cy.get('#register').click();
    }

    function clickContractor(){
        cy.get('#contractorButton').click();
    }

    function addLicense(){
        cy.get('#addContractorLicenseButton').click();
        cy.get('#state').type('Arizona');
        cy.contains('span','Arizon').click();
        cy.get('#type').type('CR-6');
        cy.contains('.v-list-item__title','CR-6').click();
        cy.get('#name').type('SWIMMING POOL SERVICE AND REPAIR');
        cy.get('#value').type('AB12345J');
        cy.get('#addLicense').click();
    }

    function addContractor(path) {

        cy.fixture(path).then((json) => {
            for (let [key, value] of Object.entries(json)) {
                console.log(`${key}: ${value}`);
                cy.get(`#${key}`).type(`${value}`);
            }
        })
    }

    function beforeVisit() {
        cy.server()
        getQuickBooksAuthURL();
        validatePhoneNumber();
    }

    function getQuickBooksAuthURL(){
        cy.route({
            method: 'GET',
            url: '/quickbooks/getAuthUrl/getCompany',
            response: 'https://appcenter.intuit.com/connect/oauth2?client_id=Q0EDLdEJz7bfFG1tBIWX8vyhRQRz1m1vNVoT1rCBClqJtcSJaz&scope=com.intuit.quickbooks.accounting+openid+profile+email+phone+address&redirect_uri=http%3A%2F%2Flocalhost%3A9500%2Fquickbooks%2FprocessToken%2F&response_type=code&state=%7B%22method%22%3A%22getCompany%22%2C%22guid%22%3A%226C8FF02C-33B9-1613-258F-ED332F7B1179%22%7D'
        })
    }

    function validatePhoneNumber() {
        cy.route({
            method: 'GET',
            url: '/api/user/validatePhoneNumber',
            response: '["success","mobile","mobile"]'
        })
    }

});