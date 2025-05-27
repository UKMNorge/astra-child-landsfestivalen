<template>
    <div v-if="dataFetched == true" class="item-container as-padding-space-4">
        <div class="ls-program-meny">
            <div class="ls-inner-meny-beholder">

            </div>
        </div>
        <template v-if="hendelse.isPersonerListe()">
            <div class="hendelse-info-extendable">
                <div class="deltakere">
                    <div class="deltaker" v-for="person in getAllePersoner()" :key="person.id">
                        <div class="svg">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="#333" stroke-width="2"/>
                                <path d="M20.9999 20C20.9999 17.2386 17.7613 15 14.0001 15H9.99988C6.23847 15 2.99988 17.2386 2.99988 20" stroke="#333" stroke-width="2"/>
                            </svg>
                        </div>
                        <span><b>{{ person.fornavn }} {{ person.etternavn }}</b> {{ person.rolle ? '(' + person.rolle + ')' : '' }}</span>
                    </div>
                </div>
            </div>
        </template>

        <template v-else v-for="hendelseItem in getFilteredContentItems()" :key="hendelseItem.id">
            <div class="hendelse">
                <div @click="hendelseItem.isOpen = !hendelseItem.isOpen" class="first-width">
                    <div class="inner-content">
                        <div class="hendelse-content-item">
                            <div class="hendelse-bilde-div">
                                <div class="hendelse-item-bilde" :class="hendelseItem.isOpen ? 'is-open' : ''">
                                    <img :src="hendelseItem.getBilde()" alt="Bilde av hendelse" />
                                </div>
                            </div>
                            <div class="hendelse-title-div">
                                <h2 class="hendelse-title">{{ hendelseItem.title }}</h2>
                                <h6 class="hc-undertittel">{{ hendelseItem.getUnderTitle() }}</h6>
                            </div>
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
                                    <AktivitetContentComponent :hendelseId="hendelse.id" :aktivitetItem="getItemAktivitet(hendelseItem)" />
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
        openInnslag: {
            type: Number,
            required: false,
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
        getAllePersoner() {
            let allePersoner : any = [];
            for(let contentItem of this.contentItems) {
                console.log(contentItem);
                console.log(contentItem.personer);
                if(!contentItem.personer) {
                    console.warn('Content item has no personer:', contentItem);
                    continue;
                }
                allePersoner = allePersoner.concat(contentItem.personer);
            }
            return allePersoner;
        },
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
                        [ak.image],
                        ak.sted,
                        ak.beskrivelse,
                        ak.beskrivelseLeder,
                        ak.tidspunkter,
                        ak.tags,
                        this.hendelse.id,
                        ak.kursholder,
                    )
                )
            }


            for (let innPerson of results.innslagPersoner) {

                // let innPerson = results.innslagPersoner[key];
                let innslag = innPerson.innslag;
                let thumbnailBilde = '';
                let innslagBilder : string[] = [];
                for(let bilde of innPerson.bilder) {
                    if(thumbnailBilde == '' && bilde.sizes && bilde.sizes.thumbnail) {
                        thumbnailBilde = bilde.sizes.thumbnail.path_external+bilde.sizes.thumbnail.file;
                    }
                    if(bilde.sizes && bilde.sizes.original) {
                        innslagBilder.push(bilde.sizes.original.path_external+bilde.sizes.original.file);
                    }
                }

                let innslagObj = new Innslag(
                    innslag.id, 
                    innslag.navn,
                    innslag.type ? innslag.type.name : '',
                    innslag.beskrivelse,
                    innslag.type && innslag.type.type == 'person' ? true : false,
                    innPerson.personer,
                    innslagBilder,
                    innslag.fylke ? innslag.fylke.navn : '',
                );
                this.contentItems.push(innslagObj);
                innslagObj.setThumbnailBilde(thumbnailBilde);

                if(this.openInnslag != null && this.openInnslag == innslag.id) {
                    innslagObj.isOpen = true;
                    setTimeout(() => {
                        innslagObj.isOpen = false;
                        const element = document.querySelector(`[data-id="${innslag.id}"]`);
                        if (element) {
                            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 1000);
                }
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
    min-width: 95px;
}
.ls-meny-item.search {
    min-width: auto;;
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
    height: 112px;
    transition: .2s all;
}
.hendelse-item-bilde.is-open {
    height: 100%;
}
.hendelse-item-bilde img {
    border-radius: 22px;
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.hendelse-content-item {
    width: 50%;
    margin-right: 12px;
    display: flex;
}
.hendelse-content-item .type-chip {
    margin: auto;
    margin-left: 16px;
    margin-top: 10px !important;
}
.hendelse-title {
    overflow: hidden;
    color: #fff;
    letter-spacing: -0.45px;
    font-size: 24px;
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
    /* display: flex; */
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
.hendelse-title-div {
    width: 100%;
}

@media (max-width: 767px) {
    .item-container {
        padding: calc(var(--initial-space-box)*2) !important;
    }
    .hendelse-title {
        margin: 0;
        font-size: 22px;
        word-break: break-word;
    }
    .hendelse-content-item {
        width: 100%;
    }
    .hendelse {
        min-height: 90px;
    }
    .hendelse .first-width {
        min-height: 45px;
    }
    .content-type {
        display: flex;
    }
    .ls-meny-item {
        min-width: 60px;
    }
}
@media (max-width: 400px) {
    .ls-meny-item {
        margin: auto 5px;
    }
}
</style>