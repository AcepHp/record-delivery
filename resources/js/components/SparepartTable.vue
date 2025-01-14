<template>
    <div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id">
                    <td>{{ item.nama_barang }}</td>
                    <td>{{ item.kode_barang }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data() {
        return {
            items: []
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/api/spareparts')
                .then(response => {
                    this.items = response.data;
                });
        }
    }
}
</script>

<style scoped>
.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    border: 1px solid #ddd;
    padding: 8px;
}

.table th {
    background-color: #f2f2f2;
}
</style>
