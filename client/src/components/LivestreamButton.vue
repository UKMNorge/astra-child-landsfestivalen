<template>
    <div>
        <div>
            <v-btn
                v-if="shouldShowButton"
                class="button-livestreaming"
                @click="redirectToLivestreaming"
                rounded="large">
                Direktesending
            </v-btn>
        </div>
    </div>
</template>
<script lang="ts">
import HendelseItem from '../objects/HendelseItem';
import Hendelse from '../objects/Hendelse';

export default {
    props: {
        programType: {
            type: String,
            default: 'publikum'
        }
    },
    components: {

    },
    data() {
        return {
            spaInteraction : (<any>window).spaInteraction, // Definert i main.ts
            tab: null as string | null,
            hendelseItems: [] as HendelseItem[],
            daysOfWeek: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
            hendelser: [] as Hendelse[],
            deltakere: {} as { [key: string]: any },
            alleInnslag: [] as any,
            innslagTitler: {} as { [key: string]: any },
            dataFetched: false as boolean,
            fetchingStarted: false as boolean,
            direktesendinger: {} as { [key: string]: any },
            buttonVisibilityTimer: null as any,
        }
    },
    computed: {
        getTabPosition() : number {
            if(this.tab == null) {
                return 0;
            }
            return this.getTabs().indexOf(this.tab);
        },
        shouldShowButton() : boolean {
            const now = new Date();

            // Check if any direktesending is starting within 20 minutes
            for (let hendelseId in this.direktesendinger) {
                const direktesending = this.direktesendinger[hendelseId];
                
                if (!direktesending || !direktesending.start) {
                    continue;
                }
                
                const startTime = new Date(direktesending.start.date);
                const timeDiff = startTime.getTime() - now.getTime();
                const minutesDiff = timeDiff / (1000 * 60);
                
                console.warn(`Direktesending ${direktesending.navn} starts in ${minutesDiff} minutes`);

                // Show button if direktesending starts within 20 minutes (and hasn't started yet)
                if (minutesDiff >= 0 && minutesDiff <= 20) {
                    return true;
                }
            }
            
            return false;
        }
    },
    mounted() {
        this.fetchProgramData();
        let tab = this.tab;
        this.testFetch();
        
        // Set up timer to update button visibility every minute
        this.buttonVisibilityTimer = setInterval(() => {
            // Force re-evaluation of computed properties by triggering a reactive update
            this.$forceUpdate();
        }, 60000); // Check every minute
    },
    beforeUnmount() {
        // Clean up timer when component is destroyed
        if (this.buttonVisibilityTimer) {
            clearInterval(this.buttonVisibilityTimer);
        }
    },
    methods : {
        redirectToLivestreaming() {
            // Redirect to the livestreaming page
            window.location.href = 'https://ukm.no/festivalen/direktesending/';
        },
        leggTilAktivitet() {
            // Handle button click - add your implementation here
            console.log('Legg til aktivitet button clicked');
            // You can add your specific functionality here
        },
        getDirektesendingInfoTekst(direktesending: any) : string {
            if(!direktesending || !direktesending.start || !direktesending.stopp) {
                return 'Ingen direktesending tilgjengelig';
            }
            const start = new Date(direktesending.start.date);
            const stopp = new Date(direktesending.stopp.date);
            const now = new Date();
            
            if (now >= start && now <= stopp) {
                return 'Direktesending pågår nå';
            } else if (now > stopp) {
                return 'Direktesending er avsluttet';
            } else {
                const timeDiff = start.getTime() - now.getTime();
                const hoursDiff = timeDiff / (1000 * 60 * 60);
                
                if (hoursDiff <= 5) {
                    const hours = Math.floor(hoursDiff);
                    const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                    
                    if (hours === 0) {
                        if (minutes <= 1) {
                            return 'Direktesending starter om ca. 1 minutt';
                        } else if (minutes < 60) {
                            return `Direktesending starter om ca. ${minutes} minutter`;
                        }
                    } else if (hours === 1) {
                        return minutes > 0 ? 
                            `Direktesending starter om ca. 1 time og ${minutes} minutter` : 
                            'Direktesending starter om ca. 1 time';
                    } else {
                        return minutes > 0 ? 
                            `Direktesending starter om ca. ${hours} timer og ${minutes} minutter` : 
                            `Direktesending starter om ca. ${hours} timer`;
                    }
                }
                
                const dayName = this.daysOfWeek[start.getDay()];
                return `Direktesending starter ${dayName.toLowerCase()} kl. ${start.getHours().toString().padStart(2, '0')}:${start.getMinutes().toString().padStart(2, '0')}`;
            }
        },
        getDirektesending(hendelseId: number): any {
            return this.direktesendinger[hendelseId] || null;
        },
        toggleTimeplanItem(tp: HendelseItem) {
            tp.toggleOpen();
        },
        getHendelse(hendelseId : number) : Hendelse | null {
            for(let hendelse of this.hendelser) {
                if(hendelse.id == hendelseId) {
                    return hendelse;
                }
            }
            return null;
        },
        openTimeplanItem(tp: HendelseItem) {
            // Only if the link is defined and not empty
            if(tp.getLink() != null && tp.getLink().length > 0) {
                window.open(tp.getLink(), '_blank');
            }
        },
        moveToTab(dayTab: string) {
            if(this.tab == dayTab) {
                return;
            }
            this.tab = dayTab;
        },
        _getDateKey(date: Date) : string {
            const day = this.daysOfWeek[date.getDay()];
            const dateDDMM = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth()+1).toString().padStart(2, '0')}`;
            return `${day} ${dateDDMM}`;
        },
        getTabs() : string[] {
            let retTabs : string[] = [];
            
            for(let tp of this.hendelseItems) {
                const hendelseIdStr = tp.getHendelseId().toString();
                if (!retTabs.includes(hendelseIdStr)) {
                    retTabs.push(hendelseIdStr);
                }
            }
            return retTabs;
        },
        hasLivestreamingStarted(hendelseId : number) : boolean {
            const direktesending = this.getDirektesending(hendelseId);

            // Direktesending can be null if no livestreaming is available for this hendelse
            if (!direktesending) {
                return false; // No livestreaming for this hendelse
            }

            const startDirektesending = new Date(direktesending.start.date);
            const stoppDirektesending = new Date(direktesending.stopp.date);

            console.log(direktesending.navn);
            console.log('startDirektesending');
            console.log(startDirektesending);
            console.log('stoppDirektesending');
            console.log(stoppDirektesending);
            console.log('.');

            const now = new Date();
            return now >= startDirektesending && now <= stoppDirektesending;
            
        },
        getHendelseNameById(hendelseId: string): string {
            const hendelse = this.hendelser.find(h => h.id.toString() === hendelseId);
            return hendelse ? hendelse.title : `Hendelse ${hendelseId}`;
        },
        
        getPersonsForInnslag(hendelseItem: HendelseItem): any[] {
            const persons: any[] = [];
            
            // Find the innslag that corresponds to this HendelseItem
            for (let innslagId in this.alleInnslag) {
                const innslag = this.alleInnslag[innslagId];
                
                // Check if this innslag belongs to the same hendelse and has the same name as the HendelseItem
                if (innslag.hendelseId === hendelseItem.getHendelseId() && 
                    innslag.navn === hendelseItem.getTitle()) {
                    
                    // Find all persons for this innslag
                    for (let deltakerKey in this.deltakere) {
                        const deltaker = this.deltakere[deltakerKey];
                        if (deltaker.innslagId == innslag.id) {
                            // Add person only if it doesn't already exist in the array
                            const existingPerson = persons.find(p => p.id === deltaker.id);
                            if (!existingPerson) {
                                persons.push(deltaker);
                            }
                        }
                    }
                    break;
                }
            }
            
            return persons;
        },
        
        getTitlesForInnslag(hendelseItem: HendelseItem): any[] {
            const titles: any[] = [];
            
            // Find the innslag that corresponds to this HendelseItem
            for (let innslagId in this.alleInnslag) {
                const innslag = this.alleInnslag[innslagId];
                
                // Check if this innslag belongs to the same hendelse and has the same name as the HendelseItem
                if (innslag.hendelseId === hendelseItem.getHendelseId() && 
                    innslag.navn === hendelseItem.getTitle()) {
                    
                    // Find all titles for this innslag
                    if (this.innslagTitler[innslag.id]) {
                        // Add titles only if they are not already in the array
                        for (let title of this.innslagTitler[innslag.id]) {
                            if (!titles.some(t => t.id === title.id)) {
                                titles.push(title);
                            }
                        }
                    }
                    break;
                }
            }
            
            return titles;
        },
        
        getHendelserTimeplan() : any {
            if(this.hendelseItems == null || this.hendelseItems.length == 0) {
                return [];
            }
            var timeplanMap: { [key: string]: HendelseItem[] } = {};
            this.hendelseItems.forEach((item : HendelseItem) => {
                const hendelseIdStr = item.getHendelseId().toString();
                if (!timeplanMap[hendelseIdStr]) {
                    timeplanMap[hendelseIdStr] = [];
                }
                timeplanMap[hendelseIdStr].push(item);
            });

            return timeplanMap;
        },
        generateTimeplan() {
            // Clear existing items
            this.hendelseItems = [];
            
            // Generate HendelseItem objects from alleInnslag which contains the actual innslag data
            let itemId = 1;
            
            console.log('this.alleInnslag');
            console.log(this.alleInnslag);

            for (let innslagId in this.alleInnslag) {
                let innslag = this.alleInnslag[innslagId];
                
                // Find the corresponding hendelse for this innslag
                let hendelse = this.hendelser.find(h => h.id === innslag.hendelseId);
                if (!hendelse) {
                    continue;
                }
                
                // Convert timestamps to Date objects
                let startDate: Date;
                let endDate: Date;
                
                if (innslag.start && innslag.start * 1000) {
                    startDate = new Date(innslag.start * 1000);
                } else {
                    // Fallback to hendelse start time
                    startDate = new Date(hendelse.start * 1000);
                }
                
                if (innslag.slutt && innslag.slutt * 1000) {
                    endDate = new Date(innslag.slutt * 1000);
                } else {
                    // If no end time, add 1 hour to start time
                    endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
                }
                
                // Generate link to hendelse page
                const hendelseLink = `https://ukm.no/festivalen/single-hendelse/?hendelse-id=${hendelse.id}`;
                
                // Create the HendelseItem
                const hendelseItem = new HendelseItem(
                    itemId++,
                    innslag.navn || 'Uten navn',
                    hendelse.sted || '',
                    innslag.beskrivelse || hendelse.beskrivelse || '',
                    startDate,
                    endDate,
                    hendelseLink,
                    hendelse.id
                );
                
                this.hendelseItems.push(hendelseItem);
            }
            
            // Sort items by start time
            this.hendelseItems.sort((a, b) => {
                return a.getStartDate().getTime() - b.getStartDate().getTime();
            });
            
            
            // Set the first tab as selected after data is loaded
            // Set the tab closest to current time
            const tabs = this.getTabs();
            if (tabs.length > 0) {
                const now = new Date().getTime();
                let closestTab = tabs[0];
                let closestDiff = Infinity;

                for (const tabId of tabs) {
                    const items = this.hendelseItems.filter(item => item.getHendelseId().toString() === tabId);
                    if (items.length > 0) {
                        const firstItemTime = items[0].getStartDate().getTime();
                        const diff = Math.abs(firstItemTime - now);
                        if (diff < closestDiff) {
                            closestTab = tabId;
                            closestDiff = diff;
                        }
                    }
                }

                this.tab = closestTab;
            } else {
                console.warn('No tabs available, please check your timeplan items.');
            }
        },
        async testFetch() {
            // let data = {};
            // var results = await this.spaInteraction.runAjaxCall('getLivestreaming.ajax.php', 'POST', data);
            // console.log('Test fetch results:', results);
        },
        async fetchProgramData() {
            this.fetchingStarted = true;
            this.dataFetched = false;

            var data = {
                erDeltakerProgram: this.programType == 'deltakere'
            };

            var results = await this.spaInteraction.runAjaxCall('getProgramOyeblikk.ajax.php', 'POST', data);
            
            this.hendelseMedAktiviteter = results.hendelseMedAktiviteter;
            this.innslagTitler = results.innslagTitler;
            
            for(let key in results.direktesendinger) {
                const ds = results.direktesendinger[key];

                if (ds != null) {
                    const start = new Date(ds.start.date);
                    const stop = new Date(ds.stopp.date);

                    // Add 2 hours to both start and stop
                    start.setHours(start.getHours() + 2);
                    stop.setHours(stop.getHours() + 2);
                    // Add 15 minutes to stop time (maybe the program may be longer than 2 hours)
                    stop.setMinutes(stop.getMinutes() + 15);

                    // Update the date strings
                    ds.start.date = start.toISOString();
                    ds.stopp.date = stop.toISOString();
                }
            }

            this.direktesendinger = results.direktesendinger;

            for (let h of results.hendelser) {
                if(h.tag != 'Forestilling' && h.tag != 'forestilling') {
                    continue; // Only process hendelser with tag 'Forestilling'
                }
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
                    this.alleInnslag.push(innslag);
                }

                // Calculate end time using start time + duration in seconds

                const endTime = Number(h.start) + (h.tidbruk && h.tidbruk.sekunder ? h.tidbruk.sekunder : 0);

                var newHendelse = new Hendelse(
                    h.id, 
                    h.navn, 
                    h.bilde ?? 'http://ukm.no/wp-content/uploads/2025/04/40ukm.png', 
                    h.start, 
                    endTime, 
                    h.sted, 
                    h.tag, 
                    h.beskrivelse,
                    innslagArrObj,
                    fylker,
                    deltakereNavn,
                    h.visning,
                );
                this.hendelser.push(newHendelse);
            }
            this.dataFetched = true;
            this.fetchingStarted = false;
            
            // Generate timeplan items after data is fetched
            this.generateTimeplan();
            
            console.log('Fetched hendelser:', this.hendelser);
        },
    }
}

</script>

<style scoped>
.button-livestreaming {
    background-color: #fff056;
    color: #00004c;
    font-weight: 700;
    width: 100%;
    box-shadow: none;
    padding: 20px;
    height: auto !important;
}
</style>