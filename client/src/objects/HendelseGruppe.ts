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
    ) {
        super(id, title, img, start, end, sted, type, beskrivelse, innslag, fylker, deltakereNavn);
        this.hendelser = hendelser;
    }

    public getTitle(): string {
        const titles = this.hendelser.map(hendelse => hendelse.getTitle());
        return titles.join(' ');
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
        return this.hendelser;
    }

    public hasHendelse(id: string|number): boolean {
        return this.hendelser.some(hendelse => hendelse.id === id);
    }
}

export default HendelseGruppe;    
