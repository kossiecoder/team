<template>
    <nav>
        <v-navigation-drawer
            v-if="isLoggedIn"
            v-model="drawer"
            fixed
            app
            temporary
        >
            <v-list>
                <v-list-item
                    v-for="(item, index) in items"
                    :key="index"
                    :to="{ name: item.componentName }"
                    router
                    exact
                >
                    <v-list-item-action>
                        <v-icon>{{ item.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title v-text="item.title" />
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <v-app-bar
            dark
            fixed
            app
            color="blue lighten-2"
        >
            <v-app-bar-nav-icon
                v-if="isLoggedIn"
                @click.stop="drawer = !drawer"
            ></v-app-bar-nav-icon>
            <v-toolbar-title v-text="title" />
            <v-spacer />
            <v-menu
                v-if="isLoggedIn"
                offset-y
                content-class="dropdown-menu"
                transition="slide-y-transition"
                max-height="500"
            >
                <template v-slot:activator="{ on }">
                    <v-btn
                        icon
                        v-on="on"
                        @click="markAsRead"
                    >
                        <v-badge color="red">
                            <template v-slot:badge>
                                <span v-if="notificationCount > 0">
                                    {{ notificationCount > 99 ? '99+' : notificationCount }}
                                </span>
                            </template>
                            <v-icon>mdi-bell</v-icon>
                        </v-badge>
                    </v-btn>
                </template>

                <v-list
                    two-line
                    class="scroll"
                >
                    <v-list-item
                        v-for="(notification, index) in notifications"
                        :key="index"
                        @click="pushTo(getNotificationInfo(notification).link)"
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{ getNotificationInfo(notification).message }}</v-list-item-title>
                            <v-list-item-subtitle>{{ notification.created_at | utcToLocal }}</v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item v-if="notificationCount === 0 && notifications.length === 0">
                        <v-list-item-content>
                            <v-list-item-title>You don't have new notifications</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-menu>
            <v-menu
                v-if="isLoggedIn"
                offset-y
                content-class="dropdown-menu"
                transition="slide-y-transition"
                class="ml-3"
            >
                <template v-slot:activator="{ on }">
                    <v-btn
                        icon
                        v-on="on"
                    >
                        <v-icon>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>

                <v-list>
                    <v-list-item :to="{name: 'profile'}">
                        <v-list-item-title>Profile</v-list-item-title>
                    </v-list-item>
                    <v-list-item v-if="hasAdminPermission" :to="{name: 'admin'}">
                        <v-list-item-title>Admin</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="logout">
                        <v-list-item-title>Log Out</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>
    </nav>
</template>

<script>
    export default {
        data () {
            return {
                drawer: false,
                items: [
                    { title: 'Home', icon: 'mdi-view-dashboard', componentName: 'home' },
                    { title: 'Chat', icon: 'mdi-chat', componentName: 'chat' },
                    { title: 'Work Requests', icon: 'mdi-folder', componentName: 'tasks' },
                ],
                title: 'Team Portal'
            }
        },

        computed: {
            notificationCount() {
                return this.$store.state.notification.notification.count;
            },

            isLoggedIn () {
                return this.$store.getters['auth/isLoggedIn'];
            },

            hasAdminPermission() {
                return this.$store.state.permission.myPermissions.some(permission => permission === 'access_admin_functions');
            },

            notifications() {
                return this.$store.state.notification.notification.notifications;
            }
        },

        methods: {
            markAsRead() {

            },

            pushTo(to) {
                this.$router.push(to);
            },

            async logout() {
                await this.$store.dispatch('auth/logout');
                await this.$router.push({name: 'login'});
            },

            getNotificationInfo(notification) {
                if(notification.type === "App\\Notifications\\TaskAssigned") {
                    const taskId = notification.data.task.id;
                    return {
                        message: 'Work request #' + taskId + ' was assigned to you',
                        link: {name: 'task', params: {id: taskId}}
                    };
                }

                if(notification.type === "App\\Notifications\\TaskUpdated") {
                    const taskId = notification.data.task.id;
                    return {
                        message: 'Work request #' + taskId + ' has been updated',
                        link: {name: 'task', params: {id: taskId}}
                    };
                }

                if(notification.type === "App\\Notifications\\TaskCommentSubmitted") {
                    const taskId = notification.data.comment.task_id;
                    if(notification.data.comment.user) {
                        const commenter = notification.data.comment.user.first_name;
                        return {
                            message: commenter + ' commented on work-requests #' + taskId,
                            link: {name: 'task', params: {id: taskId}}
                        };
                    }
                }
            }
        }
    }
</script>
