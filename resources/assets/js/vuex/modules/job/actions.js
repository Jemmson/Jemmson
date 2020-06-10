/**
 * Created by shawnpike on 3/2/17.
 */

export const getInitialDate = ({commit, getters, rootState}, column) => {
  if (column === 'agreed_start_date') {
    let date = getters.getAgreedStartDate
    return date
  } else if (column === 'agreed_end_date') {
    return getters.getAgreedEndDate
  }
  // commit('updateValue', payload)
}

// Exception: TypeError: 'caller', 'callee',
// and 'arguments' properties may not be accessed on strict mode functions
// or the arguments objects for calls to them
// at Function.remoteFunction (<anonymous>:2:14)
// at VueComponent.theDate (http://127.0.0.1:9500/js/app.js:53781:7)
// at Watcher.get (http://127.0.0.1:9500/js/app.js:46421:25)
// at Watcher.evaluate (http://127.0.0.1:9500/js/app.js:46528:21)
// at VueComponent.computedGetter [as theDate] (http://127.0.0.1:9500/js/app.js:46803:17)
// at Object.get (http://127.0.0.1:9500/js/app.js:45221:20)
// at Proxy.render (http://127.0.0.1:9500/js/app.js:53859:50)
// at VueComponent.Vue._render (http://127.0.0.1:9500/js/app.js:47661:22)
// at VueComponent.updateComponent (http://127.0.0.1:9500/js/app.js:46080:21)
// at Watcher.get (http://127.0.0.1:9500/js/app.js:46421:25)]
// assign