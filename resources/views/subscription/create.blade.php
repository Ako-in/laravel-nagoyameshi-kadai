@extends('layouts.app')

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
            const stripeKey = "{{ env('STRIPE_KEY') }}";
            // const stripe = Stripe(stripeKey);
            // const elements = stripe.elements();
            
            // その他のコード（抜粋）...
        // });
        
    </script>
    <script src="{{ asset('/js/stripe.js') }}"></script>
@endpush



{{-- @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // const stripeKey = "{{ env('STRIPE_KEY') }}";

        const stripeKey = "{{ config('services.stripe.key') }}";
        const stripe = Stripe(stripeKey);

    </script>
    <script src="{{ asset('/js/stripe.js') }}"></script>
@endpush --}}

@section('content')
    <div class="container nagoyameshi-container pb-5">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <nav class="my-3" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">ホーム</a></li>
                        <li class="breadcrumb-item active" aria-current="page">有料プラン登録</li>
                    </ol>
                </nav>

                <h1 class="mb-3 text-center">有料プラン登録</h1>

                @if (session('subscription_message'))
                    <div class="alert alert-info" role="alert">
                        <p class="mb-0">{{ session('subscription_message') }}</p>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header text-center">
                        有料プランの内容
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">・当日の2時間前までならいつでも予約可能</li>
                        <li class="list-group-item">・店舗をお好きなだけお気に入りに追加可能</li>
                        <li class="list-group-item">・レビューを全件閲覧可能</li>
                        <li class="list-group-item">・レビューを投稿可能</li>
                        <li class="list-group-item">・月額たったの300円</li>
                    </ul>
                </div>

                <hr class="mb-4">

                <div class="alert alert-danger nagoyameshi-card-error" id="card-error" role="alert">
                    <ul class="mb-0" id="error-list"></ul>
                </div>

                {{-- <form id="card-form" action="{{ route('subscription.store') }}" method="post">
                    @csrf
                    <input class="nagoyameshi-card-holder-name mb-3" id="card-holder-name" type="text" placeholder="カード名義人" required>
                    <div class="nagoyameshi-card-element mb-4" id="card-element"></div>

                </form> --}}
                
                <form id="card-form" action="{{ route('subscription.store') }}" method="post">
                    @csrf
                    <input class="nagoyameshi-card-holder-name mb-3" id="card-holder-name" type="text" placeholder="カード名義人" required>
                    <div class="nagoyameshi-card-element mb-4" id="card-element"></div>
                    <!-- 使用する国を日本に設定し、郵便番号を制限 -->
                    <label for="postal-code">郵便番号</label>
                    <input type="text" id="postal-code" name="postal_code" pattern="\d{3}-\d{4}" title="郵便番号は、例：123-4567形式で入力してください。">
                    <div class="d-flex justify-content-center">
                        <button class="btn text-white shadow-sm w-50 nagoyameshi-btn" type="submit" id="card-button" data-secret="{{ $intent->client_secret }}">登録</button>
                    </div>
                </form>

                
            </div>
        </div>
    </div>
@endsection