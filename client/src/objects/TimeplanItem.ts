// Create a class called TimeplanItem
class TimeplanItem {
    private id: number;
    private title: string;
    private place: string;
    private description: string;
    private startDate: Date;
    private endDate: Date;
    private link?: string;

    constructor(id: number, 
        title: string, 
        place: string, 
        description: string, 
        startDate: Date, 
        endDate: Date,
        link?: string
    ) {
        this.id = id;
        this.title = title;
        this.place = place;
        this.description = description;
        this.startDate = startDate;
        this.endDate = endDate;
        this.link = link;
    }

    public getId(): number {
        return this.id;
    }

    public getTitle(): string {
        return this.title;
    }

    public getPlace(): string {
        return this.place;
    }

    public getLink(): string | null {
        return this.link ? this.link : null;
    }

    public getStartEndTimeHumanReadable(): string {
        // 10:00 - 11:00
        const startHours = this.startDate.getHours().toString().padStart(2, '0');
        const startMinutes = this.startDate.getMinutes().toString().padStart(2, '0');
        const endHours = this.endDate.getHours().toString().padStart(2, '0');
        const endMinutes = this.endDate.getMinutes().toString().padStart(2, '0');
        return `${startHours}:${startMinutes} - ${endHours}:${endMinutes}`;
    }


    getDescription(): string {
        return this.description;
    }

    getStartDate(): Date {
        return this.startDate;
    }

    getendDate(): Date {
        return this.endDate;
    }
}

export default TimeplanItem;