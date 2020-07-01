<template>
    <!-- /all bids shown in a list as a customer should see it -->

    <div class="container-fluid" :class="getTopMargin()">

        <div v-if="bidsContractorSectionPicked" ref="jobs">
            <h2 class="text-center uppercase black--text" style="margin-bottom: 2rem;">Jobs Page</h2>
            <search-bar>
                <input type="text" class="form-control" placeholder="Search Jobs" v-model="searchTerm" @keyup="search">
            </search-bar>

            <info-modal-generic
                    :text="modalText.jobs"
                    title="Jobs"
                    modal="jobs"
                    :open-dialog="modal.jobs"
                    @close-modal="closeModal($event)"
            >
            </info-modal-generic>

            <v-icon
                    color="primary"
                    @click="showHelp()"
                    class="w-break mt-3 pr-3 w-95 justify-content-end">mdi-information
            </v-icon>

            <v-card
                    v-for="bid in sBids" v-bind:key="bid.id"
                    class="margins-quarter-rem"
                    v-if="getJobStatus(bid) !== 'paid'"
            >
                <v-card-title>{{ jobName(bid.job_name) }}</v-card-title>
                <v-card-subtitle>{{ getJobStatus(bid) }}</v-card-subtitle>
                <v-card-subtitle
                        v-if="bid.payment_type === 'cash'"
                >
                    <v-icon
                    >mdi-cash
                    </v-icon>
                    Cash Job
                </v-card-subtitle>
                <v-card-subtitle
                        v-else-if="bid.payment_type === 'creditCard'"
                >
                    <v-icon
                    >mdi-credit-card
                    </v-icon>
                    Credit Card Job
                </v-card-subtitle>

                <v-card-text>
                    <v-card-subtitle v-if="isContractor()"
                                     class="p-0"
                    >
                                     <span ref="total_number_of_subs"
                                           class="float-right list-card-info">
                                            {{ totalNumberOfSubsBiddingForTheJob(bid.job_tasks) }} Subs
                                        <i class="fas fa-users"></i>
                                     </span>

                        <span class="float-right mr-2 list-card-info"
                              ref="show_number_of_job_tasks">
                                            {{ bid.job_tasks_length }} Tasks
                                        <i class="far fa-check-square"></i>
                                    </span>
                    </v-card-subtitle>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn
                            class="w-40"
                            text
                            color="red"
                            @click="showDeleteJobModal(bid)"
                    >
                        DELETE JOB
                    </v-btn>

                    <v-spacer></v-spacer>

                    <v-btn
                            @click="goToJob(bid.id)"
                            text
                            color="primary"
                            class="w-40"
                    >VIEW JOB
                    </v-btn>

                </v-card-actions>

            </v-card>
        </div>
        <tasks v-else>
        </tasks>

        <delete-task-modal
                @action="deleteTheJob($event)"
                title="Do You Wish To Delete This Job?"
        >
        </delete-task-modal>
        <feedback
                page="jobs"
        ></feedback>
    </div>
</template>

