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

    getStart(): string {
        // Convert timestamp to date
        const startDate = new Date(this.start * 1000);
        
        // Days of the week array
        const daysOfWeek = ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'];
    
        // Format the date to the desired format "Mandag hh:mm"
        return `${daysOfWeek[startDate.getDay()]} ${String(startDate.getHours()).padStart(2, '0')}:${String(startDate.getMinutes()).padStart(2, '0')}`;
    }
    
}



export default Hendelse;