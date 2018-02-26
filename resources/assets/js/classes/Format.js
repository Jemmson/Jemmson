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
}