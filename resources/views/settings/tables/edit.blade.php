@extends('layouts.app')
@section('title', 'تعديل بيانات الطاولة')
@section('content')
<div class="container py-4">
    <h2 class="mb-4">تعديل بيانات الطاولة</h2>
    <form action="{{ route('tables.update', $table) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">اسم الطاولة</label>
            <input type="text" name="name" class="form-control" value="{{ $table->name }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">رقم الطاولة</label>
            <input type="text" name="number" class="form-control" value="{{ $table->number }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">الغرفة</label>
            <select name="room_id" class="form-select">
                <option value="">بدون غرفة</option>
                @foreach($rooms as $room)
                <option value="{{ $room->id }}" {{ $table->room_id == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">الحالة</label>
            <input type="text" name="status" class="form-control" value="{{ $table->status }}">
        </div>
        <button type="submit" class="btn btn-success">تحديث</button>
        <a href="{{ route('tables.index') }}" class="btn btn-secondary">إلغاء</a>
    </form>
</div>
@endsection
