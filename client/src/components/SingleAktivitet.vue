<template>
    <div>
        <div v-if="fetched && noAktivitetId">
            <div class="as-card-1 as-padding-space-6">
                <h4 class="as-text-center">Aktiviteten finnes ikke!</h4>
            </div>
        </div>
        <div v-else>
            <AktivitetContent :aktivitetItem="aktivitet"  />
        </div>
    </div>
</template>
<script lang="ts">

import AktivitetContent from './utils/AktivitetContent.vue';
import Aktivitet from '../objects/Aktivitet';

export default {
    components: {
        AktivitetContent : AktivitetContent
    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            director : (<any>window).director, // Definert i main.ts
            aktivitetId : -1 as number,
            aktivitet : null as Aktivitet | null,
            noAktivitetId : false as boolean,
            fetched : false as boolean,
        }
    },
    mounted() {
        console.log('Single aktivitet mounted');
        this.aktivitetId = this.director.getParam('aktivitet-id') ?? -1;
        this.fetchAktivitet();
    },
    methods : {
        getAktivitet() : Aktivitet | null {
            if(this.aktivitet != null) {
                return this.aktivitet;
            }
            return null;
        },

        async fetchAktivitet() {
            if(this.aktivitetId == -1) {
                this.noAktivitetId = true;
                this.fetched = true;
                return;
            }
            var data = {
                aktivitetId: this.aktivitetId,
            };

            var results = await this.spaInteraction.runAjaxCall('getSingleAktivitet.ajax.php', 'POST', data);
            
            if(results == null || results.aktivitet == null) {
                this.noAktivitetId = true;
                this.fetched = true;
                return;
            }

            this.aktivitet = new Aktivitet(
                results.aktivitet.id, 
                results.aktivitet.navn,
                results.aktivitet.image,
                results.aktivitet.sted,
                results.aktivitet.beskrivelse,
                results.aktivitet.beskrivelseLeder,
                results.aktivitet.tidspunkter,
                results.aktivitet.tags,
                -1,
                results.kursholder,
            );

            this.fetched = true;

            return results;
        }
    }
}

</script>