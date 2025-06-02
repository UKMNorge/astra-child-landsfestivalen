<template>
    <div>
        <div v-if="fetched && noHendelseId && hendelse">
            <div class="as-card-1 as-padding-space-6">
                <h4 class="as-text-center">Hendelse finnes ikke!</h4>
            </div>
        </div>
        <div v-if="fetched && !noHendelseId">
            <div class="as-margin-bottom-space-4 as-margin-top-space-4">
                <h1 class="title-hendelse" v-if="hendelse != null">{{ hendelse.getTitle() }} - {{ hendelse.getStart() }}</h1>
                <p class="beskrivelse-hendelse" v-if="hendelse != null && hendelse.beskrivelse">{{ hendelse.beskrivelse }}</p>
            </div>
            <div v-show="noDataInHendelse == false" class="single-hendelse as-margin-bottom-space-8">
                <HendelseContent :hendelse="hendelse" :openInnslag="openInnslagId ?? null"  />
            </div>
        </div>
    </div>
</template>
<script lang="ts">

import HendelseContent from './utils/HendelseContent.vue';
import Hendelse from '../objects/Hendelse';

export default {
    components: {
        HendelseContent : HendelseContent
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            director : (<any>window).director, // Definert i main.ts
            hendelseId : -1 as number,
            openInnslagId : -1 as number,
            hendelse : null as Hendelse | null,
            noHendelseId : false as boolean,
            fetched : false as boolean,
            noDataInHendelse : false as boolean,
        }
    },
    mounted() {
        console.log('Single hendelse mounted');
        this.hendelseId = this.director.getParam('hendelse-id') ?? -1;
        this.openInnslagId = this.director.getParam('innslag') ?? -1;
        this.fetchHendelse();
        this.fetchHendelseContent();
    },
    methods : {
        async fetchHendelseContent() {
            var data = {
                hendelseId: this.hendelseId,
            };

            var results = await this.spaInteraction.runAjaxCall('getHendelseContent.ajax.php', 'POST', data);
            if(results == null || results.aktiviteter.length < 1 && results.innslagPersoner < 1) {
                this.noDataInHendelse = true;
                return;
            }

        },
        async fetchHendelse() {
            if(this.hendelseId == -1) {
                this.noHendelseId = true;
                this.fetched = true;
                return;
            }
            var data = {
                hendelseId: this.hendelseId,
            };

            var results = await this.spaInteraction.runAjaxCall('getSingleHendelse.ajax.php', 'POST', data);
            
            if(results == null || results.hendelse == null) {
                this.noHendelseId = true;
                this.fetched = true;
                return;
            }

            let h = results.hendelse;
            this.hendelse = new Hendelse(
                h.id, 
                h.navn, 
                h.bilde ?? 'http://ukm.no/wp-content/uploads/2025/04/40ukm.png', 
                h.start, 
                0, 
                h.sted, 
                h.tag, 
                h.beskrivelse,
                [],
                [],
                [],
                h.visning
            );

            this.fetched = true;

            return results;
        }
    }
}

</script>

<style scoped>

.single-hendelse {
    background-color: #fff;
    border-radius: var(--radius-high);
}
.title-hendelse,
.beskrivelse-hendelse {
    color: #fff;
    text-align: center;
}
</style>