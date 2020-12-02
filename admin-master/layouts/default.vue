<template>
    <div>
        <el-menu mode="horizontal"
                 :default-active="$route.fullPath"
                 router>
            <el-menu-item v-if="$store.getters.isAuthenticated"
                          index="/proposals">Заявки
            </el-menu-item>
            <el-menu-item v-if="$store.getters.isAuthenticated"
                          index="/">Создать заявку
            </el-menu-item>
<!--            <el-menu-item v-if="$store.getters.isAuthenticated"-->
<!--                          index="/portfolio?type=project">Проекты-->
<!--            </el-menu-item>-->
            <el-menu-item v-if="$store.getters.isAuthenticated"
                          index="/portfolio?type=article">Статьи
            </el-menu-item>
<!--            <el-menu-item v-if="$store.getters.isAuthenticated"-->
<!--                          index="/comments">Комментарии-->
<!--            </el-menu-item>-->
            <el-menu-item v-if="$store.getters.isAuthenticated"
                          index="/tags">Теги
            </el-menu-item>
            <el-menu-item v-if="$store.getters.isAuthenticated"
                          index="/"
                          @click="logOut">Выход
            </el-menu-item>
            <el-menu-item v-else
                          index="/login">Войти
            </el-menu-item>

        </el-menu>
        <nuxt/>
    </div>
</template>

<script>
export default {
    middleware: 'isAuth',
    methods: {
        logOut() {
            this.$api.logout().then(() => {
                this.$message({
                    message: 'Вы успешно вышли из системы',
                    center: true,
                    showClose: true,
                    type: 'success',
                    customClass: 'message-style',
                });
                this.$store.commit('setUser', null);
            });
        },
    },
};
</script>

<style lang="scss" scoped>
    .center-button {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .margin-button {
        margin-right: 10px;
    }

    .message-style {
        margin-left: 40%;
    }
</style>
