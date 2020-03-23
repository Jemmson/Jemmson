<template>
    <v-card class="margins">
        <v-card-title class="capitalize w-break">Please Associate the Image to a Task</v-card-title>
        <v-img
                :src="image ? image.url : null"
                height="200px"
        ></v-img>

        <v-card-text>
            <v-autocomplete
                    :items="tasks"
                    v-model="task"
                    class="capitalize"
                    color="white"
                    item-text="name"
                    item-value="id"
                    label="Select A Task"
                    @change="associateImageToTask"
            ></v-autocomplete>

<!--            <v-card-text>-->
<!--                <div class="flex flex-col">-->
<!--                    <div class="flex"-->
<!--                         style="align-items: center;"-->
<!--                         v-for="item in taskArray" :key="item.id">-->
<!--                        <div-->
<!--                                class="capitalize"-->
<!--                                style="font-size:12pt"-->
<!--                        >{{ getTaskName(item) }}-->
<!--                        </div>-->
<!--                        <v-btn-->
<!--                                icon-->
<!--                                @click="x(item)"-->
<!--                        >-->
<!--                            <v-icon right>mdi-close-circle</v-icon>-->
<!--                        </v-btn>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </v-card-text>-->

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                        :disabled="!task"
                        color="primary"
                        @click="clearAll()"
                >
                    Clear All
                    <v-icon right>mdi-close-circle</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card-text>
    </v-card>
</template>

<script>
  export default {
    name: 'ImageTask',
    data() {
      return {
        task: null,
        taskArray: []
      }
    },
    props: {
      image: Object,
      tasks: Array
    },
    methods: {

      getTaskName(item) {
        if (this.tasks) {
          for (let i = 0; i < this.tasks.length; i++) {
            if (this.tasks[i].id === item) {
              return this.tasks[i].name
            }
          }
        }
      },

      clearTask(task) {
        this.removeTaskId(task)
        this.$emit('clearTask', task)
        if (this.taskArrayIsEmpty()) {
          this.noSelectedTasks()
        }
      },

      duplicateExists(jobTaskId) {
        for (let i = 0; i < this.taskArray.length; i++) {
          if (this.taskArray[i] === jobTaskId) {
            return true
          }
        }
        return false
      },

      taskArrayIsEmpty() {
        return this.taskArray.length === 0
      },

      noSelectedTasks() {
        this.task = null
      },

      clearAll() {
        this.noSelectedTasks()
        this.taskArray = []
        this.$emit('clearAllTasks')
      },

      removeTaskId(jobTaskId) {
        for (let i = 0; i < this.taskArray.length; i++) {
          if (this.taskArray[i] === jobTaskId) {
            this.taskArray.splice(i, 1)
          }
        }
      },

      associateImageToTask() {
        this.$emit('associate', this.task)
        // if (!this.duplicateExists(this.task)) {
        //   this.taskArray.push(this.task)
        //   this.$emit('associate', this.task)
        // }
      }
    }
  }
</script>

<style scoped>
</style>