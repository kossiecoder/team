<template>
    <v-container>
        <v-layout>
            <v-flex>
                <h1 class="mb-3">
                    Settings
                </h1>
                <v-tabs
                    color="teal lighten-3"
                    dark
                    slider-color="teal"
                    fixed-tabs
                >
                    <v-tab
                        v-for="(tabTitle, index) in tabTitles"
                        :key="index"
                        ripple
                        growsave
                    >
                        {{ tabTitle.title }}
                    </v-tab>
                    <v-tab-item>
                        <v-card flat>
                            <v-container grid-list-xl>
                                <v-form>
                                    <v-layout wrap>
                                        <v-flex
                                            xs12
                                            lg4
                                            offset-lg2
                                        >
                                            <v-text-field
                                                v-model="user.email"
                                                prepend-icon="mdi-email"
                                                name="email"
                                                label="Email"
                                                placeholder="Email Address"
                                                type="email"
                                                required
                                            ></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 lg4>
                                            <v-text-field prepend-icon="mdi-account-outline" name="first_name" label="First Name" placeholder=" " type="text" v-model="user.firstName" required></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 lg4 offset-lg2>
                                            <v-text-field prepend-icon="mdi-account-outline" name="last_name" label="Last Name" placeholder=" " type="email" v-model="user.lastName" required></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 lg4>
                                            <v-text-field
                                                v-model="user.password"
                                                prepend-icon="mdi-lock"
                                                name="password"
                                                label="Update Password"
                                                placeholder=" "
                                                type="password"
                                                required
                                            ></v-text-field>
                                        </v-flex>
                                        <v-flex xs12 lg4 offset-lg2>
                                            <v-text-field
                                                v-model="user.password_confirmation"
                                                prepend-icon="mdi-lock"
                                                name="password_confirmation"
                                                label="Confirm Password"
                                                placeholder=" "
                                                type="password"
                                                required
                                            ></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                    <v-layout>
                                        <v-flex xs12 lg8 offset-lg2>
                                            <p class="text-lg-right">
                                                <v-btn color="info" large @click.stop="changePassword">Save</v-btn>
                                            </p>
                                        </v-flex>
                                        <v-flex xs2></v-flex>
                                    </v-layout>
                                </v-form>
                            </v-container>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-layout>
                            <v-flex>
                                Preferences to be here
                            </v-flex>
                        </v-layout>
                    </v-tab-item>
                </v-tabs>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import userApi from '@/api/user';
    import snackBarMixin from "@/mixins/snackBarMixin";

    export default {
        mixins: [snackBarMixin],

        data () {
            return {
                tabTitles: [
                    { title: 'Profile'},
                    { title: 'Preferences'},
                ],
                tabContents: [

                ],
                user: {
                    id: '',
                    email: '',
                    firstName: '',
                    lastName: '',
                    password: '',
                    password_confirmation: '',
                },
                notification: false,
                message: ''
            }
        },

        async created() {
            try {
                const res = await userApi.fetchMe();
                const me = res.data.me;
                this.user.id = me.id;
                this.user.email = me.email;
                this.user.firstName = me.first_name;
                this.user.lastName = me.last_name;
            } catch (err) {
                console.log(err);
            }
        },

        methods: {
            async changePassword() {
                try {
                    await userApi.updateUser(this.user);
                    this.openSnackBar('Update Successful');
                } catch (err) {
                    this.openSnackBar(err.response.data.message);
                }
            }
        },
    }
</script>
