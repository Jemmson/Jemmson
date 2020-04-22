import {shallowMount, createLocalVue} from '@vue/test-utils'
import AddJobTask from '../../resources/assets/js/pages/AddJobTask'
import Vuetify from 'vuetify'

global.Spark = {
    state: {
        user: {
            id: 1,
            contractor: {
                accounting_software: ''
            },
            usertype: 'contractor'
        }
    }
}

require('./setup');

const localVue = createLocalVue()
localVue.use(Vuetify, {})

describe('AddJobTask', () => {
    let vuetify = new Vuetify()

    test('is a Vue instance', () => {

        let wrapper = shallowMount(AddJobTask, {
            vuetify,
            localVue,
            directives: {
                mask() {
                }
            },
            mocks: {
                $router: {
                    push: jest.fn()
                }
            }
        })

        expect(wrapper.isVueInstance()).toBeTruthy()
    })

    test('test that addnewtaskform.updateTask is false and addnewtaskform.createnew os true', () => {

        let wrapper = shallowMount(AddJobTask, {
            vuetify,
            localVue,
            directives: {
                mask() {
                }
            },
            mocks: {
                $router: {
                    push: jest.fn()
                }
            },
            methods: {
                addNewTaskToBid: jest.fn()
            }
        })

        wrapper.setData({
            addNewTaskForm: {
                updateTask: true,
                createNew: false
            }
        })

        wrapper.vm.changeTask('New')

        expect(wrapper.vm.$data.addNewTaskForm.updateTask).toBe(false)
        expect(wrapper.vm.$data.addNewTaskForm.createNew).toBe(true)

    })

    test('show an error message if an error exists', async () => {

        let wrapper = shallowMount(AddJobTask, {
            vuetify,
            localVue,
            directives: {
                mask() {
                }
            },
            mocks: {
                $router: {
                    push: jest.fn()
                }
            },
            methods: {
                addNewTaskToBid: jest.fn()
            }
        });

        wrapper.setData({
            errors: {
                subPriceTooHigh: {
                    exists: false,
                    message: 'Sub Price Can Not Be Higher Than Contractor Price'
                },
                general: {
                    errorExists: true,
                    message: 'Errors exist on page. Please review'
                },
                notANumber: {
                    price: false,
                    quantity: false,
                    subTaskPrice: false,
                    message: 'must be a number'
                }
            }
        })

        await wrapper.vm.$nextTick()
        expect(wrapper.find({ref: 'errorMessage'}).text()).toBe('Errors exist on page. Please review')

    })

})