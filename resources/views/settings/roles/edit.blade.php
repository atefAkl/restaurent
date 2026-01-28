@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4>تعديل الدور: {{ $role->name }}</h4>
    <div class="card mt-3">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.update', $role) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">الاسم الظاهر</label>
                    <input type="text" name="display_name" class="form-control" value="{{ $role->display_name }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">الصلاحيات</label>
                    <div>
                        @foreach($permissions as $perm)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm->id }}" id="perm_{{ $perm->id }}" {{ in_array($perm->id, $rolePermissions) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button class="btn btn-primary">حفظ</button>
            </form>
        </div>
    </div>
</div>
@endsection
