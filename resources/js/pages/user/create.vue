<template>
    <v-container fluid mt-4>
        <v-row align-center justify-center>
            <v-col xs12 sm8>
                <v-form
                    ref="form"
                    lazy-validation
                    @submit.prevent="onSubmit"
                >
                    <v-card class="elevation-12">
                        <v-toolbar dark color="primary">
                            <v-toolbar-title>Add New Member</v-toolbar-title>
                        </v-toolbar>
                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col px-3 cols="12" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-email"
                                            name="email"
                                            label="Email*"
                                            placeholder="Please type email address"
                                            type="email"
                                            v-model="user.email"
                                            :rules="emailRules"
                                            required
                                            autocomplete="new-password"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col px-3 cols="12" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-account-outline"
                                            name="first_name"
                                            label="First Name*"
                                            placeholder="Please type first name"
                                            type="text"
                                            v-model="user.firstName"
                                            :rules="nameRules"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col px-3 cols="12" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-account-outline"
                                            name="last_name"
                                            label="Last Name"
                                            placeholder="Please type last name"
                                            type="text"
                                            v-model="user.lastName"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col px-3 cols="12" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-lock"
                                            name="password"
                                            label="Password*"
                                            placeholder="Please type your password"
                                            id="password"
                                            type="password"
                                            v-model="user.password"
                                            :rules="nameRules"
                                            autocomplete="new-password"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                    <v-col px-3 cols="12" sm="6">
                                        <v-text-field
                                            prepend-icon="mdi-lock"
                                            name="password_confirmation"
                                            label="Confirm Password*"
                                            placeholder="Please type your password"
                                            type="password"
                                            v-model="user.password_confirmation"
                                            :rules="nameRules"
                                            required
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-container>

                            <p class="red--text text-xs-right" v-if="error">{{error}}</p>

                        </v-card-text>
                        <v-card-actions class="pr-4 pb-4">
                            <v-spacer></v-spacer>
                            <v-btn color="primary" type="submit">Add New Member</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import userApi from '@/api/user';
    import snackBarMixin from "@/mixins/snackBarMixin";

    export default {
        mixins: [snackBarMixin],

        data() {
            return {
                user: {
                    email: '',
                    firstName: '',
                    lastName: '',
                    password: '',
                    password_confirmation: '',
                },
                nameRules:[
                    v => !!v || 'This field is required.',
                ],
                emailRules: [
                    v => !!v || 'E-mail is required',
                    v => /.+@.+/.test(v) || 'E-mail must be valid'
                ],
                error: ''
            }
        },
        methods: {
            async onSubmit() {
                if(this.$refs.form.validate()) {
                    this.error = '';
                    try {
                        await userApi.storeUser({...this.user});
                        this.$refs.form.reset();
                        this.openSnackBar('You have successfully create a user!')
                    } catch (err) {
                        if(err.response.status === 403) {
                            this.error = 'You do not have permission to create a user';
                        }
                    }
                }
            },

            initUserData() {
                this.user.email = '';
                this.user.firstName = '';
                this.user.lastName = '';
                this.user.password = '';
                this.user.password_confirmation = '';
            }
        }
    }
</script>
