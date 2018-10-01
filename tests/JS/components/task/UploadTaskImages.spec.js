import {
    shallowMount
} from "@vue/test-utils";
import UploadTaskImages from '../../../../resources/assets/js/components/task/UploadTaskImages';

require('../../bootstrap');

describe('Upload Task Images', () => {
    const wrapper = shallowMount(UploadTaskImages, {
    });

    it('Should be true', () => {
        expect(true).toBe(false);
    });
});