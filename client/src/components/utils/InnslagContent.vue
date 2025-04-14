<template>
    <div v-if="innslagItem != null">
        <div class="tags">
            <v-chip-group>
                <!-- <v-chip variant="outlined" v-for="tag in aktivitetItem.tags">
                    {{ tag.navn }}
                </v-chip> -->
            </v-chip-group>
        </div>
        <div v-show="innslagItem.getImage() != null && innslagItem.getImage().length > 0" class="hendelse-item-bilde as-margin-top-space-4">
            <img :src="innslagItem.getImage() ?? ''" alt="Bilde av aktivitet">
        </div>

        <div class="as-margin-top-space-4">
            <v-chip-group>
                <v-chip v-for="tidspunkt in aktivitetItem.tidspunkter" :key="tidspunkt.id">
                    {{ tidspunkt.getHumanFormat() }}
                </v-chip>
            </v-chip-group>
        </div>

        <div v-show="innslagItem.sted.length > 0" class="as-margin-top-space-4">
            <h5>Sted: {{ innslagItem.sted }}</h5>
        </div>
        
        <div class="beskrivelser as-margin-top-space-4">
            <div class="beskrivelse">
                <span class="beskrivelse" v-html="innslagItem.getBeskrivelse()"></span>
            </div>

            <div class="beskrivelse beskrivelse as-margin-top-space-4">
                <h5>Beskrivelse av den som leder aktiviteten</h5>
                <span class="beskrivelse" v-html="innslagItem.getLederBeskrivelse()"></span>
            </div>
        </div>

        <div v-if="innslagItem.harMeldPaa()" class="as-margin-top-space-4">
            <v-btn
                class="v-btn-as v-btn-success"
                color="#000"
                size="x-large"
                rounded="large"
                variant="outlined" >
                Meld på
            </v-btn>
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
