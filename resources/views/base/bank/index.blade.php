@include('component.navigation')
@include('component.footer')
@extends('layouts.app')

@push('stylesheets')
<link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
<link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
<link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="ui container">
    <!-- Bank List -->
    <div id="bank-list"></div>
</div>
<!-- Modal -->

<script id="bank-template" type="text/html">
    <div class="ui basic vertical clearing segment">
        @{{each banks bank index}}
        <div class="ui fluid vertical menu">
            <a class="item" href="{{ url('bank') }}/@{{bank.id}}">
                <h1 class="ui medium header">@{{bank.name}}</h1>
                <p>@{{bank.description}}</p>
            </a>
        </div>
        @{{/each}}
    </div>
    @{{if paginate.last_page > 1}}
    <div class="ui basic vertical clearing segment">
        <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadBanks('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
        <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadBanks('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
    </div>
    @{{/if}}
</script>
@endsection
@push('scripts')
<script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
<script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
<script src="{{ asset('js/common/tip.js') }}"></script>
<script src="{{ asset('js/common/misc.js') }}"></script>
<script src="{{ asset('js/common/error.js') }}"></script>
<script>
    function disableBankForm() {
        $("#form input[type=text]").attr("readonly", "");
        $("#bank-dimmer").addClass("active").children(".loader").html("正在添加");
    }
    function enableBankForm() {
        $("#bank-dimmer").removeClass("active");
        $("#form input[type=text]").removeAttr("readonly");
    }
    function loadBanks(url) {
        if(url == null) {
            url = "{{ url('api/banks') }}";
        }
        $.ajax({
            "url": url,
            "method": "GET",
            "success": function(response) {
                if(response && response.success) {
                    let html = template("bank-template", {
                        "banks": response.data,
                        "paginate": response.paginate
                    });
                    $("#bank-list").html(html);
                }
            },
            "error": handleError
        });
    }
    $(document).ready(function() {
        loadBanks();
        @can('addBank')
        let validator = $("#form").validate({
            "submitHandler": function(form) {
                disableBankForm();
                $(form).ajaxSubmit({
                    "success": function(response) {
                        if(response && response.success) {
                            validator.resetForm();
                            $("#bank-modify").modal('hide');
                            location.reload(true);
                        } else {
                            tip.error(response.message || "{{ __('global.unknownError') }}")
                        }
                    },
                    "error": handleError,
                    "complete": function() {
                        enableBankForm();
                    }
                });
            },
            "rules": {
                "name": {"required": true, "maxlength": 60},
                "description": {"required": true, "maxlength": 1000}
            }
        });
        @endcan
        $("#bank-add").click(function() {
            $("#bank-modify").modal('show');
        });
        $("#btn-submit").click(function() {
            $("#form").submit();
        });
    });
</script>
@endpush
