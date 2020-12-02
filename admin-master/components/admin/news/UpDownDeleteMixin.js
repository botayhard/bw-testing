const mixin = {
    props: {
        ableToUp: { type: Boolean, default: true },
        ableToDown: { type: Boolean, default: true },
        ableToDelete: { type: Boolean, default: true },
    },
    methods: {
        sendUp() {
            this.$emit('up');
        },
        sendDown() {
            this.$emit('down');
        },
        sendDelete() {
            this.$emit('delete');
        },
    },
};

export default mixin;
