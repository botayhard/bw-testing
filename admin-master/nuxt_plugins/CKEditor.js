import InlineEditor from '@ckeditor/ckeditor5-build-inline';
import GFMDataProcessor from '@ckeditor/ckeditor5-markdown-gfm/src/gfmdataprocessor';

function Markdown(editor) {
    editor.data.processor = new GFMDataProcessor();
}

const pluginsToRemove = [
    'CKFinderUploadAdapter',
    'Autoformat',
    'EasyImage',
    'Image',
    'ImageCaption',
    'ImageStyle',
    'ImageToolbar',
    'ImageUpload',
];
const neededPlugins = InlineEditor.builtinPlugins.filter(plugin => !pluginsToRemove.includes(plugin.pluginName));

InlineEditor.builtinPlugins = [Markdown, ...neededPlugins];
InlineEditor.defaultConfig.toolbar = [
    'headings',
    'bold',
    'italic',
    'link',
    'bulletedList',
    'numberedList',
    'blockQuote',
    'undo',
    'redo',
];

global.CKInlineEditor = InlineEditor;
