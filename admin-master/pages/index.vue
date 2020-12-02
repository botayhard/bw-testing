<template>
    <div class="center-button">
        <el-form ref="ruleForm2"
                 :model="ruleForm2"
                 :rules="rules2"
                 status-icon
                 label-width="120px"
                 class="demo-ruleForm">
            <el-form-item label="Имя"
                          prop="name">
                <el-input v-model="ruleForm2.name"
                          class="margin-input"></el-input>
            </el-form-item>
            <el-form-item label="Телефон"
                          prop="phone">
                <el-input v-model="ruleForm2.phone"
                          class="margin-input"></el-input>
            </el-form-item>
            <el-form-item label="Email"
                          prop="email">
                <el-input v-model.number="ruleForm2.email"
                          class="margin-input"></el-input>
            </el-form-item>
            <el-form-item label="Описание"
                          prop="description">
                <el-input v-model.number="ruleForm2.description"
                          class="margin-input"
                          type="textarea"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary"
                           class="margin-input"
                           @click="submitForm('ruleForm2')">Отправить</el-button>
                <el-button @click="resetForm('ruleForm2')">Очистить</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>


<script>
export default {
    data() {
        const validateName = (rule, value, callback) => {
            if (value.length > 255) callback(new Error('Ваше имя не должно превышать 255 символов'));
            else {
                callback();
            }
        };
        const validatePhone = (rule, value, callback) => {
            if (!(/^([+]?[0-9\s-\(\)]{3,25})*$/.test(value))) {
                callback(new Error('введите корректный номер телефона'));
            } else {
                callback();
            }
        };
        const validateEmail = (rule, value, callback) => {
            if (value.length === 0) {
                callback();
            } else if (!(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/.test(value))) {
                callback(new Error('Введите корректный email'));
            } else {
                callback();
            }
        };
        const validateDescription = (rule, value, callback) => {
            callback();
        };
        return {
            ruleForm2: {
                name: '',
                phone: '',
                email: '',
                description: '',
            },
            rules2: {
                name: [
                    { validator: validateName, trigger: 'blur' },
                ],
                phone: [
                    { validator: validatePhone, trigger: 'blur' },
                ],
                email: [
                    { validator: validateEmail, trigger: 'blur' },
                ],
                description: [
                    { validator: validateDescription, trigger: 'blur' },
                ],
            },
        };
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.$api.createProposal({
                        name: (this.ruleForm2.name === '') ? null : this.ruleForm2.name,
                        phone: (this.ruleForm2.phone === '') ? null : this.ruleForm2.phone,
                        email: (this.ruleForm2.email === '') ? null : this.ruleForm2.email,
                        description: (this.ruleForm2.description === '') ? null : this.ruleForm2.description,
                    }).then(() => {
                        this.$message({
                            message: 'заявка успешно создана',
                            center: true,
                            showClose: true,
                            type: 'success',
                            customClass: 'message-style',
                        });
                        this.$refs[formName].resetFields();
                    });
                } else {
                    this.$message({
                        message: 'заявка не была создана',
                        center: true,
                        showClose: true,
                        type: 'warning',
                        customClass: 'message-style',
                    });
                }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
    },
    mounted() {
    },
    head: {
        title: 'Создание заказа',
    },
};
</script>

<style lang="scss">
    .message-style {
        margin-left: 40%;
    }
</style>

<style lang="scss" scoped>
    .center-button {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 20vh;
    }
    .margin-input {
        margin-top: 10px;
    }

</style>
