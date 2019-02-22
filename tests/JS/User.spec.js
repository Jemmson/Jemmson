import expect from 'expect'
import User from '../../resources/assets/js/classes/User'
import { router } from '../app.js'

describe('User', () => {

  it('should say hello', function() {
    console.log(User.prototype.hello());
    expect(User.prototype.hello()).toBe('world');
  })

  it('should return success when contractor info is sent to be registered', function() {

    let form = {
      email: '',
        name: '',
        company_name: '',
        phone_number: '',
        address_line_1: '',
        address_line_2: '',
        city: '',
        state: '',
        zip: '',
        terms: false,
        notes: '',
        password: '',
        password_confirmation: '',
        email_contact: true,
        phone_contact: false,
        sms_text: false,
        qbCompanyInfo: {},
      errors: {
        errors: {}
      }
    }

    let updateAccountingCompanyInfoAPI = false

    User.prototype.registerContractor(form, updateAccountingCompanyInfoAPI)

    moxios.wait(function () {
      let request = moxios.requests.mostRecent()
      request.respondWith({
        status: 200,
        response: [
          { id: 1, firstName: 'Fred', lastName: 'Flintstone' },
          { id: 2, firstName: 'Wilma', lastName: 'Flintstone' }
        ]
      }).then(function () {
        let list = document.querySelector('.UserList__Data')
        equal(list.rows.length, 2)
        equal(list.rows[0].cells[0].innerHTML, 'Fred')
        equal(list.rows[1].cells[0].innerHTML, 'Wilma')
        done()
      })
    })
  })


})

})