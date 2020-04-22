<template>
    <ListWrapperLayoutWithTopArea :back-to="{ name: 'admin-work-request-category' }">
        <template v-slot:title>
            Add Work Request Category
        </template>
        <template v-slot:main>
            <WorkRequestForm :is-editing="false" />
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
