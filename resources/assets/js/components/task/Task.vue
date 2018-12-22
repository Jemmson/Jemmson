<template>
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
                <button class="btn btn-sm btn-primary flex-1 mr-3" @click="showTheTask = true">
                    Show
                </button>
                <button class="btn btn-sm btn-primary flex-1 ml-3" @click="showTheTask = false">
                    Hide
                </button>
            </div>
        </div>

        <div v-show="showTheTask">
            <div class="w-full mt-6 shadow-md">

                <div class="wrapper">
                    <div>Start On:</div>
                    <label class="font-normal">{{ prettyDate(bidTask.job_task.start_date) }}</label>
                </div>

                <!--<div v-if="isBidOpen(bidTask)" class="flex-1 flex-col">-->


                <div class="wrapper" v-if="isBidOpen(bidTask)">

                    <label class="w-full">Task Price:</label>


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

                <h4 class="text-center m-2">How do you prefer to be paid for this task?</h4>
                <div class="flex justify-around">
                    <button class="btn btn-md blue flex-1" :class="paymentType === 'stripe' ? 'btn-active' : 'btn-inactive'" @click="setPaymentType('stripe')">Stripe</button>
                    <button class="btn btn-md blue flex-1 mr-6 ml-6" :class="paymentType === 'cash' ? 'btn-active' : 'btn-inactive'" @click="setPaymentType('cash')">Cash</button>
                    <button class="btn btn-md blue flex-1" :class="paymentType === 'other' ? 'btn-active' : 'btn-inactive'" @click="setPaymentType('other')">other</button>
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
</template>

<script>

  import TaskImages from '../../components/task/UploadTaskImages'

  export default {
    name: 'Task',
    components: {
      TaskImages
    },
    computed: {},
    mounted () {
      this.paymentType = this.bidTask.payment_type;
    },
    data() {
      return {
        paymentType: 'cash',
        showTheTask: false,
        disabled: {
          submit: false,
          finished: false
        }
      }
    },
    props: {
      bidTask: Object
    },
    methods: {
      update(e) {
        let id = e.target.id
        // debugger;
        let bid_price = $('#price-' + id).val()
        let po = this.paymentType
        this.disabled.submit = true
        console.log(id, bid_price)
        axios.put('/api/bid/task/' + id, {
          id: id,
          bid_price: bid_price,
          paymentType: po
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
      setPaymentType (value) {
        this.paymentType = value;
      },
      showBid(bid) {
        // TODO: backend what should happen to the bids that wheren't accepted
        if (bid.job_task === null) {
          return false
        }
        return (bid.id === bid.job_task.bid_id && (bid.job_task.job.status === 'job.approved' || bid.job_task.job.status === 'job.completed' || bid.job_task.status === 'bid_task.accepted')) || (bid.job_task.status ===
          'bid_task.bid_sent' || bid.job_task.status === 'bid_task.initiated')
      },
      getLabelClass(status) {
        return Format.statusLabel(status)
      },
      status(bid_task) {
        return User.status(bid_task.job_task.status, bid_task.job_task, true)
      },
      jobName(name) {
        return Format.jobName(name)
      },
      prettyDate(date) {

        if (date == null)
          return ''
        // return the date and ignore the time
        date = date.split(' ')
        return date[0]
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
      bidPrice(target) {
        let price = $('#' + target).val().replace(/[^0-9.]/g, '')
        $('#' + target).val(price)
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
      showDeclinedMsg(msg) {
        return msg !== null && msg !== ''
      },
      showFinishedBtn(bid) {
        return bid.job_task.status === 'bid_task.approved_by_customer' || bid.job_task.status === 'bid_task.denied'
      },
      finished(bid) {
        SubContractor.finishedTask(bid, this.disabled)
      }
    }
  }
</script>

<style scoped>

    .blue {
        /*background-color: #1c3d5a;*/
    }

    .btn-active {
        background-color: orangered;
    }

    .btn-inactive {
        background-color: #1c3d5a;
    }

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
