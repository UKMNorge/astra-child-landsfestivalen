/**
 * Abstrakt klasse som definerer og representerer innhold i en hendelse.
 * Kan utvides til innslag, aktivitet eller andre typer i fremtiden.
 */
export abstract class HendelseContent {
    public id: string;
    public title: string;
    public beskrivelse?: string|undefined;
    public isOpen: boolean = false;
    protected bilder : string[] = [];

    constructor(id: string, title: string, bilder : string[], beskrivelse?: string) {
        this.id = id;
        this.title = title;
        this.bilder = bilder ? bilder : [];
        this.beskrivelse = beskrivelse;
    }

    
    /**
     * Henter innholdets unike identifikator
     */
    getId(): string {
        return this.id;
    }

    /**
     * Henter innholdets tittel
     */
    getTitle(): string {
        return this.title;
    }

    /**
     * Henter undertittel
     */
    getUnderTitle(): string|null {
        return null;
    }

    /**
     * Henter innholdets beskrivelse
     */
    public getBeskrivelse(): string | undefined {
        return this.beskrivelse;
    }

    public getBilde(): string {
        return this.bilder && this.bilder.length > 0 && this.bilder[0] ? this.bilder[0] : 'http://ukm.no/wp-content/uploads/2025/04/40ukm.png';
    }
    
    public getBilder(): string[] {
        return this.bilder != null ? this.bilder : [];
    }
}


export default HendelseContent;