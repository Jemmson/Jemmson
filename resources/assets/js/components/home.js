Vue.component('home', {
  props: ['user'],

  mounted() {
    //

    axios.get('/api/test')
      .then(response => {
        console.log(response.data);
      });
  },

  computed: {
    upperName() {
      return this.user.name.toUpperCase()
    }
  }

});
