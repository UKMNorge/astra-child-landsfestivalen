import HendelseContent from "./HendelseContent";

class Innslag extends HendelseContent {
    key : string;
    isSinglePerson: boolean;
    loading: boolean = false;
    fylke: string|null = null;

    personer : any[] = [];

    constructor(
        id : string, 
        title : string, 
        key : string, 
        beskrivelse: string,
        isSinglePerson: boolean,
        personer : any[] = [],
        bilder : string[] = [],
        fylke : string|null,
    ) {
        super(id, title, bilder, beskrivelse);
        this.key = key;
        this.isSinglePerson = isSinglePerson;
        this.personer = personer;
        this.fylke = fylke;
    }

    // Override
    public getUnderTitle(): string|null {
        return this.fylke;
    }
}

export default Innslag;