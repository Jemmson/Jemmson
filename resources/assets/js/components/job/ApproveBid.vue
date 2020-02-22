<template>

    <!-- /customer approve bid form -->
    <form role="form" class="flex flex-col">
<!--        <div class="flex form-group">-->
<!--            <label for="job_location_same_as_home">Job Location Same as Home Location</label>-->
<!--            <input type="checkbox" class="" id="job_location_same_as_home"-->
<!--                   v-model="form.job_location_same_as_home">-->
<!--        </div>-->

<!--        <div class="flex form-group" :class="{'has-error': form.errors.has('agreed_start_date')}">-->
<!--            <label for="start_date">Start Date</label>-->
<!--            <input type="date" class="form-control" id="start_date" v-model="form.agreed_start_date">-->
<!--            <span class="help-block" v-show="form.errors.has('agreed_start_date')">-->
<!--                {{ form.errors.get('agreed_start_date') }}-->
<!--            </span>-->
<!--        </div>-->

        <!-- /job location -->
        <div v-show="!form.job_location_same_as_home" class="flex flex-col" ref="address-section">
            <!-- Address Line 1 -->
            <div class="flex form-group" :class="{'has-error': form.errors.has('address_line_1')}">
                <label for="">Address Line 1</label>
                <input type="text" class="form-control" name="address_line_1" id="route3"
                       v-model="form.address_line_1"
                       autofocus>
                <span class="help-block" v-show="form.errors.has('address_line_1')">
                    {{ form.errors.get('address_line_1') }}
                </span>
            </div>

            <!-- Address Line 2 -->
            <div class="flex form-group">
                <label for="">Address Line 2</label>
                <input type="text" class="form-control" name="address_line_2" v-model="form.address_line_2">
            </div>

            <!-- City -->
            <div class="flex form-group" :class="{'has-error': form.errors.has('city')}">
                <label class=" ">City</label>
                <input type="text" class="form-control" name="city" v-model="form.city">
                <span class="help-block" v-show="form.errors.has('city')">
                    {{ form.errors.get('city') }}
                </span>
            </div>

            <!-- State -->
            <div class="flex form-group" :class="{'has-error': form.errors.has('state')}">
                <label for="">State</label>
                <input type="text" class="form-control" name="state" v-model="form.state">
                <span class="help-block" v-show="form.errors.has('state')">
                    {{ form.errors.get('state') }}
                </span>
            </div>

            <!-- Zip Code -->
            <div class="flex form-group" :class="{'has-error': form.errors.has('zip')}">
                <label for="">ZipCode</label>
                <input type="text" class="form-control btn-normal" name="zip" v-model="form.zip">
                <span class="help-block" v-show="form.errors.has('zip')">
                    {{ form.errors.get('zip') }}
                </span>
            </div>
        </div>
        <div class="w-full">
           <v-row>
               <v-btn
                       class="w-full mb-half-rem"
                       color="primary"
                       @click.prevent="openModal('approveBid')"
                       :loading="disabled.approve"
                       ref="approve">
                   Approve
               </v-btn>
           </v-row>

            <v-row>
                <v-btn
                        class="w-40"
                        color="red"
                        @click.prevent="openModal('cancelBid')"
                        :loading="disabled.cancelBid"
                        ref="cancel">
                    Cancel
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                        class="w-40"
                        color="warning"
                        @click.prevent="openDeclineForm" ref="decline">
                    Change Bid
                </v-btn>
            </v-row>
        </div>
        <!-- / decline bid section -->
        <transition name="slide-fade">
            <div v-if="showDeclineForm" ref="decline-form" class="top">
                <!-- deny message -->
                <div class="form-group col-md-12" :class="{'has-error': form.errors.has('message')}">
                    <div class="text-center title" for="">Please input what you would like to change</div>
                    <input type="text" class="form-control" name="message" v-model="form.message"
                           placeholder="Optional Message">
                    <span class="help-block" v-show="form.errors.has('message')">
                        {{ form.errors.get('message') }}
                    </span>
                </div>
                <div class="form-group col-md-12">
                    <v-btn
                            class="w-40"
                            color="primary"
                            @click.prevent="declineBid" :loading="disabled.declineBid"
                            ref="declineBid">
                        Change Bid
                    </v-btn>
                </div>
            </div>
        </transition>
        <modal :header="modalHeader"
               :body="modalBody"
               :modalId="modalId"
               @modal="modalYes()"
               :yes="mYes"
               :no="mNo"></modal>
    </form>

