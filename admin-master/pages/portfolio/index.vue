<template>
    <div class="all-articles-container">
        <el-button type="success"
                   class="create-new-article-button"
                   @click="createNewArticle">Добавить новую статью</el-button>
        <article-container v-for="item in articles"
                           :author="item.author"
                           :id="item.id"
                           :title="item.title"
                           :created_at="item.created_at"
                           :updated_at="item.updated_at"
                           :views="item.views"
                           :is-main="item.isMain"
                           :key="item.id"
                           class="article-container"/>
        <el-pagination
            :total="total"
            :current-page="currentPage"
            layout="total, prev, pager, next"
            @current-change="currentPageChange"/>
    </div>
</template>

<script>
import ArticleContainer from '@/components/article';
import ArticleAdapter from '@/adapters/article';

export default {
    data() {
        return {
            articles: [],
            currentPage: 1,
            total: 0,
        };
    },
    methods: {
        currentPageChange(current) {
            this.loadArticles({ page: current });
        },
        async loadArticles({ page } = { page: 1 }) {
            const { result } = await this.$api.getAllArticles({ type: this.$route.query.type, page });
            this.articles = result.data.map(ArticleAdapter);
            this.currentPage = result.current_page;
            this.total = result.total;
        },
        createNewArticle() {
            this.$router.push({ path: '/portfolio/new'.concat(this.$route.query.type), params: { id: 'new'.concat(this.$route.query.type) } });
        },
    },
    mounted() {
        this.loadArticles();
    },
    components: {
        ArticleContainer,
    },
    watchQuery: ['type'],
};
</script>

<style lang="scss" scoped>
    .article-container {
        margin-top: 10px;
        width: 80%;
        margin-bottom: 10px;
    }

    .all-articles-container {
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }

    .create-new-article-button {
        margin-top: 1em;
    }
</style>
