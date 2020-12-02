<template>
    <div class="article-container">
        <div class="title-container-article">
            <span class="title-article">{{ title }}</span>
            <div v-if="isMain"
                 class="main-article-text">Основная статья</div>
            <div>
                <span class="comment-title-article">Автор: </span>
                <span class="author-article">{{ author }}</span>
            </div>
        </div>
        <div class="date-container">
            <span>Статья создана: {{ created_at }}</span>
            <span>Просмотров: {{ views }}</span>
        </div>
        <div class="buttons-article-container">
            <el-button type="primary"
                       @click="moreInfo">Смотреть подробнее
            </el-button>
            <el-button type="danger"
                       @click="deleteArticle">Удалить статью</el-button>
        </div>
    </div>
</template>

<style lang="scss" scoped>
    .title-container-article {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        margin-bottom: 10px;
        .main-article-text {
            font-weight: bold;
        }
    }

    .article-container {
        border-style: solid;
        border-width: 1px;
        border-color: gray;
        padding: 5px;
        display: flex;
        flex-direction: column;
    }

    .date-container {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        font-size: 14px;
        opacity: 0.6;
    }

    .title-article {
        font-weight: bold;
        font-size: 18px;
        margin-right: 1em;
    }

    .author-article {
        font-size: 18px;
    }

    .comment-title-article {
        font-size: 18px;
        font-weight: bold;
    }

    .buttons-article-container {
        display: flex;
        flex-direction: row;
        justify-content: center;
        margin-top: 1em;
    }
</style>

<script>
export default {
    name: 'Article',
    props: {
        id: { type: Number, required: true },
        title: { type: String, required: true },
        author: { type: String, required: true },
        created_at: { type: String, required: true },
        updated_at: { type: String, required: true },
        views: { type: Number, required: true },
        isMain: { type: Boolean, required: true },
    },
    methods: {
        moreInfo() {
            this.$router.push({ path: `/portfolio/${this.id}`, params: { id: this.id } });
        },
        deleteArticle() {
            this.$confirm('Вы уверены, что хотите удалить эту статью?', 'Предупреждение', {
                confirmButtonText: 'Да',
                cancelButtonText: 'Нет',
                type: 'warning',
            }).then(() => {
                this.$api.deleteArticle({ article: this.id }).then(() => {
                    window.location.reload();
                });
                this.$message({
                    center: true,
                    showClose: true,
                    type: 'success',
                    message: 'Статья была успешно удалена',
                });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    center: true,
                    showClose: true,
                    message: 'Статья не была удалена',
                });
            });
        },
    },
};
</script>
