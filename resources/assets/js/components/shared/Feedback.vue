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
                width="400">
            <v-card>
                <v-card-actions
                        align="center"
                        justify="center"
                >
                    <div
                            style="margin-left: auto; margin-right: auto;"
                            class="flex space-evenly"
                    >
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <div class="flex flex-col">
                                    <v-btn
                                            dark v-on="on"
                                            v-if="isContractor()"
                                            class="ma-2 white--text"
                                            color="primary"
                                            to="/initiate-bid"
                                            id="addbtn"
                                    >
                                        <v-icon dark>mdi-plus-circle</v-icon>
                                    </v-btn>
                                    <strong class="fob-btn-description">New Job</strong>
                                </div>
                            </template>
                            <span>Add A Job</span>
                        </v-tooltip>
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <div class="flex flex-col">
                                    <v-btn
                                            dark v-on="on"
                                            v-if="isContractor() && activeJobsExist()"
                                            color="primary"
                                            class="ma-2 white--text"
                                            @click="showJobs()"
                                    >
                                        <v-icon dark>mdi-worker</v-icon>
                                    </v-btn>
                                    <strong class="fob-btn-description">Add Task</strong>
                                </div>
                            </template>
                            <span>Add A Task</span>
                        </v-tooltip>
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on }">
                                <div class="flex flex-col">
                                    <v-btn
                                            dark v-on="on"
                                            color="primary"
                                            class="ma-2 white--text"
                                            @click="showFeedback()"
                                    >
                                        <v-icon dark>mdi-voice</v-icon>
                                    </v-btn>
                                    <strong class="fob-btn-description">Feedback</strong>
                                </div>
                            </template>
                            <span>Feedback</span>
                        </v-tooltip>
                    </div>
                </v-card-actions>
                <v-divider></v-divider>
                <div v-show="tasks">
                    <v-list
                            shaped
                            v-if="this.jobs && this.jobs.length > 0 ? true : false "
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
                    <div v-else>
                        <v-subheader>There Are No Active Jobs</v-subheader>
                    </div>
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
    mounted() {
      this.getJobs()
    },
    methods: {
      printHello() {
        return 'Hello'
      },
      showJobs() {
        this.tasks = true
        this.feedback = false
      },
      activeJobsExist() {
        if (this.jobs) {
          return this.jobs.length > 0
        }
      },
      open() {
        $('#feedback-modal').modal()
      },
      async getJobs() {
        let data = await axios.get('/getJobs')
        this.jobs = data.data
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

      isContractor() {
        if (Spark.state.user) {
          return Spark.state.user.usertype === 'contractor'
        }
      },
      submit() {
        let theComment = this.comment
        this.comment = ''
        User.submitFeedback(theComment, this.disabled)
        this.feedback = false
        this.dialog = false
      }
    }
  }
</script>

<style>
    #feedback {
        z-index: 1000;
    }

    .long {
        width: 45%;
    }

    .short {
        width: 205%;
    }

    .fob-btn-description {
        font-size: 10pt;
    }

</style>