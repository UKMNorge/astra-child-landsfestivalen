<template>
    <v-select 
        :class="selectedItems.length > 0 ? 'program-meny-selected' : ''"
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
      <!-- Hide selected items display -->
      <template v-slot:selection="{ item }"></template>
  
      <!-- Add a button to the right side -->
      <template v-slot:append-inner>
        <v-btn 
            v-show="selectedItems.length > 0"
            @mousedown.stop
            @click="handleButtonClick"
            icon
            class="ml-2 btn-close"
            size="x-small"
            variant="text"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
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
    data() {
        return {
            director : (<any>window).director, // Definert i main.ts
        };
    },
    mounted() {
        console.log(this.label)
        let filterId = 'filter-'+this.label.toLowerCase();
        let selectedItem = this.director.getParam(filterId) ?? null;

        // Check for array
        if(selectedItem != null && selectedItem.length > 0) {
            selectedItem = selectedItem.split(',');
        } else {
            selectedItem = [];
        }

        // Check if selectedItem is an array
        if(Array.isArray(selectedItem)) {
            for(let item of selectedItem) {
                for(let availableItem of this.availableItems) {
                    if((<string>availableItem.title).toLowerCase() == item.toLowerCase() || (<string>availableItem.id).toLowerCase() == item.toLowerCase()) {
                        this.selectedItems.push((<any>availableItem.id));
                        break;
                    }
                }
            }
        }
        // If selectedItem is not an array, check if it exists in availableItems
        // and push it to selectedItems if found
        else
        if(typeof selectedItem === 'string') {
            for(let item of this.availableItems) {
                if((<string>item.title).toLowerCase() == selectedItem.toLowerCase() || (<string>item.id).toLowerCase() == selectedItem.toLowerCase()) {
                    this.selectedItems.push((<any>item.id));
                    break;
                }
            }
        }
    },
    emits: ['update:selectedItems'], // Declare the event
    methods: {
        updateSelectedItems(newItems: any[]) : any {
            this.$emit('update:selectedItems', newItems); // Emit the updated value
            this.updateUrlArgs(newItems);
        },
        handleButtonClick() {
            this.$emit('update:selectedItems', []); // Emit the updated value
            this.updateUrlArgs(null);
        },
        updateUrlArgs(newItem : any) {
            if(newItem == null) {
                this.director.addParam('filter-'+this.label.toLowerCase(), '');
                return;
            }
            
            let currentItems = this.selectedItems;
            currentItems.push(newItem);

            this.director.addParam('filter-'+this.label.toLowerCase(), '');
            
            let value = currentItems[currentItems.length-1].join(',');
            
            let filterId = 'filter-'+this.label.toLowerCase();
            this.director.addParam(filterId, value);
        }
    }
});
</script>

<style scoped>
.program-meny-selected :deep(.v-input__control) {
    border-color: #00ff89 !important;
}
.program-meny-selected :deep(.v-label) {

}
.v-select-program-meny :deep(.mdi-menu-down) {
    display: none !important;
}
.btn-close {
    position: absolute;
    top: 13px;
    right: 7px;
}
@media (max-width: 520px) {
    .btn-close {
        top: 7px;
        right: 0px;
    }
}
</style>
