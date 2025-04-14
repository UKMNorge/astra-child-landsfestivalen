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
        personer : any[] = []
    ) {
        super(id, title, beskrivelse);
        this.key = key;
        this.isSinglePerson = isSinglePerson;
        this.personer = personer;
    }
}

export default Innslag;