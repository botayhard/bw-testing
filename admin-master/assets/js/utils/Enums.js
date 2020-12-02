// region CKEditor Enums

const CKEditorType = { MINIMAL: 'minimal', FULL: 'full', CUSTOM: 'custom' };
const CKEditorToolbarPredefined = {
    [CKEditorType.MINIMAL]: ['undo', 'redo'],
    [CKEditorType.FULL]: [
        'heading',
        'bold',
        'italic',
        'link',
        'bulletedList',
        'numberedList',
        'blockQuote',
        'undo',
        'redo',
    ],
};
const CKEditorPluginsPredefined = {
    [CKEditorType.MINIMAL]: ['Paragraph'],
    [CKEditorType.FULL]: ['Bold', 'Italic', 'BlockQuote', 'Heading', 'Link', 'List', 'Paragraph'],
};

// endregion

const ArticleBlockType = { TEXT: 'text', IMAGE: 'image' };

const TimeFormats = {
    SERVER_DATETIME: 'YYYY-MM-DD HH:mm:ss',
    SERVER_FULLDATE: 'YYYY-MM-DD',
    CLIENT_TIME: 'HH:mm',
    CLIENT_FULLTIME: 'HH:mm:ss',
    CLIENT_DATE: 'DD.MM',
    CLIENT_DATETIME: 'HH:mm DD.MM',
    CLIENT_FULLDATETIME: 'HH:mm DD.MM.YYYY',
    CLIENT_FULLDATEFULLTIME: 'HH:mm:ss DD.MM.YYYY',
    CLIENT_FULLDATE: 'DD.MM.YYYY',
};
const ContainersMainNames = { NEWS: 'NEWS' };

export {
    CKEditorType,
    CKEditorToolbarPredefined,
    CKEditorPluginsPredefined,
    ArticleBlockType,
    TimeFormats,
    ContainersMainNames,
};
