<template>
    <div id="admin-news-article-page">
        <div class="edit-zone">
            <div class="editor-block">
                <div class="editor-label">
                    Превью
                </div>
                <image-block :block="editingData.previewImage"
                             is-permament/>
            </div>
<!--            <div class="editor-block">-->
<!--                <div class="editor-label">-->
<!--                    Задний фон-->
<!--                </div>-->
<!--                <image-block :block="editingData.backgroundImage"-->
<!--                             is-permament/>-->
<!--            </div>-->
            <div class="editor-block border-block">
                <div class="editor-label">
                    Заголовок
                </div>
                <editor :editor-type="CKEditorType.MINIMAL"
                        v-model="editingData.title"
                        class="editor"/>
            </div>
            <div class="editor-block border-block">
                <div class="editor-label">
                    Подзаголовок
                </div>
                <editor :editor-type="CKEditorType.MINIMAL"
                        v-model="editingData.subtitle"
                        class="editor"/>
            </div>
            <el-alert v-if="isInvalidUniqueName"
                      title="Использование русских символов в поле уникального имени может привести к преобразованию символов в транслит"
                      type="error"
                      class="alert-style"/>
            <div class="editor-block border-block">
                <div class="editor-label">
                    Url
                </div>
                <editor :editor-type="CKEditorType.MINIMAL"
                        v-model="editingData.uniqueName"
                        class="editor"/>
            </div>
            <transition-group name="transition-list"
                              tag="div"
                              class="transition-list added-blocks">
                <div v-for="(block, i) in editingData.blocks"
                     :key="block.id"
                     class="transition-list-item editor-block">
                    <template v-if="block.type === ArticleBlockType.TEXT">
                        <text-block :block="block"
                                    :able-to-up="i !== 0"
                                    class="text-block border-block"
                                    :able-to-down="i !== editingData.blocks.length - 1"
                                    @up="raiseBlock(i)"
                                    @down="lowerBlock(i)"
                                    @delete="deleteBlock(i)"/>
                    </template>
                    <template v-else-if="block.type === ArticleBlockType.IMAGE">
                        <image-block :block="block"
                                     :able-to-up="i !== 0"
                                     class="image-block"
                                     :able-to-down="i !== editingData.blocks.length - 1"
                                     @up="raiseBlock(i)"
                                     @down="lowerBlock(i)"
                                     @delete="deleteBlock(i)"/>
                    </template>
                </div>
                <div key="add-new-block"
                     class="transition-list-item add-new-container">
                    <div class="add-new-button"
                         @click="addNewBlock(ArticleBlockType.TEXT)">
                        <span class="add-sign">
                            <i class="fa fa-plus"/>
                        </span>
                        <span class="add-text">
                            Текст
                        </span>
                    </div>
                    <div class="add-new-button"
                         @click="addNewBlock(ArticleBlockType.IMAGE)">
                        <span class="add-sign">
                            <i class="fa fa-plus"/>
                        </span>
                        <span class="add-text">Фото</span>
                    </div>
                </div>
                <!--<div class="transition-list-item piblish-at-container"-->
                <!--key="piblish-at-block">-->
                <!--<el-date-picker v-model="articlePublishAt"-->
                <!--type="datetime"-->
                <!--format="yyyy-MM-dd HH:mm"-->
                <!--value-format="yyyy-MM-dd HH:mm"-->
                <!--:picker-options="{ firstDayOfWeek: 1 }"-->
                <!--popper-class="admin-news-publish-at-time-picker"-->
                <!--placeholder="Время публикации"/>-->
                <!--</div>-->
                <div key="tag-selecter"
                     class="selecter-container transition-list-item">
                    <el-select v-model="value"
                               multiple
                               value-key="id"
                               placeholder="Введите название тега">
                        <el-option v-for="item in options"
                                   :key="item.id"
                                   :label="item.text"
                                   :value="item.id"/>
                    </el-select>
                </div>
                <div key="SEO-inputs"
                     class="transition-list-item SEO-container">
                    <el-input v-model="SEOKeywords" 
class="SEOStyle" 
placeholder="keywords"/>
                    <el-input v-model="SEOTitle" 
class="SEOStyle" 
placeholder="title"/>
                    <el-input v-model="SEODescription" 
class="SEOStyle" 
placeholder="description" type="textarea"/>
                </div>
                <div key="author-container" 
class="portfolio-author-container">
                    <div class="portfolio-author-text">Введите имя автора данной статьи</div>
                    <el-select v-model="editingData.author"
                               placeholder="Select">
                        <el-option
                            v-for="item in users"
                            :key="item.id"
                            :label="item.firstname + ' ' + item.lastname"
                            :value="item.firstname + ' ' + item.lastname"/>
                    </el-select>
                </div>
                <div key="order-input" 
