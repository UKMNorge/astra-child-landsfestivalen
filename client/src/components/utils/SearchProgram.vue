<template>
    <v-text-field
        class="search-program"
        v-model="searchWords"
        :class="isFullWidth ? 'full-width' : ''"
        @update:model-value="updateSearchWords"
        @focus="searchFocus = true"
        @blur="searchFocus = false"
        :label="searchLabel ?? 'Søk'"
        prepend-inner-icon="mdi-magnify"
        clearable
    ></v-text-field>
</template>

<script lang="ts">
import type { PropType } from 'vue';  
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        searchLabel: {
            type: String,
            required: false
        },
    },
    data() {
        return {
            searchWords: '',
            searchFocus: false,
        };
    },
    computed: {
        isFullWidth() : boolean {
            if(this.searchWords != null && this.searchWords.length > 0) {
                return true;
            }
            return this.searchFocus;
        }
    },
    emits: ['update:searchWords'], // Declare the event
    methods: {
        updateSearchWords(newItems: string) : any {
            this.$emit('update:searchWords', newItems); // Emit the updated value
        },
    }
});
</script>

<style scoped>
.search-program {
    padding: 0;
}
.search-program :deep(.v-field) {
    background: transparent;
    border: solid 2px;
    border-radius: 10px;
}
.search-program :deep(.v-input__control input.v-field__input) {
    background: transparent !important;
    color: #fff;
    padding-top: 28px;
    padding-left: 7px;
}
.search-program :deep(.v-label) {
    color: #c9c9c9 !important;
}
.search-program :deep(.v-icon) {
    opacity: 1;
}
.search-program :deep(.v-input__control) {
    width: 53px;
    overflow: hidden;
    border-radius: 10px;
}
.search-program.full-width :deep(.v-input__control) {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    background: #00004c;
}
@media (max-width: 520px) {
    .btn-close {
        top: 7px;
        right: 0px;
    }
    .search-program :deep(.v-field),
    .search-program :deep(.v-field__prepend-inner) {
        height: 48px;
    }
    .search-program.full-width :deep(.v-field),
    .search-program.full-width :deep(.v-field__prepend-inner) {
        height: 60px;
    }
    .search-program.full-width :deep(.v-input__control) {
        top: -8px;
    }
}
</style>
