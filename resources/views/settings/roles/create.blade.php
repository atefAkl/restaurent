@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{__('app.sidebar.dashboard')}}</a></li>
                <li class="breadcrumb-item fw-bold"><a href="{{ route('roles.create') }}">{{__('app.sidebar.roles')}}</a></li>
                <li class="breadcrumb-item active">{{__('app.actions.create')}}</li>
            </ol>
        </nav>
    </div>
    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="title">
                <h4 class="fw-bold mb-0">اضافة دور جديد</h4>
                <p>اضافة الأدوار التى تسهل تنظيم المستخدمين ومنحهم الصلاحيات المطلوبة</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('roles.index') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="bi bi-sign-out-alt"></i>&nbsp; {{__('app.actions.back')}}
                </a>
                <button type="button" data-bs-toggle="modal" data-bs-target="#addPermissionModal" class="btn btn-success d-flex align-items-center">
                    <i class="bi bi-shield-plus"></i>&nbsp; {{__('roles.actions.add_permission')}}
                </button>
            </div>
        </div>
    </div>
    <!-- Table Box -->
    <div class="card mt-3">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text">الاسم الظاهر</label>
                            <input type="text" name="display_name" class="form-control" required placeholder="Administrator">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text">الاسم (مفتاح)</label>
                            <input type="text" name="name" class="form-control" required placeholder="admin">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h4 class="form-label">الصلاحيات</h4>
                    <div class="row">
                        @foreach($permissions as $perm)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}">
                                <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button class="btn btn-primary">إنشاء</button>
            </form>
        </div>
    </div>
</div>
<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-0 col-auto fwb-bold" id="addPermissionModalLabel">{{__('roles.labels.new_permission')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPermissionForm" method="POST" action="{{ route('permissions.store') }}">
                    @csrf
                    
                    <div class="input-group mb-2">
                        <label class="input-group-text" for="permission_name">{{__('roles.labels.permission_name')}}</label>
                        <input type="text" class="form-control" id="permission_name" name="name" required placeholder="manage_users">
                    </div>
                    <div class="input-group mb-2">
                        <label class="input-group-text" for="permission_display_name">{{__('roles.labels.permission_display_name')}}</label>
                        <input type="text" class="form-control" id="permission_display_name" name="display_name" required placeholder="Manage Users">
                    </div>
                    <div class="input-group mb-2">
                        <label class="input-group-text" for="permission_group">{{__('roles.labels.permission_group')}}</label>
                        <input type="text" class="form-control" id="permission_group" name="group" required placeholder="users">
                    </div>
                    <div class="form-floating mb-2">
                        <textarea class="form-control" rows="5" id="permission_description" name="description" placeholder=""></textarea>
                        <label for="permission_description">{{__('roles.labels.permission_description')}}</label>
                    </div>
                    <button type="submit" class="btn btn-success">{{__('roles.actions.add_permission')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
