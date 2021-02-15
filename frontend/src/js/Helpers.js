/**
 *
 * @param {int} timestamp
 * @return {Promise<Object>}
 */
export function parseDate (timestamp) {
    return dayjs.unix(timestamp).format('DD MMM YYYY HH:mm')
}

/**
 *
 * @param {array} nums - array of numbers 
 * @return {int}
 */

export function  increaseMaxYaxes(nums){
    let max = Math.max(...nums);
    let num = Math.abs(max/10);
    num  = 10 - (num - Math.floor(num))*10+max+5;
    return num;
}

/**
 *
 * @param {string} str - 
 * @return {str}
 */

export function stripNumber(str){
    return str.replace(/[0-9]/g, '');
}