<template>
    <div>
        <div class="header-section">
            <div class="upper txt-white font-12">{{ user.name }}</div>
            <div class="home-title upper txt-white font-3rem">home</div>
            <div class="home-icon">
                <span class="text-center">J</span>
            </div>
            <div v-if="(user.usertype === 'contractor') && user.contractor !== null">
                <div class="txt-white upper company-name">{{ user.contractor.company_name }}</div>
            </div>
        </div>
        <div class="container">
            <div class="header-section">
                <div class="mini-slogan upper">mini slogan</div>
                <div class="main-slogan upper">main slogan</div>
            </div>
            <div class="feature-section first-feature">
                <div class="feature-main-section">
                    <div class="feature-main-icon-section">
                        <div class="icon">icon</div>
                        <div class="feature">9 Bids</div>
                    </div>
                    <div class="feature-main-icon-section">
                        <div class="manage">Manage</div>
                        <div class="icon">icon</div>
                    </div>
                </div>
            </div>
            <div class="feature-main-section-statuses">
                <div class="status">6 are waiting for approval</div>
                <div class="status">2 are initited</div>
            </div>
            <hr>
            <div class="feature-section">
                <div class="feature-main-section">
                    <div class="feature-main-icon-section">
                        <div class="icon">icon</div>
                        <div class="feature">6 Tasks</div>
                    </div>
                    <div class="feature-main-icon-section">
                        <div class="manage">Manage</div>
                        <div class="icon">icon</div>
                    </div>
                </div>
            </div>
            <div class="feature-main-section-statuses">
                <div class="status">2 are ready for you</div>
                <div class="status">23 are completed</div>
            </div>
            <hr>
            <div class="feature-section">
                <div class="feature-main-section">
                    <div class="feature-main-icon-section">
                        <div class="icon">icon</div>
                        <div class="feature">5 Invoices</div>
                    </div>
                    <div class="feature-main-icon-section">
                        <div class="manage">Manage</div>
                        <div class="icon">icon</div>
                    </div>
                </div>
            </div>
            <div class="feature-main-section-statuses">
                <div class="status">4 are wating for payment</div>
                <div class="status">1 is processing</div>
            </div>
            <hr>
        </div>
    </div>
</template>

<script>

  export default {
    props: {
      user: Object
    },
    data () {
      return {
        bids: '',
        tasks: ''
      }
    },
    computed: {},
    methods: {
      route (value) {
        if (value === 'express') {
          axios.post ('/stripe/express/dashboard').then ((response) => {
            console.log (response.data);
            window.location = response.data.url;
          });
        } else {
          this.$router.push (value)
        }
      },
      bidData (bids, message) {
        let count = 0;
        for (let i = 0; i < bids.length; i++) {
          if (bids[i].status === message) {
            count++;
          }
        }
        return count;
      },
      taskData (tasks, message) {
        let count = 0;
        for (let i = 0; i < tasks.length; i++) {
          if (tasks[i].job_task.status === message) {
            count++;
          }
        }
        return count;
      },
    },
    mounted: function () {
      console.log ('getBids');
      axios.post ('/jobs').then ((response) => {
        this.bids = response.data;
        this.sBids = this.bids;
        console.log (this.bids)
      });
      console.log ('getTasks');
      axios.post ('/bid/tasks').then ((response) => {
        this.tasks = response.data;
        this.sTasks = this.tasks;
      });
    }
  }
</script>

<style scoped>

    .header-section {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .company-name {
        font-size: 1.75rem;
        margin-top: .75rem;
    }

    .home-icon {
        margin-top: 2rem;
        border-radius: 50%;
        background-color: white;
        height: 9rem;
        width: 9rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .home-title {
        margin-top: -.5rem;
    }

    .home-icon span {
        font-size: 4rem;
        font-weight: bolder;
    }

    .txt-white {
        color: white;
    }

    .upper {
        text-transform: uppercase;
    }

    .font-12 {
        font-size: 12px;
    }

    .font-3rem {
        font-size: 3rem;

    }

    .mini-slogan {
        margin-top: 2.15rem;
        font-size: 12px;
        color: #888888;
    }

    .main-slogan {
        font-size: 2.5rem;
        font-family: 'Anton', sans-serif;
        font-weight: 900;
    }

    .container {
        background-color: white;
        width: 90%;
        height: 300rem;
        margin-top: 3rem;
        /*border-radius: 10px;*/
        /*background-image: linear-gradient(to right, rgba(22, 20, 17, 0.20), rgba(130, 182, 144, 0.20));*/
    }

    @media (min-width: 575px) {
        .container {
            width: 1200px;
        }
    }

    .feature-section {
        display: flex;
        flex-direction: column;
        margin-left: 1.25rem;
        margin-right: 1.25rem;
    }

    .feature-main-section {
        display: flex;
        justify-content: space-between;
    }

    .feature-main-icon-section {
        display: flex;
    }

    .feature-main-section-statuses {
        display: flex;
        flex-direction: column;
        align-items: left;
    }

    .icon {
        margin-right: 1rem;
    }

    .feature {
        font-size: 2.25rem;
        font-weight: 900;
        font-family: 'Anton', sans-serif;
    }

    .manage {
        margin-right: 1rem;
        font-size: 2.25rem;
        font-weight: 900;
        font-family: 'Anton', sans-serif;
    }

    .status {
        margin-left: 5rem;
        color: #0000005c;
    }

    .first-feature {
        margin-top: 1.13rem;
    }

    .home-sub-heading {
        display: flex;
        justify-content: left;
        margin-top: 2rem;
        margin-bottom: 0px;
        color: #81171F;
    }

    h3 {
        margin-top: 0rem;
        margin-bottom: 0px;
        display: flex;
    }

    .home-summary-data {
        display: flex;
        justify-content: space-between;
        margin-top: -1.5rem;
        padding-left: 3em;
        padding-right: 1em;
        color: black;
    }

    .summary-footer {
        height: 2rem;
    }

    .home-sub-heading-tasks {
        margin-top: 5rem;
    }

    h1 {
        color: #81171F;
    }

    h5 {
        color: #81171F;
        font-size: 2rem;
        font-weight: bolder;
    }

</style>