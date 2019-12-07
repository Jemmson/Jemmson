import { mount } from '@vue/test-utils'
import AddJobTask from '../../resources/assets/js/pages/AddJobTask';

describe('AddJobTask', () => {
  test('is a Vue instance', () => {
    const wrapper = mount(AddJobTask)
    expect(wrapper.isVueInstance()).toBeTruthy()
  })
})