import axios from 'axios';
import Vue from 'vue';



export default {
  async login({
    commit
  }, payload) {
    payload.error = null;
    payload.busy = true;
    try {
      const data = await axios.post('login', payload);
      commit('setUser', data.data);
      location.href = '/#/home';
      payload.busy = false;

    } catch (error) {
      console.log(error);
      Vue.toasted.error('The user name or password is incorrect');
      payload.error = 'The user name or password is incorrect';
      payload.busy = false;
    }
  },
}