v-if="editingData.type === 'project'" class="order-project-container">
                    <div class="project-order-input-text">Введите порядковый номер проекта</div>
                    <input v-model="editingData.order" 
type="number">
                </div>
<!--                <div key="main-selecter" -->
<!--v-if="editingData.type === 'article'" class="main-selecter-container">-->
<!--                    <div class="portfolio-selecter-label">Сделать ли эту статью основной?</div>-->
<!--                    <input v-model="editingData.isMain" -->
<!--type="checkbox">-->
<!--                </div>-->
                <div key="save-block"
                     class="transition-list-item save-container">
                    <div :disabled="isInvalidUniqueName"
                         class="save-button"
                         @click="saveArticle">
                        <span class="save-sign">
                            <i class="fa fa-floppy-o"/>
                        </span>
                        <span class="save-text">
                            <template v-if="mode === ArticleMode.NEW">
                                Создать
                            </template>
                            <template v-else>
                                Применить
                            </template>
                        </span>
                    </div>
                </div>
            </transition-group>
        </div>
    </div>
</template>
<script>
import { CKEditorType, ArticleBlockType } from '@/assets/js/utils/Enums';
import { defaultNewArticleData, emptyBlock, convertArticleForEdit } from '~/assets/js/utils/articlesFunctions';
import Editor from '@/components/admin/news/CKEditor';
import TextBlock from '@/components/admin/news/TextBlock';
import ImageBlock from '@/components/admin/news/ImageBlock';

const ArticleMode = { NEW: 'new', EDIT: 'edit' };

function swapBlocks(blocks, i1, i2) {
    if (i1 < 0 || i2 < 0 || i1 >= blocks.length || i2 >= blocks.length) {
        throw new Error(`Cannot be swapped. Array length: ${blocks.length}, 1st index: ${i1}, 2nd index: ${i2}`);
    }
    const el1 = blocks[i1];
    const el2 = blocks[i2];
    blocks.splice(i1, 1, el2);
    blocks.splice(i2, 1, el1);
}

function findMaxIdOfBlock(blocks) {
    if (!blocks || blocks.length === 0) {
        return 0;
    }
    return Math.max(..._.map(blocks, ({ id }) => id));
}


