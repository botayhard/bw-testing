import api from '@/api/.api_result';
import { proxify } from './proxy';

export default ({ $axios }, inject) => {
    const _api = proxify(api, $axios);
    const APIObject = { api: _api.proxy, api_groups: _api.groups };
    inject('api', APIObject.api);
};
