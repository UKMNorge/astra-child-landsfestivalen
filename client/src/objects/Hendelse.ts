class Hendelse {
    id: number;
    title: string;
    img: string;
    start: number;
    end: number;
    sted: string;
    type: string;
    beskrivelse: string;
    isOpen: boolean = true;


    constructor(id : number, title : string, img : string, start : number, end : number, sted : string, type : string, beskrivelse : string) {
        this.id = id;
        this.title = title;
        this.img = img;
        this.start = start;
        this.end = end;
        this.sted = sted;
        this.type = type;
        this.beskrivelse = beskrivelse;
    }

    getStart() : string {
        // convert timestamp to date string
        return new Date(this.start).toLocaleString();
    }
}



export default Hendelse;