import HendelseContent from "./HendelseContent";

class Innslag extends HendelseContent {
    key : string;
    isSinglePerson: boolean;
    loading: boolean = false;

    personer : any[] = [];

    constructor(
        id : string, 
        title : string, 
        key : string, 
        beskrivelse: string,
        isSinglePerson: boolean,
        personer : any[] = [],
        image : string|null,
    ) {
        super(id, title, image, beskrivelse);
        this.key = key;
        this.isSinglePerson = isSinglePerson;
        this.personer = personer;
    }

    public getBilde(): string {
        return this.bilde ? this.bilde : this.key;
    }
}

export default Innslag;