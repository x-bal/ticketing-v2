@extends('layouts.master')

@section('content')
<div class="row">
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-analytics"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">Today's Transaction</div>
                <div class="stats-number">{{ $transaction }}</div>
                <div class="stats-progress progress">
                </div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-pricetags"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">Today Rental</div>
                <div class="stats-number">{{ $sewa }}</div>
                <div class="stats-progress progress">
                </div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-cart"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">Transaction Income</div>
                <div class="stats-number">{{ number_format($incometrx,0, ',','.') }}</div>
                <div class="stats-progress progress">
                </div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
    <!-- BEGIN col-3 -->
    <div class="col-xl-3 col-md-6">
        <div class="widget widget-stats bg-white text-inverse">
            <div class="stats-icon stats-icon-square bg-gradient-cyan-blue text-white"><i class="ion-ios-chatboxes"></i></div>
            <div class="stats-content">
                <div class="stats-title text-gray-700">Rental Income</div>
                <div class="stats-number">{{ number_format($incomerent,0, ',','.') }}</div>
                <div class="stats-progress progress">
                </div>
            </div>
        </div>
    </div>
    <!-- END col-3 -->
</div>
@endsection