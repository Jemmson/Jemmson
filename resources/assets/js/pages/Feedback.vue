<template>
        <div class="card flex flex-col">
            <h1 class="text-center">Feedback</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>user_id</th>
                        <th>comment</th>
                        <th>page_url</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="comment in feedback" :key="comment.id">
                        <td>{{ comment.user_id }}</td>
                        <td>{{ comment.comment }}</td>
                        <td>{{ comment.page_url }}</td>
                    </tr>
                </tbody>
            </table>
        <!-- <pre>{{ feedback }}</pre> -->
        <!-- <pre>{{ comments }}</pre> -->
    </div>
</template>

<script>
export default {
  name: "",
  data() {
    return {
      feedback: []
    };
  },
  mounted() {
    this.comments;
  },
  computed: {
    comments() {
      // charge customer
      axios
        .get("/feedback")
        .then(response => {
          console.log(JSON.stringify(response));
          console.log(JSON.stringify(response.data));
          console.log(JSON.stringify(response.data.feedback));
          this.feedback = response.data.feedback;
        })
        .catch(error => {
          console.log(JSON.stringify(error));
          this.$router.push("/home");
        });
    }
  },
  methods: {}
};
</script>

<style scoped>
</style>
