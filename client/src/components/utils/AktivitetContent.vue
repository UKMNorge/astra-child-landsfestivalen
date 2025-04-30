<template>
    <div v-if="aktivitetItem != null">
        <div class="tags as-margin-top-space-4">
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
                <v-chip class="as-margin-right-space-1 as-margin-bottom-space-1" v-for="tidspunkt in aktivitetItem.getTidspunkterSortert()" :key="tidspunkt.id">
                    {{ tidspunkt.getHumanFormat() }}
                </v-chip>
        </div>

        <div v-show="aktivitetItem.sted.length > 0" class="as-margin-top-space-4">
            <h5>Sted: {{ aktivitetItem.sted }}</h5>
        </div>

        <div v-show="aktivitetItem.kursholder && aktivitetItem.kursholder.length > 0" class="as-margin-top-space-2">
            <h5>Med: {{ aktivitetItem.kursholder }}</h5>
        </div>
        
        <div class="beskrivelser as-margin-top-space-4">
            <div class="beskrivelse">
                <span class="beskrivelse" v-html="aktivitetItem.getBeskrivelse()"></span>
            </div>

            <div class="beskrivelse beskrivelse as-margin-top-space-4">
                <div class="as-margin-bottom-space-4">
                    <hr>
                </div>
                <h5>Beskrivelse av den som leder aktiviteten</h5>
                <span class="beskrivelse" v-html="aktivitetItem.getLederBeskrivelse()"></span>
            </div>
        </div>

        <div v-if="aktivitetItem.harMeldPaa()" class="as-margin-top-space-4">
            <div>
                <h6>Antall ledige plasser <b>{{ getAntallLedige() }}</b></h6>
            </div>
            <v-btn
                class="v-btn-as as-btn-default landsfestivalen-btn-green"
                color="#000"
                size="x-large"
                rounded="large"
                @click="meldPaa(aktivitetItem)"
                variant="outlined" >
                Meld deg på
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
        getAntallLedige() {
            if (this.aktivitetItem != null && this.aktivitetItem.harMeldPaa()) {
                return this.aktivitetItem.getAlleLedigePlasser();
            }
            return 0;
        },
        meldPaa(aktivitetItem : Aktivitet) {
            window.location.href = 'https://aktiviteter.ukm.no/' + aktivitetItem.id;
        }
    }
});
</script>

<style scoped>
.hendelse-item-bilde {
    border-radius: var(--radius-high);
    overflow: hidden;
}
.landsfestivalen-btn-green {
    background: #00ff89 !important;
}
</style>
