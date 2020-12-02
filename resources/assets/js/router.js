import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

//Route Components
import CheckAccountingApp from './pages/CheckAccountingApp'
import Home from './pages/Home'
import SplashScreen from './pages/SplashScreen'
import Feedback from './pages/Feedback'
import PublicHome from './pages/PublicHome'
import Jobs from './pages/Jobs'
import JobTasks from './pages/JobTasks'
import AddJobTask from './pages/AddJobTask'
import JobTask from './pages/JobTask'
import AssociatedContractors from './pages/AssociatedContractors'

import Assessor from './pages/Assessor'
import ContractorInfo from './pages/ContractorInfo'
import CustomerInfo from './pages/CustomerInfo'
import Documentation from './pages/Documentation'
import Job from './pages/Job'
import InitiateBid from './pages/InitiateBid'
import Tasks from './pages/Tasks'
import Invoices from './pages/Invoices'
import Invoice from './pages/Invoice'
import SubInvoice from './pages/SubInvoice'
import Settings from './pages/Settings'
import FurtherInfo from './pages/FurtherInfo'
import TaskImages from './pages/TaskImages'
import JobImages from './pages/JobImages'
import Benefits from './pages/Benefits'
import Pricing from './pages/Pricing'
import Demo from './pages/Demo'
import ImageAssociation from './pages/ImageAssociation'
import HowTo from './pages/HowTo'
import RegisterQuickBooks from './pages/RegisterQuickBooks'
import Register from './pages/Register'
import UserAuthorizationPage from './pages/UserAuthorizationPage'
import Terms from "./pages/Terms";
import PasswordReset from "./pages/PasswordReset";
import PasswordEmailVerification from "./pages/PasswordEmailVerification";
import Tailwind from "./components/tailwind/tailwind"

import Help from "./pages/Help"
// import BidTask from './components/job/BidTask';


// vue routes
const routes = [
  {
    path: '/tailwind',
    component: Tailwind
  },
  {
    path: '/assessor/:location',
    component: Assessor
  },
  {
    path: '/help/:page',
    component: Help
  },
  {
    path: '/bids',
    component: Jobs
    // name: 'jobs',
    // component: () =>
    //   import(/* webpackChunkName: "about" */ "./pages/Jobs")
  },
  {
    path: '/terms',
    component: Terms
  },
  {
    path: '/termsAuth',
    component: Terms
  },
  {
    path: '/associatedContractors',
    component: AssociatedContractors
  },
  {
    path: '/bids/subs',
    component: Jobs
  },
  {
    path: '/passwordEmailVerification',
    component: PasswordEmailVerification
  },
  {
    path: '/passwordReset',
    component: PasswordReset
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
    path: '/documentation',
    component: Documentation
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
    path: '/splashScreen',
    component: SplashScreen
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
    path: '/job/:id/images',
    component: JobImages
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
    path: '/settings',
    component: Settings
  },
  {
    path: '/contractor-info',
    component: ContractorInfo,
    name: 'contractor-info',
    props: true
  },
  {
    path: '/customer-info',
    component: CustomerInfo,
    name: 'customer-info',
    props: true
  },
  {
    path: '/documentation',
    component: Documentation,
    name: 'documentation',
    props: true
  },
  {
    path: '/image-association',
    component: ImageAssociation,
    name: 'image-association',
    props: true
  },
  {
    path: '/pricing',
    component: Pricing
  },
  {
    path: '/#*'
  }
]


// mode: 'history',

export const router = new VueRouter({
  routes
})