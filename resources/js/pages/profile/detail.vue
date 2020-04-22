<template>
    <v-container>
        <v-layout>
            <v-flex>
                <h1 class="mb-3">Settings</h1>
                <v-tabs
                    color="teal lighten-3"
                    dark
                    slider-color="teal"
                    fixed-tabs
                >
                    <v-tab
                        v-for="(tab, index) in tabs"
                        :key="index"
                        v-if="tab.permission"
                        ripple
                        grow
                    >
                        {{ tab.title }}
                    </v-tab>
                    <v-tab-item>
                        <profile-form></profile-form>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-container grid-list-xl>
                                <v-layout wrap>
                                    <v-flex xs12 lg8 offset-lg2>
                                        Preferences
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item v-if="hasPermission('edit_user_permissions')">
                        <ProfilePermission />
                    </v-tab-item>
                </v-tabs>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import ProfilePermission from '@/components/profile/Detail/ProfilePermission';
    import ProfileForm from '@/components/profile/Detail/ProfileForm';
    import permissionMixin from "@/mixins/permissionMixin";

    export default {
        components: {
            ProfilePermission,
            ProfileForm,
        },

        mixins: [
            permissionMixin
        ],

        data () {
            return {
                tabs: [
                    {
                        title: 'Profile',
                        permission: true
                    },
                    {
                        title: 'Preferences',
                        permission: true
                    },
                    {
                        title: 'Permissions',
                        permission: this.hasPermission('edit_user_permissions')
                    },
                ],
                tabContents: [

                ],
                notification: false,
                message: ''
            }
        },
    }
</script>

<style scoped lang="scss">
    .tab-items-row >>> .v-window__container,
    .tab-items-row >>> .v-window-item {
        height: 100%;
    }
</style>
