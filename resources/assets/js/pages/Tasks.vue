<template>
    <div class="main flex flex-col justify-between">
        <div>
            <div class="search-bar shadow-md">
                <search-bar>
                    <input type="text" placeholder="Search Tasks" v-model="searchTerm" @keyup="search">
                </search-bar>
            </div>

            <paginate ref="paginator"
                      name="sTasks"
                      :list="sTasks"
                      :per="8"
                      class="paginated"
                      v-show="sTasks.length > 0">
                <div class=""
                     v-for="(bidTask, index) in paginated('sTasks')"
                     v-bind:key="bidTask.id"
                     :id="'task_' + bidTask.task_id"
                     style="z-index:2;">
                    <div class="task shadow-md flex flex-col" v-if="showBid(bidTask)">

                        <div class="task-box shadow-md">
                            <div class="flex items-start">
                                <label class="job-status mr-3"
                                       :class="getLabelClass(bidTask.job_task.status)">
                                    {{ status(bidTask) }}</label>
                                <label class="text-size w-full mt-2 ml-3 uppercase text-center">
                                    {{ jobName(bidTask.job_task.task.name) }}</label>
                            </div>

                            <div class="flex mt-1">
                                <button class="btn btn-sm btn-primary flex-1 mr-3" @click="showTheTask(index, 'show')">
                                    Show
                                </button>
                                <button class="btn btn-sm btn-primary flex-1 ml-3" @click="showTheTask(index, 'hide')">
                                    Hide
                                </button>
                            </div>
                        </div>

                        <div :id="'showTask' + index" style="display:none;">
                            <div class="w-full mt-6 shadow-md">

                                <div class="wrapper">
                                    <div>Start On:</div>
                                    <label class="font-normal">{{ prettyDate(bidTask.job_task.start_date) }}</label>
                                </div>

                                <!--<div v-if="isBidOpen(bidTask)" class="flex-1 flex-col">-->


                                <div class="wrapper" v-if="isBidOpen(bidTask)">

                                    <label for="details" class="w-full">Task Price:</label>


                                    <input v-if="bidTask.job_task.sub_sets_own_price_for_job === 1" type="text"
                                           class="form-control form-control-input" v-bind:id="'price-' + bidTask.id"
                                           v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)"/>
                                    <input v-else type="text" class="form-control form-control-input"
                                           v-bind:id="'price-' + bidTask.id"
                                           v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)"/>
                                </div>
                                <div v-else class="wrapper">
                                    <label class="margin-adjust">
                                        Accepted Bid Price:
                                    </label>
                                    <label class="margin-adjust">${{ bidTask.bid_price }}</label>
                                </div>

                                <hr class="hr">

                                <div class="wrapper">

                                    <label class="">QTY: </label>
                                    <label>{{ bidTask.job_task.qty }}</label>

                                    <label class="mt-1">Total: </label>
                                    <label class="mt-1"> ${{ bidTask.bid_price }}</label>
                                </div>

                                <hr class="hr">

                                <div class="address-adjust">
                                    <p v-if="getAddress(bidTask) !== 'Address Not Available'">
                                        <a target="_blank"
                                           :href="'https://www.google.com/maps/search/?api=1&query=' + getAddress(bidTask)">
                                            <i class="fas fa-map-marker icon"></i>
                                            {{ getAddress(bidTask) }}
                                        </a>
                                    </p>
                                    <p v-else>
                                        <i class="fas fa-map-marker icon"></i>
                                        {{ getAddress(bidTask) }}
                                    </p>
                                </div>

                                <hr class="hr" v-if="showDeclinedMsg(bidTask.job_task.declined_message)">

                                <!--<div class="flex border-b mb-4 p-2">-->
                                <div v-if="showDeclinedMsg(bidTask.job_task.declined_message)" class="wrapper">
                                    <label class="">Declined Reason</label>
                                    <p>
                                        {{ bidTask.job_task.declined_message }}
                                    </p>
                                </div>

                                <hr class="hr">

                                <div v-if="bidTask.job_task.sub_message !== null && bidTask.job_task.sub_message != ''"
                                     class="wrapper">
                                    <label>Sub Instructions</label>
                                    <p>
                                        {{ bidTask.job_task.sub_message }}
                                    </p>
                                </div>

                                <hr class="hr">

                                <task-images class="images" :jobTask="bidTask.job_task" type="sub">
                                </task-images>

                                <hr class="hr" v-if="isBidOpen(bidTask) || showFinishedBtn(bidTask)">

                                <div class="flex w-full flex-row-reverse pr-6 pt-6 pb-6">
                                    <button v-if="isBidOpen(bidTask)" class="btn btn-green" @click.prevent="update"
                                            v-bind:id="bidTask.id" :disabled="disabled.submit">
                                        <span v-if="disabled.submit">
                                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                                        </span>
                                        Submit
                                    </button>
                                    <button v-if="showFinishedBtn(bidTask)" class="btn btn-green"
                                            @click="finished(bidTask)"
                                            :disabled="disabled.finished">
                                        <span v-if="disabled.finished">
                                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                                        </span>
                                        Finished
                                    </button>
                                </div>


                            </div>

                        </div>

                    </div>
                </div>
            </paginate>

        </div>

        <div class="card p-5 card-body justify-center">
            <paginate-links for="sTasks" :limit="2" :show-step-links="true">
            </paginate-links>
        </div>
        <!-- / end tasks -->
        <stripe :user='user'>
        </stripe>
        <feedback></feedback>


    </div>
