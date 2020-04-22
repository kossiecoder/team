<template>
    <v-card flat>
        <v-container grid-list-xl>
            <v-form>
                <v-layout wrap>
                    <v-flex xs12 lg4 offset-lg2>
                        <v-text-field
                            prepend-icon="email"
                            name="email"
                            label="Email"
                            placeholder="Email Address"
                            type="email"
                            v-model="user.email"
                            required
                            :disabled="!hasPermission('edit_users')"
                        ></v-text-field>
                    </v-flex>
                    <v-flex xs12 lg4>
                        <v-text-field
                            prepend-icon="person"
                            name="first_name"
                            label="First Name"
                            placeholder=" "
                            type="text"
                            v-model="user.firstName"
                            required
                            :disabled="!hasPermission('edit_users')"
                        ></v-text-field>
                    </v-flex>
                    <v-flex xs12 lg4 offset-lg2>
                        <v-text-field
                            prepend-icon="person"
                            name="last_name"
                            label="Last Name"
                            placeholder=" "
                            type="email"
                            v-model="user.lastName"
                            required
                            :disabled="!hasPermission('edit_users')"
                        ></v-text-field>
                    </v-flex>
                    <v-flex xs12 lg4>
                        <v-text-field
                            prepend-icon="lock"
                            name="password"
                            label="Update Password"
                            placeholder=" "
                            type="password"
                            v-model="user.password"
                            required
                            :disabled="!hasPermission('edit_users')"
                        ></v-text-field>
                    </v-flex>
                    <v-flex xs12 lg4 offset-lg2>
                        <v-text-field
                            v-model="user.password_confirmation"
                            prepend-icon="lock"
                            name="password_confirmation"
                            label="Confirm Password"
                            placeholder=" "
                            type="password"
                            required
                            :disabled="!hasPermission('edit_users')"
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
</template>
<script>
    import userApi from '@/api/user';
    import snackBarMixin from "@/mixins/snackBarMixin";
    import permissionMixin from "@/mixins/permissionMixin";

    export default {
        mixins: [
            snackBarMixin,
            permissionMixin
        ],

        data () {
            return {
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
