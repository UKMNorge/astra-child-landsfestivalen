/**
 * Abstrakt klasse som definerer og representerer innhold i en hendelse.
 * Kan utvides til innslag, aktivitet eller andre typer i fremtiden.
 */
export abstract class HendelseContent {
    public id: string;
    public title: string;
    public beskrivelse?: string|undefined;
    public isOpen: boolean = false;

    constructor(id: string, title: string, beskrivelse?: string) {
        this.id = id;
        this.title = title;
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
     * Henter innholdets beskrivelse
     */
    public getBeskrivelse(): string | undefined {
        return this.beskrivelse;
    }
}


export default HendelseContent;