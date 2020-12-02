<template>
    <div class="control-block-buttons">
        <div class="control-button up"
             :class="{disabled: !ableToUp}"
             @click="sendUp">
            <i class="fa fa-chevron-up"/>
        </div>
        <div class="control-button down"
             :class="{disabled: !ableToDown}"
             @click="sendDown">
            <i class="fa fa-chevron-down"/>
        </div>
        <div class="control-button delete"
             :class="{disabled: !ableToDelete}"
             @click="sendDelete">
            <i class="fa fa-trash-o"/>
        </div>
    </div>
</template>

<script>
import ControlMixin from './UpDownDeleteMixin';

export default {
    name: 'ControlBlockButtons',
    mixins: [ControlMixin],
};
</script>
<style lang="scss" scoped>
    .control-block-buttons {
        display: flex;
        align-items: center;
        .control-button {
            cursor: pointer;
            padding: 0.2em;
            &.delete {
                margin-left: auto;
            }
            &.disabled {
                cursor: not-allowed;
                opacity: 0.5;
            }
        }
    }
</style>
