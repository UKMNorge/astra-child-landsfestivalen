<template>
    <div v-if="dataFetched == true" class="program-container">
        <div class="ls-program-meny">
            <div class="ls-inner-meny-beholder">
                <div class="ls-meny-item">                 
                    <SelectProgramStyle
                        background="#fff"
                        :label="'Dag'"
                        :availableItems="availableTider" 
                        v-model:selectedItems="selectedTider" 
                    />
                </div>
                <div class="ls-meny-item">                 
                    <SelectProgramStyle 
                        :label="'Sted'" 
                        :availableItems="availableSteder" 
                        v-model:selectedItems="selectedSteder" 
                    />
                </div>
                <div class="ls-meny-item">                 
                    <SelectProgramStyle 
                        :label="'Type'"
                        :availableItems="availableTyper" 
                        v-model:selectedItems="selectedTyper" 
                    />
                </div>
                <div class="ls-meny-item search">
                    <SearchProgram
                        searchLabel="Søk etter deltaker, gruppe, innslag eller fylke"
                        v-model:searchWords="searchWords"
                    />
                </div>
            </div>
        </div>

        <div v-show="getFoundDeltakere().length > 0" class="deltakere-found">
            <h6 class="title">Deltakere: </h6>
            <div class="as-margin-bottom-space-2">
                <v-chip @click="openSingleHendelse(deltaker)" v-for="deltaker in getFoundDeltakere()" :key="deltaker.id" class="deltaker-result-chip as-margin-right-space-1">
                    <span class="found-deltaker-inside-chip">
                        <span>
                            <svg class="deltaker-icon" width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.99963 8.63672C8.05213 8.63672 9.39608 8.77662 10.5309 9.22949L10.7545 9.32422C11.9393 9.85533 12.8638 10.7701 12.8639 12.2725V12.9092C12.8638 13.5038 12.3818 13.9998 11.7848 14H2.21545C1.61829 13.9999 1.1364 13.5039 1.13635 12.9092V12.2725C1.13645 10.8642 1.94844 9.97233 3.026 9.42871L3.24573 9.32422C4.42391 8.79618 5.8772 8.63675 6.99963 8.63672ZM6.99963 1C8.4807 1 9.68216 2.2006 9.68225 3.68164C9.68225 5.16277 8.48076 6.36328 6.99963 6.36328C5.51867 6.36309 4.31799 5.16265 4.31799 3.68164C4.31809 2.20072 5.51873 1.00019 6.99963 1Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>

                        </span>
                        <b>{{ deltaker.navn }}</b></span> <span>{{ deltaker.hendelse ? '('+deltaker.hendelse.title+')' : '' }}</span>
                </v-chip>
            </div>
        </div>

        <div v-show="getFoundInnslag().length > 0" class="deltakere-found">
            <h6 class="title">Innslag: </h6>
            <div class="as-margin-bottom-space-2">
                <v-chip @click="openSingleInnslag(innslag)" v-for="innslag in getFoundInnslag()" :key="innslag.id" class="deltaker-result-chip as-margin-right-space-1">
                    <span class="found-deltaker-inside-chip">
                        <span>
                            <svg class="deltaker-icon" width="14" height="15" viewBox="0 0 24 24" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                                <title>calendar-heart</title><path d="M19 19V8H5V19H19M16 1H18V3H19C20.11 3 21 3.9 21 5V19C21 20.11 20.11 21 19 21H5C3.9 21 3 20.11 3 19V5C3 3.9 3.9 3 5 3H6V1H8V3H16V1M12 17.17L11.42 16.64C9.36 14.77 8 13.54 8 12.03C8 10.8 8.97 9.83 10.2 9.83C10.9 9.83 11.56 10.15 12 10.66C12.44 10.15 13.1 9.83 13.8 9.83C15.03 9.83 16 10.8 16 12.03C16 13.54 14.64 14.77 12.58 16.64L12 17.17Z" />
                            </svg>
                        </span>
                        <b>{{ innslag.navn }}</b></span>
                </v-chip>
            </div>
        </div>

        <template v-for="hendelse in getFilteredHendelser()" :key="hendelse.id">
            <div class="hendelse">
                <div @click="hendelse.isOpen = !hendelse.isOpen" class="first-width">
                    <div class="inner-content">
                        <div class="hendelse-bilde">
                            <img :src="hendelse.img" alt="Bilde av hendelse">
                        </div>
                        <div class="hendelse-content">
                            <h2 class="hendelse-title">{{ hendelse.title }}</h2>
                            <span v-show="!hendelse.isOpen" class="tid under-tittel">{{ hendelse.getStart() }}</span>
                        </div>
                        <div class="hendelse-sted">
                            <span v-show="!hendelse.isOpen" class="sted">{{ hendelse.getSted() }}</span>
                            <span v-show="!hendelse.isOpen" class="dag">{{ hendelse.getStartDag() }}</span>
                            <!-- <v-chip class="tid" v-show="hendelse.isOpen">{{ hendelse.getStart() }}</v-chip> -->
                        </div>
                        <div class="open-indicator">
                            <div :class="{'open': hendelse.isOpen}" class="svg">
                                <svg width="30" height="17" viewBox="0 0 30 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M28 1.17822L15 14.1782L2 1.17822" stroke="white" stroke-width="3"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="collapse-wrapper">
                    <transition name="collapse">

                        <div v-if="hendelse.isOpen" class="hendelse-under collapsible-content">
                            <div class="hendelse-info-extendable">
                                <div class="hendelse-bilde">
            
                                </div>
                                <div class="under-content">
                                    <div v-if="!hendelse.isGruppe" class="share-buttons as-margin-bottom-space-4">
                                        <v-btn
                                            icon
                                            class="rounded-circle share-button"
                                            @click="onShare(hendelse)">
                                            <v-icon>mdi-share-variant</v-icon>
                                        </v-btn>
                                    </div>
                                    <div class="sted-tid-after-open as-margin-bottom-space-2">
                                        <!-- <h5 class="sted-after-open">Sted: <b>{{ hendelse.sted }}</b></h5> -->
                                        <v-chip class="tid-after-open as-margin-right-space-1">Sted: {{ hendelse.getSted() }}</v-chip>
                                        <v-chip class="tid-after-open as-margin-right-space-1">Tid: {{ hendelse.getStart() }}</v-chip>
                                    </div>
                                    <!-- <div class="deltakere">
                                        <div v-for="innslag in hendelse.innslag" class="deltaker">
                                            <div class="svg">
                                                <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g id="user-head">
                                                <path id="Shape" d="M5.75 5.17822C5.75 3.3833 7.20508 1.92822 9 1.92822C10.7949 1.92822 12.25 3.3833 12.25 5.17822C12.25 6.97315 10.7949 8.42822 9 8.42822C7.20508 8.42822 5.75 6.97315 5.75 5.17822ZM4.53836 11.747C5.94349 11.1171 7.67103 10.9282 9 10.9282C10.329 10.9282 12.0565 11.1171 13.4616 11.747C14.8773 12.3816 16 13.485 16 15.3032V16.0532C16 16.802 15.3936 17.4282 14.6387 17.4282H3.36126C2.60644 17.4282 2 16.802 2 16.0532V15.3032C2 13.485 3.12267 12.3816 4.53836 11.747Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                                </svg>
                                                <span>{{ innslag.antallDeltakere }}</span>
                                            </div>
                                            <div class="deltaker-info">
                                                <span>{{ innslag.name }}</span>
                                            </div>
                                        </div>
    
                                    </div> -->
                                    <div class="hendelse-beskrivelse">
                                        <span class="beskrivelse">{{ hendelse.beskrivelse }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="hendelse.isGruppe == true" class="hendelse-gruppe as-margin-bottom-space-2 hendelse-info-extendable">
                                <div class="hendelse-bilde">
                                    <!-- Intentional empty div for styling -->
                                </div>
                                <v-timeline class="hendelse-gruppe-timeline" side="end">
                                    <v-timeline-item @click="openHendelse(hendelse)"
                                        v-for="hendelse in hendelse.getHendelser()"
                                        :key="hendelse.id"
                                        hide-dot
                                        class="custom-timeline-item"
                                    >
                                        <v-img
                                            :src="hendelse.img"
                                            width="200"
                                            class="gruppe-hendelse-img as-margin-right-space-2"
                                            cover
                                        ></v-img>
                                    

                                        <div class="group-hendelse-content">
                                            <h2 class="nom-impt">{{ hendelse.title }}</h2>
                                            <div>{{ hendelse.getStart() }}</div>
                                            <div class="text-caption">{{ hendelse.sted }}</div>
                                        </div>
                                    </v-timeline-item>
                                </v-timeline>



                            </div>
                            
                            <div v-else class="hendelse-info-extendable">
                                <div class="hendelse-bilde">
                                    <!-- Intentional empty div for styling -->
                                </div>
                                <div v-if="hasShowMore(hendelse)" class="as-margin-top-space-2">
                                    <v-dialog width="95%" max-width="800px">
                                        <template v-slot:activator="{ props: activatorProps }">
                                            <v-btn
                                                class="v-btn-as v-btn-hvit"
                                                v-bind="activatorProps"
                                                color="#000"
                                                size="large"
                                                rounded="large"
                                                variant="outlined" >
                                                Vis mer
                                            </v-btn>
                                        </template>

                                        <template v-slot:default="{ isActive }">
                                            <v-card :title="hendelse.title + ', ' + hendelse.getStart().toLowerCase()" class="hendelse-card">
                                           <HendelseContentComponent :hendelse="hendelse" />
                                            <v-card-actions>
                                                <v-spacer></v-spacer>

                                                <v-btn
                                                    icon
                                                    class="close-btn"
                                                    @click="isActive.value = false"
                                                    size="40"
                                                >
                                                    <v-icon style="color: #fff;">mdi-close</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    class="close-btn-down"
                                                    text="Lukk"
                                                    @click="isActive.value = false"
                                                ></v-btn>
                                            </v-card-actions>
                                            </v-card>
                                        </template>
                                    </v-dialog>


                                </div>
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
        </template>
    </div>
</template>

<script lang="ts">
import Hendelse from '../objects/Hendelse';
import HendelseGruppe from '../objects/HendelseGruppe';
import SelectProgramStyle from './utils/SelectProgramStyle.vue';
import HendelseContentComponent from './utils/HendelseContent.vue';
import SearchProgram from './utils/SearchProgram.vue';
import Fuse from 'fuse.js';

export default {
    props: {
        programType: {
            type: String,
            validator: (value: string) => {
                return ['publikum', 'deltakere'].includes(value)
            },
            required: true
        },
    },
    mounted() {
        this.fetchProgramData();
    },
    components: {
        SelectProgramStyle : SelectProgramStyle,
        HendelseContentComponent : HendelseContentComponent,
        SearchProgram : SearchProgram,
    },
    data() {
        return {
            isActive : false,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            dataFetched: false,
            fetchingStarted: false,
            hendelser: [] as Hendelse[],
            availableSteder: [] as {id: number|string, title: string}[],
            availableTider: [] as {id: number|string, title: string}[],
            availableTyper: [] as {id: number|string, title: string}[],
            searchWords: '' as string,

            selectedSteder : [] as {id: number|string, title: string}[],
            selectedTider : [] as {id: number|string, title: string}[],
            selectedTyper : [] as {id: number|string, title: string}[],

            hendelseMedAktiviteter : {} as any,
            deltakere : {} as any,
            alleInnslag : {} as any,
            hendelseGrupper : [] as HendelseGruppe[],

        };
    },
    methods: {
        onShare(hendelse : Hendelse) {
            if (navigator.share) {
                console.log(hendelse);
                console.log('https://ukm.no/festivalen/single-hendelse/?hendelse-id='+hendelse.getId());
                navigator.share({
                    title: hendelse.getTitle(),
                    text: hendelse.beskrivelse,
                    url: 'https://ukm.no/festivalen/single-hendelse/?hendelse-id='+hendelse.getId(), // or any other URL you'd like to share
                })
                .then(() => {
                    console.log('Shared successfully');
                })
                .catch((error) => {
                    console.error('Error sharing:', error);
                });
            } else {
                // Fallback for browsers that do not support the Web Share API
                this.openHendelse(hendelse);
            }
        },
        openSingleInnslag(innslag : any) {
            if (innslag.hendelseId) {
                this._redirectToInnslag(innslag.hendelseId, innslag.id);
            }
        },
        openSingleHendelse(deltaker : any) {
            if (deltaker.hendelse && deltaker.hendelse.id) {
                this._redirectToInnslag(deltaker.hendelse.id, deltaker.innslagId);
            }
        },
        openHendelse(hendelse : Hendelse) {
            if (hendelse.id) {
                this._redirectToInnslag(hendelse.id, -1); // -1 for no specific innslag
            }
        },
        _redirectToInnslag(hendelseId : number, innslagId : number) {
            const url = new URL('/festivalen/single-hendelse', window.location.origin);
            url.searchParams.append('hendelse-id', String(hendelseId));
            if(innslagId != -1) {
                url.searchParams.append('innslag', String(innslagId));
            }
            window.open(url.toString(), '_blank');
        },
        // Sjekker om hendelsen har aktiviteter eller innslag for å vise "Vis mer" knappen
        hasShowMore(hendelse : Hendelse) : boolean {
            if(this.hendelseMedAktiviteter[hendelse.id] && this.hendelseMedAktiviteter[hendelse.id] == true) {
                return true;
            }

            if(hendelse.getInnslagNavn() && hendelse.getInnslagNavn().length > 0) {
                return true;
            }

            return false;
        },
        getFoundInnslag() : any[] {
            let filtered = Object.values(this.alleInnslag);
            console.log('filtered innslag');
            console.log(filtered);

            if (this.searchWords && this.searchWords.length > 2) {
                const fuse = new Fuse(filtered, {
                    threshold: 0.0, // exact match only
                    keys: ['navn'],
                    useExtendedSearch: true,
                    getFn: (deltaker: any, path: string | string[]) => {
                        const key = Array.isArray(path) ? path[0] : path;
                        switch (key) {
                            case 'navn':
                                return deltaker.navn;
                            default:
                                return '';
                        }
                    }
                });

                // Use exact match operator with '
                const results = fuse.search(`'${this.searchWords}`);
                return results.slice(0, 5).map(r => r.item);
            } else {
                return [];
            }

        },
        getFoundDeltakere() : any[] {
            let filtered = Object.values(this.deltakere);

            if (this.searchWords && this.searchWords.length > 2) {
                const fuse = new Fuse(filtered, {
                    threshold: 0.0, // exact match only
                    keys: ['navn'],
                    useExtendedSearch: true,
                    getFn: (deltaker: any, path: string | string[]) => {
                        const key = Array.isArray(path) ? path[0] : path;
                        switch (key) {
                            case 'navn':
                                return deltaker.navn;
                            default:
                                return '';
                        }
                    }
                });

                // Use exact match operator with '
                const results = fuse.search(`'${this.searchWords}`);
                return results.slice(0, 5).map(r => r.item);
            } else {
                return [];
            }
        },
        // getFilteredHendelser() : Hendelse[] {
        //     if(this.selectedSteder.length == 0 && this.selectedTider.length == 0 && this.selectedTyper.length == 0) {
        //         return this.hendelser;
        //     }

        //     return this.hendelser.filter(h => {
        //         if(this.selectedSteder.length > 0 && !this.selectedSteder.find(sted => String(sted) == h.sted)) {
        //             return false;
        //         }
        //         if(this.selectedTider.length > 0 && !this.selectedTider.find(t => (<any>t) == h.getStartDag())) {
        //             return false;
        //         }

        //         if(this.selectedTyper.length > 0 && !this.selectedTyper.find(t => String(t) == h.type)) {
        //             return false;
        //         }

        //         return true;
        //     });
        // },
        getFilteredHendelser(): Hendelse[] {
            let filtered = this.hendelser.filter(h => {
                for(let hendelseGruppe of this.hendelseGrupper) {
                    if(hendelseGruppe.hasHendelse(h.id)) {
                        return false; // Håp over hendelseGruppe, de skal vises som grupper
                    }
                }
                return true;
            });

            // Append hendelser from hendelseGrupper
            for(let hendelseGruppe of this.hendelseGrupper) {
                if(hendelseGruppe.getHendelser().length == 0) continue; // Skip empty groups
                filtered = filtered.concat(hendelseGruppe);
            }

            if (
                this.selectedSteder.length > 0 ||
                this.selectedTider.length > 0 ||
                this.selectedTyper.length > 0
            ) {
                filtered = filtered.filter((h : Hendelse) => {
                    if (this.selectedSteder.length > 0 && !this.selectedSteder.find(sted => h.hasSted(String(sted)))) return false;
                    if (this.selectedTider.length > 0 && !this.selectedTider.find(t => (<any>t) == h.getStartDag())) return false;
                    if (this.selectedTyper.length > 0 && !this.selectedTyper.find(t => h.hasType(String(t)))) return false;
                    return true;
                });
            }

            if (this.searchWords && this.searchWords.length > 0) {
                const fuse = new Fuse(filtered, {
                    threshold: 0.3,
                    getFn: (hendelse: Hendelse, path: string | string[]) => {
                        const key = Array.isArray(path) ? path[0] : path;

                        switch (key) {
                            case 'title':
                                return hendelse.getTitle();
                            case 'deltakere':
                                return hendelse.getAlleDeltakereNavn(); // returns string[]
                            case 'fylker':
                                return hendelse.getAlleFylkerString(); // returns string or string[]
                            case 'innslag':
                                return hendelse.getInnslagNavn(); // returns string or string[]
                            default:
                                return '';
                        }
                    },
                    keys: ['title', 'deltakere', 'fylker', 'innslag'], // keys still act as identifiers for `getFn`
                });

                const results = fuse.search(this.searchWords);
                return results.map(r => r.item);
            }

            // Sort by hendele.start
            filtered.sort((a : Hendelse, b : Hendelse) => {
                return a.start - b.start;
            });
            
            return filtered;
        },
        async fetchProgramData() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {
                erDeltakerProgram: this.programType == 'deltakere'
            };

            var results = await this.spaInteraction.runAjaxCall('getProgram.ajax.php', 'POST', data);
            
            this.hendelseMedAktiviteter = results.hendelseMedAktiviteter;

            for (let h of results.hendelser) {
                const startDate = new Date(h.start * 1000);
                let alleInnslag = h.innslag;

                // Innslag og deltakere
                let innslagArrObj : {name : string, antallDeltakere : number, url : string}[] = [];
                let antallDeltakere = 0;

                let fylker : string[] = [];
                let deltakereNavn : string[] = [];

                for(let innslag of alleInnslag.innslag) {
                    antallDeltakere += results.innslagPersoner[innslag.id] ? results.innslagPersoner[innslag.id].length : 0;

                    if(innslag.fylke) {
                        fylker.push(innslag.fylke.navn);
                    }

                    innslagArrObj.push({
                        name: innslag.navn,
                        antallDeltakere: antallDeltakere,
                        url: 'inPerson.url'
                    });

                    if(results.innslagPersoner[innslag.id]) {
                        for(let person of results.innslagPersoner[innslag.id]) {
                            person.hendelse = h;
                            // Legger til innslagId i deltakeren
                            person.innslagId = innslag.id;
                            this.deltakere[person.id +'_'+ person.context.forestilling.id+ '_' + person.innslagId] = person;
                            deltakereNavn.push(person.fornavn + ' ' + person.etternavn);
                        }
                    }
                    innslag.hendelseId = h.id;
                    this.alleInnslag[innslag.id] = innslag;
                }

                var newHendelse = new Hendelse(
                    h.id, 
                    h.navn, 
                    h.bilde ?? 'http://ukm.no/wp-content/uploads/2025/04/40ukm.png', 
                    h.start, 
                    0, 
                    h.sted, 
                    h.tag, 
                    h.beskrivelse,
                    innslagArrObj,
                    fylker,
                    deltakereNavn,
                    h.visning,
                );
                this.hendelser.push(newHendelse);
                
                if(this.availableTider.find(t => t.id == newHendelse.getStartDag()) == undefined && h.start && h.start.trim() != '') {
                    this.availableTider.push({'id' : newHendelse.getStartDag(), 'title' : newHendelse.getStartDag()});
                }

                if(this.availableSteder.find(s => s.id == h.sted) == undefined && h.sted && h.sted.trim() != '') {
                    this.availableSteder.push({'id' : h.sted, 'title' : h.sted});
                }
                let tag = h.tag ?? '';
                if(this.availableTyper.find(t => t.id == tag) == undefined && h.context && tag.trim() != '') {
                    this.availableTyper.push({'id' : tag, 'title' : tag});
                }
            }

            // Add correct hendelse object to each deltakere
            for(let d of Object.values(this.deltakere)) {
                let deltaker = (<any>d);
                let hendelse = this.getHendelse(deltaker.context.forestilling.id);
                if(hendelse != null) {
                    deltaker.hendelse = hendelse;
                }
            }

            // Hendelse gruppering
            for(let key in results.hendelseGrupper) {
                let hG = results.hendelseGrupper[key];

                let tag = hG.tag ?? '';
                if(this.availableTyper.find(t => t.id == tag) == undefined && tag.trim() != '') {
                    this.availableTyper.push({'id' : tag, 'title' : tag});
                }

                let alleHendelser = [];
                for(let hId of hG.hendelser) {
                    let hendelse = this.getHendelse(hId);
                    if(hendelse) {
                        alleHendelser.push(hendelse);
                    }
                }

                this.hendelseGrupper.push(
                    new HendelseGruppe(
                        hG.id, 
                        hG.navn, 
                        'http://ukm.no/wp-content/uploads/2025/04/40ukm.png',
                        hG.start,
                        -1, // Ingen sluttidspunkt
                        '',
                        'Gruppe',
                        hG.beskrivelse,
                        [],
                        [],
                        [],
                        alleHendelser,
                        hG.tag ?? null,
                    )
                );
            }

            console.log('Grupper fetched:');
            console.log(this.hendelseGrupper);
            
            this.dataFetched = true;
            return results;
        },
        getHendelse(hendelseId : number) : Hendelse | null {
            for(let h of this.hendelser) {
                if(h.id == hendelseId) {
                    return h;
                }
            }
            return null;
        }
    },
};
</script>


