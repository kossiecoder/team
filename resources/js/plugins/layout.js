// Register Layout Component
import Default from '@/layouts/Default';
import NoOverflow from '@/layouts/NoOverflow';

export default {
    install(Vue) {
        Vue.component('default-layout', Default);
        Vue.component('no-overflow-layout', NoOverflow);
    }
}
