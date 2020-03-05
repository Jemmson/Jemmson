export default {
    methods: {

        dateOnly(datetime) {
            if (datetime) {
                let d = datetime.split(' ');
                let time = moment.utc(d[0] + 'T' + d[1]);
                return time.local().format('MM/DD/YYYY');
            }
        },

        dateOnlyHyphen(datetime) {
            if (datetime) {
                let d = datetime.split(' ');
                let time = moment.utc(d[0] + 'T' + d[1]);
                return time.local().format('YYYY-MM-DD');
            }
        },

        localTime(time) {
            return moment(time)
                .local()
                .format('MMMM Do YYYY, h:mm:ss A');
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