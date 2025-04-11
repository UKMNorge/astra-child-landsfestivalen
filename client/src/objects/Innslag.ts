import HendelseContent from "./HendelseContent";

class Innslag extends HendelseContent {
    key : string;
    isPerson: boolean;
    loading: boolean = false;

    constructor(id : string, key : string, title : string, beskrivelse : string, isPerson: boolean) {
        super(id, title, beskrivelse);
        this.key = key;
        this.isPerson = isPerson;
    }
}

export default Innslag;