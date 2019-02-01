import { mount } from '@vue/test-utils'
import expect from 'expect'
import sinon from 'sinon'
import RegisterQuickBooks from '../../resources/assets/js/pages/RegisterQuickBooks'

console.log('RegisterQuickBooks.spec')

// describe ('CheckAccountingApp', () => {
//   it('should say hello', function() {
//     let wrapper = mount(Home);
//     console.log(wrapper);
//     console.log('Hello')
//   })
// });

describe('RegisterQuickBooks', () => {
  let wrapper
  const getCompanyInfo = sinon.spy()

  wrapper = mount(RegisterQuickBooks, {
    methods: {
      getCompanyInfo
    },
    data: {
      companyInfo: {}
    }
  })

// beforeEach (() => {
// });

  it('should show the json company information on mount', function() {
    // visually verified

  })

  it('should call the getcompanyinfo from the server session variable on mount', function() {
    // visually verified

  })

  it('should show the edit button for each of the sections pulled back from the quickbooks get company info', function() {
    // visually verified

  })

  it('should show the cancel, reset button, and the save button for each section if the edit button is clicked', function() {
    // visually verified

  })

  it('should reset all values to the original values if the reset button is clicked', function() {
    // visually verified

  })

  it('should cancel all editable changes from a previous save if the cancel button is clicked', function() {
    // visually verified

  })

  it('should save all changes to the editable fields if the save button is clicked', function() {
    // visually verified

  })

  it('should update quickbooks data when the register button is clicked', function() {

  })

  it('should highlight any mandatory fields that may have not been filled out with quickbooks', function() {

  })

  it('should hide any read-only fields that are apart of the registration if they were not filled out on the quickbooks setup', function() {

  })

  it('should show all editable fields even if they were not mandatory in this app or quickbooks and they are emoty in the get company info response', function() {

  })

  it('should have all blank values that are not mandatory and are related to the quickbooks api be changed to null before it the request is sent to update the API', function() {
    
  })

  it('should have page specific documentation on how this page works', function() {
    
  })

})
