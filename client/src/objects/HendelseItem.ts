import TimeplanItem from './TimeplanItem';

class HendelseItem extends TimeplanItem {
    private hendelseId: number;
    public isOpen: boolean = false;

    constructor(
        id: number,
        title: string,
        place: string,
        description: string,
        startDate: Date,
        endDate: Date,
        link: string,
        hendelseId: number

    ) {
        super(id, title, place, description, startDate, endDate, link);
        this.hendelseId = hendelseId;
        this.isOpen = false;
    }

    public getHendelseId(): number {
        return this.hendelseId;
    }

    public toggleOpen(): void {
        this.isOpen = !this.isOpen;
    }
}

export default HendelseItem;