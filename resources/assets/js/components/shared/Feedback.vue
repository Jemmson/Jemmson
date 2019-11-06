<template>
    <div>
        <v-btn
                fab
                class="elevation-10"
                color="primary"
                id="feedback"
                style="position:fixed; bottom: 11px; right: 11px; margin-bottom: 4rem"
                @click="dialog = true"
        >
            <v-icon>mdi-navigation</v-icon>
        </v-btn>

        <v-dialog
                v-model="dialog"
                width="400"
        >
            <v-card>
                <v-card-title class="text-center">Shortcuts</v-card-title>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn
                            color="primary"
                            to="/initiate-bid"
                    >ADD JOB
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="primary"
                            @click="getJobs()"
                    >
                        ADD TASK
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="primary"
                            @click="showFeedback()"
                    >
                        ADD FEEDBACK
                    </v-btn>
                </v-card-actions>
                <v-divider></v-divider>
                <div v-show="tasks">
                    <v-list
                            shaped
                    >
                        <v-subheader>Which Job Would You Like To Add A Task To?</v-subheader>
                        <v-list-item-group
                                v-model="item"
                                color="primary"
                        >
                            <v-list-item
                                    v-for="(item, i) in jobs"
                                    :key="i"
                                    :to="'/bid/' + item.id"
                            >
                                <v-list-item-icon>
                                    <v-icon>mdi-tools</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title v-text="item ? item.job_name: ''"></v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </div>
                <div v-show="feedback">
                        <v-sheet
                                elevation="2"
                                class="pa-12"
                        >
                            <v-textarea
                                    v-model="comment"
                                    :auto-grow="true"
                                    :clearable="true"
                                    :label="label"
                                    :placeholder="placeholder"
                                    :rows="rows"
                            ></v-textarea>
                            <v-btn
                                    @click="submit"
                                    color="primary"
                            >
                                Submit
                            </v-btn>
                        </v-sheet>
                </div>
            </v-card>

        </v-dialog>
    </div>
</template>

<script>
  export default {
    data: function() {
      return {
        dialog: false,
        disabled: {
          submit: false,
        },
        tasks: false,
        subs: false,
        item: '',
        jobs: [],
        subTasks: [],
        feedback: false,
        label: 'App Feedback',
        comment: '',
        placeholder: 'Please tell us what you think about the site. What can be improved, something missing or whatever works well.',
        rows: 5
      }
    },
    props: {
      page: String
    },
    methods: {
      open() {
        $('#feedback-modal').modal()
      },
      async getJobs() {
        let data = await axios.get('/getJobs')
        this.jobs = data.data
        this.tasks = true
        this.feedback = false
      },
      async getTasks() {
        let data = await axios.get('/getTasks')
        this.subTasks = data.data
        this.subs = true
      },
      showFeedback() {
        this.tasks = false
        this.feedback = true
      },
      submit() {
        let theComment = this.comment
        this.comment = ''
        User.submitFeedback(theComment, this.disabled)
        this.feedback = false
      }
    }
  }
</script>

<style>
    #feedback {
        z-index: 1000;
    }
</style>