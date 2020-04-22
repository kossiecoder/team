<template>
    <v-card>
        <v-container grid-list-xl>
            <v-layout wrap>
                <v-flex xs12 lg8 offset-lg2>
                    <ProfilePermissionTree
                        v-for="(node, index) in nodes"
                        :key="index"
                        :nodes="node"
                        :depth="0"
                        @updated-tree="updateTree"
                    />
                </v-flex>
            </v-layout>
        </v-container>
    </v-card>
</template>
<script>
    import ProfilePermissionTree from '@/components/profile/Detail/ProfilePermissionTree';
    import permissionApi from '@/api/permission';

    export default {
        components: {
            ProfilePermissionTree
        },

        data() {
            return {
                nodes: []
            }
        },

        async created() {
            try {
                const res = await permissionApi.fetchUserPermissions(this.$route.params.id);
                this.nodes = res.data.permission_tree_array;
            } catch (err) {
                console.log(err);
            }
        },

        methods: {
            updateTree(val) {
                this.nodes = val;
            }
        }
    }
</script>
