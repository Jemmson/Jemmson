<template>
    <v-card class="margins">
        <v-card-title>Please Associate the Image to the Task</v-card-title>
        <v-img
                :src="image.url"
                height="200px"
        ></v-img>

        <v-card-text>
            <v-autocomplete
                    :items="tasks"
                    v-model="task"
                    color="white"
                    item-text="name"
                    item-value="id"
                    label="Select A Task"
                    @blur="associateImageToTask"
            ></v-autocomplete>
        </v-card-text>
    </v-card>
</template>

<script>
  export default {
    name: 'ImageTask',
    data(){
      return {
        task: -1
      }
    },
    props: {
      image: Object,
      tasks: Array
    },
    methods: {
      associateImageToTask() {
        this.$emit('associate', this.task)
      }
    }
  }
</script>

<style scoped>
    .margins {
        margin-left: 1rem;
        margin-right: 1rem;
        margin-bottom: 1rem;
    }
</style>