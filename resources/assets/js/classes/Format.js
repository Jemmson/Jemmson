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
}