@extends('admin.layout.master')

@section('unit.product', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="product" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            {{-- create / update --}}
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-6">
                    <form v-on:submit.prevent="createItem()" id="createArea" style="display:none">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-plus"></i>
                                    新增
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>*伺服器</label>
                                            <select class="form-control" v-model="createData.server_id" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in servers" :value="row.id">@{{ row.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>*商品名稱</label>
                                            <input type="text" class="form-control" v-model="createData.name" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>*金額</label>
                                            <input type="number" min="1" class="form-control" v-model="createData.amount" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    新增
                                </button>
                                <button type="button" @click="open('list')" class="btn float-right btn-warning">
                                    <i class="fas fa-undo-alt"></i>
                                    返回
                                </button>
                            </div>
                        </div>
                    </form>
                    <form v-on:submit.prevent="updateItem(editData.id)" id="editArea" style="display:none">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-pencil-alt"></i>
                                    編輯
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>*伺服器</label>
                                            <select class="form-control" v-model="editData.server_id" required>
                                                <option value="">請選擇</option>
                                                <option v-for="row in servers" :value="row.id">@{{ row.name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>*商品名稱</label>
                                            <input type="text" class="form-control" v-model="editData.name" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>*金額</label>
                                            <input type="number" min="1" class="form-control" v-model="editData.amount" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    更新
                                </button>
                                <button type="button" @click="open('list')" class="btn float-right btn-warning">
                                    <i class="fas fa-undo-alt"></i>
                                    返回
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- list --}}
            <div class="row" id="listArea">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <button @click="open('create')" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                新增
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>伺服器</th>
                                        <th>商品名稱</th>
                                        <th>金額</th>
                                        <th>建立時間</th>
                                        <th style="width: 10%">
                                            排序
                                            <a href="javascript:void(0)" @click="sortItems">
                                                <i class="fas fa-sync-alt text-gray"></i>
                                            </a>
                                        </th>
                                        <th style="width: 15%">功能</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id">
                                        <td>@{{ item.server ? item.server.name : '遺失伺服器' }}</td>
                                        <td>@{{ item.name }}</td>
                                        <td>@{{ item.amount }}</td>
                                        <td>@{{ item.created_at }}</td>
                                        <td>
                                            <input type="number" class="form-control" v-model="item.sort">
                                        </td>
                                        <td class="method-button">
                                            <button class="btn btn-primary btn-sm" @click="open('edit', item.id)">
                                                <i class="fas fa-pencil-alt"></i>
                                                編輯
                                            </button>
                                            <button class="btn btn-danger btn-sm" @click="deleteItem(item.id)">
                                                <i class="fas fa-trash"></i>
                                                刪除
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <x-components::pagination />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        var vm = new Vue({
            el: '#container',
            data: {
                url: '{{ route('admin.product') }}',
                items: [],
                servers: [],
                createData: {},
                editData: {},
                page: 1,
                pagination: {
                    start: 0,
                    total: 0,
                    current_page: 1
                },
            },
            created: function() {
                this.getItems();
            },
            methods: {
                clear: function() {
                    vm.createData = {
                        server_id: '',
                    };
                    vm.editData = {};
                },
                open: function(active = '', id = '') {
                    vm.clear();

                    switch (active) {
                        case 'list':
                            $('#createArea').fadeOut(0);
                            $('#editArea').fadeOut(0);
                            $('#listArea').fadeIn(300);
                            break;

                        case 'create':
                            $('#listArea').fadeOut(0);
                            $('#createArea').fadeIn(300);
                            break;

                        case 'edit':
                            try {
                                axios.get(vm.url + '/' + id).then(function(response) {
                                    vm.editData = response.data.item;
                                    $('#listArea').fadeOut(0);
                                    $('#editArea').fadeIn(300);
                                }).catch(function(error) {
                                    vm.showMessage('error', error.response.data.message);
                                });
                            } catch (error) {
                                vm.showMessage('error', error);
                            }
                            break;

                        default:
                            vm.showMessage('error', '系統異常。');
                            break;
                    }
                },
                getItems: function(page = 1, pageMove = true) {
                    let vm = this;
                    vm.page = page;

                    if (pageMove) {
                        $('html, body').animate({scrollTop: 0}, 'slow');
                    }

                    try {
                        axios.get(vm.url + '/all', {
                            params: {
                                page: page
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.servers = response.data.servers;
                            vm.setPagination(response.data.items.current_page, total)
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                createItem: function() {
                    try {
                        axios.post(vm.url, vm.createData).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems();
                            vm.open('list');
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                updateItem: function(id) {
                    try {
                        axios.patch(vm.url + '/' + id, vm.editData).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                            vm.open('list');
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                deleteItem: function(id) {
                    if (confirm('確定要刪除？') !== true) return false;

                    try {
                        axios.delete(vm.url + '/' + id).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page, false);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                sortItems: function() {
                    if (vm.items.length == 0) return false;

                    try {
                        axios.patch(vm.url + '/all/sort', {items: vm.items}).then(function(response) {
                            vm.showMessage('success', response.data.message);
                            vm.getItems(vm.page);
                        }).catch(function(error) {
                            vm.showMessage('error', error.response.data.message);
                        });
                    } catch (error) {
                        vm.showMessage('error', error);
                    }
                },
                setPagination: function(current_page, total) {
                    vm.pagination.current_page = current_page;
                    if (current_page > 6) {
                        vm.pagination.start = current_page - 5;
                        vm.pagination.total  = (total > (current_page+ 5)) ? current_page + 5 : total;
                    } else {
                        vm.pagination.start = 1;
                        vm.pagination.total = total < 10 ? total : 10;
                    }
                },
                showMessage: function(format, message) {
                    if (format == 'success') {
                        toastr.success(message);
                    } else {
                        toastr.warning(message);
                    }
                }
            }
        });
    </script>
@endsection
