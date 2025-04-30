import type Aktivitet from "./Aktivitet";

class Tidspunkt {
    id : string;
    title : string;
    erKunInterne : boolean;
    erSammeStedSomAktivitet : boolean;
    harPaamelding : boolean;
    maksAntallDeltakere : number;
    antallDeltakere : number;
    sted : string;
    start : Date;
    slutt : Date;

    aktivitet : Aktivitet;

    constructor(
        id : string,
        title : string,
        erKunInterne : boolean,
        erSammeStedSomAktivitet : boolean,
        harPaamelding : boolean,
        maksAntallDeltakere : number,
        antallDeltakere : number,
        sted : string,
        start : string,
        slutt : string,
        aktivitet : Aktivitet,
    ) {
        this.id = id;
        this.title = title;
        this.erKunInterne = erKunInterne;
        this.erSammeStedSomAktivitet = erSammeStedSomAktivitet;
        this.harPaamelding = harPaamelding;
        this.maksAntallDeltakere = maksAntallDeltakere;
        this.antallDeltakere = antallDeltakere;
        this.sted = sted;
        this.start = new Date(start.replace(" ", "T"));
        this.slutt = new Date(slutt.replace(" ", "T"));

        this.aktivitet = aktivitet;
    }
    
    public getHumanFormat() : string {
        return this.getHumanDates() + this.getStedString();
    }

    private getStedString() : string {
        return this.erSammeStedSomAktivitet ? '' : (' (STED: ' + this.sted +')');
    }

    public getStartDag() : string {        
        // Days of the week array
        const daysOfWeek = ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'];
    
        // Format the date to the desired format "Mandag hh:mm"
        return `${daysOfWeek[this.start.getDay()]}`;

    }

    private getHumanDates(): string {
        let dates = [this.start, this.slutt];
        if (!dates || dates.length !== 2) return "";

        const formatDate = (date: Date, hourOnly: boolean = false) => {
            const hours = String(date.getHours()).padStart(2, "0");
            const minutes = String(date.getMinutes()).padStart(2, "0");

            const daysOfWeek = ['søndag', 'mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag'];

            if(hourOnly) {
                return `${hours}:${minutes}`;
            }
            return `${daysOfWeek[date.getDay()]} ${hours}:${minutes}`;
        };

        if(dates[0].getDay() == dates[1].getDay()) {
            let from = formatDate(dates[0]).charAt(0).toUpperCase() + formatDate(dates[0]).slice(1);
            return `${from} - ${formatDate(dates[1], true)}`;
        }
        return `Fra ${formatDate(dates[0])} til ${formatDate(dates[1])}`;
    };
}

export default Tidspunkt;