import { mount } from '@vue/test-utils'
import expect from 'expect'
// import sinon from 'sinon'
import PublicHome from '../../resources/assets/js/pages/PublicHome'

console.log('PublicHome.spec')

// describe ('CheckAccountingApp', () => {
//   it('should say hello', function() {
//     let wrapper = mount(Home);
//     console.log(wrapper);
//     console.log('Hello')
//   })
// });

describe('PublicHome', () => {
  let wrapper
  // const getCompanyInfo = sinon.spy()

  wrapper = mount(PublicHome, {
    methods: {
      // getCompanyInfo
    },
    data: {
      // companyInfo: {}
    }
  })

// beforeEach (() => {
// });

  it('should show pricing plans for the application', function() {
    
  })

  it('should show the number of free jobs that one can get', function() {
    
  })

  it('should show the technologies that I am using, quickbooks, stripe, zero, pusher, nexmo, google', function() {
    
  })

  it('should show why this app is helpful', function() {
    
  })

  it('should show how to get documentation on how it will work', function() {
    
  })

  it('should show a video on how the site works', function() {
    
  })

  it('should explain that this site can be used as a tool to solve a problem and not an end all be all site', function() {
    
  })

  it('should encourage people to use quickbooks or xero online', function() {
    
  })

})
