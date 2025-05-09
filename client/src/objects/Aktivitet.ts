import HendelseContent from "./HendelseContent";
import Tidspunkt from "./Tidspunkt";

class Aktivitet extends HendelseContent {
    loading: boolean = false;
    image:  string;
    sted: string;
    beskrivelseLeder: string;
    hendelseId: string|number;
    tidspunkter: Tidspunkt[] = [];
    kursholder : string;
    tags : any[] = [];

    constructor(
        id : string, 
        navn : string, 
        image : string,
        sted : string,
        beskrivelse : string|undefined,
        beskrivelseLeder : string,
        tidspunkter : any[],
        tags : any[],
        hendelseId : string|number,
        kursholder : string
    ) {
        super(id, navn, image, beskrivelse);
        this.image = image;
        this.sted = sted;
        this.beskrivelseLeder = beskrivelseLeder;
        this.hendelseId = hendelseId;
        this.kursholder = kursholder;
        this.tags = tags;
        
        // add tidspunkter
        for(let tidspunkt of tidspunkter) {
            // Legg til kun tidspunkter som er tilknyttet hendelsen
            if(this.hendelseId = -1 || tidspunkt.hendelseId == this.hendelseId) {
                this.tidspunkter.push(
                    new Tidspunkt(
                        tidspunkt.id,
                        tidspunkt.title,
                        tidspunkt.erKunInterne,
                        tidspunkt.erSammeStedSomAktivitet,
                        tidspunkt.harPaamelding,
                        tidspunkt.maksAntall ?? 99999,
                        tidspunkt.antallDeltakere,
                        tidspunkt.sted,
                        tidspunkt.start,
                        tidspunkt.slutt,
                        this,
                        tidspunkt.hendelseId
                    )
                );
            }
        }
    }

    public getImage() : string {
        return this.image;
    }
    
    // Returnerer tidspunkter sortert etter starttidspunkt
    public getTidspunkterSortert() : Tidspunkt[] {
        // Kopierer array for å ikke redigere originalen
        return [...this.tidspunkter].sort((a, b) => {
            return a.start.getTime() - b.start.getTime();
        })
    }

    public hasTag(tagId: number): boolean {
        if (!this.tags || this.tags.length === 0) {
            return false;
        }

        // Search for a tag with matching id or title
        return this.tags.some(tag => 
            tag.id == tagId
        );
    }

    public hasSted(stedNavn : string) : boolean {
        if(this.sted.toLocaleLowerCase() === stedNavn.toLocaleLowerCase()) {
            return true;
        }
        
        // sjekk alle tidspunkter for annet sted
        for(let tidspunkt of this.tidspunkter) {
            if(!tidspunkt.erSammeStedSomAktivitet) {
                if(tidspunkt.sted.toLocaleLowerCase() === stedNavn.toLocaleLowerCase()) {
                    return true;
                }
            }
        }
        return false;
    }

    public hasDay(day : string) : boolean {
        for(let tidspunkt of this.tidspunkter) {
            if(tidspunkt.getStartDag() === day) {
                return true;
            }
        }
        return false;
    }

    // Override of super class method
    public getBeskrivelse(): string | undefined {
        if(!this.beskrivelse) return '';

        return decodeURIComponent(this.beskrivelse);
    }

    public getLederBeskrivelse(): string | undefined {
        if(!this.beskrivelseLeder) return '';

        return decodeURIComponent(this.beskrivelseLeder);
    }
    
    // Sjekker harPaamelding på alle tidspunkt. Hvis en av dem har harPaamelding=true, skal det være mulig å melde seg på denne aktiviteten (i det tidspunktet)
    public harMeldPaa() : boolean {
        for(let tidspunkt of this.tidspunkter) {
            if(tidspunkt.harPaamelding) return true;
        }
        return false;
    }

    // Sjekker harPaamelding på alle tidspunkt som tilhører denne hendelsen. Hvis en av dem har harPaamelding=true, skal det være mulig å melde seg på denne aktiviteten (i hendelseId)
    // Hvis hendelseId = -1, er aktiviteten utenfor programmet og sjekkes derfor alle tidspunkter
    public harMeldPaaIHendelse(hendelseId : number) : boolean {
        for(let tidspunkt of this.tidspunkter) {
            if(tidspunkt.harPaamelding) {
                if(hendelseId == -1 || hendelseId == tidspunkt.hendelseId) {
                    return true;
                } 
            } 
        }
        return false;
    }

    // Get antall ledige plasser fra alle tidspunkter
    public getAlleLedigePlasser() {
        let ledigePlasser = 0;
        for(let tidspunkt of this.tidspunkter) {
            if(tidspunkt.harPaamelding) {
                ledigePlasser += tidspunkt.maksAntallDeltakere - tidspunkt.antallDeltakere;
            }
        }
        return ledigePlasser;
    }

    public getBilde(): string {
        return this.bilde ? this.bilde : super.getBilde();
    }
}

export default Aktivitet;