<template>
    <div>
        <div class="zakhse-ck-editor-wrapper"
             :class="{mounting}">
            <div class="loader"><span/><span/><span/></div>
            <div class="ckeditor-area"
                 ref="editor"></div>
        </div>
    </div>
</template>

<script>
import { CKEditorType, CKEditorPluginsPredefined, CKEditorToolbarPredefined } from '~/assets/js/utils/Enums';

export default {
    name: 'CKEditor',
    props: {
        value: { type: String, default: '' },
        editorType: {
            type: String,
            default: CKEditorType.FULL,
            validator(val) {
                return Object.values(CKEditorType).some(type => type === val);
            },
        },
        customToolbar: {
            type: Array,
            default: () => ([]),
            validator(val) {
                return val.every(item => CKEditorToolbarPredefined[CKEditorType.FULL].include(item));
            },
        },
        customPlugins: {
            type: Array,
            default: () => ([]),
            validator(val) {
                return val.every(item => CKEditorPluginsPredefined[CKEditorType.FULL].includes(item));
            },
        },
    },
    data() {
        return {
            instance: null,
            mounting: true,
        };
    },
    computed: {
        toolbar() {
            if (this.editorType === CKEditorType.CUSTOM) {
                return this.customToolbar;
            }
            return CKEditorToolbarPredefined[this.editorType];
        },
        pluginsToRemove() {
            if (this.editorType === CKEditorType.FULL) {
                return [];
            }
            let neededPlugins;
            if (this.editorType === CKEditorType.CUSTOM) {
                neededPlugins = this.customPlugins;
            } else {
                neededPlugins = CKEditorPluginsPredefined[this.editorType];
            }
            return CKEditorPluginsPredefined[CKEditorType.FULL].filter(plugin => !neededPlugins.includes(plugin));
        },
        watchedEditorConfig() {
            return {
                toolbar: this.toolbar,
                pluginsToRemove: this.pluginsToRemove,
            };
        },
    },
    methods: {
        emitValueUpdate(value) {
            this.$emit('input', value);
        },
        setValue(value) {
            this.instance.setData(value);
        },
        destroyEditor() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        },
        recreateEditor() {
            this.destroyEditor();
            this.createEditor();
        },
        createEditor() {
            if (this.instance) {
                return Promise.resolve();
            }
            this.mounting = true;
            return CKInlineEditor
                .create(this.$refs.editor, {
                    toolbar: this.toolbar,
                    removePlugins: this.pluginsToRemove,
                })
                .then((editor) => {
                    this.instance = editor;
                    this.setValue(this.value);
                    this.emitValueUpdate(editor.getData());
                    editor.model.document.on('change:data', (/* evt, data */) => {
                        this.emitValueUpdate(editor.getData());
                    });
                    this.mounting = false;
                });
        },
    },
    mounted() {
        this.createEditor();
    },
    beforeDestroy() {
    },
    destroyed() {
        setTimeout(() => {
            this.destroyEditor();
        }, 3000);
    },
    watch: {
        value() {
            const html = this.instance.getData();
            if (html !== this.value) {
                this.setValue(this.value);
            }
        },
        watchedEditorConfig() {
            this.recreateEditor();
        },
    },
};
</script>
<style lang="scss">
    .zakhse-ck-editor-wrapper {
        .ckeditor-area {
            p + p {
                margin-top: 0.7em;
            }
            p {
                margin-top: 0;
                margin-bottom: 0;
            }
        }
    }
</style>
<style lang="scss" scoped>
    .zakhse-ck-editor-wrapper {
        position: relative;
        min-height: 2em;
        .loader {
            display: none;
        }
        &.mounting {
            &::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background-color: whitesmoke;
            }
            .loader {
                display: block;
                $height: 5px;
                height: $height;
                border-radius: 100%;
                position: absolute;
                left: calc(50% - #{$height / 2});
                top: calc(50% - #{$height / 2});
                line-height: 0;
                span {
                    @keyframes opacitychange {
                        0%, 100% {
                            opacity: 0;
                        }

                        60% {
                            opacity: 1;
                        }
                    }
                    display: inline-block;
                    width: $height;
                    height: $height;
                    border-radius: 100%;
                    background-color: black;
                    margin-left: $height / 2;
                    margin-right: $height / 2;
                    opacity: 0;
                    &:nth-child(1) {
                        animation: opacitychange 1s ease-in-out infinite;
                    }
                    &:nth-child(2) {
                        animation: opacitychange 1s ease-in-out 0.33s infinite;
                    }
                    &:nth-child(3) {
                        animation: opacitychange 1s ease-in-out 0.66s infinite;
                    }
                }
            }
        }
    }

    .ckeditor-area {
        width: 100%;
        box-sizing: border-box;
        background-color: white;
        color: black;
        padding: 0.4em;
    }
</style>
