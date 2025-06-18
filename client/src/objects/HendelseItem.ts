import TimeplanItem from './TimeplanItem';

class HendelseItem extends TimeplanItem {
    private hendelseId: number;

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
    }

    public getHendelseId(): number {
        return this.hendelseId;
    }
}

export default HendelseItem;