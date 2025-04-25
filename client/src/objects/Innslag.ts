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
        image : string|null,
        fylke : string|null,
    ) {
        super(id, title, image, beskrivelse);
        this.key = key;
        this.isSinglePerson = isSinglePerson;
        this.personer = personer;
        this.fylke = fylke;
    }

    public getBilde(): string {
        return this.bilde ? this.bilde : super.getBilde();
    }

    // Override
    public getUnderTitle(): string|null {
        return this.fylke;
    }
}

export default Innslag;