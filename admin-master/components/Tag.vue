<template>
    <div class="tag-container">
        <div class="tag-text">{{ text }}</div>
        <div class="tag-button-edit"
             @click="editTag"></div>
        <div class="tag-button-delete"
             @click="deleteTag"></div>
    </div>
</template>

<script>
export default {
    props: {
        id: {
            type: Number,
            required: true,
        },
        text: {
            type: String,
            required: true,
        },
    },
    methods: {
        editTag() {
            this.$prompt('Введите новое название для тага', 'Форма ввода', {
                confirmButtonText: 'Подтвердить',
                cancelButtonText: 'Выйти',
            }).then(value => {
                if (value.value.length === 0) {
                    this.$message({
                        type: 'info',
                        message: 'Тег не может быть пустым',
                        showClose: true,
                    });
                    return;
                }
                this.$api.updateTag({ tag: this.id, text: value.value }).then(() => {
                    this.$message({
                        type: 'success',
                        message: 'Новый тег был успешно сохранен',
                        showClose: true,
                    });
                    this.$emit('reload');
                });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Тег не был сохранен',
                    showClose: true,
                });
            });
        },
        deleteTag() {
            this.$confirm('Вы действительно хотите удалить этот тег?', 'Warning', {
                confirmButtonText: 'Подтвердить',
                cancelButtonText: 'Отмена',
                type: 'warning',
            }).then(() => {
                this.$message({
                    type: 'success',
                    message: 'Тег был успешно удален',
                    showClose: true,
                });
                this.$api.deleteTag({ tag: this.id }).then(() => {
                    this.$emit('reload');
                });
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Тег не был удален',
                    showClose: true,
                });
            });
        },
    },
};
</script>

<style lang="scss" scoped>
    .tag-container {
        border: 1px solid black;
        margin-bottom: 10px;
        padding: 5px;
        margin-right: 10px;
        display: flex;
        flex-direction: row;
        .tag-text {
            font-size: 16px;
        }
        %tag-button {
            margin-left: 5px;
            height: 14px;
            width: 14px;
            &:hover {
                cursor: pointer;
            }
        }
        .tag-button-edit {
            @extend %tag-button;
            background-image: url("/static/edit.svg");
        }
        .tag-button-delete {
            @extend %tag-button;
            background-image: url("/static/cross.svg");
        }
    }
</style>
