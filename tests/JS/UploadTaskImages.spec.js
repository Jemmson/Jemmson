import {
    shallowMount
} from "@vue/test-utils";
import UploadTaskImages from '../../resources/assets/js/components/task/UploadTaskImages';

// require('../../bootstrap');

describe('UploadTaskImages', () => {
    const wrapper = shallowMount(UploadTaskImages, {
        propsData: {
            jobTask: {
                images: []
            },
            type: ''
        }
    });

    it('Should render itself', () => {
        expect(wrapper.isEmpty()).toBe(false);
    });
});