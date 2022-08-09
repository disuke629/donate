<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>贊助平台</title>
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body class="container bg-light">
    <div class="text-center pt-5 pb-2">
        <h2>贊助平台</h2>
        <h5>遊戲伺服器 : {{ $server->name }}</h5>
    </div>

    <div class="card col-6 m-auto m-0" id="app" v-cloak>
        <div class="card-body">
            <form v-on:submit.prevent="checkForm">
                <div class="form-group">
                    <label>帳號</label>
                    <input type="text" class="form-control" v-model="account" required />
                </div>

                <div class="form-group">
                    <label>贊助方式</label>
                    <select class="form-control" v-model="product_id" required>
                        <option value="">請選擇</option>
                        <option value="-1">自行輸入金額</option>
                        @foreach ($products as $row)
                            <option  value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" v-if="product_id != -1 && product_id != ''">
                    <label>金額</label>
                    <input type="number" min="1" class="form-control" v-model="product_amomunt"  readonly />
                </div>

                <div class="form-group" v-if="is_self_amount">
                    <label>金額</label>
                    <input type="number" min="1" class="form-control" id="self_amount" v-model="amount" />
                </div>

                <div class="form-group">
                    <label>付款方式</label>
                    <select class="form-control" v-model="pay_method" required>
                        <option value="">請選擇</option>
                        {{-- <option value="1">信用卡</option> --}}
                        <option value="2">超商代碼繳款</option>
                        <option value="3">超商條碼繳款</option>
                        <option value="4">ATM轉帳</option>
                        <option value="5">WebATM</option>
                    </select>
                    <label v-if="pay_method == 2" class="text-red">*超商代碼繳款金額只能在30~20000元之間</label>
                    <label v-else-if="pay_method == 3" class="text-red">*超商條碼繳款金額只能在20~40000元之間</label>
                    <label v-else-if="pay_method == 4" class="text-red">*ATM轉帳金額只能在50000元內</label>
                    <label v-else-if="pay_method == 5" class="text-red">*WebATM金額只能在50000元內</label>
                </div>

                <div class="form-group">
                    <label>驗證碼</label>
                    <input type="text" class="form-control" v-model="code" required />
                    <div id="code">{!! $code !!}</div>
                </div>

                <div class="form-group">
                    <button
                        v-if="is_checked === false"
                        class="btn btn-primary btn-block"
                        type="submit">
                        確定贊助
                    </button>
                    <button class="btn btn-primary btn-block" type="button" disabled v-else>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        資料驗證中...
                    </button>
                </div>
                <p>所有繳費資料包含IP等等紀錄皆已留存，如有惡意人士利用此繳費平台進行第三方詐騙，請受害者立即與我們客服聯繫提供資料報警處理，請注意您的贊助皆為個人自願性，繳費後將無法做退費的動作，我們會將該筆費用維持伺服器運行與開發研究，如贊助平台故障請聯絡客服人員</p>
                <p class="text-center">ip:{{ $ip }}</p>
            </form>
        </div>
    </div>

    <div id="blue" style="display: none"></div>

    <footer>
        <div class="my-4 text-muted text-center">
            <p>©贊助平台</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Vue -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <!-- Toastr -->
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(function () {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
            };
        });

        var vm = new Vue({
            el: '#app',
            data: {
                account: '',
                product_id: '',
                amount: 0,
                product_amomunt: 0,
                code: '',
                pay_method: '',
                is_checked: false,
                is_self_amount: false,
                products: JSON.parse('{!! $products !!}')
            },
            watch: {
                'product_id': function(val) {
                    if (val == -1) {
                        $('#self_amount').prop('required', true);
                        this.is_self_amount = true;
                    } else {
                        $('#self_amount').prop('required', false);
                        this.is_self_amount = false;

                        for (let index = 0; index < this.products.length; index++) {
                            if (val == this.products[index].id) {
                                this.product_amomunt = this.products[index].amount;
                            }
                        }
                    }
                },
            },
            methods: {
                checkForm: function() {
                    vm.is_checked = true;

                    try {
                        axios.post('{{ route('index', ['type' => $server->url_suffix]) }}', {
                            server_id: {{ $server->id }},
                            account: vm.account,
                            product_id: vm.product_id,
                            amount: vm.amount,
                            pay_method: vm.pay_method,
                            code: vm.code,
                        }).then(function(response) {
                            $('#blue').append(response.data.html);
                            $('#newebpay').empty();
                            $('#newebpay').submit();
                        }).catch(function(error) {
                            toastr.warning(error.response.data.message ?? error.message);
                            vm.is_checked = false;
                            vm.refreshCode();
                        })
                    } catch (error) {
                        toastr.error('系統異常');
                        vm.is_checked = false;
                        vm.refreshCode();
                    }
                },
                refreshCode: function() {
                    vm.code = '';

                    try {
                        axios.get('{{ route('code') }}').then(function(response) {
                            $("#code").html(response.data.code);
                        }).catch(function(error) {
                            toastr.warning(error.response.data.message ?? error.message);
                        })
                    } catch (error) {
                        toastr.error('系統異常');
                    }
                }
            }
        })

        @if (session('error_message'))
            toastr.warning('{!! session('error_message') !!}');
        @endif
    </script>
</body>

</html>
