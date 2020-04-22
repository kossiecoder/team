import Vuex from "vuex";
import Vue from 'vue';

Vue.use(Vuex);

import modules from "@/store/modules";

export default new Vuex.Store({
    modules
});
