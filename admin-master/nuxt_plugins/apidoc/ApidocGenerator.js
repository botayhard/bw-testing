const watch = require('watch');
const apidoc = require('apidoc');
const fs = require('fs');

function generateApidoc(pathToApidoc, destFileName) {
    const apidocData = apidoc.createDoc({
        parse: true,
        src: pathToApidoc,
        markdown: false,
        colorize: false,
        silent: true,
    }).data;

    fs.writeFile(destFileName, `export default ${apidocData}`, err => {
        if (err) {
        }
    });
}

module.exports = class ApidocWatcher {
    constructor(apiDirectoryPath, apiFilename) {
        if (typeof apiDirectoryPath !== 'string' || apiDirectoryPath.length === 0) {
            throw new Error('api_dirrectory_path must be non empty string');
        }

        this.apiPath = apiDirectoryPath;
        this.apiFilename = apiFilename;
    }

    apply() {
        if (process.env.NODE_ENV === 'production') {
        } else {
            watch.watchTree(this.apiPath, { ignoreDotFiles: true }, (f, curr, prev) => {
                if (typeof f === 'object' && prev === null && curr === null) {
                    // Finished walking the tree
                    return;
                }
                generateApidoc(this.apiPath, this.apiFilename);
            });
        }
        generateApidoc(this.apiPath, this.apiFilename);
    }
};
