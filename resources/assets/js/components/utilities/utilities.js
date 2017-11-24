export const getTimeForDate = (dbDate) => {
  let d = new Date(dbDate)
  let year = d.getFullYear()
  let month = d.getMonth() + 1
  let day = d.getDate()

  if (month < 10) {
    month = '0' + month
  }
  if (day < 10) {
    day = '0' + day
  }
  return year+"-"+month+"-"+day
}