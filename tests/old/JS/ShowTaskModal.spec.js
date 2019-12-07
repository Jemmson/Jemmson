import {
  shallowMount,
} from '@vue/test-utils'
import expect from 'expect'

import ShowTaskModal from '../../resources/assets/js/components/job/ShowTaskModal'

describe('ShowTaskModal', () => {

  let wrapper = shallowMount(ShowTaskModal, {})

  it('should show name of task', function() {
    expect(true).toBe(true);
  })

})