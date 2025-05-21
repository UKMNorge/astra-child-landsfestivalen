<template>
    <div>
        <div v-if="fetched && noHendelseId && hendelse">
            <div class="as-card-1 as-padding-space-6">
                <h4 class="as-text-center">Hendelse finnes ikke!</h4>
            </div>
        </div>
        <div v-if="fetched && !noHendelseId">
            <div class="single-hendelse">
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
        }
    },
    mounted() {
        console.log('Single hendelse mounted');
        this.hendelseId = this.director.getParam('hendelse-id') ?? -1;
        this.openInnslagId = this.director.getParam('innslag') ?? -1;
        this.fetchHendelse();
    },
    methods : {
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
}
</style>