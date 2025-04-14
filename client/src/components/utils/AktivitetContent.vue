<template>
    <div v-if="aktivitetItem != null">
        <div class="tags">
            <v-chip-group>
                <v-chip variant="outlined" v-for="tag in aktivitetItem.tags">
                    {{ tag.navn }}
                </v-chip>
            </v-chip-group>
        </div>
        <div v-show="aktivitetItem.getImage() != null && aktivitetItem.getImage().length > 0" class="hendelse-item-bilde as-margin-top-space-4">
            <img :src="aktivitetItem.getImage() ?? ''" alt="Bilde av aktivitet">
        </div>

        <div class="as-margin-top-space-4">
            <v-chip-group>
                <v-chip v-for="tidspunkt in aktivitetItem.tidspunkter" :key="tidspunkt.id">
                    {{ tidspunkt.getHumanFormat() }}
                </v-chip>
            </v-chip-group>
        </div>

        <div v-show="aktivitetItem.sted.length > 0" class="as-margin-top-space-4">
            <h5>Sted: {{ aktivitetItem.sted }}</h5>
        </div>
        
        <div class="beskrivelser as-margin-top-space-4">
            <div class="beskrivelse">
                <span class="beskrivelse" v-html="aktivitetItem.getBeskrivelse()"></span>
            </div>

            <div class="beskrivelse beskrivelse as-margin-top-space-4">
                <h5>Beskrivelse av den som leder aktiviteten</h5>
                <span class="beskrivelse" v-html="aktivitetItem.getLederBeskrivelse()"></span>
            </div>
        </div>

        <div v-if="aktivitetItem.harMeldPaa()" class="as-margin-top-space-4">
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
import Aktivitet from '../../objects/Aktivitet';
import type { PropType } from 'vue';  
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        aktivitetItem: {
            type: [Aktivitet, null],
            required: true
        },
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
