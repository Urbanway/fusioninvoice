@extends('layouts.master')

@section('javascript')
    <script type="text/javascript">
      $(function () {
        $('.btn-bill-income').click(function () {
          $('#modal-placeholder').load("{{ route('incomeBill.create') }}", {
            id: $(this).data('income-id'),
            redirectTo: '{{ request()->fullUrl() }}'
          });
        });

        $('.income_filter_options').change(function () {
          $('form#filter').submit();
        });

        $('#btn-bulk-delete').click(function () {

          var ids = [];

          $('.bulk-record:checked').each(function () {
            ids.push($(this).data('id'));
          });

          if (ids.length > 0) {
            if (!confirm('{!! trans('fi.bulk_delete_record_warning') !!}')) return false;
            $.post("{{ route('incomes.bulk.delete') }}", {
              ids: ids
            }).done(function () {
              window.location = decodeURIComponent("{{ urlencode(request()->fullUrl()) }}");
            });
          }
        });
      });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1 class="pull-left">
            {{ trans('fi.incomes') }}
        </h1>

        <div class="pull-right">

            <a href="javascript:void(0)" class="btn btn-default bulk-actions" id="btn-bulk-delete"><i
                        class="fa fa-trash"></i> {{ trans('fi.delete') }}</a>

            <div class="btn-group">
                {!! Form::open(['method' => 'GET', 'id' => 'filter']) !!}
                {!! Form::select('company_profile', $companyProfiles, request('company_profile'), ['class' => 'income_filter_options form-control inline']) !!}
                {!! Form::select('status', $statuses, request('status'), ['class' => 'income_filter_options form-control inline']) !!}
                {!! Form::select('category', $categories, request('category'), ['class' => 'income_filter_options form-control inline']) !!}
                {!! Form::select('vendor', $vendors, request('vendor'), ['class' => 'income_filter_options form-control inline']) !!}
                {!! Form::close() !!}
            </div>
            <a href="{{ route('incomes.create') }}" class="btn btn-primary"><i
                        class="fa fa-plus"></i> {{ trans('fi.new') }}</a>
        </div>

        <div class="clearfix"></div>
    </section>

    <section class="content">

        @include('layouts._alerts')

        <div class="row">

            <div class="col-xs-12">

                <div class="box box-primary">

                    <div class="box-body no-padding">
                        <table class="table table-hover">

                            <thead>
                            <tr>
                                <th>
                                    <div class="btn-group"><input type="checkbox" id="bulk-select-all"></div>
                                </th>
                                <th class="col-md-2">{!! Sortable::link('income_date', trans('fi.date')) !!}</th>
                                <th class="col-md-2">{!! Sortable::link('income_categories.name', trans('fi.category')) !!}</th>
                                <th class="col-md-3">{!! Sortable::link('description', trans('fi.description')) !!}</th>
                                <th class="col-md-2">{!! Sortable::link('amount', trans('fi.amount')) !!}</th>
                                <th class="col-md-2">{{ trans('fi.attachments') }}</th>
                                <th class="col-md-1">{{ trans('fi.options') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($incomes as $income)
                                <tr>
                                    <td><input type="checkbox" class="bulk-record" data-id="{{ $income->id }}"></td>
                                    <td>{{ $income->formatted_income_date  }}</td>
                                    <td>
                                        {{ $income->category_name }}
                                        @if ($income->vendor_name)
                                            <br><span class="text-muted">{{ $income->vendor_name }}</span>
                                        @endif
                                    </td>
                                    <td>{!! $income->formatted_description !!}</td>
                                    <td>
                                        {{ $income->formatted_amount }}
                                        @if ($income->is_billable)
                                            @if ($income->has_been_billed)
                                                <br><a href="{{ route('invoices.edit', [$income->invoice_id]) }}"><span
                                                            class="label label-success">{{ trans('fi.billed') }}</span></a>
                                            @else
                                                <br><span class="label label-danger">{{ trans('fi.not_billed') }}</span>
                                            @endif
                                        @else
                                            <br><span class="label label-default">{{ trans('fi.not_billable') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach ($income->attachments as $attachment)
                                            <a href="{{ $attachment->download_url }}"><i
                                                        class="fa fa-file-o"></i> {{ $attachment->filename }}</a><br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown">
                                                {{ trans('fi.options') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                @if ($income->is_billable and !$income->has_been_billed)
                                                    <li><a href="javascript:void(0)" class="btn-bill-income"
                                                           data-income-id="{{ $income->id }}"><i
                                                                    class="fa fa-money"></i> {{ trans('fi.bill_this_income') }}
                                                        </a></li>
                                                @endif
                                                <li><a href="{{ route('incomes.edit', [$income->id]) }}"><i
                                                                class="fa fa-edit"></i> {{ trans('fi.edit') }}</a></li>
                                                <li><a href="{{ route('incomes.delete', [$income->id]) }}"
                                                       onclick="return confirm('{{ trans('fi.delete_record_warning') }}');"><i
                                                                class="fa fa-trash-o"></i> {{ trans('fi.delete') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="pull-right">
                    {!! $incomes->appends(request()->except('page'))->render() !!}
                </div>

            </div>

        </div>

    </section>

@stop