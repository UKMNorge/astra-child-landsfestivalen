// Create a class called TimeplanItem
class TimeplanItem {
    private id: number;
    private title: string;
    private place: string;
    private description: string;
    private startDate: Date;
    private endDate: Date;

    constructor(id: number, title: string, place: string, description: string, startDate: Date, endDate: Date) {
        this.id = id;
        this.title = title;
        this.place = place;
        this.description = description;
        this.startDate = startDate;
        this.endDate = endDate;
    }

    getId(): number {
        return this.id;
    }

    getTitle(): string {
        return this.title;
    }

    public getPlace(): string {
        return this.place;
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