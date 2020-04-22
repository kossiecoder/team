import Vue from 'vue';
import moment from 'moment/moment';

Vue.filter('utcToLocal', value => moment.utc(value).local().format('d/M/Y h:mm a'));
