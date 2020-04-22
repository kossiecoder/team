<template>
    <ListWrapperLayoutWithTopArea
        :back-to="{ name: 'admin' }"
        :new-to="{ name: 'admin-work-request-category-create' }"
    >
        <template v-slot:button-title>
            NEW CATEGORY
        </template>
        <template v-slot:title>
            Work Request Categories
        </template>
        <template v-slot:main>
            <WorkRequestList />
        </template>
    </ListWrapperLayoutWithTopArea>
</template>

<script>
    import ListWrapperLayoutWithTopArea from "@/components/layouts/ListWrapperLayoutWithTopArea";
    import WorkRequestList from "@/components/admin/work-request/WorkRequestList";

    export default {
        components: {
            ListWrapperLayoutWithTopArea,
            WorkRequestList
        },
    }
</script>