<style>
.entry-content {
    background: #00004C !important;
}
.v-autocomplete-arr-sys div .v-field,
.v-text-field-arr-sys div .v-field {
    border-radius: var(--radius-normal) !important;
}
.v-field__input input {
    background: transparent !important;
    border: none !important;
}
</style>
<style scoped>
.ls-program-meny {
    display: flex;
}
.ls-inner-meny-beholder {
    margin: auto;
    display: flex;
    position: relative;
}
.ls-meny-item {
    width: auto;
    margin: auto 10px;
    min-width: 95px;
}
.ls-meny-item.search {
    min-width: auto;;
}
.program-container {
    color: #fff;
    padding: 20px 0;
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
.hendelse-bilde {
    margin-right: 24px;
    width: 200px;
    min-width: 200px;
    height: 150px;
    background: transparent;
}
.hendelse-bilde img {
    border-radius: 22px;
    width: 100%;
    height: 100%;
    object-fit: cover;
    background: #fff;
}
.hendelse-content {
    width: 50%;
    margin-right: 12px;
}
.hendelse-title {
    overflow: hidden;
    color: #fff;
    letter-spacing: -0.45px;
    font-size: 45px;
    word-break: break-word;
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
    margin: auto;
}
.hendelse-sted .tid {
    font-size: 20px;
    font-weight: 400;
    margin-top: 8px;
}
.program-container {
    background: #00004C;
}
.hendelse-info-extendable {
    display: flex;
}
.hendelse-under {
    margin-top: 24px;
}
.hendelse-under .deltakere {
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
    color: #fff;
    opacity: 1;
}
.v-select-program-meny :deep(.v-label.v-field-label.v-field-label--floating) {
    color: #fff !important;
    padding-bottom: 5px;
}
.v-select-program-meny.v-select--active-menu :deep(.v-label) {
    color: #fff !important;
}
.v-select-program-meny :deep(.v-input__control .v-field) {
    background: #00004c;
}
/* .v-select-program-meny.v-select--active-menu .v-input__control { */
.v-select-program-meny.v-select--active-menu :deep(.v-input__control) {
    background: red !important;
}
.v-select-program-meny :deep(.mdi-menu-down) {
    color: #fff !important;
    opacity: 1;
}
.v-select-program-meny.v-select--active-menu :deep(.mdi-menu-down) {
    color: #000 !important;
}
.v-select-program-meny :deep(.v-input__control) {
    border: solid 2px #fff;
    border-radius: 10px !important;
    overflow: hidden;
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
.sted-tid-after-open {
    display: flex;
}
.sted-after-open {
    margin: 0;
    color: #fff !important;
}
.close-btn {
    background: #00004c;
    border-radius: 50% !important;
    width: 40px;
    height: 40px;
    min-width: 0; /* remove Vuetify default min-width */
    position: absolute;
    top: 16px;
    right: 16px;
}
.close-btn-down {
    background: #00004c;
    color: #fff;
    margin-right: 10px;
    margin-bottom: 10px;
}
.deltakere-found .title {
    color: #fff;
    font-size: 20px;
    margin-bottom: 16px;
}
.found-deltaker-inside-chip {
    margin-right: 5px;
}
.deltaker-result-chip {
    margin-top: 4px;
    margin-bottom: 4px;
}
.deltaker-icon {
    margin-right: 5px;
    margin-top: -3px;
}
.hendelse-gruppe-timeline {
    float: left;
    display: block !important;
}
.custom-timeline-item :deep(.v-timeline-divider),
.custom-timeline-item :deep(.v-timeline-item__opposite),
.custom-timeline-item :deep(.v-timeline-divider__before) {
    display: none !important;
}
.custom-timeline-item :deep(.v-timeline-item__body) {
    display: flex !important;
    padding-left: 0 !important;
    margin-top: calc(4*var(--initial-space-box));
    cursor: pointer;
}
.group-hendelse-content * {
    color: #fff;
}
.gruppe-hendelse-img :deep(img) {
    background: #fff;
    border-radius: 22px;
}
.gruppe-hendelse-img :deep(.v-responsive__sizer) {
    padding: 0;
}
.hendelse-card :deep(.v-card-title) {
    white-space: normal;
    margin-right: 40px;
}
.share-button {
    height: 40px !important;
    width: 40px !important;
    background: #fff !important;
    box-shadow: none !important;
}
.share-button :deep(.mdi-share-variant) {
    color: #00004c !important;
    margin-left: -2px;
}
.hendelse-sted .sted {
    margin: 0;
}
.tid.under-tittel {
    display: none !important;
}

@media (max-width: 767px) {
    .hendelse-bilde {
        margin-right: 24px;
        width: 150px;
        min-width: 150px;
        height: 112px;
        background: transparent;
    }
    .hendelse-title {
        font-size: 30px !important;
    }
    .hendelse-sted {
        text-align: right;
        padding-right: 16px !important;
        min-width: 115px;
    }
    .hendelse-sted .sted {
        font-size: 16px;
    }
    .hendelse-sted .tid {
        font-size: 11px;
        text-align: center;
    }
    .hendelse-bilde {
        margin-right: 24px;
        width: 120px;
        height: 90px;

    }
    .dialog-program-popup {
        padding-left: 8px;
        padding-right: 8px;
    }
    .v-select-program-meny :deep(.v-label) {
        font-size: 16px;
    }
    .sted-tid-after-open {
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }
    .sted-tid-after-open .v-chip {
        flex-shrink: 0;
    }
}
@media (max-width: 520px) {
    .hendelse-bilde {
        margin-right: 24px;
        width: 100px !important;
        min-width: 100px !important;
        height: 75px !important;
    }
    .hendelse-content {
        width: 100%;
    }
    .hendelse-info-extendable .hendelse-bilde {
        display: none;
    }
    .under-content { 
        width: 100%;
    }
    .hendelse-title {
        font-size: 5vw !important;
    }
    .hendelse-beskrivelse .beskrivelse {
        font-size: 18px;
    }
    .hendelse-sted {
        display: none !important;
    }
    .tid.under-tittel {
        display: inline !important;
    }
    .hendelse-title {
        margin-bottom: 0;
    }
    .hendelse-sted .dag {
        font-size: 12px;
        font-weight: 300;
    }
    .v-select-program-meny {
        width: 80px;
        height: 70px;
    }
    .ls-meny-item {
        min-width: 60px;
    }
    .v-select-program-meny :deep(.v-label) {
        top: 3px !important;
    }
    .gruppe-hendelse-img {
        width: 120px !important;
    }

}
@media (max-width: 400px) {
    /* .hendelse-bilde {
        display: none;
    } */
    .hendelse-title {
        font-size: 18px !important;
    }
    .ls-meny-item {
        margin: auto 5px;
    }
}
</style>