const ApidocGenerator = require('./nuxt_plugins/apidoc/ApidocGenerator.js');
const config = require('dotenv').config({ path: '.env' }).parsed;
const webpack = require('webpack');
const path = require('path');

const proxyfiedUrls = [
    process.env.API_URL,
    process.env.API_URL.replace(/\/api\/?$/, '/storage'),
];

const momentGlobalPath = path.join(__dirname, 'nuxt_plugins/moment/global.js');
const assetsPath = path.join(__dirname, 'assets');


module.exports = {
    css: [
        'element-ui/lib/theme-chalk/index.css',
    ],
    env: config,
    build: {
        plugins: [
            new ApidocGenerator('./api', './api/.api_result.js'),
            new webpack.ProvidePlugin({
                moment: ['moment-global', 'default'],
                _: 'lodash',
            }),
        ],
        extend(config) {
            config.resolve.alias.moment$ = momentGlobalPath;
            config.resolve.alias['moment-global'] = momentGlobalPath;
            // It's helpful for using assets in url() in css styles for resolving them in WebStorm (not for html templates)
            config.resolve.alias['/assets'] = assetsPath;
        },
    },
    head: {
        htmlAttrs: {
            prefix: 'og: http://ogp.me/ns#',
        },
        title: process.env.SITE_TITLE,
        bodyAttrs: { class: 'body-attr' },
        meta: [
            { charset: 'utf-8' },
            {
                name: 'viewport',
                content: 'width=device-width, initial-scale=1',
            },
            {
                hid: 'description',
                name: 'description',
                content: process.env.SITE_DESCRIPTION,
            },
            {
                rel: 'favicon',
                href: 'favicon.ico',
            },
            {
                hid: 'open-graph-type',
                property: 'og:type',
                content: 'website',
            },
            {
                hid: 'open-graph-title',
                property: 'og:title',
                content: process.env.SITE_TITLE,
            },
            {
                hid: 'open-graph-url',
                property: 'og:url',
                content: process.env.HOST_NAME,
            },
            {
                hid: 'open-graph-description',
                property: 'og:description',
                content: process.env.SITE_DESCRIPTION,
            },
            {
                hid: 'open-graph-site_name',
                property: 'og:site_name',
                content: process.env.SITE_TITLE,
            },
        ],
        link: [
        ],
    },
    router: {
        base: '/admin/',
    },
    modules: [
        '@nuxtjs/axios',
        '@nuxtjs/proxy',
        'nuxt-device-detect',
        '@nuxtjs/font-awesome',
        '@nuxtjs/toast',
    ],
    proxy: process.env.NUXT_PROXY_ENABLED === 'true'
        ? proxyfiedUrls : [],
    plugins: [
        { src: '@/nuxt_plugins/NuxtClientInit.js', ssr: false },
        { src: '@/nuxt_plugins/CKEditor.js', ssr: false },
        { src: '@/nuxt_plugins/moment/Plugin.js' },
        { src: '@/nuxt_plugins/TranslatedErrors.js' },
        { src: '@/nuxt_plugins/apidoc/ApiPlugin.js' },
        { src: '@/nuxt_plugins/ElementUI.js' },
    ],
};