</template>


<script>

  import SearchBar from '../components/shared/SearchBar'
  import Feedback from '../components/shared/Feedback'
  import Stripe from '../components/stripe/Stripe'
  import Card from '../components/shared/Card'
  import TaskImages from '../components/task/UploadTaskImages'

  export default {
    props: {
      user: Object
    },
    components: {
      SearchBar,
      Feedback,
      Stripe,
      TaskImages,
      Card
    },
    data() {
      return {
        showTasks: {},
        paginate: ['sTasks'],
        address: '',
        location: {
          location: []
        },
        localArea: '',
        area: {
          area: ''
        },
        hello: 'world',
        tasks: [],
        sTasks: [],
        price: '',
        searchTerm: '',
        disabled: {
          submit: false,
          finished: false
        },
      }
    },
    methods: {
      showTheTask(index, action) {

        if (action === 'show') {
          let a = window.document.getElementById('showTask' + index);
          a.setAttribute('style', '')
                    // return true
        } else {
          let a = window.document.getElementById('showTask' + index);
          a.setAttribute('style', 'display:none;')
        }

        // for (let i = 0; i < this.tasks.length; i++) {
        // }
      },
      showDeclinedMsg(msg) {
        return msg !== null && msg !== ''
      },
      getLabelClass(status) {
        return Format.statusLabel(status)
      },
      search() {
        this.sTasks = this.tasks.filter((task) => {
          if (this.searchTerm == '' || this.searchTerm.length <= 1) {
            return true
          }
          return task.job_task.task.name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
        })
        if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
          this.$refs.paginator.goToPage(1)
        }
      },
      showBid(bid) {
        // TODO: backend what should happen to the bids that wheren't accepted
        if (bid.job_task === null) {
          return false
        }
        return (bid.id === bid.job_task.bid_id && (bid.job_task.job.status === 'job.approved' || bid.job_task.job.status === 'job.completed' || bid.job_task.status === 'bid_task.accepted')) || (bid.job_task.status ===
          'bid_task.bid_sent' || bid.job_task.status === 'bid_task.initiated')
      },
      jobName(name) {
        return Format.jobName(name)
      },
      bidPrice(target) {
        let price = $('#' + target).val().replace(/[^0-9.]/g, '')
        $('#' + target).val(price)
      },
      getArea(bidTask) {
        // console.log(bidTask)
        // debugger
        // Customer.getArea(bidTask.job_id, this.area)
        // this.localArea = this.area

        // return this.localArea.area
      },
      showAddress(bidTask) {
        const status = bidTask.job_task.status
        return status !== 'bid_task.initiated' && status !== 'bid_task.bid_sent' && status !== 'bid_task.finished_by_sub'
      },
      getAddress(bidTask) {
        console.log(JSON.stringify(bidTask.job_task.location))

        if (bidTask.job_task.location !== null) {
          return bidTask.job_task.location.address_line_1 + ' ' +
            bidTask.job_task.location.address_line_2 + ' ' +
            bidTask.job_task.location.city + ' ' +
            bidTask.job_task.location.state + ' ' +
            bidTask.job_task.location.zip
        } else {
          return 'Address Not Available'
        }

        // return bidTask.job_task.location.address_line_1+" "+

        // <a target="_blank" href="https://www.google.com/maps/search/?api=1&amp;query=3140 Talon Track Apt. 800  McCulloughton Utah 42620-5408">
        // let location_id = 0;
        // if (bidTask.job_task.location_id !== null) {
        //   location_id = bidTask.job_task.location_id;
        // } else {
        //   location_id = bidTask.job_task.job.location_id;
        // }
        // Customer.getAddress(location_id, this.location)
        // return this.location.location
      },
      showFinishedBtn(bid) {
        return bid.job_task.status === 'bid_task.approved_by_customer' || bid.job_task.status === 'bid_task.denied'
      },
      isBidOpen(bid) {
        let acceptedBid = bid.job_task.bid_id

        // the contractor has not chosen a bid for the
        // task yet
        if (acceptedBid === null) {
          return true
        }

        return false
      },
      status(bid_task) {
        return User.status(bid_task.job_task.status, bid_task.job_task, true)
      },
      prettyDate(date) {

        if (date == null)
          return ''
        // return the date and ignore the time
        date = date.split(' ')
        return date[0]
      },
      showStripeToggle(jobTask) {
        return jobTask.contractor_id === User.getId() && (jobTask.job.status === 'bid.initiated' || jobTask.job.status === 'bid.in_progress')
      },
      toggleStripePaymentOption(jobTask) {
        SubContractor.toggleStripePaymentOption(jobTask)
      },
      finished(bid) {
        SubContractor.finishedTask(bid, this.disabled)
      },
      update(e) {
        let id = e.target.id
        let bid_price = $('#price-' + id).val()
        this.disabled.submit = true
        console.log(id, bid_price)
        axios.put('/api/bid/task/' + id, {
          id: id,
          bid_price: bid_price
        }).then((response) => {
          // TODO: security review
          console.log(response)
          Vue.toasted.success('Bid Sent.')
          User.emitChange('bidUpdated')
          this.disabled.submit = false
        }).catch((error) => {
          console.log(error.response, '#error-' + id)
          Vue.toasted.error(error.response.data.message)
          this.disabled.submit = false
        })
      },
      getTasks() {
        console.log('getTasks')
        axios.post('/bid/tasks').then((response) => {
          this.tasks = response.data
          this.sTasks = this.tasks
        })
      }
    },
    created: function() {
      this.getTasks()
      Bus.$on('bidUpdated', (payload) => {
        this.getTasks()
      })
      Bus.$on('needsStripe', () => {
        $('#stripe-modal').modal()
      })

      window.Echo.private('')

    },
    mounted() {

      console.log(this.tasks)

      let i = -1

      // this.showTasks = this.tasks.map(function(task) {
      //   i++
      //   return this.showTasks[i]
      // }, i)

      for (let j = 0; j < this.tasks.length; j++) {
        this.showTasks[j] = false
      }

      const taskId = User.getParameterByName('taskId')
      if (taskId !== null && taskId !== '') {
        $('#task_' + taskId).addClass('info')
      }
      let success = this.$route.query.success
      if (success !== undefined) {
        success = Language.lang().sub.stripe_success
        Vue.toasted.success(success)
      }
      const error = this.$route.query.error
      Vue.toasted.error(error)
    }
  }