<script>
    import {mapState, mapMutations} from 'vuex'
    import Tasks from './Tasks'
    import SearchBar from '../components/shared/SearchBar'
    import Card from '../components/shared/Card'
    import DeleteTaskModal from '../components/job/DeleteTaskModal'
    import Feedback from '../components/shared/Feedback'
    import Status from '../components/mixins/Status'
    import InfoModalGeneric from '../components/documentation/InfoModalGeneric'

    export default {
        name: 'Jobs',
        components: {
            Tasks,
            Card,
            SearchBar,
            Feedback,
            InfoModalGeneric,
            DeleteTaskModal
        },
        mixins: [
            Status
        ],
        props: {
            user: Object
        },
        data() {
            return {
                modal: {
                    jobs: false
                },
                modalText: {
                    jobs: ''
                },
                bids: [],
                sBids: [],
                showBid: false,
                bidIndex: 0,
                searchTerm: '',
                paginate: ['sBids'],
                disabled: {
                    deleteJob: false
                },
                deleteJob: {
                    id: ''
                },
                job: {}
            }
        },
        watch: {
            '$route'(to, from) {
                // get the bids
                this.getBids()
            }
        },
        computed: {
            ...mapState({
                page: state => state.page,
                bidsContractorSectionPicked: state => state.bidsContractorSectionPicked,
            })
        },
        methods: {

            getTopMargin() {
                return this.isCustomer() ? 'customer-top-margin' : 'contractor-top-margin'
            },

            ...mapMutations([
                'toggleBidsContractor'
            ]),

            showHelp() {
                this.$router.push({
                    path: '/help/jobs'
                });
            },

            setModalText(status) {
                if (status === 'changed') {
                    this.modalText.jobs = `Job Has Changed`
                }
            },

            showModal(modal, status) {
                if (modal === 'jobs') {
                    this.setModalText(status)
                    this.modal.jobs = true;
                }
            },

            closeModal(modal) {
                if (modal === 'jobs') {
                    this.modal.jobs = false;
                }
            },

            getJobStatus(bid) {
                const status = this.formatStatus(this.getJobStatus_latest(bid))

                if (status === 'sent' && this.isCustomer()) {
                    return 'Job has been submitted. Please approve the bid.'
                } else {
                    return status
                }
            },
            showDeleteJobModal(job) {
                this.deleteJob.id = job.id
                this.job = job
                $('#delete-task-modal').modal('show')
            },
            deleteTheJob(action) {
                if (action === 'delete') {
                    this.deleteTheActualJob(this.deleteJob.id)
                }
                $('#delete-task-modal').modal('hide')
            },
            async deleteTheActualJob(id) {
                try {
                    const data = await axios.post('/job/delete', {
                        id: id
                    })
                    this.getBids()
                } catch (error) {
                }
            },
            isContractor() {
                if (this.user) {
                    return this.user.usertype === 'contractor'
                }

            },
            totalNumberOfSubsBiddingForTheJob(jobTasks) {
                if (jobTasks && jobTasks.bid_contractor_job_tasks) {
                    return jobTasks.bid_contractor_job_tasks.length
                }
            },
            search() {
                this.sBids = this.bids.filter((bid) => {
                    if (this.searchTerm === '' || this.searchTerm.length <= 1) {
                        return true
                    }
                    return bid.job_name.toLowerCase().search(this.searchTerm.toLowerCase()) > -1
                })
                if (this.$refs.paginator && this.$refs.paginator.lastPage >= 1) {
                    this.$refs.paginator.goToPage(1)
                }
            },
            getLabelClass(bid) {
                return Format.statusLabel(bid.status, User.isCustomer, this.isGeneral(bid))
            },
            jobName(name) {
                return Format.jobName(name)
            },
            isGeneral(bid) {
                if (bid !== null && this.user) {
                    return bid.contractor_id === this.user.id
                }
                return false
            },
            isCustomer() {
                return this.spark.state.user.usertype === 'customer'
            },
            status(bid) {
                if (bid !== null && this.user !== undefined) {
                    return User.status(bid.status, bid, this.user)
                }
            },
            prettyDate(date) {
                if (date == null)
                    return ''
                // return the date and ignore the time
                date = date.split(' ')
                return date[0]
            },
            goToJob(id) {
                this.$router.push('/bid/' + id)
            },
            getBids() {
                let url = ''
                if (User.isCustomer()) {
                    url = 'getJobsForCustomer'
                } else {
                    url = 'jobs'
                }
                axios.get(url).then((response) => {
                    if (Array.isArray(response.data)) {
                        this.bids = response.data
                        this.sBids = this.bids
                    }
                })
            },
            previewSubForTask(bidId, jobTaskId, subBidId) {
                // console.log(TaskUtil.previewSubForTask(this.bids, bidId, jobTaskId, subBidId))
            }
        },
        mounted() {

            if (this.$route.path === '/bids/subs') {
                this.toggleBidsContractor(false)
            }

            this.$store.commit('setCurrentPage', this.$router.history.current.path)
        },
        created() {
            document.body.scrollTop = 0 // For Safari
            document.documentElement.scrollTop = 0 // For Chrome, Firefox, IE and Opera
            this.getBids()
            Bus.$on('bidUpdated', (payload) => {
                this.getBids()
            })
            Bus.$on('jobCanceled', (payload) => {
                this.getBids()
            })
            Bus.$on('previewSubForTask', (payload) => {
                this.previewSubForTask(payload[0], payload[1], payload[2])
            })

        },
    }
</script>

<style lang="less" scoped>

    .customer-top-margin {
        margin-top: 10px
    }

    .contractor-top-margin {
        margin-top: -62px
    }

    .list-card {
        margin-left: 0px;
    }

    .v-height {
        height: 125vh;
    }

    .bid-btn {
        height: 100%;
        padding-top: 1.25rem;
        padding-right: .5rem;
        padding-left: .5rem;
    }

    .job-section {
        height: 3.75rem;
    }

    #bids-item {
        background-color: #2779BD;
    }

    .customer {
        display: flex;
        justify-content: center;
    }

    .customer span {
        margin: 1rem 2rem;
    }

    .card-1 {
        width: 90%;
        display: flex;
        align-items: stretch;
    }

    .search {
        flex-direction: column;
        padding-top: 1rem;
        padding-bottom: 2rem;
    }

    .bid {
        flex-direction: column;
    }

    label {
        width: 100%;
        padding: .75rem 0rem;
        font-size: 2rem;
    }

    button {
        margin-bottom: 1rem;
    }

    .row-sizing {
        padding: 0;
    }

</style>