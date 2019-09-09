import VueRouter from 'vue-router'
import Vue from 'vue'

Vue.use(VueRouter)

//Route Components
import CheckAccountingApp from './pages/CheckAccountingApp'
import Home from './pages/Home'
import Feedback from './pages/Feedback'
import PublicHome from './pages/PublicHome'
import Jobs from './pages/Jobs'
import JobTasks from './pages/JobTasks'
import AddJobTask from './pages/AddJobTask'
import JobTask from './pages/JobTask'

import Job from './pages/Job'
import InitiateBid from './pages/InitiateBid'
import Tasks from './pages/Tasks'
import Invoices from './pages/Invoices'
import Invoice from './pages/Invoice'
import SubInvoice from './pages/SubInvoice'
import FurtherInfo from './pages/FurtherInfo'
import TaskImages from './pages/TaskImages'
import Benefits from './pages/Benefits'
import Demo from './pages/Demo'
import HowTo from './pages/HowTo'
import RegisterQuickBooks from './pages/RegisterQuickBooks'
import Register from './pages/Register'
import UserAuthorizationPage from './pages/UserAuthorizationPage'
// import BidTask from './components/job/BidTask';


// vue routes
const routes = [
  {
    path: '/bids',
    component: Jobs
  },
  {
    path: '/bids/subs',
    component: Jobs
  },
  {
    path: '/job/tasks',
    component: JobTasks
  },
  {
    path: '/job/task/:index',
    component: JobTask
  },
  {
    path: '/job/add/task',
    component: AddJobTask
  },
  {
    path: '/userAuthorizationPage',
    component: UserAuthorizationPage
  },
  {
    path: '/feedback',
    component: Feedback
  },
  {
    path: '/benefits',
    component: Benefits
  },
  {
    path: '/check_accounting',
    component: CheckAccountingApp
  },
  {
    path: '/demo',
    component: Demo
  },
  {
    path: '/howto',
    component: HowTo
  },
  {
    path: '/bid/:id',
    component: Job
  },
  {
    path: '/tasks',
    component: Tasks
  },
  {
    path: '/home',
    component: Home
  },
  {
    path: '/',
    component: PublicHome
  },
  {
    path: '/initiate-bid',
    component: InitiateBid
  },
  {
    path: '/invoices',
    component: Invoices
  },
  {
    path: '/invoice/:id',
    component: Invoice
  },
  {
    path: '/sub/invoice/:id',
    component: SubInvoice
  },
  {
    path: '/furtherInfo',
    component: FurtherInfo
  },
  {
    path: '/task/:id/images',
    component: TaskImages
  },
  {
    path: '/registerQuickBooks',
    component: RegisterQuickBooks
  },
  {
    path: '/register',
    component: Register
  },
  {
    path: '/#*'
  }

]

export const router = new VueRouter({
  routes
})