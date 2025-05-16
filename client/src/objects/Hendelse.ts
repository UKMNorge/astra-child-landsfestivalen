class Hendelse {
    id: number;
    title: string;
    img: string;
    start: number;
    end: number;
    sted: string;
    type: string;
    beskrivelse: string;
    isOpen: boolean = false;
    innslag: {name : string, antallDeltakere : number, url : string}[] = [];
    fylker : string[] = [];
    deltakereNavn : string[] = [];

    constructor(id : number, 
                title : string, 
                img : string, 
                start : number, 
                end : number, 
                sted : string, 
                type : string, 
                beskrivelse : string,
                innslag : {name : string, antallDeltakere : number, url : string}[] = [],
                fylker : string[],
                deltakereNavn : string[],
    ) {
        this.id = id;
        this.title = title;
        this.img = img;
        this.start = start;
        this.end = end;
        this.sted = sted;
        this.type = type;
        this.beskrivelse = beskrivelse;
        this.innslag = innslag;
        this.fylker = fylker;
        this.deltakereNavn = deltakereNavn;

    }

    async getSmallerBilde(): Promise<string> {
        const originalUrl = this.img; 
        // Split off extension
        const lastDot = originalUrl.lastIndexOf('.');
        if (lastDot === -1) {
            // no extension? just return original
            return originalUrl;
        }

        const base = originalUrl.slice(0, lastDot);
        const ext  = originalUrl.slice(lastDot); // includes “.”
        const thumbUrl = `${base}-400x600${ext}`;

        try {
            const resp = await fetch(thumbUrl, { method: 'HEAD' });
            if (resp.ok) {
            return thumbUrl;
            }
        } catch (e) {
            console.warn('Could not fetch thumbnail', e);
        }
        return originalUrl;
    }
    
    getTitle(): string {
        return this.title;
    }

    getAlleDeltakereNavn() : string[] {
        console.log(this.deltakereNavn);
        return this.deltakereNavn;
    }

    getAlleFylkerString() : string {
        let fylker : string[] = [];
        for(let i = 0; i < this.fylker.length; i++) {
            if(this.fylker[i] != null) {
                fylker.push(this.fylker[i]);
            }
        }
        return fylker.join(', ');
    }

    getInnslagNavn() : string[] {
        let innslagNavn : string[] = [];
        for(let i = 0; i < this.innslag.length; i++) {
            if(this.innslag[i].name != null) {
                innslagNavn.push(this.innslag[i].name);
            }
        }
        return innslagNavn;
    }

    getStartDag() : string {
        // Convert timestamp to date
        const startDate = new Date(this.start * 1000);
        
        // Days of the week array
        const daysOfWeek = ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'];
    
        // Format the date to the desired format "Mandag hh:mm"
        return `${daysOfWeek[startDate.getDay()]}`;

    }

    getStart(): string {
        // Convert timestamp to date
        const startDate = new Date(this.start * 1000);
        
        // Days of the week array
        const daysOfWeek = ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'];
    
        // Format the date to the desired format "Mandag hh:mm"
        return `${daysOfWeek[startDate.getDay()]} ${String(startDate.getHours()).padStart(2, '0')}:${String(startDate.getMinutes()).padStart(2, '0')}`;
    }
    
}



export default Hendelse;