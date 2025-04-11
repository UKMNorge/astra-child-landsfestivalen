/**
 * Abstrakt klasse som definerer og representerer innhold i en hendelse.
 * Kan utvides til innslag, aktivitet eller andre typer i fremtiden.
 */
export abstract class HendelseContent {
    public id: string;
    public title: string;
    public description?: string;

    constructor(id: string, title: string, description?: string) {
        this.id = id;
        this.title = title;
        this.description = description;
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
    getDescription(): string | undefined {
        return this.description;
    }
}


export default HendelseContent;