{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="License Plates" icon="la la-question" :link="backpack_url('license-plate')" />
<x-backpack::menu-item title="Users" icon="la la-question" :link="backpack_url('user')" />
<x-backpack::menu-item title="Banks" icon="la la-question" :link="backpack_url('bank')" />
<x-backpack::menu-item title="Province" icon="la la-question" :link="backpack_url('region')" />
<x-backpack::menu-item title="Cities" icon="la la-question" :link="backpack_url('city')" />

<x-backpack::menu-item title="Packages" icon="la la-question" :link="backpack_url('package')" />
<x-backpack::menu-item title="Managers" icon="la la-question" :link="backpack_url('manager')" />