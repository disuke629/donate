@extends('admin.layout.master')

@section('unit.record', 'active')

@section('content')
    <x-components::unit-title guard="admin" area="record" />

    <div class="content">
        <div class="container-fluid" id="container" v-cloak>
            {{-- detail --}}
            <div class="modal fade" id="detail_modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">詳情</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-3 col-form-label">編號 :</label>
                                <label class="col-9 col-form-label">@{{ item.number }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">伺服器 :</label>
                                <label class="col-9 col-form-label">@{{ item.server ? item.server.name : '遺失伺服器' }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">商品 :</label>
                                <label class="col-9 col-form-label">@{{ item.product ? item.product.name : '手動輸入金額' }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">贊助金額 :</label>
                                <label class="col-9 col-form-label">@{{ item.amount }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">付款方式 :</label>
                                <label class="col-9 col-form-label" v-if="item.pay_method == 1">信用卡</label>
                                <label class="col-9 col-form-label" v-else-if="item.pay_method == 2">超商代碼繳款</label>
                                <label class="col-9 col-form-label" v-else-if="item.pay_method == 3">超商條碼繳款</label>
                                <label class="col-9 col-form-label" v-else-if="item.pay_method == 4">ATM轉帳</label>
                                <label class="col-9 col-form-label" v-else="item.pay_method == 1">WebATM</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 col-form-label">藍新資料回傳 :</label>
                                <label class="col-12 col-form-label">
                                    <code>@{{ item.blue_callback }}</code>
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                        </div>
                    </div>
                </div>
            </div>

           {{-- search --}}
            <div class="modal fade" id="search_modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">搜尋</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>*伺服器</label>
                                <select class="form-control" v-model="search.server_id" required>
                                    <option value="">請選擇</option>
                                    <option v-for="row in servers" :value="row.id">@{{ row.name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">帳號</label>
                                <input type="text" v-model="search.account" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="float-left btn btn-primary" @click="getItems()">送出</button>
                            <button type="button" class="float-right btn btn-default" @click="clear('search')">清空</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- list --}}
            <div class="row" id="listArea">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-tools">
                                <button type="button" class="btn btn-default" v-if="search.is_search == true" @click="clear('search')">
                                    <i class="fa-solid fa-align-justify"></i>
                                    總覽
                                </button>

                                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#search_modal">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    搜尋
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>編號</th>
                                        <th>伺服器</th>
                                        <th>帳號</th>
                                        <th>商品</th>
                                        <th>贊助金額</th>
                                        <th>付款方式</th>
                                        <th>狀態</th>
                                        <th>建立時間</th>
                                        {{-- <th style="width: 15%">功能</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, num) in items" :key="item.id">
                                        <td>@{{ item.number }}</td>
                                        <td>@{{ item.server ? item.server.name : '遺失伺服器資訊' }}</td>
                                        <td>@{{ item.account }}</td>
                                        <td>@{{ item.product ? item.product.name : '手動輸入金額' }}</td>
                                        <td>@{{ item.amount }}</td>
                                        <td>
                                            <span v-if="item.pay_method == 1">信用卡</span>
                                            <span v-else-if="item.pay_method == 2">超商代碼繳款</span>
                                            <span v-else-if="item.pay_method == 3">超商條碼繳款</span>
                                            <span v-else-if="item.pay_method == 4">ATM轉帳</span>
                                            <span v-else="item.pay_method == 1">WebATM</span>
                                        </td>
                                        <td>
                                            <span class="text-block" v-if="item.status == 0">尚未付款</span>
                                            <span class="text-green" v-else-if="item.status == 1">付款成功</span>
                                            <span class="text-red" v-else>付款失敗</span>
                                        </td>
                                        <td>@{{ item.created_at }}</td>
                                        {{-- <td class="method-button">
                                            <button class="btn btn-primary btn-sm" @click="open('detail', item.id)">
                                                <i class="fa-solid fa-eye"></i>
                                                詳情
                                            </button>
                                        </td> --}}
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
                url: '{{ route('admin.record') }}',
                items: {},
                item: {},
                servers: {},
                page: 1,
                pagination: {
                    start: 0,
                    total: 0,
                    current_page: 1
                },
                search: {
                    server_id: '',
                    account: '',
                    is_search: false
                },
            },
            created: function() {
                this.getItems();
            },
            methods: {
                clear: function() {
                    vm.search = {
                        server_id: '',
                        account: '',
                        is_search: false
                    };

                    vm.getItems();
                },
                open: function(active = '', id = '') {
                    vm.clear();

                    switch (active) {
                        case 'list':
                            $('#createArea').fadeOut(0);
                            $('#editArea').fadeOut(0);
                            $('#listArea').fadeIn(300);
                            break;

                        case 'detail':
                            try {
                                axios.get(vm.url + '/' + id).then(function(response) {
                                    vm.item = response.data.item;
                                    $('#detail_modal').modal('show');
                                }).catch(function(error) {
                                    vm.showMessage('warning', error.response.data.message ?? error.message);
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
                                page: page,
                                server_id: vm.search.server_id,
                                account: vm.search.account,
                            }
                        }).then(function(response) {
                            let total = Math.ceil(response.data.items.total / response.data.items.per_page);
                            vm.items = response.data.items.data;
                            vm.servers = response.data.servers;
                            vm.search.is_search = response.data.is_search;
                            vm.setPagination(response.data.items.current_page, total)
                        }).catch(function(error) {
                            vm.showMessage('warning', error.response.data.message ?? error.message);
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
                    } else if (format == 'error') {
                        toastr.error(message);
                    } else {
                        toastr.warning(message);
                    }
                }
            }
        });
    </script>
@endsection
