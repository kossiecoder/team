<template>
    <ListWrapperLayoutWithTopArea :back-to="{ name: 'admin-work-request-category' }">
        <template v-slot:title>
            Edit Work Request Category
        </template>
        <template v-slot:main>
            <WorkRequestForm :is-editing="true" />
        </template>
    </ListWrapperLayoutWithTopArea>
</template>

<script>
    import ListWrapperLayoutWithTopArea from "@/components/layouts/ListWrapperLayoutWithTopArea";
    import WorkRequestForm from "@/components/admin/work-request/WorkRequestForm";

    export default {
        components: {
            ListWrapperLayoutWithTopArea,
            WorkRequestForm
        },
    }
</script>
