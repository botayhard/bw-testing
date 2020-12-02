<template>
    <div>
        <div class="center-card">
            <div>
                <el-card class="box-card">
                    <div slot="header"
                         class="clearfix">
                        <span>Подробная информация</span>
                    </div>
                    <div>
                        <div v-for="item in cardData"
                             :key="item.label"
                             class="text item">
                            <strong>{{ item.label }}</strong>
                            <br>
                            <span class="pre-row">{{ item.value || 'Пусто' }}</span>
                        </div>
                    </div>
                </el-card>
            </div>
        </div>
<!--        <div class="center">-->
<!--            <el-switch v-model="value"-->
<!--                       style="display: block"-->
<!--                       active-color="#13ce66"-->
<!--                       inactive-color="#ff4949"-->
<!--                       active-text="Отправление сообщения по email"-->
<!--                       inactive-text="Добавление комментария">-->
<!--            </el-switch>-->
<!--        </div>-->
        <el-form v-show="false"
                 ref="formData"
                 :model="formData"
                 :rules="rules"
                 status-icon
                 label-width="120px"
                 class="demo-ruleForm center-form"
                 style="margin-top: 8vh">
            <el-form-item label="Email"
                          prop="email">
                <el-input v-model="formData.email"
                          auto-complete="on"
                          style="width: 500px"></el-input>
            </el-form-item>
            <el-form-item label="Тема"
                          prop="title">
                <el-input v-model="formData.title"
                          auto-complete="on"
                          style="width: 500px"></el-input>
            </el-form-item>
            <el-form-item label="Сообщение"
                          prop="message">
                <el-input v-model="formData.message"
                          type="textarea"
                          style="width: 500px"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary"
                           @click="submitForm('formData')">Отправить сообщение</el-button>
                <el-button @click="resetForm('formData')">Очистить</el-button>
            </el-form-item>
        </el-form>
        <el-form v-show="true"
                 ref="formData2"
                 :model="formData2"
                 :rules="rules2"
                 status-icon
                 label-width="120px"
                 class="demo-ruleForm center-form"
                 style="margin-top: 8vh;">
            <div class="add-comment-label">Добавить комментарий:</div>
            <el-form-item label="Комментарий"
                          prop="message">
                <el-input v-model="formData2.message"
                          auto-complete="on"
                          style="width: 500px"
                          type="textarea"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary"
                           @click="submitForm2('formData2')">Отправить сообщение</el-button>
                <el-button @click="resetForm('formData2')">Очистить</el-button>
            </el-form-item>
        </el-form>
        <div class="center-text">
            <i>История работы с сообщением</i>
            <hr width="1000px">
        </div>
        <div class="center-table">
            <el-table :data="tableData"
                      :row-class-name="tableRowClassName"
                      class="history-table">
                <el-table-column prop="name"
                                 label="Имя"
                                 width="200">
                </el-table-column>
                <el-table-column prop="title"
                                 label="Тема"
                                 width="200">
                </el-table-column>
                <el-table-column prop="message"
                                 label="Текст сообщения">
                    <template slot-scope="scope">
                        <p class="pre-row">{{ scope.row.message }}</p>
                    </template>
                </el-table-column>
                <el-table-column prop="Date"
                                 label="Дата отпраки"
                                 width="180">
                </el-table-column>
            </el-table>
        </div>
    </div>
</template>

<script>
import userAdapter from '@/adapters/User';

