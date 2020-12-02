import Vue from 'vue';
import { TimeFormats } from '~/assets/js/utils/Enums';

function randomInteger(min, max) {
    return Math.floor(min + (Math.random() * ((max + 1) - min)));
}

function contentWidth(elem) {
    const cs = getComputedStyle(elem);
    const paddingX = parseFloat(cs.paddingLeft) + parseFloat(cs.paddingRight);
    const borderX = parseFloat(cs.borderLeftWidth) + parseFloat(cs.borderRightWidth);
    return elem.offsetWidth - paddingX - borderX;
}

function contentHeight(elem) {
    const cs = getComputedStyle(elem);
    const paddingY = parseFloat(cs.paddingTop) + parseFloat(cs.paddingBottom);
    const borderY = parseFloat(cs.borderTopWidth) + parseFloat(cs.borderBottomWidth);
    return elem.offsetHeight - paddingY - borderY;
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
}

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

function addMoment(obj) {
    const res = Object.assign({}, obj);
    _.forEach(['updated_at', 'created_at', 'deleted_at'], (key) => {
        if (res[key] !== undefined) {
            res[`${key}_moment`] = moment(res[key], TimeFormats.SERVER_DATETIME);
        }
    });
    return res;
}

function createdAtFormat(createdAtString, now) {
    const createdAtMoment = moment.utc(createdAtString);

    if (now.diff(createdAtMoment, 'h', true) < 20) {
        return createdAtMoment.from(now);
    }

    createdAtMoment.tz(now.tz());
    if (now.diff(createdAtMoment, 'M', true) < 12) {
        return createdAtMoment.format('HH:mm D MMM');
    }

    return createdAtMoment.format('HH:mm DD.MM.YYYY');
}

function assignObjectReactively(target, source) {
    _.forEach(Object.entries(source), ([key, val]) => {
        Vue.set(target, key, val);
    });
}

function wordCase(caseOne, caseTwo, caseFive, cnt) {
    if (Math.floor((cnt % 100) / 10) !== 1) {
        if (cnt % 10 === 1) {
            return caseOne;
        }
        if (cnt % 10 >= 2 && cnt % 10 <= 4) {
            return caseTwo;
        }
    }
    return caseFive;
}

function separateNumberDigitsByTriples(cnt) {
    let res = '';
    let pow = 1;
    for (let i = 0; cnt >= pow || pow < 2; ++i) {
        if (i % 3 === 0 && pow > 0) {
            res = ` ${res}`;
        }
        res = `${Math.floor(cnt / pow) % 10}${res}`;
        pow *= 10;
    }
    return res;
}

/**
 * Возвращает единицу измерения с правильным окончанием
 *
 * @param {Number} num      Число
 * @param {Object} cases    Варианты слова {nom: 'час', gen: 'часа', plu: 'часов'}
 * @return {String}
 */
const units = (num, cases) => {
    num = Math.abs(num);
    if (num.toString().indexOf('.') > -1) {
        return cases.gen;
    }
    if (num % 10 === 1 && num % 100 !== 11) {
        return cases.nom;
    }
    if (num % 10 >= 2 && num % 10 <= 4 && (num % 100 < 10 || num % 100 >= 20)) {
        return cases.gen;
    }
    return cases.plu;
};

export {
    contentWidth,
    contentHeight,
    randomInteger,
    replaceAll,
    addMoment,
    createdAtFormat,
    assignObjectReactively,
    wordCase,
    separateNumberDigitsByTriples,
    units,
};
