<template>
    <div>
        <!--<span>{{ new Date() | moment("dddd, MMMM Do YYYY") }}</span>-->
        <pre>{{ thedate }}</pre>
        <pre>{{ date }}</pre>
        <label for="startDate" class="startDate control-label">Job Start Date
            <input type="date" id="startDate" v-model="thedate" class="form-control" @blur="mouseLeave()">
        </label>
    </div>
</template>

<script>
  export default {
    name: '',
    data () {
      return {
        thedate: '',
        hello: 'world'
      }
    },
    props: {
      date: {
        type: String
      },
      id: {
        type: String
      },
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
    mounted () {
      this.thedate = this.date
    },
    updated () {
//      console.log(typeof this.startdate)
    },
    methods: {
      mouseLeave () {
        this.checkDate()
        console.log (this.id)
        console.log (this.thedate)
        console.log (this.serverurl)
        console.log (this.serverurl)
        axios.post (this.serverurl, {
          params: {
            dateType: this.dbcolumn,
            date: this.thedate,
            id: this.id
          }
        }).then (response => {
          console.log (response.data)
        })
      },
      checkDate () {
        console.log('I am checking the date')
        this.$emit('theselecteddate', this.thedate)
      }
    }
  }
</script>

<style>

</style>
