<template>
    <div class="paginator">
        <el-table :highlight-current-row="false"
                  :row-class-name="tableRowClassName"
                  :data="tableData"
                  :current-page.sync="currentPage"
                  class="proposals-table"
                  @row-click="moreInfo">
            <el-table-column
                label="Имя">
                <template slot-scope="scope">
                    <span style="margin-left: 10px">{{ scope.row.name || 'Пусто' }}</span>
                </template>
            </el-table-column>
            <el-table-column
                label="Телефон">
                <template slot-scope="scope">
                    <span style="margin-left: 10px">{{ scope.row.phone || 'Пусто' }}</span>
                </template>
            </el-table-column>
            <el-table-column
                label="Email">
                <template slot-scope="scope">
                    <span style="margin-left: 10px">{{ scope.row.email || 'Пусто' }}</span>
                </template>
            </el-table-column>
            <el-table-column
                label="Операции">
                <template slot-scope="scope">
                    <el-button type="danger"
                               icon="el-icon-delete"
                               circle
                               @click="showDialog(scope.row)"></el-button>
                </template>

            </el-table-column>
        </el-table>
        <div class="block">
            <el-pagination :total="currentItemsCount"
                           :current-page="currentPage"
                           layout="prev, pager, next"
                           @current-change="handleCurrentChange">
            </el-pagination>
        </div>
        <el-dialog :visible.sync="dialogVisible"
                   title="Предупреждение"
                   width="30%">
            <span>Вы уверены, что хотите безвозвратно удалить эту запись?</span>
            <span slot="footer"
                  class="dialog-footer">
                <el-button @click="dialogVisible = false">Закрыть</el-button>
                <el-button type="primary"
                           @click="deleteProposal(row)">Подтвердить</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
export default {
    middleware: 'isAuth',
    data() {
        return {
            deleteFlag: false,
            dialogVisible: false,
            row: null,
        };
    },
    async asyncData({ app, route }) {
        let currentPage = route.query.hasOwnProperty('page') ? Number(route.query.page) : 1;
        let { result } = await app.$api.allProposals({ page: currentPage });
        let currentItemsCount = (await app.$api.proposalsCount()).result;
        return {
            tableData: result.data.map(el => {
                return {
                    id: el.id,
                    name: el.name,
                    phone: el.phone,
                    email: el.email,
                    status: el.status,
                };
            }),
            currentItemsCount,
            currentPage,
        };
    },
    methods: {
        handleCurrentChange(val) {
            this.$router.push({ query: { page: val }});
        },
        deleteProposal(row) {
            this.dialogVisible = false;
            this.deleteFlag = true;
            this.$api.deleteProposal({ proposal: row.id });
            --this.currentItemsCount;
            let pagesCount = Math.floor(this.currentItemsCount / 10);
            if (this.currentItemsCount % 10 > 0) ++pagesCount;
            if (pagesCount < this.currentPage) this.$router.push({ query: { page: pagesCount }});
            this.$api.allProposals({ page: this.currentPage }).then(({ result }) => this.tableData = result.data.map(el => {
                return {
                    id: el.id,
                    name: el.name,
                    phone: el.phone,
                    email: el.email,
                    status: el.status,
                };
            }));
        },
        moreInfo({ id }) {
            if (this.deleteFlag) {
                this.deleteFlag = false;
                return;
            }
            this.$router.push({ path: `/proposals/${id}`, params: { id } });
        },
        tableRowClassName({ row }) {
            if (row.status === 'ok') {
                return 'success-row';
            }
            else if (row.status === 'answered') {
                return 'answered-row';
            }
            return '';
        },
        showDialog(row) {
            this.deleteFlag = true;
            this.dialogVisible = true;
            this.row = row;
        },
    },
    watchQuery: ['page'],
    head: {
        title: 'Заказы',
    },
};

</script>

<style lang="scss">
    .paginator {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        .proposals-table {
            max-width: 70vw;
        }
    }
    .success-row {
        background-color: #98fb98 !important;
    }
    .answered-row {
        background-color: #fff26a !important;
    }

</style>
