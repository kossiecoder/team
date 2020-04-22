import Vue from 'vue';
import Router from 'vue-router';
import routes from '@/routes/routes';
import store from '@/store/index';
import permissionApi from '@/api/permission';

Vue.use(Router);

const router = new Router({
    mode: 'history',
    routes
});

//Auth check
router.beforeEach(async (to, from, next) => {
    if(to.matched.some(record => record.meta.publicAccess)) {
        if(store.getters['auth/isLoggedIn']) {
            next('/');
            return
        }
        next()
    } else {
        if(store.getters['auth/isLoggedIn']) {
            try {
                await store.dispatch('permission/fetchMyPermissions');
                if (to.meta.permissionCode) {
                    const res = await permissionApi.checkIfUserHasPermission(to.meta.permissionCode);
                    res.data.hasPermission ? next() : next('/');
                }
            } catch (err) {
                console.log(err);
            }

            if(store.state.user.users.length === 0) {
                await store.dispatch('user/fetchUsers');
            }
            next();
            return;
        }
        next('/login')
    }
});

export default router;
