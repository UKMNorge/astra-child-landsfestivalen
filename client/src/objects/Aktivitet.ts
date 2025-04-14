import HendelseContent from "./HendelseContent";
import Tidspunkt from "./Tidspunkt";

class Aktivitet extends HendelseContent {
    loading: boolean = false;
    image:  string;
    sted: string;
    beskrivelseLeder: string;
    hendelseId: string|number;
    tidspunkter: Tidspunkt[] = [];
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
    ) {
        super(id, navn, beskrivelse);
        this.image = image;
        this.sted = sted;
        this.beskrivelseLeder = beskrivelseLeder;
        this.hendelseId = hendelseId;

        this.tags = tags;
        
        console.log('tidspunkter');
        console.log(tidspunkter);
        // add tidspunkter
        for(let tidspunkt of tidspunkter) {
            // Legg til kun tidspunkter som er tilknyttet hendelsen
            if(tidspunkt.hendelseId == this.hendelseId) {
                this.tidspunkter.push(
                    new Tidspunkt(
                        tidspunkt.id,
                        tidspunkt.title,
                        tidspunkt.erKunInterne,
                        tidspunkt.erSammeStedSomAktivitet,
                        tidspunkt.harPaamelding,
                        tidspunkt.sted,
                        tidspunkt.start,
                        tidspunkt.slutt,
                        this
                    )
                );
            }
        }
    }

    public getImage() : string {
        return this.image;
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

}

export default Aktivitet;