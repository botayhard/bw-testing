module.exports = {
    root: true,
    env: {
        browser: true,
    },
    parserOptions: {
        parser: 'babel-eslint',
        sourceType: 'module',
    },
    extends: [
        // https://github.com/vuejs/eslint-plugin-vue#priority-a-essential-error-prevention
        // consider switching to `plugin:vue/strongly-recommended` or `plugin:vue/recommended` for stricter rules.
        'plugin:vue/recommended',
        'airbnb-base',
    ],
    // required to lint *.vue files
    plugins: [
        'vue',
    ],
    // add your custom rules here
    rules: {
        'import/extensions': ['error', 'always', {
            js: 'never',
            vue: 'never',
        }],
        'indent': [
            'error',
            4, {
                'SwitchCase': 1,
            },
        ],
        'max-len': [
            'error',
            {
                'code': 120,
                'tabWidth': 4,
                'ignoreComments': true,
                'ignoreUrls': true,
                'ignoreStrings': true,
                'ignoreTemplateLiterals': true,
                'ignoreRegExpLiterals': true,
            },
        ],
        'no-restricted-syntax': 'off',
        'no-console': 'off',
        'comma-dangle': [2, 'always-multiline'],
        'no-param-reassign': 'off',
        'no-plusplus': 'off',
        'no-shadow': 'off',
        'import/no-extraneous-dependencies': 'off',
        'prefer-destructuring': 'off',
        'object-curly-newline': ['error', { 'multiline': true, 'consistent': true }],
        'no-underscore-dangle': 'off',
        'vue/html-indent': ['error', 4, {
            'attribute': 1,
            'closeBracket': 0,
            'alignAttributesVertically': true,
            'ignores': [],
        }],
        'vue/max-attributes-per-line': [2, {
            'singleline': 1,
            'multiline': {
                'max': 1,
                'allowFirstLine': true,
            },
        }],
        'vue/order-in-components': ['error', {
            'order': [
                'el',
                'name',
                ['template', 'render'],
                'parent',
                'functional',
                ['delimiters', 'comments'],
                'extends',
                'mixins',
                'inheritAttrs',
                'model',
                ['props', 'propsData'],
                'data',
                'computed',
                'methods',
                'LIFECYCLE_HOOKS',
                'watch',
                'renderError',
                ['components', 'directives', 'filters'],
            ],
        }],
    },
    settings: {
        'import/resolver': {
            webpack: { config: 'webpack-eslint-config.js' },
        },
    },
    globals: {
        moment: false,
        CKInlineEditor: false,
        _: false,
    },
};
