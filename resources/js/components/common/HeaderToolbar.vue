<template>
    <v-toolbar>
        <v-toolbar-side-icon @click.stop="toggleDrawer"></v-toolbar-side-icon>
        <v-toolbar-title>Team Portal</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items class="hidden-sm-and-down" v-if="isLoggedIn">
            <v-btn flat>{{ currentUser.name }}</v-btn>
            <v-menu bottom left>
                <template v-slot:activator="{ on }">
                    <v-btn
                        light
                        icon
                        v-on="on"
                    >
                        <v-icon>expand_more</v-icon>
                    </v-btn>
                </template>

                <v-list>
                    <v-list-item @click="">
                        <v-list-item-title>Profile</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="logout">
                        <v-list-item-title>Log Out</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-toolbar-items>
    </v-toolbar>
</template>
<script>
    import userMixin from "@/mixins/userMixin";

    export default {
        mixins: [userMixin],

        data(){
            return {
                drawer: false,
            }
        },

        computed: {
            isLoggedIn () {
                return this.$store.getters.isLoggedIn;
            }
        },

        methods: {
            toggleDrawer() {
                this.drawer = !this.drawer;
                this.$emit('drawer', this.drawer);
            },
            updateDrawer(value) {
                this.drawer = value;
            },
            logout() {
                this.$store.dispatch('auth/logout').then(()=> {
                    this.$router.push('/login');
                });
            },

        },


    }
</script>
