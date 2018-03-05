<template>
    <div>
        <div v-if="startDateError" class="text-danger">the start date must be before the end date</div>
        <div v-if="endDateError" class="text-danger">the end date must be after the start date</div>
        <!--<span>{{ new Date() | moment("dddd, MMMM Do YYYY") }}</span>-->
        <!--<pre>{{ theDate }}</pre>-->
        <!--<pre>{{ thedate }}</pre>-->
        <label class="startDate control-label">{{ label }}
            <!--<input type="date" value="2016-11-17">-->
            <input type="date" v-model="thedate" class="form-control" @blur="mouseLeave()">
        </label>
    </div>
</template>

<script>
  import {mapMutations, mapGetters} from 'vuex'
  import * as ut from '../utilities/utilities.js'

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
        if (this.dateIsTheStartDate ()) {
//          debugger
          return this.getAgreedStartDate
//          return this.getAgreedStartDate
        } else if (this.dateIsTheEndDate ()) {
          return this.getAgreedEndDate
        }

//        let date = this.$store.dispatch ('job/getInitialDate', this.dbcolumn)
//        console.log (date)
//        return this.$store.dispatch ('job/getInitialDate', this.dbcolumn)
      }
    },
    mounted () {
//      debugger
      if (this.dateIsTheStartDate ()) {
        let end = this.getTheEndDateInMilliseconds ()
        this.initializeTheStartValue ()
        let currentDate = this.getTheCurrentDateInMilliseconds ()
        if (this.theSelectedEndDateIsBeforeTheStartDate (end, currentDate)) {
          this.displayStartDateError ()
          this.resetTheDateToTheAgreedStartDate ()
        }
      } else if (this.dateIsTheEndDate ()) {
        this.initializeTheEndValaue ()
        let start = this.getTheStartDateInMilliseconds ()
        let currentDate = this.getTheCurrentDateInMilliseconds ()
        if (this.theSelectedStartDateIsBeforeTheEndDate (start, currentDate)) {
          this.displayEndDateError ()
          this.resetTheDateToTheAgreedEndDate ()
        }
      }
    },
    methods: {
      ...mapMutations ('job', [
        'setDate'
      ]),
      initializeTheStartValue () {
        this.resetTheDateToTheAgreedStartDate ()
      },
      initializeTheEndValaue () {
        this.resetTheDateToTheAgreedEndDate ()
      },
      mouseLeave () {
        this.displayValues (this.getId, this.thedate, this.serverurl)
//        debugger
        if (this.thedate !== '') {
          this.checkTheDate ()
        } else if (this.dateIsTheStartDate ()) {
          this.resetTheDateToTheAgreedStartDate ()
        } else if (this.dateIsTheEndDate ()) {
          this.resetTheDateToTheAgreedEndDate ()
        }
      },
      dateIsTheStartDate () {
        return this.dbcolumn === 'agreed_start_date'
      },
      dateIsTheEndDate () {
        return this.dbcolumn === 'agreed_end_date'
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
        this.thedate = ut.getTimeForDate(this.getAgreedStartDate)
      },
      resetTheDateToTheAgreedEndDate () {
        this.thedate = ut.getTimeForDate(this.getAgreedEndDate)
      },
      getTheCurrentDateInMilliseconds () {
        return this.getTimeInSeconds (this.thedate)
      },
      getTheStartDateInMilliseconds () {
        return this.getTimeInSeconds (this.getAgreedStartDate)
      },
      getTheEndDateInMilliseconds () {
        return this.getTimeInSeconds (this.getAgreedEndDate)
      },
      theSelectedStartDateIsBeforeTheEndDate (date1, date2) {
        return date1 > date2
      },
      theSelectedEndDateIsBeforeTheStartDate (date1, date2) {
        return date1 < date2
      },
      checkTheDate () {
//        debugger
        let currentDate = this.getTheCurrentDateInMilliseconds ()
        if (this.dateIsTheStartDate ()) {
          let end = this.getTheEndDateInMilliseconds ()
          if (this.theSelectedEndDateIsBeforeTheStartDate (end, currentDate)) {
            this.displayStartDateError ()
            this.resetTheDateToTheAgreedStartDate ()
          } else {
            this.removeError ()
            this.updateTheDatabase ()
          }
        } else if (this.dateIsTheEndDate ()) {
          let start = this.getTheStartDateInMilliseconds ()
          if (this.theSelectedStartDateIsBeforeTheEndDate (start, currentDate)) {
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
