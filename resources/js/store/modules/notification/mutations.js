export default {
    add_notification_count(state, count) {
        state.notification.count += count;
    },

    update_notifications(state, notification) {
        if(state.notification.notifications.length >= 10) {
            state.notification.notifications.pop();
        }
        state.notification.notifications.unshift(notification);
    },

    set_initial_notification_count(state, count) {
        state.notification.count = count;
    },

    set_initial_notifications(state, notifications) {
        state.notification.notifications = notifications;
    },

    updateNewMessage(state, boolean) {
        state.notification.newMessage = boolean;
    },

    updateNewChannelMessage(state, boolean) {
        state.notification.newChannelMessage = boolean;
    },
}
