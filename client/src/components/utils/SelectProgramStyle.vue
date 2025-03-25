<template>
    <v-select
        :model-value="selectedItems"
        @update:model-value="updateSelectedItems"
        :items="availableItems"
        :label="label"
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
        updateSelectedItems(newItems: string[]) {
            this.$emit('update:selectedItems', newItems); // Emit the updated value
        }
    }
});
</script>
