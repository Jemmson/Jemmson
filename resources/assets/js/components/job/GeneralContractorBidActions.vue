<template>
    <div>

        <div ref="subTaskWarning"
             class="text-white btn-red rounded p-3 mt-2 mb-2 text-center"
             style="font-weight: 700"
             v-if="subTaskWarning">
            PLEASE CHECK TASKS. SOME TASKS HAVE SUB PRICES HIGHER THAN CONTRACTOR PRICE
        </div>

        <button ref="submitBid"
                class="btn btn-normal btn-sm"
                @click="notifyCustomerOfFinishedBid()">Submit Bid</button>
<!--                @click="openBidSubmissionDialog()">Submit Bid</button>-->

    </div>

</template>

<script>
    export default {
      name: 'GeneralContractorBidActions',
      props: {
        submitTheBid: Boolean,
        bid: Object
      },
      watch: {
        submitTheBid: this.notifyCustomerOfFinishedBid
      },
      data(){
        return {
          subTaskWarning: false
        }
      },
      methods: {
        openBidSubmissionDialog() {
          return this.$emit('open-bid-submission', true)
        },
        notifyCustomerOfFinishedBid(){
          // go through each job task and compare the sub price to the contractor task price
          // first check if there is a sub.
          // check if the sub price is an accepted price
          // compare the the accepted sub price to the contractor price
          // if the accepted sub price is higher then throw an error


          this.subTaskWarning = false
          for (let i = 0; i < this.bid.job_tasks.length; i++) {
            if (this.bid.job_tasks[i].sub_final_price > this.bid.job_tasks[i].cust_final_price) {
              console.log('sub final price')
              console.log(this.bid.job_tasks[i].sub_final_price)
              console.log('cust final price')
              console.log(this.bid.job_tasks[i].cust_final_price)
              this.subTaskWarning = true
            }
          }

          if (!this.subTaskWarning) {
            GeneralContractor.notifyCustomerOfFinishedBid(this.bid, this.disabled)
          }

        }
      }
    }
</script>

<style>

</style>