</template>

<script>

  import Modal from '../shared/Modal'

  export default {
    props: {
      bid: Object
    },
    components: {
      Modal
    },
    data() {
      return {
        taskIndex: 0,
        form: new SparkForm({
          id: this.bid.id,
          agreed_start_date: '',
          end_date: '',
          area: '',
          status: this.bid.status,
          job_location_same_as_home: true,
          address_line_1: '',
          address_line_2: '',
          city: '',
          state: '',
          zip: '',
          message: ''
        }),
        modalCurrentlyOpenFor: '',
        modalHeader: '',
        modalBody: '',
        modalId: '',
        mYes: 'yes',
        mNo: 'no',
        disabled: {
          approve: false,
          declineBid: false,
          cancelBid: false
        },
        showDeclineForm: false,
        modalBody: Language.lang().modal.reviewBidConfirmationModal
      }
    },
    methods: {
      updateFormLocation(location) {
        console.log(location)

        this.form.address_line_1 = location.route
        this.form.city = location.locality
        this.form.state = location.administrative_area_level_1
        this.form.zip = location.postal_code
      },
      openModal(forBtn) {
        // update model header and body
        switch (forBtn) {
          case 'approveBid':
            this.updateModal(
              'Confirm Approval',
              'You are about to approve this bid. Click approve bid to approve or back to cancel this action.',
              'approveBid',
              'Approve Bid',
              'Back'
            )
            this.modalCurrentlyOpenFor = 'approveBid'
            break
          case 'cancelBid':
            this.updateModal(
              'Confirm Cancellation',
              'Are you sure you want to cancel this job? ' +
              ' To confirm please select Delete Job.',
              'cancelBid',
              'Delete Job',
              'Back'
            )
            this.modalCurrentlyOpenFor = 'cancelBid'
            break
        }

        // open model after content has been updated
        $('#modal').modal()
      },
      updateModal(header, body, id, yes, no) {
        this.modalHeader = header
        this.modalBody = body
        this.modalId = id
        this.mYes = yes
        this.mNo = no
      },
      modalYes() {
        switch (this.modalCurrentlyOpenFor) {
          case 'approveBid':
            this.approve()
            $('#modal').modal('hide')
            break
          case 'cancelBid':
            this.cancelBid()
            $('#modal').modal('hide')
            break
        }
      },
      openDeclineForm() {
        this.showDeclineForm
          ? (this.showDeclineForm = false)
          : (this.showDeclineForm = true)
      },
      approve(data) {
        if (this.form.id && this.bid.status) {
          Customer.approveBid(this.form, this.disabled)
        } else {
          this.form.id = this.bid.id
          this.form.status = this.bid.status
          Customer.approveBid(this.form, this.disabled)
        }
      },
      declineBid() {
        Customer.declineBid(this.form, this.disabled)
      },
      cancelBid() {
        Customer.cancelBid(this.bid, this.disabled)
      },
      initAutocomplete() {
        User.initAutocomplete('route3')
      }
    },
    mounted() {
      Bus.$on('updateFormLocation', payload => {
        this.updateFormLocation(payload)
      })

      let d = new Date()
      let month = d.getMonth() + 1
      let day = d.getDate()

      if (month < 10) {
        month = '0' + month
      }

      if (day < 10) {
        day = '0' + day
      }

      this.form.agreed_start_date = d.getFullYear() + '-' + month + '-' + day
      // this.agreed_start_date = '2018-07-03';
    }
  }
</script>

<style scoped>
    .btn-group {
        margin-bottom: 16px;
    }

    .top {
        margin-top: 1.5rem;
        border: black thin solid;
        border-radius: 5px;
        padding: .75rem .5rem 0rem .5rem;
    }

    .title {
        font-size: 1.5rem;
    }
</style>