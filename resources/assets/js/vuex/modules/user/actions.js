import axios from 'axios';
import Vue from 'vue';



export default {
  async login({
    commit
  }, payload) {
    payload.error = null;
    try {
      const data = await axios.post('login', payload);
      location.href = '/#/home';
    } catch (error) {
      console.log(error);
      Vue.toasted.error('The user name or password is incorrect');
      payload.error = 'The user name or password is incorrect';
    }
  },
}
