<template>
    <div v-if="dataFetched == true" class="aktiviteter-container">
        <div class="ls-program-meny">
            <div class="ls-inner-meny-beholder">
                <div class="ls-meny-item">
                    <SelectProgramStyle 
                        :label="'Dag'"
                        :availableItems="availableDager" 
                        v-model:selectedItems="selectedDager" 
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
                        background="#fff"
                        :label="'Tagg'"
                        :availableItems="availableTags" 
                        v-model:selectedItems="selectedTags" 
                    />
                </div>
            </div>
        </div>

        <template v-for="hendelse in getFilteredHendelser()" :key="hendelse.id">
            <div class="hendelse">
                <div @click="hendelse.isOpen = !hendelse.isOpen" class="first-width">
                    <div class="inner-content">
                        <div class="hendelse-bilde" v-show="!hendelse.isOpen">
                            <img :src="hendelse.image" alt="Bilde av hendelse">
                        </div>
                        <div class="hendelse-content">
                            <h2 class="hendelse-title">{{ hendelse.title }}</h2>
                        </div>
                        <div class="hendelse-sted">
                            <span v-show="!hendelse.isOpen" class="sted">{{ hendelse.sted }}</span>
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
                                <AktivitetContentComponent :aktivitetItem="hendelse" />
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
import Aktivitet from '../objects/Aktivitet';
import SelectProgramStyle from './utils/SelectProgramStyle.vue';
import AktivitetContentComponent from './utils/AktivitetContent.vue';


export default {
    props: {
       
    },
    mounted() {
        this.fetchAktiviteter();
    },
    components: {
        SelectProgramStyle : SelectProgramStyle,
        AktivitetContentComponent : AktivitetContentComponent,
    },
    data() {
        return {
            isActive : false,
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            dataFetched: false,
            fetchingStarted: false,
            availableSteder: [] as {id: number|string, title: string}[],
            availableTags: [] as {id: number|string, title: string}[],
            availableDager: [] as {id: number|string, title: string}[],

            selectedSteder : [] as {id: number|string, title: string}[],
            selectedTags : [] as {id: number|string, title: string}[],
            selectedDager : [] as {id: number|string, title: string}[],
            
            aktiviteter: [] as Aktivitet[],


            hendelseMedAktiviteter : {} as any,

        };
    },
    methods: {
        // Sjekker om hendelsen har aktiviteter eller innslag for å vise "Vis mer" knappen
        // hasShowMore(hendelse : Hendelse) : boolean {
        //     if(this.hendelseMedAktiviteter[hendelse.id] && this.hendelseMedAktiviteter[hendelse.id] == true) {
        //         return true;
        //     }

        //     if(hendelse.innslag && hendelse.innslag.length > 0) {
        //         return true;
        //     }

        //     return false;
        // },
        getFilteredHendelser() : Aktivitet[] {
            if(this.selectedSteder.length == 0 && this.selectedTags.length == 0 && this.selectedDager.length == 0) {
                return this.aktiviteter;
            }

            return this.aktiviteter.filter(aktivitet => {
                if(this.selectedSteder.length > 0 && !this.selectedSteder.find(sted => aktivitet.hasSted(sted))) {
                    return false;
                }

                
                if(this.selectedTags.length > 0 && !this.selectedTags.find(tagId => aktivitet.hasTag(tagId))) {
                    return false;
                }

                if(this.selectedDager.length > 0 && !this.selectedDager.find(day => aktivitet.hasDay(day))) {
                    return false;
                }

                // if(this.selectedDager.length > 0 && !this.selectedDager.find(t => String(t) == h.type)) {
                //     return false;
                // }

                return true;
            });
        },
        async fetchAktiviteter() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {};

            var results = await this.spaInteraction.runAjaxCall('getAlleAktiviteter.ajax.php', 'POST', data);
            
            let availableTags : any = {};
            let availableSteder : any = {};
            let availableDager : any = {};

            for (let key in results.aktiviteter) {
                let ak = results.aktiviteter[key];
                let newAktivitet = new Aktivitet(
                    ak.id, 
                    ak.navn,
                    ak.image,
                    ak.sted,
                    ak.beskrivelse,
                    ak.beskrivelseLeder,
                    ak.tidspunkter,
                    ak.tags,
                    -1,
                )
                this.aktiviteter.push(newAktivitet);

                for(let t of ak.tags) {
                    if(availableTags[t.id] == undefined) {
                        availableTags[t.id] = {id : t.id, title : t.navn};
                    }
                }

                if(availableSteder[newAktivitet.sted] == undefined) {
                    availableSteder[newAktivitet.sted] = {id : newAktivitet.sted, title : newAktivitet.sted};
                }

                // Tidspunkter kan ha andre steder
                for(let t of newAktivitet.tidspunkter) {
                    if(availableSteder[t.sted] == undefined) {
                        availableSteder[t.sted] = {id : t.sted, title : t.sted};
                    }

                    availableDager[t.getStartDag()] = {id : t.getStartDag(), title : t.getStartDag()};
                }
                
                
            }
            
            for(let key in availableTags) {
                let tag = availableTags[key];
                if(tag.title.length > 0) {
                    this.availableTags.push({'id' : tag.id, 'title' : tag.title});
                }
            }

            for(let key in availableSteder) {
                let sted = availableSteder[key];
                if(sted.title.length > 0) {
                    this.availableSteder.push({'id' : sted.id, 'title' : sted.title});
                }
            }

            for(let key in availableDager) {
                let dag = availableDager[key];
                if(dag.title.length > 0) {
                    this.availableDager.push({'id' : dag.id, 'title' : dag.title});
                }
            }


            this.dataFetched = true;

            
            return results;
        },
    },
};
</script>


<style>
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
}
.ls-meny-item {
    width: auto;
    margin: auto 10px;
    min-width: 110px;
}
.aktiviteter-container {
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
    width: 150px;
}
.hendelse-bilde img {
    border-radius: 22px;
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
    color: #000 !important;
}
.v-select-program-meny.v-select--active-menu :deep(.v-label) {
    color: #000 !important;
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
.v-select-program-meny :deep(.v-field__append-inner) {
    display: none !important;
}
.v-select-program-meny :deep(.v-input__control) {
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
.aktiviteter-container :deep(p),
.aktiviteter-container :deep(h1),
.aktiviteter-container :deep(h2),
.aktiviteter-container :deep(h3),
.aktiviteter-container :deep(h4),
.aktiviteter-container :deep(h5),
.aktiviteter-container :deep(h6) {
    color: #fff !important;
}
.aktiviteter-container :deep(.tags) {
    margin-top: 0 !important;
}
.aktiviteter-container :deep(.v-btn-success) {
    background-color: #fff !important;
}
.aktiviteter-container :deep(.hendelse-under) {
    margin-bottom: 30px;
}

@media (max-width: 767px) {
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
        min-width: 120px;
        max-width: 120px;
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
    .hendelse-content {
        width: 100%;
    }
    .hendelse-bilde {
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
    .hendelse-sted .sted {
        font-size: 14px;
    }
    .v-select-program-meny {
        width: 90px;
        height: 70px;
    }
    .ls-meny-item {
        min-width: 50px;
    }
    .v-select-program-meny :deep(.v-label) {
        top: 3px !important;
    }

}
@media (max-width: 400px) {
    .hendelse-bilde {
        display: none;
    }
    .hendelse-title {
        font-size: 18px !important;
    }
}
</style>