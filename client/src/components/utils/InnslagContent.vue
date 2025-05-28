<template>
    <div v-if="innslagItem != null" :data-id="innslagItem.getId()">
        <!-- <div v-show="innslagItem.getImage() != null && innslagItem.getImage().length > 0" class="hendelse-item-bilde as-margin-top-space-4">
            <img :src="innslagItem.getImage() ?? ''" alt="Bilde av aktivitet">
        </div> -->
        <div class="share-buttons as-margin-top-space-3 as-margin-bottom-space-4">
            <v-btn
                icon
                class="rounded-circle share-button"
                @click="onShare">
                <v-icon>mdi-share-variant</v-icon>
            </v-btn>
        </div>
        
        <div class="as-margin-top-space-4">
            <div class="chip-group-innslag-personer">
                <v-chip class="as-margin-right-space-1 as-margin-bottom-space-1" v-for="person in innslagItem.personer">
                    <span><b>{{ person.fornavn }} {{ person.etternavn }}</b> {{ person.rolle ? '(' + person.rolle + ')' : '' }}</span>
                </v-chip>
            </div>
        </div>
        
        <div class="innslag-bilder">
            <div v-for="bilde in innslagItem.getBilder()" class="hendelse-item-bilde as-margin-top-space-4">
                <img class="innslag-bilde-item" :src="bilde" alt="Bilde av innslag" />
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
        hendelseId: {
            type: Number,
            required: false,
            default: -1
        },
        innslagItem: {
            type: [Innslag, null],
            required: true
        }
    },
    mounted() {
        console.log('innslag content mounted');
    },
    methods: {
        onShare() {
            console.log(this.innslagItem);
            if(this.innslagItem == null || this.hendelseId == -1) {
                console.error('Cannot share, innslagItem or hendelseId is not set');
                return;
            }
            if (navigator.share) {
                
                console.log('https://ukm.no/festivalen/single-hendelse/?hendelse-id='+this.hendelseId+ '&innslag=' + this.innslagItem.getId());

                navigator.share({
                    title: this.innslagItem ? this.innslagItem.getTitle() : 'UKM Innslag',
                    text: this.innslagItem ? this.innslagItem.getBeskrivelse() : 'Se dette innslaget på UKM Festivalen',
                    url: 'https://ukm.no/festivalen/single-hendelse/?hendelse-id='+this.hendelseId+ '&innslag=' + this.innslagItem.getId(), // or any other URL you'd like to share
                })
                .then(() => {
                    console.log('Shared successfully');
                })
                .catch((error) => {
                    console.error('Error sharing:', error);
                });
            } else {
                // Fallback for browsers that do not support the Web Share API
                window.open("https://ukm.no/festivalen/single-hendelse/?hendelse-id='+this.hendelseId+ '&innslag=' + this.innslagItem.getId()", '_blank');

            }
        },
    }
});
</script>

<style scoped>
.hendelse-item-bilde {
    border-radius: var(--radius-high);
    overflow: hidden;
}
.share-button {
    background: #00004c !important;
    box-shadow: none !important;
}
.share-button :deep(.mdi-share-variant) {
    color: #fff !important;
}
@media (max-width: 767px) {
    .chip-group-innslag-personer :deep(.v-chip__content) {
        overflow: scroll;
    }
}
</style>
