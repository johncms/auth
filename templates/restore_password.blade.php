@extends('system::layout/default')
@section('content')
    <form action="{{$data['storeUrl']}}" method="post">
        <h4>{{ __('Restore password') }}</h4>

        @if($data['commonErrors'])
            @include('system::app/alert', ['alert_type' => 'alert-danger', 'alert' => e($data['commonErrors'])])
        @endif

        @if($data['successMessage'])
            @include('system::app/alert', ['alert_type' => 'alert-success', 'alert' => $data['successMessage']])
        @endif

        @include('system::forms/simple_form', [
            'fields' => $data['formFields'],
            'errors' => $data['validationErrors'],
        ])

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">{{ __('Continue') }}</button>
            <a class="btn btn-outline-secondary" href="{{ $data['loginUrl'] }}">{{ __('Back') }}</a>
        </div>

        <div class="alert alert-warning mt-3">
            {!! __('Password will be send to E-mail address specified in your profile.<br>WARNING! If E-mail address has not been specified in your profile, you will not be able to recover your password.') !!}
        </div>
    </form>
@endsection
