Parent

1. Import Info modal
    import InfoModalGeneric from '../components/documentation/InfoModalGeneric'
2. Add To components
    components: {
        InfoModalGeneric
    },
3. Place Triggering Icon on the DOM
    <v-icon
            color="primary"
            @click="showModal('paymentType')"
            class="ml-1rem">mdi-information
    </v-icon>
4. Add Method into methods
    showModal(modal) {
        if (modal === 'paymentType') {
            this.modal.paymentTypeInfoDialog = true;
            return false;
        }
        if (modal === 'createJob') {
            this.modal.createJobDialog = true;
            return false;
        }
    },
    closeModal(modal) {
        if (modal === 'paymentType') {
            this.modal.paymentTypeInfoDialog = false;
            return false;
        }
        if (modal === 'createJob') {
            this.modal.createJobDialog = false;
            return false;
        }
    },
5. Add data element into the data object
    data() {
        return {
            modal: {
                paymentTypeInfoDialog: false,
                createJobDialog: false,
            },
            modalText: {
                 paymentTypeText: `Every job is either a cash job or a credit card job.
                     To handle credit payments you will need to setup with Stripe. Stripe handles all of our
                     credit card processing. You will have an account with stripe and you will collect payments there.
                     If you have a stripe account already then you will connect to your existing stripe account.
                 `,
                 createJobText: ``
             },
        }
    }
6. Add the component to the dom
    <info-modal-generic
            :text="modalText.paymentTypeText"
            title="Payment Types"
            modal="paymentType"
            :open-dialog="modal.paymentTypeInfoDialog"
            @closeModal="closeModal($event)">
    </info-modal-generic>
    <info-modal-generic
            :text="modalText.createJobText"
            title="Create A New Job"
            :open-dialog="modal.createJobDialog"
            modal="createJob"
            @closeModal="closeModal($event)">
    </info-modal-generic>
    
        - text is the info to be displayed
        - modal is the particular modal to close
        - open-dialog triggers the modal to open
        - @closeModal is an event to trigger the modal to close
