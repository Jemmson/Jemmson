<template>
    <!-- /contractor bid actions -->
    <div class="col-md-12">
                <button class="btn btn-sm btn-primary button" @click="openAddTask">
                    Add Task To Bid
                </button>
                <button class="btn btn-sm btn-warning button" @click="openModal('notifyCustomerOfFinishedBid')">
                    Notify Customer of Finished Bid
                </button>
        <modal :header="modalHeader" :body="modalBody" :modalId="modalId" @modal="modalYes()">
        </modal>
    </div>
</template>

<script>
  export default {
      props: {
          bid: Object,
          show: false,
      },
      data() {
          return {
              userType: '',
              modalCurrentlyOpenFor: '',
              modalHeader: '',
              modalBody: '',
              modalId: '',
          }
      },
      methods: {
          openModal(forBtn) {
              // update model header and body
              switch (forBtn) {
                  case 'notifyCustomerOfFinishedBid':
                      this.updateModal('Bid Finished', 'You are about to submit this job bid to the customer,' +
                          'you will not be able to edit this bid after its been submitted.' +
                          ' Click yes to submit or no to cancel.',
                          'notifyCustomerOfFinishedBid');
                      this.modalCurrentlyOpenFor = 'notifyCustomerOfFinishedBid';
                      break;
              }

              // open model after contect has been udpated
              $('#modal').modal();
          },
          updateModal(header, body, id) {
              this.modalHeader = header;
              this.modalBody = body;
              this.modalId = id;
          },
          modalYes() {
              switch (this.modalCurrentlyOpenFor) {
                  case 'notifyCustomerOfFinishedBid':
                      this.$emit('notifyCustomerOfFinishedBid');
                      $('#modal').modal('hide');
                      break;
              }
          },
          openAddTask() {
              this.$emit('openAddTask');
          }
      },
      mounted: function () {
          this.userType = Spark.state.user.usertype;
      }
  }
</script>