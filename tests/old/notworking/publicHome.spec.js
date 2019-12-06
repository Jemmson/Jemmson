import { shallowMount, createLocalVue } from '@vue/test-utils'
import VueRouter from 'vue-router'
import expect from 'expect'
// import sinon from 'sinon'
import PublicHome from '../../resources/assets/js/pages/PublicHome'

const localVue = createLocalVue()
localVue.use(VueRouter)
const router = new VueRouter()

console.log('PublicHome.spec')

// describe ('CheckAccountingApp', () => {
//   it('should say hello', function() {
//     let wrapper = mount(Home);
//     console.log(wrapper);
//     console.log('Hello')
//   })
// });

describe('PublicHome', () => {
  let wrapper;
  // const getCompanyInfo = sinon.spy()
  const router = new VueRouter();
  const $route = {
    path: '/home'
  };
  wrapper = shallowMount(PublicHome, {
    localVue,
    router,
    methods: {
      // getCompanyInfo
    },
    mocks: {
      $route
    },
    data() {
      return {
        currentWindow: window.location.origin,
        form: {
          username: '',
          password: '',
          remember: null,
          error: null,
          busy: false,
        }
      }
    }
  })

  it('should route to home when logging in', function() {

    let username = wrapper.find({
      ref: 'username'
    })
    let password = wrapper.find({
      ref: 'password'
    })
    let submit = wrapper.find({
      ref: 'submit'
    })

    username.setValue('noreply@quickbooks.com');
    password.setValue('asdasd');
    submit.trigger('click');
    

  })

  it.skip('should show pricing plans for the application', function() {

  })

  it.skip('should show the number of free jobs that one can get', function() {

  })

  it.skip('should show the technologies that I am using, quickbooks, stripe, zero, pusher, nexmo, google', function() {

  })

  it.skip('should show why this app is helpful', function() {

  })

  it.skip('should show how to get documentation on how it will work', function() {

  })

  it.skip('should show a video on how the site works', function() {

  })

  it.skip('should explain that this site can be used as a tool to solve a problem and not an end all be all site', function() {

  })

  it.skip('should encourage people to use quickbooks or xero online', function() {

  })

})
