<template>
    <div v-if="dataFetched == true" class="item-container as-padding-space-4">
        <div class="ls-program-meny">
            <div class="ls-inner-meny-beholder">

            </div>
        </div>

        <template v-for="hendelseItem in getFilteredContentItems()" :key="hendelseItem.id">
            <div class="hendelse">
                <div @click="hendelseItem.isOpen = !hendelseItem.isOpen" class="first-width">
                    <div class="inner-content">
                        <div class="hendelse-content-item">
                            <h2 class="hendelse-title">{{ hendelseItem.title }}</h2>
                            <v-chip 
                                class="type-chip blue-chip"
                                size="small"
                                >
                                {{ isAkivitetInstance(hendelseItem) ? 'Aktivitet' : 'Innslag' }}
                            </v-chip>
                        </div>
                        <div class="hendelse-sted">
                            <!-- <span class="sted">{{ hendelse.sted }}</span> -->
                            <!-- <span class="tid" v-show="hendelse.isOpen">{{ hendelse.getStart() }}</span> -->
                        </div>
                        <div class="open-indicator">
                            <div :class="{'open': hendelseItem.isOpen}" class="svg">
                                <svg width="30" height="17" viewBox="0 0 30 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M28 1.17822L15 14.1782L2 1.17822" stroke="#333" stroke-width="3"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="collapse-wrapper ">
                    <transition name="collapse">

                        <div v-if="hendelseItem.isOpen" class="content-under collapsible-content">
                            <div class="hendelse-info-extendable">
                                <template v-if="isAkivitetInstance(hendelseItem)">
                                    <AktivitetContentComponent :aktivitetItem="getItemAktivitet(hendelseItem)" />
                                </template>
                                <template v-else>
                                    <InnslagContentComponent :innslagItem="getItemInnslag(hendelseItem)" />
                                </template>
                                
                            </div>

                        </div>


                        <!-- <div v-if="hendelse.isOpen" class="collapsible-content">
                            <v-card>
                                <v-card-title>Collapsible Content</v-card-title>
                                <v-card-text>
                                    This content will collapse when the button is clicked.
                                </v-card-text>
                            </v-card>
                        </div> -->
                    </transition>
                </div>
                <!-- <div class="ls-program-item">
                    <h3>{{ hendelse.tittel }}</h3>
                    <p>{{ hendelse.beskrivelse }}</p>
                    <p>{{ hendelse.sted }}</p>
                    <p>{{ hendelse.dato }}</p>
                </div> -->
            </div>
            <hr>
        </template>
    </div>
</template>

<script lang="ts">
import Hendelse from '../../objects/Hendelse';
import AktivitetContentComponent from './AktivitetContent.vue';
import InnslagContentComponent from './InnslagContent.vue';
import type HendelseContent from '../../objects/HendelseContent';
import Aktivitet from '../../objects/Aktivitet';
import Innslag from '../../objects/Innslag';

export default {
    props: {
        hendelse: {
            type: Hendelse,
            required: true
        },
    },
    mounted() {
        this.fetchHendelseItems();
    },
    components: {
        AktivitetContentComponent : AktivitetContentComponent,
        InnslagContentComponent : InnslagContentComponent,
    },
    data() {
        return {
            isActive : false,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            dataFetched: false,
            fetchingStarted: false,
            contentItems: [] as HendelseContent[],
        };
    },
    methods: {
        isAkivitetInstance(item : HendelseContent) {
            return item instanceof Aktivitet;
        },
        getItemAktivitet(item : HendelseContent) {
            if(item instanceof Aktivitet) {
                return item;
            }
            return null;

        },
        getItemInnslag(item : HendelseContent) {
            if(item instanceof Innslag) {
                return item;
            }
            return null;
        },
        getFilteredContentItems() : HendelseContent[] {
            return this.contentItems;
        },
        async fetchHendelseItems() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {
                hendelseId: this.hendelse.id,
            };

            var results = await this.spaInteraction.runAjaxCall('getHendelseContent.ajax.php', 'POST', data);
            
            for (let key in results.aktiviteter) {
                let ak = results.aktiviteter[key];
                this.contentItems.push(
                    new Aktivitet(
                        ak.id, 
                        ak.navn,
                        ak.image,
                        ak.sted,
                        ak.beskrivelse,
                        ak.beskrivelseLeder,
                        ak.tidspunkter,
                        ak.tags,
                        this.hendelse.id,
                    )
                )
            }

            for (let key in results.innslagPersoner) {
                let innPerson = results.innslagPersoner[key];
                let innslag = innPerson.innslag;
                this.contentItems.push(
                    new Innslag(
                        innslag.id, 
                        innslag.navn,
                        innslag.type ? innslag.type.name : '',
                        innslag.beskrivelse,
                        innslag.type && innslag.type.type == 'person' ? true : false,
                        innPerson.personer
                    )
                )
            }

            // for (let h of results.hendelser) {
            //     const startDate = new Date(h.start * 1000);
            //     let dataInnslag = h.innslag && h.innslag.innslag ? h.innslag.innslag[0] : null;

            //     console.log(h);
            //     console.log(h.innslag);
            //     console.log(h.innslag.innslag);
                
            //     console.warn(results.innslagPersoner);

            //     // Innslag og deltakere
            //     let innslag : {name : string, antallDeltakere : number, url : string}[] = [];
            //     let antallDeltakere = 0;

            //     if(dataInnslag != null) {
            //         antallDeltakere += results.innslagPersoner[dataInnslag.id].length;
    
            //         innslag.push({
            //             name: dataInnslag.navn,
            //             antallDeltakere: antallDeltakere,
            //             url: 'inPerson.url'
            //         });
                    
            //     }

            //     var newHendelse = new Hendelse(
            //         h.id, 
            //         h.navn, 
            //         'https://placehold.co/155', 
            //         h.start, 
            //         0, 
            //         h.sted, 
            //         h.context.type, 
            //         h.beskrivelse,
            //         innslag
            //     );
            //     this.hendelser.push(newHendelse);
                
            //     if(this.availableTider.find(t => t.id == newHendelse.getStartDag()) == undefined && h.start && h.start.trim() != '') {
            //         this.availableTider.push({'id' : newHendelse.getStartDag(), 'title' : newHendelse.getStartDag()});
            //     }

            //     if(this.availableSteder.find(s => s.id == h.sted) == undefined && h.sted && h.sted.trim() != '') {
            //         this.availableSteder.push({'id' : h.sted, 'title' : h.sted});
            //     }

            //     if(this.availableTyper.find(t => t.id == h.context.type) == undefined && h.context && h.context.type.trim() != '') {
            //         this.availableTyper.push({'id' : h.context.type, 'title' : h.context.type});
            //     }
            // }

            
            this.dataFetched = true;

            
            return results;
        },
    },
};
</script>


