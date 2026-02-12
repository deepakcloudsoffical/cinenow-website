@extends('backend.layouts.app')

@section('content')
<x-back-button-component route="backend.users.index" />
<div class="card">
    <div class="card-body">
        <div class="row gy-3">
            <div class="col-md-3">
                <div class="poster">

                    <img src="{{ setBaseUrlWithFileName($data->file_url)  }}" alt="{{ $data->first_name }}" class="img-fluid w-100 rounded">
                </div>
            </div>
            <div class="col-md-9">
                <div class="details">
                    <h1 class="mb-4">{{ $data->first_name ?? '--' }} {{ $data->last_name ?? '--' }}</h1>
                    <div class="d-flex align-items-center gap-3 gap-xl-5 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="m-0">{{ __('users.lbl_email') }} :</h6>
                            <p class="mb-0">{{  $data->email ?? '--'  }}</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="m-0">{{ __('users.lbl_contact_number') }} :</h6>
                            <p class="mb-0">{{ $data->mobile ?? '--' }}</p>
                        </div>
                    </div>
                    <hr class="my-5 border-bottom-0">
                    <div class="user-info">
                        <div class="d-flex align-items-center gap-3 gap-xl-5 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="m-0">{{ __('users.lbl_gender') }} :</h6>
                                <p class="mb-0">{{ $data->gender ? ucfirst($data->gender) : '--' }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="m-0">{{ __('users.lbl_date_of_birth') }} :</h6>
                                <p class="mb-0">
                                    {{ $data->date_of_birth ? formatDate(date('Y-m-d', strtotime($data->date_of_birth))) : '--' }}
                                </p>

                            </div>
                        </div>
                    </div>
                    <hr class="my-5 border-bottom-0">
                    <div class="address">
                        <h5>{{ __('users.lbl_address') }}</h5>
                        <p>{{ $data->address ?? '--' }}</p>
                    </div>
                    <hr class="my-5 border-bottom-0">
                </div>
            </div>

        <div class="subscription-details">
            <h5 class="mb-3">{{ __('users.lbl_subscription_details') }}</h5>
            <table id="datatable" class="table table-responsive">
            </table>
        </div>

        </div>
    </div>
</div>

@endsection

@push ('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<!-- DataTables Core and Extensions -->
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>
<script src="{{ asset('js/form/index.js') }}" defer></script>


<script type="text/javascript" defer>
const subscriptionColumns = [
    {
        data: 'name',
        name: 'name',
        title: "{{ __('dashboard.plan') }}",
        orderable: false,
    },
    {
        data: 'start_date',
        name: 'start_date',
        title: "{{ __('users.date') }}",
        orderable: false,
    },
    {
        data: 'amount',
        name: 'amount',
        title: "{{ __('dashboard.amount') }}",
        orderable: false,
    },
    {
        data: 'tax_amount',
        name: 'tax_amount',
        title: "{{ __('frontend.tax') }}",
        orderable: false,
    },
    {
        data: 'coupon_discount',
        name: 'coupon_discount',
        title: "{{ __('frontend.coupon_discount') }}",
        orderable: false,
    },
    {
        data: 'total_amount',
        name: 'total_amount',
        title: "{{ __('frontend.total') }}",
        orderable: false,
    },
    {
        data: 'duration',
        name: 'duration',
        title: "{{ __('dashboard.duration') }}",
        orderable: false,
    },
    {
        data: 'payment_method',
        name: 'payment_method',
        title: "{{ __('dashboard.payment_method') }}",
        orderable: false,
    },
    {
        data: 'transaction_id',
        name: 'transaction_id',
        title: "{{ __('dashboard.txn_id') }}",
        orderable: false,
    },
    {
        data: 'status',
        name: 'status',
        title: "{{ __('dashboard.status') }}",
        orderable: false,
    }
];

document.addEventListener('DOMContentLoaded', (event) => {
                initDatatable({
                    url: '{{ route("backend.users.subscription_data", ["id" => $data->id]) }}',
                    finalColumns: subscriptionColumns,
                    advanceFilter: () => {
                        return {
                            name: $('#user_name').val()
                        }
                    }
                });
            });
</script>
@endpush
