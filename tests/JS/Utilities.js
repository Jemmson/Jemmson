import moment from 'moment'

export default {
  /**
   * Show the time in local time.
   */
  localTime() {
    return moment(Date.now())
      .utc()
      .local()
      .format('YYYY-MM-D');
  }

}