<style>

/* .v-autocomplete-arr-sys div .v-field,
.v-text-field-arr-sys div .v-field {
    border-radius: var(--radius-normal) !important;
}
.v-field__input input {
    background: transparent !important;
    border: none !important;
} */
</style>
<style scoped>
.ls-program-meny {
    display: flex;
}
.ls-inner-meny-beholder {
    margin: auto;
    display: flex;
}
.ls-meny-item {
    width: auto;
    margin: auto 10px;
    min-width: 110px;
}
.item-container * {
    color: #00004c !important;
}
.hendelse {
    border-top: solid 1px #fff;
    padding: 24px 0;
}

.hendelse .first-width {
    display: flex;
}
.hendelse .first-width .inner-content{
    width: 100%;
    display: flex
}
.under-content {
    display: inline;
}
.under-content { 
    width: 70%;
}
.open-indicator {
    margin: auto;
    margin-right: 0;
    margin-top: 10px;
}
.open-indicator .svg {
    transition: transform 0.3s;
    transform: rotate(-90deg);
}
.open-indicator .svg.open {
    transition: transform 0.3s;
    transform: rotate(0);
}
.hendelse-item-bilde {
    margin-right: 24px;
    width: 150px;
}
.hendelse-item-bilde img {
    border-radius: 22px;
}
.hendelse-content-item {
    width: 50%;
    margin-right: 12px;
    display: flex;
}
.hendelse-content-item .type-chip {
    margin: auto;
    margin-left: 16px;
}
.hendelse-title {
    overflow: hidden;
    color: #fff;
    letter-spacing: -0.45px;
    font-size: 45px;
}
.hendelse-sted {
    display: grid;
    margin: auto;
    margin-top: 0;
    margin-left: 0;
}
.hendelse-sted .sted {
    font-size: 20px;
    font-weight: 700;
}
.hendelse-sted .tid {
    font-size: 20px;
    font-weight: 400;
}

.hendelse-info-extendable {
    display: flex;
}
.content-under {
    
}
.content-under .deltakere {
    display: flex;
    margin-bottom: 32px;
}
.deltakere .deltaker {
    display: flex;
    padding: 8px 18px;
    border: solid 1px #fff;
    border-radius: 100px;
    margin-right: 8px;
}
.deltakere .deltaker .svg {
    display: flex;
    margin-right: 12px;
}
.hendelse-beskrivelse .beskrivelse {
    font-size: 24px;
}
.v-select-program-meny :deep(.v-label) {
    font-size: 20px;
    font-weight: 400;
    margin-top: 10px;
    color: #fff !important;
    opacity: 1;
}
/* .v-select-program-meny.v-select--active-menu .v-input__control { */
.v-select-program-meny.v-select--active-menu :deep(.v-input__control) {
    background: #fff !important;
}
.v-select-program-meny :deep(.mdi-menu-down) {
    color: #fff !important;
    opacity: 1;
}
.v-select-program-meny.v-select--active-menu :deep(.mdi-menu-down) {
    color: #000 !important;
}
.v-select-program-meny :deep(.v-input__control) {
    border: solid 2px;
    border-radius: 10px !important;
    overflow: hidden;
}
.beskrivelser {
    display: inline;
    width: 100%;
}

/* COLLAPSE TRANSITION */
/* Wrapper to prevent content from jumping */
.collapse-wrapper {
    overflow: hidden;
}

/* Transition for smooth collapse */
.collapse-enter-active, .collapse-leave-active {
    transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
    overflow: hidden;
}

/* Initial hidden state */
.collapse-enter-from, .collapse-leave-to {
    max-height: 0;
    opacity: 0;
}

/* Fully visible state */
.collapse-enter-to, .collapse-leave-from {
    max-height: 200px; /* Adjust as needed */
    opacity: 1;
}

/* Collapsible content styling */
.collapsible-content {
    overflow: hidden;
}

.blue-chip {
    border: solid 1px #00004c;
    background: #fff !important;
    color: #fff !important;
}
.blue-chip >>> .v-chip__underlay {
    background: #fff !important;
}

@media (max-width: 767px) {
    .hendelse-title {
        margin: 0;
        font-size: 24px;
    }
}

</style>