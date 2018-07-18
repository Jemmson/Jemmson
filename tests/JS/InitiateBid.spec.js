import {mount} from 'vue-test-utils'
import axios from 'axios'
import expect from 'expect'
// window.jQuery = require('jquery');
// import jquery from 'jquery'
// import jQuery from 'jquery'
// require('jquery');


// import Spark from '../../spark/resources/assets/js/spark-bootstrap'
// import SparkForm from '../../spark/resources/assets/js/forms/bootstrap.js'
import InitiateBid from '../../resources/assets/js/pages/InitiateBid'

describe ('InitiateBid', () => {

  it ('should display The Phone field is required. if name is input but not phone number', function () {
    const wrapper = mount(InitiateBid, {
      data () {
        return {
          query: '',
          results: [],
          form: new SparkForm ({
            phone: '',
            customerName: ''
          }),
          disabled: {
            submit: false
          }
        }
      }
    })
  });
})