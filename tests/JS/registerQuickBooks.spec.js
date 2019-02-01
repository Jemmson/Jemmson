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

  })

  it('should call the getcompanyinfo from the server session variable on mount', function() {

  })

  it('should show the edit button for each of the sections pulled back from the quickbooks get company info', function() {

  })

  it('should show the cancel and the save button for each section if the edit button is clicked', function() {

  })

})
