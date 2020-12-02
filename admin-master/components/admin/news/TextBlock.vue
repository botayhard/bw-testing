<template>
    <div class="edit-article-text-block">
        <control-buttons class="control-block-buttons"
                         v-if="!isPermament"
                         :able-to-up="ableToUp"
                         :able-to-down="ableToDown"
                         :able-to-delete="ableToDelete"
                         @up="sendUp"
                         @down="sendDown"
                         @delete="sendDelete"/>
        <editor class="article-text-editor"
                :editor-type="CKEditorType.FULL"
                v-model="block.text"/>
    </div>
</template>

<script>
import { CKEditorType, ArticleBlockType } from '@/assets/js/utils/Enums';
import Editor from '@/components/admin/news/CKEditor';
import ControlButtons from './ControlBlockButtons';
import ControlMixin from './UpDownDeleteMixin';
import PermamentMixin from './PermamentBlockMixin';

export default {
    name: 'EditArticleTextBlock',
    mixins: [ControlMixin, PermamentMixin],
    props: {
        block: {
            type: Object,
            required: true,
            validate(block) {
                return block.id > 0 && block.type === ArticleBlockType.TEXT && typeof block.text === 'string';
            },
        },
    },
    data() {
        return {
            CKEditorType,
        };
    },
    methods: {},
    components: { Editor, ControlButtons },
};
</script>
<style lang="scss">

</style>
<style lang="scss" scoped>
    .edit-article-text-block {
        display: flex;
        flex-direction: column;
        .control-block-buttons {

        }
        .article-text-editor {

        }
    }
</style>
