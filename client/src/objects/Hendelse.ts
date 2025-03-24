class Hendelse {
    id: number;
    title: string;
    img: string;
    start: number;
    end: number;
    sted: string;
    type: string;
    beskrivelse: string;
    isOpen: boolean = false;


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
        const startDate = new Date(this.start * 1000);
        
        return `${String(startDate.getDate()).padStart(2, '0')}.${String(startDate.getMonth() + 1).padStart(2, '0')} ${String(startDate.getHours()).padStart(2, '0')}:${String(startDate.getMinutes()).padStart(2, '0')}`
    }
}



export default Hendelse;