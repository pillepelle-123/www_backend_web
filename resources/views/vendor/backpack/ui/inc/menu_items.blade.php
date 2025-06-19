{{-- This file is used for menu items by any Backpack v6 theme --}}
{{-- <li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li> --}}

<x-backpack::menu-item title="Dashboard" icon="la la-shapes" :link="backpack_url('dashboard')" />

<x-backpack::menu-item title="Users" icon="la la-user" :link="backpack_url('user')" />
<x-backpack::menu-item title="Companies" icon="las la-industry" :link="backpack_url('company')" />

<x-backpack::menu-item title="Offers" icon="las la-tags" :link="backpack_url('offer')" />
@php
    $unreadCount = \App\Models\Application::where('is_read_by_owner', false)->count();
@endphp
<x-backpack::menu-item title="Applications" icon="las la-envelope-open" :link="backpack_url('application')">
    @if($unreadCount > 0)
        <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger" style="margin-left: -5px; margin-top: -5px;">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
    @endif
</x-backpack::menu-item>
<x-backpack::menu-item title="User Matches" icon="las la-handshake" :link="backpack_url('user-match')" /> {{-- Alternativ Icons: user-friends --}}
<x-backpack::menu-item title="Ratings" icon="las la-thumbs-up" :link="backpack_url('rating')" />

<x-backpack::menu-item title="Affiliate-Links" icon="las la-link" :link="backpack_url('affiliate-link')" />