export default {
    name: 'AdminNewsArticlePage',
    props: {},
    data() {
        return {
            ArticleMode,
            CKEditorType,
            ArticleBlockType,
            mode: '',
            loadedArticle: null,
            editingData: {},
            SEOTitle: '',
            SEOKeywords: '',
            SEODescription: '',
            value: [],
            options: [],
        };
    },
    computed: {
        // articlePublishAt: {
        //     get() {
        //         return this.editingData.publishAt && moment.isMoment(this.editingData.publishAt)
        //             ? this.editingData.publishAt.format('YYYY-MM-DD HH:mm') : '';
        //     },
        //     set(val) {
        //         this.editingData.publishAt = val ? moment(val) : null;
        //     },
        // },
        isInvalidUniqueName() {
            return this.validateRussianSymbols(this.editingData.uniqueName);
        },
    },
    methods: {
        validateErrorMessages(err) {
            if (err.reason === 'this user did not found') {
                this.$toast.error('Пользователя с такими данными не существует');
                return;
            }
            if (err.reason === 'this unique_name is already used') {
                this.$toast.error('Статья с таким url уже существует');
                return;
            }
            if (JSON.parse(err.message).reason_extra.fails_reason.title) {
                this.$toast.error('Статья с таким названием уже существует');
            }
            if (JSON.parse(err.message).reason_extra.fails_reason.unique_name) {
                this.$toast.error('Статья с таким url уже существует');
            }
            if (JSON.parse(err.message).reason_extra.fails_reason.order) {
                this.$toast.error('Проект с таким порядковым номером уже существует');
            }
        },
        addNewBlock(type) {
            const newId = findMaxIdOfBlock(this.editingData.blocks) + 1;
            this.editingData.blocks.push(emptyBlock(newId, type));
        },
        raiseBlock(blockIndex) {
            if (blockIndex > 0) {
                swapBlocks(this.editingData.blocks, blockIndex, blockIndex - 1);
            }
        },
        lowerBlock(blockIndex) {
            const lastIndex = this.editingData.blocks.length - 1;
            if (blockIndex < lastIndex) {
                swapBlocks(this.editingData.blocks, blockIndex, blockIndex + 1);
            }
        },
        deleteBlock(blockIndex) {
            this.editingData.blocks.splice(blockIndex, 1);
        },
        saveArticle() {
            this.$api.createMeta({
                title: this.SEOTitle,
                keywords: this.SEOKeywords,
                description: this.SEODescription,
            }).then(({ result }) => {
                this.editingData.metaId = result.id;
                switch (this.mode) {
                    case ArticleMode.NEW:
                        return this.$store.dispatch('createNewArticle', this.editingData).then(
                            ({ result }) => {
                                console.log(this.pickedTags);
                                this.$api.addTagToArticle({ article: result.id, tags: this.value }).then(() => {
                                    this.$router.push({ path: '/portfolio', query: { type: this.editingData.type } });
                                });
                            },
                            (err) => {
                                this.validateErrorMessages(err);
                            },
                        );
                    case ArticleMode.EDIT:
                        return this.$store.dispatch('editArticle', this.editingData).then(
                            () => {
                                this.$api.deleteAllTags({ article: this.editingData.id }).then(() => {
                                    this.$api.addTagToArticle({ article: this.editingData.id, tags: this.value }).then(() => {
                                        this.$router.push({ path: '/portfolio', query: { type: this.editingData.type } });
                                    });
                                });
                            },
                            (err) => {
                                this.validateErrorMessages(err);
                            },
                        );
                    default:
                        return Promise.resolve();
                }
            });
        },
        validateRussianSymbols(text) {
            for (let i = 0; i < text.length; ++i) {
                if (((text[i] >= 'а') && (text[i] <= 'я')) || (text[i] === 'ё')) return true;
            }
            return false;
        },
    },
    mounted() {
        this.value = this.listTags.map(item => item.id);
    },
    created() {
        if (!process.server) {
            if (this.editingData.publishAt) {
                this.editingData.publishAt = moment(this.editingData.publishAt);
            }
        }
    },
    async asyncData({ app, route }) {
        const loadAllTags = await app.$api.getAllTags();
        const loadUsers = await app.$api.getAllUsers();
        console.info('test', loadAllTags.result);
        if (route.params.id.substring(0, 3) === 'new') {
            return {
                users: loadUsers.result,
                mode: ArticleMode.NEW,
                SEOTitle: '',
                SEODescription: '',
                options: loadAllTags.result,
                listTags: [],
                SEOKeywords: '',
                editingData: defaultNewArticleData(route.params.id.substring(3)),
            };
        }
        const { result } = await app.$api.getArticle({ article: route.params.id });
        const loadAllArticleTags = await app.$api.getArticleTag({ article: route.params.id });
        let meta;
        if (result.meta_id === null) {
            meta = undefined;
        } else meta = await app.$api.getMeta({ metatag: result.meta_id });
        return {
            mode: ArticleMode.EDIT,
            users: loadUsers.result,
            loadedArticle: result,
            listTags: loadAllArticleTags.result,
            options: loadAllTags.result,
            editingData: convertArticleForEdit(result),
            SEOTitle: meta ? meta.result.title : '',
            SEOKeywords: meta ? meta.result.keywords : '',
            SEODescription: meta ? meta.result.description : '',
        };
    },
    components: { Editor, TextBlock, ImageBlock },
};
</script>
<style lang="scss">
    .admin-news-publish-at-time-picker {
        font-family: ProximaNova, sans-serif;
    }
</style>
<style lang="scss" scoped>
    .edit-zone {
        margin: 10px;
        .editor-block {
            margin-bottom: 10px;
            .editor-label {
                margin-bottom: 3px;
            }
            .editor {
            }
        }
        .add-new-container {
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            .add-new-button {
                cursor: pointer;
                padding: 0.2em 0.3em;
                background-color: #8945fc;
                display: flex;
                align-items: center;
                font-size: 1.3em;
                line-height: 0;
                .add-sign {
                    /*font-size: 1.5em;*/
                }
                .add-text {
                    margin-left: 0.5ch;
                }
            }
        }
        .piblish-at-container {
            margin-top: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .save-container {
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            .save-button {
                cursor: pointer;
                padding: 0.2em 0.3em;
                background-color: forestgreen;
                display: flex;
                align-items: center;
                font-size: 1.3em;
                line-height: 0;
                .save-sign {

                }
                .save-text {
                    margin-left: 0.5ch;
                }
            }
        }
        .transition-list {
            &-item {
                transition: all 0.5s;
            }
            &-leave-active {
                position: absolute;
                width: 100%;
            }
            &-enter, &-leave-to {
                opacity: 0;
                transform: translateX(50px);
            }
        }
    }
    .alert-style {
        margin-bottom: 10px;
    }
    .border-block {
        border-color: gray;
        border-width: 1px;
        border-style: solid;
        padding: 1em;
    }
    .SEO-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        .SEOStyle {
            margin-top: 10px;
            margin-bottom: 10px;
            outline: none;
            width: 50%;
        }
    }
    .selecter-container {
        display: flex;
        justify-content: center;
    }
    .main-selecter-container {
        display: flex;
        justify-content: center;
        .main-selecter-label {
            margin-right: 20px;
        }
    }
    .order-project-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 10px;
        .project-order-input-text {
            margin-bottom: 10px;
        }
    }
    .portfolio-author-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 10px;
        .portfolio-author-text {
            margin-bottom: 10px;
        }
    }
</style>
