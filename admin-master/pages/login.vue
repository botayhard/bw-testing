<template>
    <div>
        <div class="center-form">
            <el-form ref="ruleForm2"
                     :model="ruleForm2"
                     :rules="rules2"
                     status-icon
                     label-width="120px"
                     class="demo-ruleForm">
                <el-form-item label="email"
                              prop="email">
                    <el-input v-model="ruleForm2.email"
                              auto-complete="off"/>
                </el-form-item>
                <el-form-item label="Пароль"
                              prop="pass">
                    <el-input v-model="ruleForm2.pass"
                              type="password"
                              auto-complete="off"/>
                </el-form-item>
                <el-form-item class="buttons-style">
                    <el-button :disabled="$store.getters.isAuthenticated"
                               type="primary"
                               @click="submitForm('ruleForm2')">Отправить</el-button>
                    <el-button @click="resetForm('ruleForm2')">Очистить</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        let checkEmail = (rule, value, callback) => {
            console.log(value);
            if (!value) {
                return callback(new Error('Пожалуйста, введите email'));
            }
            setTimeout(() => {
                if (value === '') callback(new Error('Пожалуйста заполните поле email'));
                else if (!(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/.test(value))) {
                    return callback(new Error('Введите корректный email'));
                }
                callback();
            }, 1000);
        };
        let validatePass = (rule, value, callback) => {
            if (value === '') {
                callback(new Error('Пожалуйста, введите пароль'));
            } else {
                callback();
            }
        };
        return {
            result: true,
            ruleForm2: {
                email: '',
                pass: '',
            },
            rules2: {
                pass: [
                    { validator: validatePass, trigger: 'blur' },
                ],
                email: [
                    { validator: checkEmail, trigger: 'blur' },
                ],
            },
        };
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.$api.login({ email: this.ruleForm2.email, password: this.ruleForm2.pass }).then(({ result }) => {
                        if (this.$store.getters.loggedUser) {
                            this.$router.push('/proposals');
                            return;
                        }
                        if (result === 'user did not found in database') {
                            this.$message({
                                message: 'Введены неверные имя или пароль',
                                center: true,
                                showClose: true,
                                type: 'warning',
                                customClass: 'message-style',
                            });
                            return;
                        }
                        this.$api.getUser().then(({ result }) => {
                            console.log('sadasd', result);
                            this.$store.commit('setUser', result);
                            this.$router.push('/proposals');
                        });
                    });
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
    },
    head: {
        title: 'Вход',
    },
};

</script>

<style lang="scss" scoped>
    .center-form {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 25vh;
    }
    .right-message {
        /*align: right;*/
    }
    .buttons-style {
        padding-top: 20px;
    }
</style>
