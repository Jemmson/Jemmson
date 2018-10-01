import {
    shallowMount
} from "@vue/test-utils";
import UploadTaskImages from '../../../../resources/assets/js/components/task/UploadTaskImages';

require('../../bootstrap');

describe('Upload Task Images', () => {
    const wrapper = shallowMount(UploadTaskImages, {
        propsData: {
            jobTask: {
                images: []
            },
            type: ''
        }
    });

    it('Should render itself', () => {
        expect(wrapper.exists()).toBe(true);
    });
});