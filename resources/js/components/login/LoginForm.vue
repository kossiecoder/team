<template>
    <v-form
        ref="form"
        v-model="valid"
        @submit.prevent="onSubmit"
    >
        <v-card class="elevation-12">
            <v-toolbar
                color="primary"
                dark
                flat
            >
                <v-toolbar-title>Login</v-toolbar-title>
            </v-toolbar>
            <v-card-text>
                <v-text-field
                    v-model="user.email"
                    label="Email"
                    name="email"
                    prepend-icon="mdi-email"
                    type="email"
                    placeholder="Please type email address"
                    :rules="emailRules"
                ></v-text-field>
                <v-text-field
                    v-model="user.password"
                    label="Password"
                    name="password"
                    prepend-icon="mdi-lock"
                    type="password"
                    placeholder="Please type your password"
                    :rules="passwordRules"
                ></v-text-field>
                <p class="red--text text-right" v-if="error">{{error}}</p>
            </v-card-text>
            <v-card-actions>
                <div class="flex-grow-1"></div>
                <v-btn
                    type="submit"
                    color="primary"
                    :disabled="!valid"
                    :loading="loading"
                >
                    Login
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-form>
</template>

<script>
    export default {
        data() {
            return {
                loading: false,
                valid: false,
                user: {
                    email: '',
                    password: ''
                },
                emailRules:[
                    v => !!v || 'Email is required.',
                    v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
                ],
                passwordRules:[
                    v => !!v || 'Password is required.',
                ],
                error: ''
            }
        },

        methods: {
            async onSubmit() {
                if(this.$refs.form.validate()) {
                    this.error = '';
                    this.loading = true;
                    try {
                        await this.$store.dispatch('auth/login', {...this.user});
                        await this.$router.push({ name: 'home' });
                    } catch (error) {
                        this.error = error.response.data.message;
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    }
</script>
