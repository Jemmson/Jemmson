<template>
    <div class="task shadow-md flex flex-col" v-if="showBid(bidTask)">
        <card class="list-card">
            <section class="flex flex-col">
                <div class="flex justify-content-around text-center">
                    <header ref="" class="page-header-title w-full"
                            :class="getLabelClass(bidTask)">
                        {{ status(bidTask) }}
                    </header>
                    <header ref="" class="text-size w-full uppercase text-center">
                        {{ jobName(bidTask.job_task.task.name) }}
                    </header>
                </div>

                <div class="flex mt-1 w-full">
                    <div class="w-full mr-1rem">
                        <button
                                class="btn btn-sm btn-normal w-full"
                                @click="showTheTask = true"
                                v-show="!showTheTask">
                            Show
                        </button>
                        <button
                                class="btn btn-sm btn-normal w-full"
                                @click="showTheTask = false"
                                v-show="showTheTask">
                            Hide
                        </button>
                    </div>
                    <div class="w-full ml-1rem">
                        <button class="btn btn-normal btn-sm w-full mr-1rem"
                                @click="showDeleteTaskModal(bidTask.job_task)">DELETE
                        </button>
                    </div>
                </div>
            </section>
        </card>

        <div v-show="showTheTask">


            <section class="col-12">
                <h1 class="card-title">Task Details</h1>
                <card>
                    <main class="row">
                        <content-section
                                label="Start On:"
                                :content="prettyDate(bidTask.job_task.start_date)"
                                type="startOn"></content-section>
                        <content-section
                                label="Quantity:"
                                :content="bidTask.job_task.qty.toString()"
                                type="quantity"></content-section>
                        <content-section
                                v-if="bidTask.job_task.sub_final_price !== 0"
                                label="Contractor Base Price:"
                                :content="convertNumToString(bidTask.job_task.sub_final_price)"
                                :dollar-sign=true
                                type="subFinalPrice"></content-section>
                        <content-section
                                v-else
                                label="Contractor Base Price:"
                                :content="zero()"
                                :dollar-sign=true
                                type="subFinalPrice"></content-section>

                        <div style="display:none;">{{ getStoredBidPrice }}</div>


                        <div class="flex justify-content-between w-full mr-1rem mt-half-rem">

                            <label v-if="isBidOpen(bidTask)" class="w-full ml-1rem mt-half-rem">Task Price:</label>

                            <input v-if="bidTask.job_task.sub_sets_own_price_for_job === 1 && isBidOpen(bidTask)"
                                   type="text"
                                   class="form-control form-control-sm w-40" v-bind:id="'price-' + bidTask.id"
                                   v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)"/>


                            <input v-else-if="!bidTask.job_task.sub_sets_own_price_for_job === 1 && isBidOpen(bidTask)"
                                   type="text" class="form-control form-control-sm w-40"
                                   v-bind:id="'price-' + bidTask.id"
                                   v-model="bidTask.bid_price" @keyup="bidPrice('price-' + bidTask.id)"/>
                        </div>

                        <div class="flex justify-content-between w-full mr-1rem mt-half-rem">
                            <label v-if="!isBidOpen(bidTask)" class="ml-1rem">
                                Accepted Bid Price:
                            </label>
                            <label v-if="!isBidOpen(bidTask)" class=""><strong>$
                                {{ getSubFinalPrice(bidTask.job_task.sub_final_price) }}</strong></label>
                        </div>
                    </main>
                </card>
            </section>


            <!--<div v-if="isBidOpen(bidTask)" class="flex-1 flex-col">-->


            <!--            <section class="col-12">-->
            <!--                <h1 class="card-title">Payment Method</h1>-->
            <!--                <card>-->
            <!--                    <main class="row">-->

            <!--                        <div class="w-full" v-if="bidTask.accepted === 1">-->

            <!--                            <content-section-->
            <!--                                    label="Selected Payment Method:"-->
            <!--                                    :content="bidTask.payment_type"-->
            <!--                                    type="paymentType"></content-section>-->

            <!--                            <content-section-->
            <!--                                    v-if="bidTask.payment_type === 'cash'"-->
            <!--                                    label="Payment Instructions:"-->
            <!--                                    :content="bidTask.job_task.job.paid_with_cash_message"-->
            <!--                                    type="paymentType"></content-section>-->
            <!--                        </div>-->
            <!--                        <div v-else class="w-full">-->
            <!--                            <div class="text-center m-2 w-full pb-half-rem"><strong>Preferred Payment Method?</strong>-->
            <!--                            </div>-->
            <!--                            <div class="flex justify-content-around w-full">-->
            <!--                                <button class="btn btn-sm btn-normal flex-1"-->
            <!--                                        :class="paymentType === 'stripe' ? 'btn-active' : 'btn-inactive'"-->
            <!--                                        @click="setPaymentType('stripe')">Stripe-->
            <!--                                </button>-->
            <!--                                <button class="btn btn-sm btn-normal flex-1 mr-6 ml-6"-->
            <!--                                        :class="paymentType === 'cash' ? 'btn-active' : 'btn-inactive'"-->
            <!--                                        @click="setPaymentType('cash')">Cash-->
            <!--                                </button>-->
            <!--                                <button class="btn btn-sm btn-normal flex-1"-->
            <!--                                        :class="paymentType === 'other' ? 'btn-active' : 'btn-inactive'"-->
            <!--                                        @click="setPaymentType('other')">Other-->
            <!--                                </button>-->
            <!--                            </div>-->
            <!--                        </div>-->

            <!--                        &lt;!&ndash;                        <content-section&ndash;&gt;-->
            <!--                        &lt;!&ndash;                                label="asassa"&ndash;&gt;-->
            <!--                        &lt;!&ndash;                                :content="saassaas"&ndash;&gt;-->
            <!--                        &lt;!&ndash;                                type="sasaasas"></content-section>&ndash;&gt;-->


            <!--                    </main>-->
            <!--                </card>-->
            <!--            </section>-->


            <section class="col-12">
                <h1 class="card-title">Job Address</h1>
                <card>
                    <main class="row">
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
                    </main>
                </card>
            </section>


            <section class="col-12">
                <h1 class="card-title">Messages</h1>
                <card>
                    <main class="row">
                        <content-section
                                v-if="showDeclinedMsg(bidTask.job_task.declined_message)"
                                label="Declined Reason:"
                                :content="bidTask.job_task.declined_message"
                                type="declinedReason"></content-section>
                        <content-section
                                v-if="bidTask.job_task.sub_message !== null && bidTask.job_task.sub_message != ''"
                                label="Sub Instructions:"
                                :content="bidTask.job_task.sub_message"
                                type="subInstructions"></content-section>
                    </main>
                </card>
            </section>

            <section class="col-12">
                <h1 class="card-title">Images</h1>
                <card>
                    <main class="row">
                        <task-images class="images w-full ml-one-quarter-negative" :jobTask="bidTask.job_task"
                                     type="sub">
                        </task-images>
                    </main>
                </card>
            </section>

            <section class="col-12">
                <h1 class="card-title">Actions</h1>
                <card>
                    <main class="row">
                        <content-section
                                label="Job Status:"
                                :content="status(bidTask)"
                                :input-classes="getLabelClass(bidTask.job_task.status)"
                                :section-classes="(isBidOpen(bidTask) || showFinishedBtn(bidTask)) ? 'border-bottom-thick-black' : ''"
                                type="startOn"></content-section>
                        <button v-if="isBidOpen(bidTask)" class="btn btn-normal mt-one-and-one-quarter-rem"
                                @click.prevent="update"
                                v-bind:id="bidTask.id" :disabled="disabled.submit">
                                        <span v-if="disabled.submit">
                                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                                        </span>
                            Submit
                        </button>
                        <button v-if="showFinishedBtn(bidTask)" class="btn btn-normal mt-1rem"
                                @click="finished(bidTask)"
                                :disabled="disabled.finished">
                                        <span v-if="disabled.finished">
                                          <i class="fa fa-btn fa-spinner fa-spin"></i>
                                        </span>
                            Finished
                        </button>
                    </main>
                </card>
            </section>

        </div>

        <delete-task-modal
                @action="deleteTheTask($event)"
        >
        </delete-task-modal>

    </div>
