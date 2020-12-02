<template>
    <div class="center-form">
        <div class="filter-block">
            <div class="radio-buttons"
                 @change="handleRadioButtonsChange()">
                <el-radio-group v-model="currentModerated">
                    <el-radio-button label="Модерация"/>
                    <el-radio-button label="Проверенные"/>
                </el-radio-group>
            </div>
            <el-select v-model="value"
                       placeholder="Select"
                       @change="handleSelectChange(value)">
                <el-option v-for="item in options"
                           :key="item.value"
                           :label="item.label"
                           :value="item.value"/>
            </el-select>
        </div>
        <div class="comments">
            <comment v-for="item in comments"
                     :text="item.text"
                     :published_at="item.published_at"
                     :project="item.project"
                     :author="item.author"
                     :id="item.id"
                     :key="item.id"
                     :moderated="item.moderated"
                     class="comment"
                     @changeData="loadData"/>
        </div>
        <div class="block">
            <el-pagination :total="commentsCount"
                           :current-page="currentPage"
                           :page-size="15"
                           layout="prev, pager, next"
                           @current-change="handleCurrentPage"/>
        </div>
    </div>
</template>

<script>
import Comment from '@/components/comment';
import CommentAdapter from '../adapters/Comment';

function getData(api, curPage, project, moder) {
    return api.getComments({
        page: curPage,
        moderated: moder,
        article_id: project,
    }).then(({ result }) => result);
}

export default {
    async asyncData({ app }) {
        const result = await getData(app.$api, 1, null, 0);
        const articles = await app.$api.getAllArticles({ type: 'project' });
        return {
            comments: result.comments.map(el => CommentAdapter(el)),
            commentsCount: result.all,
            options: articles.result.map((el) => {
                return {
                    value: el.id,
                    label: el.title,
                };
            }).concat([
                {
                    value: null,
                    label: 'all',
                },
            ]),
        };
    },
    data() {
        return {
            moderatedVisible: false,
            currentModerated: 'Модерация',
            currentPage: 1,
            currentSelected: null,
            value: 'all',
            commentsCount: 0,
            comments: [],
            options: [],
        };
    },
    methods: {
        handleRadioButtonsChange() {
            this.moderatedVisible = !this.moderatedVisible;
        },
        handleSelectChange(val) {
            this.currentSelected = val;
        },
        handleCurrentPage(newVal) {
            getData(this.$api, newVal, this.currentSelected, this.moderatedVisible ? 1 : 0).then((result) => {
                this.comments = result.comments.map(el => CommentAdapter(el));
                this.commentsCount = result.all;
            });
        },
        loadData() {
            getData(this.$api, this.currentPage, this.currentSelected, this.moderatedVisible ? 1 : 0).then((result) => {
                this.comments = result.comments.map(el => CommentAdapter(el));
                this.commentsCount = result.all;
            });
        },
    },
    watch: {
        moderatedVisible: {
            handler(newVal) {
                getData(this.$api, this.currentPage, this.currentSelected, newVal ? 1 : 0).then((result) => {
                    this.comments = result.comments.map(el => CommentAdapter(el));
                    this.commentsCount = result.all;
                });
            },
        },
        currentSelected: {
            handler(newVal) {
                getData(this.$api, this.currentPage, newVal, this.moderatedVisible ? 1 : 0).then((result) => {
                    this.comments = result.comments.map(el => CommentAdapter(el));
                    this.commentsCount = result.all;
                });
            },
        },
    },
    components: {
        Comment,
    },
};
</script>

<style lang="scss" scoped>
    .comments {
        display: flex;
        width: 90%;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .comment {
        width: 100%;
        margin-top: 10px;
        border-style: solid;
        border-color: gray;
        border-width: 1px;
        margin-bottom: 20px;
        padding: 10px;
    }

    .radio-buttons {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .filter-block {
        margin-bottom: 10px;
    }

    .center-form {
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
    }
</style>
