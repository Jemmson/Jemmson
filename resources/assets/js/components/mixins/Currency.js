export default {
    methods: {
        convertPriceToIntegers(price) {
            let intString = ''
            if (price.length > 3) {
                for (let i = 0; i < price.length; i++) {
                    if (parseInt(price[i]) > -1) {
                        intString = intString + '' + price[i]
                    } else if (price[i] === '.') {
                        intString = intString + '.'
                    }
                }
                const intStringArray = intString.split('.')
                if (intStringArray[1].length === 1) {
                    intString = intString + '' + 0
                }
                return parseFloat(intString)

            } else {
                return null
            }
        },

        currencyMask(price) {

            if (price !== undefined) {
                if (price / 100) {
                    if ((price / 100) < 1) {
                        price = price.toString()
                        if (price.length === 2) {
                            return '$ .##'
                        } else if (price.length === 1) {
                            return '$ .0#'
                        }
                    } else {
                        price = price.toString()
                        if (price.length === 3) {
                            return '$ #.##'
                        } else if (price.length === 4) {
                            return '$ ##.##'
                        } else if (price.length === 5) {
                            return '$ ###.##'
                        } else if (price.length === 6) {
                            return '$ #,###.##'
                        } else if (price.length === 7) {
                            return '$ ##,###.##'
                        } else if (price.length === 8) {
                            return '$ ###,###.##'
                        } else if (price.length === 9) {
                            return '$ #,###,###.##'
                        } else if (price.length === 10) {
                            return '$ ##,###,###.##'
                        } else if (price.length === 11) {
                            return '$ ###,###,###.##'
                        } else if (price.length === 12) {
                            return '$ #,###,###,###.##'
                        } else if (price.length === 13) {
                            return '$ ##,###,###,###.##'
                        }
                    }

                } else {
                    price = price.toString()
                    if (price.length < 6) {
                        return '$ .##'
                    } else if (price.length === 6) {
                        return '$ #.##'
                    } else if (price.length === 7) {
                        return '$ ##.##'
                    } else if (price.length === 8) {
                        return '$ ###.##'
                    } else if (price.length === 9 || price.length === 10) {
                        return '$ #,###.##'
                    } else if (price.length === 11) {
                        return '$ ##,###.##'
                    } else if (price.length === 12) {
                        return '$ ###,###.##'
                    } else if (price.length === 13 || price.length === 14) {
                        return '$ #,###,###.##'
                    } else if (price.length === 15) {
                        return '$ ##,###,###.##'
                    } else if (price.length === 16) {
                        return '$ ###,###,###.##'
                    } else if (price.length === 17 || price.length === 18) {
                        return '$ #,###,###,###.##'
                    } else if (price.length === 19) {
                        return '$ ##,###,###,###.##'
                    }
                }
            }
        },

        convertNumToString(num) {

            let numString = ''
            let numArray = ''
            let finalVal = ''
            if (typeof num !== 'string') {
                numString = num + ''
            } else {
                numString = num
            }
            numArray = numString.split('.')

            if (numArray.length === 2 && numArray[1].length === 1) {
                finalVal = numString + '0'
            } else if (numArray.length === 1) {
                finalVal = numArray[0] + '.00'
            } else {
                finalVal = numString
            }
            return finalVal
        },

    }
}