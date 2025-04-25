/**
 * Abstrakt klasse som definerer og representerer innhold i en hendelse.
 * Kan utvides til innslag, aktivitet eller andre typer i fremtiden.
 */
export abstract class HendelseContent {
    public id: string;
    public title: string;
    public beskrivelse?: string|undefined;
    public isOpen: boolean = false;
    protected bilde : string|null = null;

    constructor(id: string, title: string, bilde : string|null, beskrivelse?: string) {
        this.id = id;
        this.title = title;
        this.bilde = bilde;
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
        return this.bilde ? this.bilde : 'http://ukm.no/wp-content/uploads/2025/04/40ukm.png';
    }
    
}


export default HendelseContent;