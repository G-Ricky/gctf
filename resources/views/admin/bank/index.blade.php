@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addBank', 'editBank'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/semantic-ui-calendar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
    @endcanany
@endpush

@section('content')
    @can('addBank')
    <div class="ui container">
        <div class="ui basic vertical clearing segment">
            <button id="bank-add" class="ui primary right floated button" onclick="addBank()"><i class="add circle icon"></i> {{ __('bank.view.admin.button.add') }}</button>
        </div>
    </div>
    @endcan
    <div class="ui container" id="container-banks"></div>

    @canany(['addBank', 'editBank'])
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="bank-modify">
        <div class="ui dimmer" id="bank-dimmer">
            <div class="ui big text loader"></div>
        </div>
        <i class="close icon"></i>
        <div class="header" id="modal-bank-title">
            {{ __('bank.view.admin.modal.title.add') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-bank" name="bank-add" action="{{ url('api/bank') }}" method="post">
                    @csrf
                    <input id="form-method" name="_method" type="hidden" value="POST">
                    <input id="bank-id" name="id" type="hidden" value="">
                    <div class="field">
                        <label for="bank-name">{{ __('bank.view.admin.modal.label.name') }}</label>
                        <input id="bank-name" name="name" type="text" maxlength="61" value="">
                    </div>

                    <div class="field">
                        <label for="bank-description">{{ __('bank.view.admin.modal.label.description') }}</label>
                        <textarea id="bank-description" name="description" type="text" rows="6" maxlength="1001"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" id="bank-save" type="button" value="{{ __('bank.view.admin.modal.button.save') }}">
        </div>
    </div>
    <!-- end modal -->
    @endcanany

    <!-- template -->
    <script id="tpl-container-banks" type="text/html">
        <div class="ui basic vertical segment" id="table-banks">
            <table class="ui single line compact table">
                <thead>
                <tr>
                    <th>{{ __('bank.view.admin.table.id') }}</th>
                    <th>{{ __('bank.view.admin.table.name') }}</th>
                    <th>{{ __('bank.view.admin.table.description') }}</th>
                    <th>{{ __('bank.view.admin.table.challengesCount') }}</th>
                    <th>{{ __('bank.view.admin.table.operation') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each banks bank index}}
                <tr>
                    <td>@{{bank.id}}</td>
                    <td><a href="{{ url('bank') }}/@{{bank.id}}" target="_blank">@{{bank.name}}</a></td>
                    <td>@{{bank.description}}</td>
                    <td>@{{bank.challenges_count}}</td>
                    <td>
                        @can('editBank')
                        <button class="ui primary icon button" data-tooltip="{{ __('bank.view.admin.table.row.tooltip.edit') }}" onclick="editBank('@{{bank.id}}')">
                            <i class="edit icon"></i>
                        </button>
                        @endcan
                        @can('deleteBank')
                        @{{if bank.challenges_count === 0}}
                        <button class="ui negative icon button" data-tooltip="{{ __('bank.view.admin.table.row.tooltip.delete') }}" onclick="confirm('{{ __('bank.view.admin.table.row.delete.confirm')}}') &amp;&amp; deleteBank('@{{bank.id}}')">
                            <i class="trash icon"></i>
                        </button>
                        @{{/if}}
                        @endcan
                    </td>
                </tr>
                @{{/each}}
                </tbody>
            </table>
        </div>
        @{{if paginate.last_page > 1}}
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadBanks('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadBanks('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
        @{{/if}}
    </script>
    <!-- end template-->
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
    <script src="{{ asset('js/semantic.min.js') }}"></script>
    <script src="{{ asset('js/semantic-ui-calendar.min.js') }}"></script>
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script src="{{ asset('js/common/error.js') }}"></script>
    <script>
        let banksDict = {};
        function loadBanks(url) {
            if(url == null) {
                url = "{{ url("api/banks") }}";
            }
            $.ajax({
                "url": url,
                "type": "GET",
                "success": function(response, status) {
                    if(response && response.success) {
                        let banks = response.data;
                        for(let i = 0;i < banks.length;++i) {
                            let id = banks[i].id;
                            banksDict[id] = banks[i];
                        }
                        $("#container-banks").html(
                            template("tpl-container-banks", {
                                "success": true,
                                "banks": response.data,
                                "paginate": response.paginate
                            })
                        );
                    } else {
                        tip.error(response.message || "{{ __('global.unknownError') }}");
                    }
                },
                "error": handleError,
                "complete": function() {

                }
            });
        }
        @can('addBank')
        function addBank() {
            $("#form-method").val("POST");
            $("#modal-bank-title").text("{{ __('bank.view.admin.modal.title.add') }}");
            $("#bank-id").val("");
            $("#bank-name").val("");
            $("#bank-description").val("");
            $("#bank-modify").modal('show');
        }
        @endcan
        @can('editBank')
        function editBank(id) {
            let bank = banksDict[id];
            $("#form-method").val("PUT");
            $("#modal-bank-title").text("{{ __('bank.view.admin.modal.title.edit') }}");
            $("#bank-id").val(bank.id);
            $("#bank-name").val(bank.name);
            $("#bank-description").val(bank.description);
            $("#bank-modify").modal('show');
        }
        @endcan
        @can('deleteBank')
        function deleteBank(id) {
            if(id == null) {
                tip.error("{{ __('bank.view.message.bankNotExist') }}");
            }
            $.ajax({
                "url": "{{ url('api/bank') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "DELETE"
                },
                "success": function(response, status) {
                    if(response && response.success) {
                        tip.success("{{ __('global.success') }}");
                        loadBanks();
                    } else {
                        tip.error(response.message || "{{ __('global.unknownError') }}");
                    }
                },
                "error": handleError,
                "complete": function() {

                }
            });
        }
        @endcan
        $(document).ready(function() {
            @canany(['addBank', 'editBank'])
            $(".ui.modal").modal();
            @endcanany
            loadBanks();
            @canany(['addBank', 'editBank'])
            $("#bank-save").click(function() {
                $("#form-bank").submit();
            });
            $("#form-bank").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(response && response.success) {
                                tip.success("{{ __('global.success') }}");
                                $("#bank-modify").modal('hide');
                                loadBanks();
                            } else {
                                tip.error(response.message || "{{ __('global.unknownError') }}");
                            }
                        },
                        "error": handleError,
                        "complete": function(jqXHR, textStatus) {

                        }
                    });
                },
                "rules": {
                    "name": {
                        "required": true,
                        "maxlength": 250
                    },
                    "description": {
                        "required": false,
                        "maxlength": 1000
                    }
                }
            });
            @endcanany
        });
    </script>
@endpush