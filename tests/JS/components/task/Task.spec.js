import {
  shallowMount
} from "@vue/test-utils";
import sinon from 'sinon';
import Task from '../../../../resources/assets/js/components/task/Task';

require('../../bootstrap');

describe('Task', () => {

  it('should show that the cash button should have class btn-active when it is selected and the stripe and other buttons should not have this class',
    function() {
    //Action
    const wrapper = mount(Task);
    wrapper.setData({

    })

  })

});
