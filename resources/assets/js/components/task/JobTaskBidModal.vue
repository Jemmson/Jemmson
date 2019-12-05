<template>
    <div class="modal h-100 modal-background-gray" id="job-task-bid-modal" tabindex="-1" role="dialog"
         aria-labelledby="stripe-modal"
         aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send Bid</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="jobTask !== null" class="modal-body">

                    <div class="row">
                        <div class="col-12 text-center">
                            <!--<label class="job-status"-->
                            <!--:class="getLabelClass(jobTask.status)">-->
                            <label class="mb-2 mt-2" :class="getLabelClass(jobTask.status)">
                                {{ status(jobTask) }}</label>
                            <label class="mt-2 mb-2">
                                - {{ jobName(jobTask.task.name) }}
                            </label>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="">Start On:</label>
                                <label class="float-right">{{ prettyDate(jobTask.start_date) }}</label>
                            </div>
                            <div class="form-group">
                                <label class="">Quantity: </label>
                                <label class="float-right">{{ jobTask.qty }}</label>
                            </div>
                            <div class="form-group">
                                <label class="">Contractor Suggested Price: </label>
                                <label class="float-right" v-if="jobTask.sub_final_price !== 0">
                                    ${{ jobTask.sub_final_price }}</label>
                                <label class="float-right" v-else>$ 0</label>
                            </div>

                            <div class="form-group">
                                <label v-if="isBidOpen(jobTask)" class="">Bid Price:</label>
                                <input v-if="jobTask.sub_sets_own_price_for_job === 1 && isBidOpen(jobTask)" type="text"
                                       class="form-control " v-bind:id="'price-' + jobTask.id"
                                       v-model="jobTask.bid_price"
                                       @keyup="bidPrice('price-' + jobTask.id)"/>
                                <input v-else-if="!jobTask.sub_sets_own_price_for_job === 1 && isBidOpen(jobTask)"
                                       type="text"
                                       class="form-control " v-bind:id="'price-' + jobTask.id"
                                       v-model="jobTask.bid_price"
                                       @keyup="bidPrice('price-' + jobTask.id)"/>
                            </div>

                            <div class="form-group">
                                <label v-if="!isBidOpen(jobTask)" class="">
                                    Accepted Bid Price:
                                </label>
                                <label v-if="!isBidOpen(jobTask)" class="">${{ jobTask.bid_price }}</label>
                            </div>

                            <div class="form-group">
                                <label class="">Job Address</label>
                                <div class="map-responsive">
                                    <iframe width="600" height="450" frameborder="0" style="border:0"
                                            :src="'https://www.google.com/maps/embed/v1/search?q='+ jobTask.location.address_line_1 + ' ' + jobTask.location.city + ' ' + jobTask.location.state + ' ' + jobTask.location.zip +'&key=AIzaSyCI21pbEus0AZc4whkqwM3VaDO1YV1Dygs'"
                                            allowfullscreen></iframe>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="">Payment Method</label>
                                <div v-if="jobTask.accepted === 1">
                                    <div class="">Selected Payment Method:</div>
                                    <div class=" uppercase">{{ paymentType }}</div>
                                    <div v-if="paymentType === 'cash'" class="">Payment Instructions</div>
                                    <div v-if="paymentType === 'cash'">{{ jobTask.job.paid_with_cash_message }}</div>
                                </div>
                                <div v-else>
                                    <div class="flex justify-around">
                                        <v-btn
                                                class="w-40"
                                                color="primary"
                                                :class="paymentType === 'stripe' ? 'btn-active' : 'btn-default'"
                                                @click="setPaymentType('stripe')">Stripe
                                        </v-btn>
                                        <v-btn
                                                class="w-40"
                                                color="primary"
                                                :class="paymentType === 'cash' ? 'btn-active' : 'btn-default'"
                                                @click="setPaymentType('cash')">Cash
                                        </v-btn>
                                        <v-btn
                                                class="w-40"
                                                color="primary"
                                                :class="paymentType === 'other' ? 'btn-active' : 'btn-default'"
                                                @click="setPaymentType('other')">other
                                        </v-btn>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="address-adjust">
                              <p v-if="getAddress(jobTask) !== 'Address Not Available'">
                                <a target="_blank" :href="'https://www.google.com/maps/search/?api=1&query=' + getAddress(jobTask)">
                                  <i class="fas fa-map-marker icon"></i>
                                  {{ getAddress(jobTask) }}
                                </a>
                              </p>
                              <p v-else>
                                <i class="fas fa-map-marker icon"></i>
                                {{ getAddress(jobTask) }}
                              </p>
                            </div> -->
                        </div>

                        <div class="col-12" v-if="showDeclinedMsg(jobTask.declined_message)">
                            <label class="">Declined Reason</label>
                            <p>
                                {{ jobTask.declined_message }}
                            </p>
                        </div>

                        <div class="col-12" v-if="jobTask.sub_message !== null && jobTask.sub_message != ''">
                            <h3 class="text-center m-2">Sub Instructions</h3>
                            <div class="text-center">
                                {{ jobTask.sub_message }}
                            </div>
                        </div>


                        <div class="col-12 mb-4">
                            <task-images class="images" :jobTask="jobTask" type="sub">
                            </task-images>
                        </div>


                        <div class="col-12">

                            <v-btn
                                    class="w-40"
                                    color="primary"
                                    v-if="isBidOpen(jobTask)"
                                    @click.prevent="update" v-bind:id="jobTask.id"
                                    :disabled="disabled.submit">
                  <span v-if="disabled.submit">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                                Submit
                            </v-btn>
                            <v-btn
                                    class="w-40"
                                    color="primary"
                                    v-if="showFinishedBtn(jobTask)"
                                    @click="finished(jobTask)"
                                    :disabled="disabled.finished">
                  <span v-if="disabled.finished">
                    <i class="fa fa-btn fa-spinner fa-spin"></i>
                  </span>
                                Finished
                            </v-btn>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

  import TaskImages from '../../components/task/UploadTaskImages'

  export default {
    components: {
      TaskImages
    },
    computed: {},
    mounted() {
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
      jobTask: Object
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
      setPaymentType(value) {
        this.paymentType = value
      },
      showBid(bid) {
        // TODO: backend what should happen to the bids that wheren't accepted
        if (bid === null) {
          return false
        }
        return (bid.id === bid.bid_id && (bid.job.status === 'job.approved' || bid.job.status === 'job.completed' || bid.status === 'bid_task.accepted')) || (bid.status ===
          'bid_task.bid_sent' || bid.status === 'bid_task.initiated')
      },
      getLabelClass(status) {
        return Format.statusLabel(status)
      },
      status(bid_task) {
        return User.status(bid_task.status, bid_task, true)
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
        let acceptedBid = bid.bid_id

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
      getAddress(jobTask) {
        console.log(JSON.stringify(jobTask.location))

        if (jobTask.location !== null) {
          return jobTask.location.address_line_1 + ' ' +
            jobTask.location.address_line_2 + ' ' +
            jobTask.location.city + ' ' +
            jobTask.location.state + ' ' +
            jobTask.location.zip
        } else {
          return 'Address Not Available'
        }

        // return jobTask.location.address_line_1+" "+

        // <a target="_blank" href="https://www.google.com/maps/search/?api=1&amp;query=3140 Talon Track Apt. 800  McCulloughton Utah 42620-5408">
        // let location_id = 0;
        // if (jobTask.location_id !== null) {
        //   location_id = jobTask.location_id;
        // } else {
        //   location_id = jobTask.job.location_id;
        // }
        // Customer.getAddress(location_id, this.location)
        // return this.location.location
      },
      showDeclinedMsg(msg) {
        return msg !== null && msg !== ''
      },
      showFinishedBtn(bid) {
        return bid.status === 'bid_task.approved_by_customer' || bid.status === 'bid_task.denied'
      },
      finished(bid) {
        SubContractor.finishedTask(bid, this.disabled)
      }
    }
  }
</script>

<style scoped>

    .btn-active {
        background-color: green;
        color: white;
    }

</style>
