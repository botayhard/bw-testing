<template>
    <div class="edit-article-image-block">
        <control-buttons v-if="!isPermament"
                         :able-to-up="ableToUp"
                         :able-to-down="ableToDown"
                         :able-to-delete="ableToDelete"
                         class="control-block-buttons"
                         @up="sendUp"
                         @down="sendDown"
                         @delete="sendDelete"/>
        <div :class="{ loading: loadingImage }"
             class="article-image-uploader">
            <div v-if="loadingImage"
                 class="loader"><span/><span/><span/></div>
            <div v-else-if="imgUnavailable"
                 class="img-error">
                Изображение недоступно
            </div>
            <el-upload v-else-if="imgNotChosen"
                       :show-file-list="false"
                       :http-request="handleUpload"
                       class="img-not-chosen"
                       action="">
                <i class="el-icon-upload"/>
                <div class="el-upload__text">
                    Загрузите изображение
                </div>
            </el-upload>
            <img v-show="!loadingImage && !imgUnavailable"
                 v-if="!imgNotChosen"
                 :src="imageUrl"
                 class="article-image"
                 @error="imgError"
                 @load="imgSuccess">
            <button v-if="!imgNotChosen && !loadingImage"
                    class="remove-button button-clear"
                    @click="resetImage">
                Сбросить
            </button>
        </div>
    </div>
</template>

<script>
import { randomInteger } from '@/assets/js/utils/utils';
import { ArticleBlockType } from '@/assets/js/utils/Enums';
import ControlButtons from './ControlBlockButtons';
import ControlMixin from './UpDownDeleteMixin';
import PermamentMixin from './PermamentBlockMixin';

const randomImgs = ['https://s5o.ru/storage/simple/ru/edt/cb/a5/c9/03/rue8036d8ae83.jpg',
    'https://s5o.ru/storage/simple/ru/edt/e9/86/a8/3f/ruef3d2c8a3c1.jpg',
    'https://s5o.ru/storage/simple/ru/edt/d2/6e/33/e9/rue578dc7aebb.jpg',
    'https://cdn.tribuna.com/fetch/?url=http%3A%2F%2Fantidotte.com%2Fapp.php%2Fpic%2F9199.jpg',
    'https://cdn.tribuna.com/fetch/?url=http%3A%2F%2Fs.ura.news%2F760%2Fimages%2Fnews%2Fupload%2Fnews%2F307%2F177%2F1052307177%2F320743_Vizit_delegatsii_fifa_na_stroyploshtadku_Ekaterinburg_Areni_ex_Tsentralyniy_stadion_Ekaterinburg_futbolynoe_pole_tsentralyniy_stadion_futbolynie_vorota_ekaterinburg_arena_250x0_6240.4160.0.0.jpg',
    'https://s5o.ru/storage/simple/ru/edt/05/ca/c5/14/rueb0ac98f0bf.jpg'];

export default {
    name: 'EditArticleImageBlock',
    mixins: [ControlMixin, PermamentMixin],

    props: {
        block: {
            type: Object,
            required: true,
            validate(block) {
                return block.type === ArticleBlockType.IMAGE && typeof block.image === 'string';
            },
        },
    },
    data() {
        return {
            imgUnavailable: false,
            uploading: false,
            imageNotLoad: true,
        };
    },
    computed: {
        imageUrl() {
            if (this.block.image) return `/storage/${this.block.image}`;
            return undefined;
        },
        imgNotChosen() {
            return !this.imageUrl;
        },
        loadingImage() {
            return this.imageNotLoad && !this.imgNotChosen;
        },
    },
    methods: {
        imgError() {
            this.imgUnavailable = true;
            this.imageNotLoad = false;
        },
        imgSuccess() {
            this.imgUnavailable = false;
            this.imageNotLoad = false;
        },
        imgUrlChanged() {
            this.imgUnavailable = false;
            this.imageNotLoad = true;
        },
        handleUpload({ file }) {
            this.uploading = true;
            const data = new FormData();
            data.append('name', 'file');
            data.append('file', file);
            const config = {
                onUploadProgress(progressEvent) {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                },
            };
            this.$axios.post('/image', data, config).then(
                ({ data: { result } }) => {
                    this.uploading = false;
                    this.block.image = result;

                    // this.block.image.url = randomImgs[randomInteger(0, randomImgs.length - 1)];
                },
                ({ response: { data: err } }) => {
                    this.uploading = false;
                    return this.$catchError(err, {
                        reject: false,
                        defaultMsg: 'Загрузка не удалась',
                        customDict: {
                            validation_failed: { fields: { file: '' } },
                        },
                    });
                },
            );
        },
        resetImage() {
            this.block.image = '';
        },
    },
    watch: {
        imageUrl: {
            handler() {
                this.imgUrlChanged();
            },
            immediate: true,
        },
    },
    components: { ControlButtons },
};
</script>
<style lang="scss">
    .article-image-uploader {
        .img-not-chosen {
            > .el-upload {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
        }
    }
</style>
<style lang="scss" scoped>
    .article-image-uploader {
        position: relative;
        min-height: 30px;
        background-color: whitesmoke;
        color: black;
        &.loading {
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
        .remove-button {
            position: absolute;
            right: 10px;
            top: 10px;
            padding: 0.2em;
            border-radius: 0.2em;
            border: 1px solid black;
            background-color: white;
        }
        .article-image {
            width: 100%;
            display: block;
        }
        .img-error, .img-not-chosen {
            height: 5em;
            display: flex;
            align-items: center;
            text-align: center;
            justify-content: center;
        }
        .img-not-chosen {
        }
    }
</style>