</template>

<script>

  import TaskImages from '../../components/task/UploadTaskImages'
  import ContentSection from '../shared/ContentSection'
  import DeleteTaskModal from '../../components/job/DeleteTaskModal'
  import Card from '../shared/Card'

  export default {
    name: 'Task',
    components: {
      TaskImages,
      ContentSection,
      DeleteTaskModal,
      Card
    },
    updated() {
      this.getStoredBidPrice
    },
    computed: {
      getStoredBidPrice() {
        if (localStorage.getItem('bidPrice' + this.bidTask.id)) {
          this.bidTask.bid_price = localStorage.getItem('bidPrice' + this.bidTask.id)
        }
      }
    },
    mounted() {
      this.paymentType = this.bidTask.payment_type
    },
    data() {
      return {
        paymentType: 'cash',
        showTheTask: false,
        disabled: {
          submit: false,
          finished: false,
          deleteTask: false
        },
        deleteTask: {
          id: ''
        }
      }
    },
    props: {
      bidTask: Object
    },
    methods: {
      showDeleteTaskModal(job_task) {
        this.deleteTask.id = job_task.id
        this.jobTask = job_task
        $('#delete-task-modal').modal('show')
      },
      deleteTheTask(action) {
        if (action === 'delete') {
          this.deleteTheActualTask(this.deleteTask.id)
        }
        $('#delete-task-modal').modal('hide')
      },
      async deleteTheActualTask(id) {
        try {
          const data = await axios.post('/jobTask/delete/', {
            id: id
          })
          this.getBid(this.job_task.job.id)
        } catch (error) {
          console.log('error')
        }
      },
      getSubFinalPrice(num) {
        return this.convertNumToString(num)
      },
      convertNumToString(num) {
        let initialVal = num.toString()
        num = num / 100
        let finalVal = num.toString()

        if (finalVal.length === initialVal.length - 1) {
          finalVal = finalVal + '0'
        } else if (finalVal.length === initialVal.length - 2) {
          finalVal = finalVal + '.00'

        }
        return finalVal
      },
      zero() {
        let zero = 0
        return zero.toString()
      },
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
        if (bid.job_task === null) {
          return false
        }
        return (bid.id === bid.job_task.bid_id && (bid.job_task.job.status === 'job.approved' || bid.job_task.job.status === 'job.completed' || bid.job_task.status === 'bid_task.accepted')) || (bid.job_task.status ===
          'bid_task.bid_sent' || bid.job_task.status === 'bid_task.initiated')
      },
      getLabelClass(bidTask) {

        if (bidTask && bidTask.job_task) {

          return Format.statusLabel(
            bidTask.job_task.status,
            this.isGeneral(bidTask),
            this.isCustomer(bidTask),
            bidTask
          )

          // if (this.isUserTheGeneral(bidTask)) {
          //   return Format.statusLabel(
          //     bidTask.job_task.status,
          //     this.isGeneral(bidTask),
          //     this.isCustomer(bidTask),
          //     bidTask
          //   )
          // } else {
          //   return Format.statusLabel(bidTask.status)
          // }
        }

      },
      isGeneral(bidTask) {
        if (bidTask && bidTask.job_task) {
          return Spark.state.user.id === bidTask.job_task.contractor_id
        }
      },
      isCustomer(bidTask){
        if (bidTask && bidTask.job_task) {
          return Spark.state.user.usertype === "customer"
        }
      },
      status(bid_task) {
        return User.status(bid_task.status, bid_task.job_task, false)
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
        localStorage.setItem('bidPrice' + this.bidTask.id, this.bidTask.bid_price)
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

    .list-card {
        margin-left: 0rem !important;
    }

    .blue {
        /*background-color: #1c3d5a;*/
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
        grid-row-gap: .5rem;
        /*margin-top: 1rem;*/
        /*padding: .75rem;*/
        /*grid-row-gap: 1rem;*/
    }

    .p-1rem {
        padding: 1rem 1rem 1rem 1rem;
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

    .box {
        border: white thin solid;
        border-radius: 5px;
        margin: 1rem .1rem 1rem .1rem;
        background-color: #3490dc52;
        padding: .75rem;
    }

    .space {
        padding-left: 2rem;
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
