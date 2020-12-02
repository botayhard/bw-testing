import { replaceAll } from '~/assets/js/utils/utils';
import { ArticleBlockType } from '~/assets/js/utils/Enums';

function prepareArticleForSave(article) {
    console.info(article.id);
    const id = article.id;
    const title = replaceAll(article.title, '\n\n', '\n');
    const subtitle = replaceAll(article.subtitle, '\n\n', '\n');
    const previewImage = article.previewImage.image;
    const backgroundImage = article.backgroundImage.image;
    const author = article.author;
    const uniqueName = article.uniqueName;
    const isMain = article.isMain ? true : false;
    const type = article.type;
    const metaId = article.metaId;
    const order = article.order;
    const blocks = _.map(article.blocks, (block) => {
        let preparedBlock = { ...block };
        delete preparedBlock.id;
        if (preparedBlock.type === ArticleBlockType.IMAGE) {
            preparedBlock = { type: ArticleBlockType.IMAGE, image: preparedBlock.image };
        }
        return preparedBlock;
    });
    const publishAt = article.publishAt ? article.publishAt.clone().utc().format('YYYY-MM-DD HH:mm:ss') : undefined;
    return {
        title, id, subtitle, preview_image: previewImage, author, blocks, publish_at: publishAt, type, background_image: backgroundImage, unique_name: uniqueName, meta_id: metaId, is_main: isMain, order,
    };
}

function missedParamsMiddleware(self, preparedArticle) {
    if (!preparedArticle.preview_image) {
        self.$toast.error('Не указано изображение для превью');
        return Promise.reject();
    }
    // if (!preparedArticle.background_image) {
    //     self.$toast.error('Не указано фоновое изображение');
    //     return Promise.reject();
    // }
    if (!preparedArticle.blocks || preparedArticle.blocks.length === 0) {
        self.$toast.error('Не добавлен контент статьи');
        return Promise.reject();
    }
    if (!preparedArticle.author) {
        self.$toast.error('Не указан автор статьи');
        return Promise.reject();
    }
    if ((!preparedArticle.order) && (preparedArticle.type === 'project')) {
        self.$toast.error('Не указан порядковый номер проекта');
        return Promise.reject();
    }
    if (_.some(preparedArticle.blocks, block => block.type === ArticleBlockType.IMAGE && !block.image)) {
        self.$toast.error('Не оставляйте пустых блоков для изображений');
        return Promise.reject();
    }
    return Promise.resolve();
}

const state = () => ({});
const getters = {};
const actions = {
    createNewArticle(store, article) {
        const preparedArticle = prepareArticleForSave(article);
        return missedParamsMiddleware(this, preparedArticle).then(() => this.$api.createArticle(preparedArticle));
    },
    editArticle(store, article) {
        const preparedArticle = prepareArticleForSave(article);
        return missedParamsMiddleware(this, preparedArticle).then(() => this.$api.updateArticle(preparedArticle));
    },
};

export default {
    state,
    getters,
    actions,
};