</script>

<style scoped>

    .images {
        margin: 1rem;
    }

    .address-adjust {
        padding: .5rem;
    }

    .hr {
        border: black solid thin;
    }

    .wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        /*margin-top: 1rem;*/
        /*padding: .75rem;*/
        padding: 1rem 1rem 1rem 1rem;
        /*grid-row-gap: 1rem;*/
    }

    .margin-adjust {
        margin-top: -1.5rem;
    }

    .form-control-input {
        /*width: 40%;*/
    }

    .text-size {
        font-size: 14pt;
    }

    .task-box {
        border: white thin solid;
        border-radius: 5px;
        margin: .1rem;
        padding: .75rem;
    }

    .task {
        width: 100%;
        padding: .25rem .25rem .25rem .25rem;
        /*border: black thin solid;*/
        border-radius: 5px;
    }

    .btn-primary {
        background-color: #3045a2;
    }

    .main {
        background-color: white;
        height: 200vh;
        padding: .25rem;
    }

    .search-bar {
        /*width: 100%;*/
        /*background-color: white;*/
        /*padding: .25rem .25rem 0rem .25rem;*/
        /*border: black thin solid;*/
    }

    .job-status {
        width: 100%;
        text-align: center;
        color: white;
        font-size: 14pt;
        margin-right: 1.75rem;
        /*margin: 1rem 1rem 1rem 1rem;*/
        padding: .5rem 1rem .5rem 1rem;
        border-radius: 5px;
    }

</style>