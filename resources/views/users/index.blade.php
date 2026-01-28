@extends('layouts.app')

@section('title', 'المنتجات')

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <div class="card mb-3">
        <nav aria-label="breadcrumb" class="">
            <ol class="breadcrumb bg-white px-3 py-3 mb-0" style="font-size:13px;">
                <li class="breadcrumb-item fw-bold"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </nav>
    </div>
    <!-- Header & Add Button -->
    <div class="card mb-3 p-3">
        <div class="d-flex justify-content-between align-items-center ">
            <div class="title">
                <h2 class="fw-bold mb-0" style="font-size:1.6rem;">المستخدمين</h2>
                <p>إدارة مستخدميك وعرضهم في النظام</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-plus-circle"></i>&nbsp; جديد
            </a>
        </div>
    </div>
    <!-- Table Box -->
    <div class="card" style="border-radius:0px;">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>تاريخ الانضمام</th>
                        <th style="min-width:90px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->pluck('display_name')->join(', ') }}</td>
                        <td>{{ $user->active ? 'نشط' : 'غير نشط' }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="تعديل"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info text-white" title="عرض"><i class="bi bi-eye"></i></a>
                            @if(Auth::user()->hasRole('admin') || Auth::user()->hasPermission('manage_users'))
                            <a href="{{ route('users.roles.edit', $user) }}" class="btn btn-sm btn-secondary" title="أدوار"><i class="bi bi-shield-lock"></i></a>
                            @endif
                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('تأكيد الحذف؟')"><i class="bi bi-trash"></i></button>
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