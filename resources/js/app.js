import './bootstrap';
import Vue from 'vue';
import vuetify from '@/plugins/vuetify';
import App from '@/App';
import 'es6-promise/auto';
import store from '@/store';
import router from '@/routes';
window.Pusher = require('pusher-js');
import moment from 'moment/moment';
import VueChatScroll from 'vue-chat-scroll';
Vue.use(VueChatScroll);

// register layouts in layout.js
import layout from "@/plugins/layout";
Vue.use(layout);

// Vue Global Filters
import '@/filters/filters';
import initMixin from "@/mixins/initMixin";

new Vue({
    el: '#app',
    store,
    router,
    vuetify,
    mixins: [initMixin],
    render: h => h(App),
});
