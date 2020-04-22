<template>
    <div class="ml-5">
        <div
            class="mb-2 pa-2 subheading hs-permission"
            :class="nodes.allowed ? 'has-permission' : 'no-permission'"
            @click="togglePermission"
        >
            <v-icon v-if="nodes.children">
                arrow_drop_down
            </v-icon> <span :class="nodes.children ? '' : 'pl-3'">{{ nodes.name }}</span>
        </div>
        <ProfilePermissionTree
            v-for="(node, index) in nodes.children"
            :key="index"
            :nodes="node"
            :depth="depth + 1"
            @updated-tree="updateTree"
        />
    </div>
</template>
<script>
    import ProfilePermissionTree from '@/components/profile/Detail/ProfilePermissionTree';
    import permissionApi from '@/api/permission';
    export default {
        name: 'ProfilePermissionTree',

        components: {
            ProfilePermissionTree
        },

        props: ['nodes', 'depth'],

        computed: {
            indent() {
                return { width: `${100 - (this.depth * 2)}%`};
            }
        },

        methods: {
            async togglePermission() {
                try {
                    const res = await permissionApi.updateUserPermission(this.$route.params.id, {
                        allowed: !this.nodes.allowed,
                        permissionCode: this.nodes.code
                    });
                    this.$emit('updated-tree', res.data.permission_tree_array);
                } catch (err) {
                    console.log(err);
                }
            },
            updateTree(val) {
                this.$emit('updated-tree', val);
            }
        },
    }
</script>
<style scoped lang="scss">
    .has-permission {
        background-color: #c8eecc;
    }

    .no-permission {
        background-color: #efcbcb;
    }

    .hs-permission {
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
    }
</style>
