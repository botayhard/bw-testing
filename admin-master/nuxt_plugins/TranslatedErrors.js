import Vue from 'vue';

const $errorMap = {
    required: 'Обязательное поле',
    must_be_an_email: 'Требуется адрес электронной почты',
    must_be_a_unique: 'Уже существует',
};


Vue.use({
    install(Vue) {
        if (Vue.prototype.hasOwnProperty('$errorMap'))
            return;
        Object.defineProperty(Vue.prototype, '$errorMap', {
            get() {
                return $errorMap;
            },
        });
    },
});
