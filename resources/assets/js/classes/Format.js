export default class Format {
    static addDollarSign(obj, key) {
        obj[key] = "$" + this.numbersOnly(obj[key]);
    }

    static numbers(obj, key) {
        obj[key] = this.numbersOnly(obj[key]);
    }

    static numbersOnly(num) {
        return num.toString().replace(/[^0-9.]/g, "");
    }

    static phone(phone) {
        return phone.replace(/[^0-9]/g, '')
            .replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
    }
    
    static jobName(name) {
        // if (name.length > 20) {
        //     return name.substring(0, 20) + '...';
        // }
        return name;
    }

    static statusLabel(status) {
        switch (status) {
            case 'bid_task.accepted' : 
                return 'label-success';
                break;
            case 'bid_task.finished_by_sub':
                return 'label-warning';
                break;
            default: 
                return 'label-info';
                break;
        }
    }
}