<template>
    <v-select 
        :class="selectedItems.length > 0 ? 'program-meny-selected' : ''"
        :model-value="selectedItems"
        @update:model-value="updateSelectedItems"
        :items="availableItems"
        :label="selectedItems.length > 0 ? label + ' filtrering er aktiv' : label"
        item-title="title"
        item-value="id"
        multiple
        persistent-placeholder
        class="v-select-program-meny"
    >
        <template v-slot:selection="{ item }">
            <!-- This prevents displaying selected items -->
        </template>
    </v-select>
</template>

<script lang="ts">
import type { PropType } from 'vue';  
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        label: {
            type: String,
            required: true
        },
        availableItems: {
            type: Array as PropType<{id: number|string, title: string}[]>, 
            required: true
        },
        selectedItems: {
            type: Array as PropType<{id: number|string, title: string}[]>, 
            required: true
        },
    },
    emits: ['update:selectedItems'], // Declare the event
    methods: {
        updateSelectedItems(newItems: any[]) : any {
            this.$emit('update:selectedItems', newItems); // Emit the updated value
        }
    }
});
</script>

<style scoped>
.program-meny-selected :deep(.v-input__control) {
    border-color: #00ff89 !important;
}
.program-meny-selected :deep(.v-label) {
    font-size: 14px !important;
    margin-left: 9px;
}
.v-select-program-meny :deep(.v-field__field) {
    display: contents;
}
</style>
