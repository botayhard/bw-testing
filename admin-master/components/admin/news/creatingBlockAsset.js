import { replaceAll } from '@/assets/js/utils/utils';
import { ArticleBlockType } from '@/assets/js/utils/Enums';

function createBlockWithContent(id, type, content) {
    switch (type) {
        case ArticleBlockType.TEXT:
            return {
                id,
                type: ArticleBlockType.TEXT,
                ...content,
            };
        case ArticleBlockType.IMAGE:
            return {
                id,
                type: ArticleBlockType.IMAGE,
                ...content,
            };
        default:
            throw new Error(`Invalid block type: ${type}`);
    }
}

function createImageBlock(blockId, { url, id }) {
    return createBlockWithContent(blockId, ArticleBlockType.IMAGE, { image: { url, id } });
}

function createTextBlock(blockId, { text }) {
    return createBlockWithContent(blockId, ArticleBlockType.TEXT, { text });
}

function emptyBlock(id, type) {
    switch (type) {
        case ArticleBlockType.TEXT:
            return createTextBlock(id, { text: 'Новый текст' });
        case ArticleBlockType.IMAGE:
            return createImageBlock(id, { url: '', id: '' });
        default:
            throw new Error(`Invalid block type: ${type}`);
    }
}

function copyBlock(block, newId) {
    if (newId === undefined) {
        newId = block.id;
    }
    switch (block.type) {
        case ArticleBlockType.TEXT:
            return createTextBlock(newId, { text: block.text });
        case ArticleBlockType.IMAGE:
            return createImageBlock(newId, { url: block.image.url, id: block.image.id });
        default:
            throw new Error(`Invalid block type: ${block.type}`);
    }
}

function defaultNewArticleData() {
    return {
        title: 'Заголовок статьи',
        subtitle: 'Подзаголовок статьи',
        previewImage: createImageBlock(0, ''),
        backgroundImage: createImageBlock(1, ''),
        publishAt: null,
        blocks: [
            emptyBlock(2, ArticleBlockType.TEXT),
        ],
    };
}

function convertArticleForEdit({ id, title, subtitle, preview_image: previewImage, blocks, publish_at: publishAt, background_image: backgroundImage }) {
    const copiedBlocks = _.map(blocks, (block, i) => copyBlock(block, i + 1));
    title = replaceAll(title, '\n', '\n\n');
    subtitle = replaceAll(subtitle || '', '\n', '\n\n');
    previewImage = createImageBlock(0, previewImage);
    backgroundImage = createImageBlock(1, backgroundImage);
    return {
        id,
        title,
        subtitle,
        previewImage,
        backgroundImage,
        blocks: copiedBlocks,
        publishAt: publishAt ? moment.utc(publishAt).local() : null,
        tags: [],
    };
}

export {
    convertArticleForEdit,
    defaultNewArticleData,
    emptyBlock,
};
