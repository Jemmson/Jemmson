export default {
  methods: {

    dateOnly(datetime) {
      if (datetime) {
        let dt = datetime.split(' ')
        return dt[0]
      }
    },

    formatDate(date) {
      if (this.contains4digitYearFirstAndDashes(date)) {
        let dateArray = date.split('-')
        return dateArray[1] + '/' + dateArray[2] + '/' + dateArray[0]
      }
    },

    contains4digitYearFirstAndDashes(date) {
      if (date) {
        const dateArray = date.split('-');
        return dateArray[0].length === 4 && dateArray.length > 1;
      }
    }
  }
}