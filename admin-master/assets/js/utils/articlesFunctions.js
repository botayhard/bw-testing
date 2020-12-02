import { replaceAll } from './utils';
import { ArticleBlockType } from './Enums';

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

function createImageBlock(blockId, url) {
    return createBlockWithContent(blockId, ArticleBlockType.IMAGE, { image: url });
}

function createTextBlock(blockId, { text }) {
    return createBlockWithContent(blockId, ArticleBlockType.TEXT, { text });
}

function emptyBlock(id, type) {
    switch (type) {
        case ArticleBlockType.TEXT:
            return createTextBlock(id, { text: 'Новый текст' });
        case ArticleBlockType.IMAGE:
            return createImageBlock(id, '');
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
            return createImageBlock(newId, block.image);
        default:
            throw new Error(`Invalid block type: ${block.type}`);
    }
}

function defaultNewArticleData(articleType) {
    return {
        title: 'Заголовок статьи',
        subtitle: 'Подзаголовок статьи',
        uniqueName: '',
        previewImage: createImageBlock(0, ''),
        backgroundImage: createImageBlock(1, ''),
        type: articleType,
        publishAt: null,
        isMain: false,
        author: '',
        order: 0,
        blocks: [
            emptyBlock(2, ArticleBlockType.TEXT),
        ],
    };
}

function convertArticleForEdit({ id, title, unique_name: uniqueName, subtitle, preview_image: previewImage, blocks, publish_at: publishAt, background_image: backgroundImage, type, is_main: isMain, order, author }) {
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
        author,
        type,
        uniqueName,
        isMain,
        order,
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
