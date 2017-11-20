<template>
    <div>
        <div v-if="startDateError" class="text-danger">the start date must be before the end date</div>
        <div v-if="endDateError" class="text-danger">the end date must be after the start date</div>
        <!--<span>{{ new Date() | moment("dddd, MMMM Do YYYY") }}</span>-->
        <!--<pre>{{ theDate }}</pre>-->
        <!--<pre>{{ thedate }}</pre>-->
        <label for="startDate" class="startDate control-label">{{ label }}
            <input type="date" id="startDate" v-model="thedate" class="form-control" @blur="mouseLeave()">
        </label>
    </div>
</template>

<script>
  import {mapMutations, mapGetters} from 'vuex'

  export default {
    name: '',
    data () {
      return {
        thedate: '',
        startDateError: false,
        endDateError: false
      }
    },
    props: {
      label: {
        type: String
      },
      serverurl: {
        type: String
      },
      dbtable: {
        type: String
      },
      dbcolumn: {
        type: String
      }
    },
    computed: {
      ...mapGetters ('job', [
        'getAgreedStartDate',
        'getAgreedEndDate',
        'getId'
      ]),
      theDate () {
        if (this.dbcolumn === 'agreed_start_date') {
//          debugger
          return this.getAgreedStartDate
//          return this.getAgreedStartDate
        } else if (this.dbcolumn === 'agreed_end_date') {
          return this.getAgreedEndDate
        }

//        let date = this.$store.dispatch ('job/getInitialDate', this.dbcolumn)
//        console.log (date)
//        return this.$store.dispatch ('job/getInitialDate', this.dbcolumn)
      }
    },
    methods: {
      ...mapMutations ('job', [
        'setDate'
      ]),
      mouseLeave () {
        this.displayValues (this.getId, this.thedate, this.serverurl)

//        debugger
        if (this.thedate !== '') {
          this.checkTheDate ()
        } else if (this.dbcolumn === 'agreed_start_date') {
          this.resetTheDateToTheAgreedStartDate ()
        } else if (this.dbcolumn === 'agreed_end_date') {
          this.resetTheDateToTheAgreedEndDate ()
        }
//        debugger

      },
      updateTheDatabase () {
        axios.post (this.serverurl, {
          params: {
            dateType: this.dbcolumn,
            date: this.thedate,
            id: this.getId
          }
        }).then (response => {
//          debugger
          console.log (response.data)
          this.setDate ({type: this.dbcolumn, date: this.thedate})
        })
      },
      displayValues (id, date, serverUrl) {
        console.log (id)
        console.log (date)
        console.log (serverUrl)
      },
      getTimeInSeconds (dateTime) {
        let dt = new Date (dateTime)
        return dt.getTime ()
      },
      displayStartDateError () {
        this.$data.startDateError = true
        this.$data.endDateError = false
      },
      displayEndDateError () {
        this.$data.startDateError = false
        this.$data.endDateError = true
      },
      removeError () {
        this.$data.startDateError = false
        this.$data.endDateError = false
      },
      resetTheDateToTheAgreedStartDate () {
        console.log ('the start date must be before the end date')
        this.thedate = this.getAgreedStartDate
      },
      resetTheDateToTheAgreedEndDate () {
        console.log ('the end date must be after the start date')
        this.thedate = this.getAgreedEndDate
      },
      checkTheDate () {
        let currentDate = this.getTimeInSeconds (this.thedate)
        if (this.dbcolumn === 'agreed_start_date') {
          let end = this.getTimeInSeconds (this.getAgreedEndDate)
          if (end < currentDate) {
            this.displayStartDateError ()
            this.resetTheDateToTheAgreedStartDate ()
          } else {
            this.removeError ()
            this.updateTheDatabase ()
          }
        } else if (this.dbcolumn === 'agreed_end_date') {
          let start = this.getTimeInSeconds (this.getAgreedStartDate)
          if (start > currentDate) {
            this.displayEndDateError ()
            this.resetTheDateToTheAgreedEndDate ()
          } else {
            this.removeError ()
            this.updateTheDatabase ()
          }
        }
      }
    }
  }
</script>

<style>

</style>