export default {
    data() {
        const validateEmail = (rule, value, callback) => {
            if (value === null) callback(new Error('Пожалуйста заполните поле email'));
            else if (value === '') callback(new Error('Пожалуйста заполните поле email'));
            else if (value.length > 255) callback(new Error('email слишком длинный'));
            else if (!(/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/.test(value))) {
                callback(new Error('Введите корректный email'));
            } else {
                callback();
            }
        };
        const validateTitle = (rule, value, callback) => {
            if (value === '') callback(new Error('Пожалйста, заполните поле темы сообщения'));
            else if (value.length > 255) callback(new Error('Тема слишком длинная'));
            else {
                callback();
            }
        };
        const validateMessage = (rule, value, callback) => {
            if (value === '') callback(new Error('Пожалуйста, заполните поле сообщения'));
            else callback();
        };
        return {
            value: true,
            formData: {
                title: '',
                message: '',
            },
            formData2: {
                message: '',
            },
            rules: {
                email: [
                    { validator: validateEmail, trigger: 'blur' },
                ],
                title: [
                    { validator: validateTitle, trigger: 'blur' },
                ],
                message: [
                    { validator: validateMessage, trigger: 'blur' },
                ],
            },
            rules2: {
                message: [
                    { validator: validateMessage, trigger: 'blur' },
                ],
            },
        };
    },
    middleware: ['isAuth', 'ProposalExist'],
    async asyncData({ params, app, redirect }) {
        let { result } = await app.$api.getProposal({ proposal: params.id }).catch(() => {
            redirect('/404');
        });
        let historyResult = (await app.$api.getHistory({ proposal: params.id })).result;
        if (!historyResult) historyResult = [];
        return {
            cardData: [
                { label: 'Имя', value: result.name },
                { label: 'Телефон', value: result.phone },
                { label: 'Email', value: result.email },
                { label: 'Описание', value: result.description },
                { label: 'Услуга', value: result.service_name }
            ],
            formData: {
                id: result.id,
                email: result.email,
                title: '',
                message: '',
            },
            tableData: historyResult.map((el) => {
                return {
                    status: el.status,
                    name: el.name,
                    email: el.email,
                    title: el.title,
                    message: el.message,
                    Date: el.created_at,
                };
            }),
        };
    },
    methods: {
        submitForm(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    console.log('ok');
                    this.$api.sendMail({
                        email: this.formData.email,
                        title: this.formData.title,
                        message: this.formData.message,
                        name: userAdapter(this.$store.getters.loggedUser).fullName,
                        proposal_id: this.$route.params.id,
                    });
                    this.$api.getHistory({ proposal: this.$route.params.id }).then(({ result }) => {
                        this.tableData = result.map((el) => {
                            return {
                                status: el.status,
                                name: el.name,
                                email: el.email,
                                title: el.title,
                                message: el.message,
                                Date: el.created_at,
                            };
                        });
                    });
                } else {
                    console.log('error submit!!');
                }
            });
        },
        submitForm2(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    console.log('ok', userAdapter(this.$store.getters.loggedUser).fullName);
                    console.log(this.formData2.message);
                    this.$api.createComment({
                        message: this.formData2.message,
                        title: 'Комментарий',
                        name: userAdapter(this.$store.getters.loggedUser).fullName,
                        proposal_id: this.$route.params.id,
                    });
                    this.$api.getHistory({ proposal: this.$route.params.id }).then(({ result }) => {
                        this.tableData = result.map((el) => {
                            return {
                                status: el.status,
                                name: el.name,
                                email: el.email,
                                title: el.title,
                                message: el.message,
                                Date: el.created_at,
                            };
                        });
                    });
                } else {
                    console.log('error submit!!');
                }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        tableRowClassName({ row }) {
            if (row.status === 3) return 'comment-row';
            if (row.status === 2) return 'mail-row';
            return '';
        },
    },
    head: {
        title: 'Просмотр заказа',
    },
};
</script>

<style lang="scss">
    .add-comment-label {
        margin-bottom: 0.5em;
    }

    .text {
        font-size: 14px;
        word-wrap: normal;
    }

    .item {
        margin-bottom: 18px;
    }

    .clearfix:before,
    .clearfix:after {
        display: table;
        content: "";
    }
    .clearfix:after {
        clear: both
    }

    .box-card {
        width: 480px;
    }
    .center-card {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        margin-top: 10vh;
    }
    .button-style {
        margin-top: 2vh;
        margin-bottom: 10vh;
    }
    .center-table {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        .history-table {
            max-width: 70vw;
        }
    }
    .center {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 25px;
    }
    .center-form {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        margin-bottom: 50px;
        margin-right: 100px;
    }
    .center-text {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        margin-bottom: 30px;
    }

    .history-table .comment-row {
        background-color: #90ee90 !important;
    }

    .history-table .mail-row {
        background-color: #fff26a !important;
    }
    .pre-row {
        white-space: pre-wrap !important;
        word-wrap: break-spaces !important;
        word-break: keep-all;
    }
</style>
