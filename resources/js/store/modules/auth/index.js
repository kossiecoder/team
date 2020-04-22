import state from '@/store/modules/auth/state';
import mutations from '@/store/modules/auth/mutations';
import actions from '@/store/modules/auth/actions';
import getters from '@/store/modules/auth/getters';

export default {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}
