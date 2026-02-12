@extends('frontend::layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class=" my-4">
                <div class="text-center p-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-semibold py-2">{{ __('messages.all_notifications') }}</h5>
                </div>

                <div class="card overflow-auto card-header-border p-0 card-body-list max-17 scroll-thin">
                    <div class="dropdown-menu-1 overflow-y-auto list-style-1 mb-0 notification-height">
                        @if(isset($notifications) && count($notifications) > 0)
                            @foreach($notifications as $notification)
                                @php
                                    $timezone = App\Models\Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
                                    $notification->created_at = $notification->created_at->setTimezone($timezone);
                                    $notification->updated_at = $notification->updated_at->setTimezone($timezone);
                                    $notification_type = ucwords(str_replace('_', ' ', $notification->data['data']['notification_type'] ?? 'notification')) . '!';
                                @endphp

                                @if($notification->data['data']['notification_group']=='subscription')
                                    <div class="dropdown-item-1 float-none p-3 mb-3 list-unstyled iq-sub-card {{ $notification->read_at ? '':'notify-list-bg'}} ">
                                        <div class="d-flex justify-content-between">
                                            <h6>{{ $notification_type }}</h6>
                                        </div>
                                        <div class="list-item d-flex">
                                            <div class="me-3 mt-1">
                                                <button type="button" class="btn btn-primary-subtle btn-icon rounded-pill">
                                                    {{ strtoupper(substr($notification->data['data']['username'] ?? 'U', 0, 1)) }}
                                                </button>
                                            </div>
                                            <div class="list-style-detail">
                                                @if($notification->data['data']['notification_type']=='new_subscription')
                                                                                                         <p class="heading-color text-start mb-1">
                                                         {{ __('messages.new') }}
                                                         <span class="body-color">{{ $notification->data['data']['name'] ?? __('messages.ultimate_plan') }}</span>
                                                         {{ __('messages.purchase_successfully') }}
                                                     </p>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-body">{{ $notification->created_at->format('d/m/Y') }}</small>
                                                        <small class="text-body">{{ $notification->created_at->format('h:i A') }}</small>
                                                    </div>
                                                @endif
                                                @if($notification->data['data']['notification_type']=='cancle_subscription')
                                                                                                         <p class="heading-color text-start mb-1">{{ __('messages.subscription_cancelled_by') }} <span class="body-color">{{ ($notification->data['data']['username'] ?? 'User') }}</span></p>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-body">{{ $notification->created_at->format('d/m/Y') }}</small>
                                                        <small class="text-body">{{ $notification->created_at->format('h:i A') }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="dropdown-item-1 float-none p-3 mb-3 list-unstyled iq-sub-card {{ $notification->read_at ? '':'notify-list-bg'}} ">
                                        <div class="d-flex justify-content-between">
                                            <h6>{{ $notification_type }}</h6>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="list-item d-flex">
                                            <div class="me-3 mt-1">
                                                <button type="button" class="btn btn-primary-subtle btn-icon rounded-pill">
                                                    <i class="ph ph-bell"></i>
                                                </button>
                                            </div>
                                            <div class="list-style-detail">
                                                <p class="heading-color text-start mb-1">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-body">{{ $notification->created_at->format('d/m/Y') }}</small>
                                                    <small class="text-body">{{ $notification->created_at->format('h:i A') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <li class="list-unstyled dropdown-item-1 float-none p-3">
                                <div class="list-item d-flex justify-content-center align-items-center">
                                    <div class="list-style-detail ml-2 mr-2">
                                        <h6 class="font-weight-bold">{{ __('messages.no_notification') }}</h6>
                                        <p class="mb-0"></p>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
