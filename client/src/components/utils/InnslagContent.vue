<template>
    <div v-if="innslagItem != null" :data-id="innslagItem.getId()">
        <!-- <div v-show="innslagItem.getImage() != null && innslagItem.getImage().length > 0" class="hendelse-item-bilde as-margin-top-space-4">
            <img :src="innslagItem.getImage() ?? ''" alt="Bilde av aktivitet">
        </div> -->

        <div class="as-margin-top-space-4">
            <div class="chip-group-innslag-personer">
                <v-chip class="as-margin-right-space-1 as-margin-bottom-space-1" v-for="person in innslagItem.personer">
                    <span><b>{{ person.fornavn }} {{ person.etternavn }}</b> {{ person.rolle ? '(' + person.rolle + ')' : '' }}</span>
                </v-chip>
            </div>
        </div>
        
        <div class="innslag-bilder">
            <div v-for="bilde in innslagItem.getBilder()" class="hendelse-item-bilde as-margin-top-space-4">
                <img :src="bilde" alt="Bilde av innslag" />
            </div>

        </div>

        <div class="beskrivelser as-margin-top-space-2">
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
