@extends('layouts.dokter')

@section('dokter-title', 'Kelola Screening')

@section('dokter-content')
@include('shared.screening-config-form', ['formAction' => route('dokter.screening')])
@endsection
