@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4>تعديل أدوار المستخدم: {{ $user->name }}</h4>
    <div class="card mt-3">
        <div class="card-body">
            <form method="POST" action="{{ route('users.roles.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">الأدوار</label>
                    <div>
                        @foreach($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
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
