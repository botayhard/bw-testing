<template>
    <div>
        <div class="title-container">
            <div class="text-title-container">
                <div>
                    <span class="comment-project">{{ project }}</span>
                    <span>: </span>
                    <span class="comment-name">{{ author }}</span>
                </div>
                <span class="comment-date">{{ published_at }}</span>
            </div>
            <div class="buttons-title-container">
                <el-button v-if="moderated"
                           type="success"
                           icon="el-icon-check"
                           circle
                           class="confirm-button"
                           @click="confirmComment"></el-button>
                <el-button type="danger"
                           icon="el-icon-delete"
                           class="delete-button"
                           circle
                           @click="deleteComment"></el-button>
            </div>
        </div>
        <span class="comment-content">{{ text }}</span>
    </div>
</template>

<style lang="scss" scoped>

    .comment-date {
        font-size: 14px;
        opacity: 0.5;
    }

    .text-title-container {
        display: flex;
        flex-direction: column;
        flex-basis: 50%;
    }
    .comment-project {
        font-size: 18px;
        flex-basis: 50%;
        font-weight: bold;
    }
    .comment-name {
        font-size: 18px;
    }
    .title-container {
        display: flex;
        border-bottom-style: solid;
        justify-content: space-between;
        border-width: 1px;
        border-color: gray;
        margin-bottom: 10px;
    }

    .text {
        font-size: 14px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .delete-button {
        width: 40px;
        height: 40px;
        margin-left: 10px;
        margin-bottom: 5px;
    }
    .buttons-title-container {
        display: flex;
        flex-direction: row;
    }
    .confirm-button {
        width: 40px;
        height: 40px;
        margin-bottom: 5px;
    }
</style>

<script>
export default {
    name: 'Comment',
    props: {
        project: { type: String, required: true },
        text: { type: String, required: true },
        published_at: { type: String, required: true },
        author: { type: String, required: true },
        id: { type: Number, required: true },
        moderated: { type: Boolean, required: true },
    },
    methods: {
        deleteComment() {
            this.$confirm('Вы уверены, что хотите удалить комментарий?', 'Предупреждение', {
                confirmButtonText: 'Да',
                cancelButtonText: 'Нет',
                type: 'warning',
            }).then(() => {
                this.$api.deleteComment({ comment: this.id }).then(() => {
                    this.$emit('changeData');
                });
                this.$message({
                    center: true,
                    showClose: true,
                    type: 'success',
                    message: 'Комментарий был успешно удален',
                });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    center: true,
                    showClose: true,
                    message: 'Комментарий не был удален',
                });
            });
        },
        confirmComment() {
            this.$confirm('Вы уверены, что хотите подвердить комментарий?', 'Предупреждение', {
                confirmButtonText: 'Да',
                cancelButtonText: 'Нет',
                type: 'warning',
            }).then(() => {
                this.$api.moderateComment({ comment: this.id, moderated: 1 }).then(() => {
                    this.$emit('changeData');
                });
                this.$message({
                    center: true,
                    showClose: true,
                    type: 'success',
                    message: 'Комментарий был успешно промодерирован',
                });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    center: true,
                    showClose: true,
                    message: 'Комментарий не был промодерирован',
                });
            });
        },
    },
};
</script>
