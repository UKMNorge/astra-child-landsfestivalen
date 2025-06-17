import Hendelse from "./Hendelse";

class HendelseGruppe extends Hendelse {
    hendelser: Hendelse[] = [];
    isGruppe: boolean = true;

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
        hendelser : Hendelse[],
        tag: string = null
    ) {
        super(id, title, img, start, end, sted, type, beskrivelse, innslag, fylker, deltakereNavn, tag);
        this.hendelser = hendelser;
        this.tag = tag;
    }

    public getTitle(): string {
        const titles = this.hendelser.map(hendelse => hendelse.getTitle());
        return titles.join(' ');
    }

    public hasSted(sted : string) : boolean {
        return this.hendelser.some(hendelse => hendelse.getSted() == sted);
    }

    // Type for hendelsegrupper er tag
    public hasType(type: string): boolean {
        if(this.hendelser.some(hendelse => hendelse.hasType(type))) {
            return true;
        }
        if( !this.tag ) {
            return false;
        }
        return this.tag.toLowerCase() == type.toLowerCase();
    }

    public getSted(): string {
        // join all unique sted from hendelser
        const unikeSteder = new Set(this.hendelser.map(hendelse => hendelse.getSted()));
        return Array.from(unikeSteder).join(', ');
    }

    public getAlleDeltakereNavn(): string[] {
        const alleDeltakere: string[] = [];
        this.hendelser.forEach(hendelse => {
            alleDeltakere.push(...hendelse.getAlleDeltakereNavn());
        });
        return alleDeltakere;
    }

    public getAlleFylkerString(): string {
        const alleFylker: string[] = [];
        this.hendelser.forEach(hendelse => {
            alleFylker.push(...hendelse.fylker);
        });
        return alleFylker.join(', ');
    }

    public getInnslagNavn(): string[] {
        const innslagNavn: string[] = [];
        this.hendelser.forEach(hendelse => {
            innslagNavn.push(...hendelse.innslag.map(i => i.name));
        });
        return innslagNavn;
    }

    public getHendelser(): Hendelse[] {
        // Sort by start date
        return this.hendelser.sort((a, b) => a.start - b.start);
    }

    public hasHendelse(id: string|number): boolean {
        return this.hendelser.some(hendelse => hendelse.id === id);
    }
}

export default HendelseGruppe;    
