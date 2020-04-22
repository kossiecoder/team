import Home from '@/pages';
import Login from '@/pages/login';
import NotFound from '@/pages/not-found';
import Chat from '@/pages/chat';
import Profile from '@/pages/profile';
import ProfileDetail from '@/pages/profile/detail';
import Tasks from '@/pages/work-request';
import Task from '@/pages/work-request/_id';

import ChatMainPrivate from '@/components/chat/private/ChatMainPrivate';
import ChatMainChannel from '@/components/chat/channel/ChatMainChannel';

import Admin from '@/pages/admin/';
import AdminUser from '@/pages/admin/user/';
import AdminUserCreate from '@/pages/admin/user/create';
import AdminWorkRequestCategory from '@/pages/admin/work-request-category';
import AdminWorkRequestCategoryCreate from '@/pages/admin/work-request-category/create';
import AdminWorkRequestCategoryEdit from '@/pages/admin/work-request-category/_id';

export default [
    {
        path: '/',
        component: Home,
        name: 'home',
    },
    {
        path: '/login',
        component: Login,
        name: 'login',
        meta: {
            publicAccess: true
        }
    },
    {
        path: '/chat',
        component: Chat,
        name: 'chat',
        meta: {
            layout: 'no-overflow'
        },
        children: [
            {
                path: ':id',
                component: ChatMainPrivate,
                name: 'chat-main-private',
                meta: {
                    layout: 'no-overflow'
                }
            },
            {
                path: 'channel/:id',
                component: ChatMainChannel,
                name: 'chat-main-channel',
                meta: {
                    layout: 'no-overflow'
                }
            }
        ]
    },
    {
        path: '/profile',
        component: Profile,
        name: 'profile',
    },
    {
        path: '/profile/:id',
        component: ProfileDetail,
        name: 'profile-detail',
        meta: {
            permissionCode: 'edit_users'
        }
    },

    {
        path: '/work-request',
        component: Tasks,
        name: 'tasks',
    },
    {
        path: '/work-request/:id',
        component: Task,
        name: 'task',
        meta: {
            layout: 'no-overflow'
        }
    },
    {
        path: '/admin',
        component: Admin,
        name: 'admin',
    },
    {
        path: '/admin/user',
        component: AdminUser,
        name: 'admin-user',
        meta: {
            permissionCode: 'access_admin_functions'
        }
    },
    {
        path: '/admin/user/create',
        component: AdminUserCreate,
        name: 'admin-user-create',
        meta: {
            permissionCode: 'add_users'
        }
    },
    {
        path: '/admin/work-request-category',
        component: AdminWorkRequestCategory,
        name: 'admin-work-request-category',
    },
    {
        path: '/admin/work-request-category/create',
        component: AdminWorkRequestCategoryCreate,
        name: 'admin-work-request-category-create',
    },
    {
        path: '/admin/work-request-category/:id',
        component: AdminWorkRequestCategoryEdit,
        name: 'admin-work-request-category-edit',
    },
    {
        path: '*',
        component: NotFound,
        name: 'not-found'
    },
]
