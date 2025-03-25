<template>
        <Program :programType="programType" />
    </template>
<script lang="ts">
import Program from './Program.vue';

export default {
    components: {
        Program : Program
    },
    data() {
        return {
            programType : 'deltakere'
        }
    },
    methods : {
        init(){
            
        }
    }
}
    
</script>