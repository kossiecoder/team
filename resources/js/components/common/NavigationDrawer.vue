<template>
    <v-navigation-drawer
        v-model="drawer"
        :mini-variant="mini"
        absolute
        light
        temporary
    >
        <v-list class="pa-1">
            <v-list-item v-if="mini" @click.stop="mini = !mini">
                <v-list-item-action>
                    <v-icon>chevron_right</v-icon>
                </v-list-item-action>
            </v-list-item>

            <v-list-item avatar tag="div">
                <v-list-item-avatar>
                    <img src="https://randomuser.me/api/portraits/men/85.jpg">
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-title>John Leider</v-list-item-title>
                </v-list-item-content>

                <v-list-item-action>
                    <v-btn icon @click.stop="mini = !mini">
                        <v-icon>chevron_left</v-icon>
                    </v-btn>
                </v-list-item-action>
            </v-list-item>
        </v-list>

        <v-list class="pt-0" dense>
            <v-divider light></v-divider>
            <v-list-item
                v-for="item in items"
                :key="item.title"
                @click="pushTo(item.componentName)"
            >
                <v-list-item-action>
                    <v-icon>{{ item.icon }}</v-icon>
                </v-list-item-action>

                <v-list-item-content>
                    <v-list-item-title>{{ item.title }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>
<script>
    import userMixin from "@/mixins/userMixin";

    export default {
        mixins: [userMixin],

        props: {
            drawer: {
                type: Boolean,
                required: true
            }
        },

        data() {
            return {
                items: [
                    { title: 'Home', icon: 'dashboard', componentName: 'Home' },
                    { title: 'chat', icon: 'question_answer', componentName: 'ChatMainPrivate' }
                ],
                mini: false,
                right: null,
            }
        },

        methods: {
            pushTo(componentName) {
                this.$router.push({name: componentName, params: { id: this.currentUser.id }})
            }
        },
    }
</script>
