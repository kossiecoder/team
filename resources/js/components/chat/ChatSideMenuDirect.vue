<template>
    <v-list subheader dense>
        <v-subheader>
            Direct Messages
        </v-subheader>
        <v-list-item
            v-for="user in users"
            :key="user.id"
            :to="{name: 'chat-main-private', params: {id: user.id}}"
        >
            <v-list-item-content>
                <v-list-item-title>{{ user.first_name + ' ' + user.last_name }} {{ user.id === currentUser.id ? '(you)' : '' }}</v-list-item-title>
            </v-list-item-content>

            <v-list-item-action>
                <span
                    v-if="user.from_messages_count > 0 && currentUser.id !== user.id"
                    class="red white--text v-btn--round px-2"
                >
                    {{ user.from_messages_count > 99 ? '99+' : user.from_messages_count }}
                </span>
            </v-list-item-action>
        </v-list-item>
    </v-list>
</template>
<script>
    import userMixin from "@/mixins/userMixin";

    export default {
        mixins: [userMixin],

        computed: {
            paramsId() {
                return this.$route.params.id;
            },
            users() {
                return _.orderBy(this.$store.state.user.users, 'from_messages_count', 'desc');
            }
        }
    }
</script>
