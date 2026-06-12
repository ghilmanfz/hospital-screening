@extends('layouts.admin')

@section('admin-title', 'Kelola Screening')

@section('admin-content')
@include('shared.screening-config-form', ['formAction' => route('admin.screening')])
@endsection
