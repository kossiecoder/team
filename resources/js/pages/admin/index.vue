<template>
    <ListWrapperLayout>
        <template v-slot:title>
            Admin Dashboard
        </template>
        <template v-slot:main>
            <AdminDashboardList />
        </template>
    </ListWrapperLayout>
</template>

<script>
    import ListWrapperLayout from "@/components/layouts/ListWrapperLayout";
    import AdminDashboardList from "@/components/admin/AdminDashboardList";

    export default {
        components: {
            ListWrapperLayout,
            AdminDashboardList
        },
    }
</script>
