<template>
    <div class="main-tag-container">
        <div class="tag-add-container">
            <input class="tag-add-input" v-model="tagText"/>
            <el-button type="success" @click="addTag">Добавить тег</el-button>
        </div>
        <div class="tag-container">
            <tag v-for="tag in tags"
                 :text="tag.text"
                 :id="tag.id"
                 :key="tag.id"
                 @reload="getTags"/>
        </div>
    </div>
</template>

<script>
import tag from '@/components/Tag';

export default {
    data() {
        return {
            tagText: '',
        };
    },
    async asyncData({ app }) {
        const { result } = await app.$api.getAllTags();
        return {
            tags: result,
        };
    },
    methods: {
        getTags() {
            this.$api.getAllTags().then((el) => {
                this.tags = el.result;
            });
        },
        addTag() {
            if (this.tagText.length === 0) {
                this.$message({
                    type: 'info',
                    message: 'Тег не может быть пустым',
                    showClose: true,
                });
                return;
            }
            this.$api.addTag({ text: this.tagText }).then(() => {
                this.getTags();
                this.tagText = '';
            });
        },
    },
    components: {
        tag,
    },
};
</script>

<style lang="scss" scoped>
    .main-tag-container {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        .tag-add-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            .tag-add-input {
                margin-right: 10px;
            }
        }
        .tag-container {
            flex-wrap: wrap;
            display: flex;
        }
    }
</style>
