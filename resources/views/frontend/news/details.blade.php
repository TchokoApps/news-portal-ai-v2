@extends('frontend.layouts.master')

@section('title', $news->title)

@section('content')
    <section class="pb-80 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumbs bg-light mb-0">
                            <li class="breadcrumbs__item">
                                <a href="{{ route('home') }}" class="breadcrumbs__url">
                                    <i class="fa fa-home"></i> {{ __('Home') }}
                                </a>
                            </li>
                            <li class="breadcrumbs__item">
                                <span class="breadcrumbs__url">{{ __('News') }}</span>
                            </li>
                            <li class="breadcrumbs__item breadcrumbs__item--current">
                                {{ $news->title }}
                            </li>
                        </ol>
                    </nav>
                </div>

                <div class="col-lg-8">
                    <article class="wrap__article-detail">
                        <div class="wrap__article-detail-title">
                            <div class="card__post__category mb-2">
                                {{ $news->category?->name ?? __('Uncategorized') }}
                            </div>

                            <h1>{{ $news->title }}</h1>
                        </div>

                        <hr>

                        <div class="wrap__article-detail-info">
                            <ul class="list-inline d-flex flex-wrap justify-content-start">
                                <li class="list-inline-item">
                                    <span>
                                        {{ __('By') }} {{ $news->author?->name ?? __('Unknown author') }}
                                    </span>
                                </li>
                                <li class="list-inline-item">
                                    <span class="text-dark text-capitalize ml-1">
                                        {{ $news->created_at?->format('F j, Y') }}
                                    </span>
                                </li>
                                <li class="list-inline-item">
                                    <span class="text-dark text-capitalize ml-1">
                                        {{ number_format($news->views) }} {{ __('Views') }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        <div class="wrap__article-detail-image mt-4">
                            <figure>
                                <img
                                    src="{{ $news->image ? asset($news->image) : asset('frontend/assets/images/news1.jpg') }}"
                                    alt="{{ $news->title }}"
                                    class="img-fluid w-100"
                                >
                            </figure>
                        </div>

                        <div class="wrap__article-detail-content">
                            <div class="total-views mb-4">
                                <div class="total-views-read">
                                    {{ number_format($news->views) }}
                                    <span>{{ __('Views') }}</span>
                                </div>
                            </div>

                            <div class="article-content">
                                {!! $news->content !!}
                            </div>

                            @if ($news->tags->isNotEmpty())
                                <div class="mt-4">
                                    <h5 class="mb-3">{{ __('Tags') }}</h5>
                                    <div>
                                        @foreach ($news->tags as $tag)
                                            <span class="badge badge-secondary mr-2 mb-2">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
