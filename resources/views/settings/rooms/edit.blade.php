@extends('layouts.app')
@section('title', 'تعديل بيانات الغرفة')
@section('content')
<div class="container py-4">
    <h2 class="mb-4">تعديل بيانات الغرفة</h2>
    <form action="{{ route('rooms.update', $room) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">اسم الغرفة</label>
            <input type="text" name="name" class="form-control" value="{{ $room->name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">رقم الغرفة</label>
            <input type="text" name="number" class="form-control" value="{{ $room->number }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">الحالة</label>
            <input type="text" name="status" class="form-control" value="{{ $room->status }}">
        </div>
        <button type="submit" class="btn btn-success">تحديث</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
