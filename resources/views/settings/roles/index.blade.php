@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">{{__('app.sidebar.dashboard')}}</a></li>
                
                <li class="breadcrumb-item active">{{__('app.sidebar.roles')}}</li>

            </ol>
        </nav>
    </div>
    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="title">
                <h4 class="fw-bold mb-0 text-sm">الأدوار</h4>
                <p>إدارة الأدوار وعرضهم في النظام</p>
            </div>
            <a href="{{ route('roles.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus-circle"></i>&nbsp; {{__('app.actions.create')}}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>key</th>
                        <th>المستخدمين</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->display_name }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('هل أنت متأكد؟');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
