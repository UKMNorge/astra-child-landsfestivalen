<template>
    <div v-if="innslagItem != null">
        <!-- <div v-show="innslagItem.getImage() != null && innslagItem.getImage().length > 0" class="hendelse-item-bilde as-margin-top-space-4">
            <img :src="innslagItem.getImage() ?? ''" alt="Bilde av aktivitet">
        </div> -->

        <div class="as-margin-top-space-2">
            <v-chip-group>
                <v-chip v-for="person in innslagItem.personer">
                    {{ person.fornavn }} {{ person.etternavn }}
                </v-chip>
            </v-chip-group>
        </div>
        
        <div class="beskrivelser as-margin-top-space-4">
            <div class="beskrivelse">
                <span class="beskrivelse" v-html="innslagItem.getBeskrivelse()"></span>
            </div>

        
        </div>
    </div>

</template>

<script lang="ts">
import Innslag from '../../objects/Innslag';
import Hendelse from '../../objects/Hendelse';
import type { PropType } from 'vue';  
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        innslagItem: {
            type: [Innslag, null],
            required: true
        },
    },
    mounted() {
        console.log('innslag content mounted');
    },
    methods: {

    }
});
</script>

<style scoped>
.hendelse-item-bilde {
    border-radius: var(--radius-high);
    overflow: hidden;
}
</style>
