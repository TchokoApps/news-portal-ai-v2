<!-- Breaking news carousel -->
<section class="bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if ($breakingNews->isNotEmpty())
                    <div class="wrapp__list__article-responsive wrapp__list__article-responsive-carousel">
                        @foreach ($breakingNews as $news)
                            <div class="item">
                                <div class="card__post card__post-list">
                                    <div class="image-sm">
                                        <a href="{{ route('news.details', ['slug' => $news->slug]) }}" aria-label="{{ $news->title }}">
                                            <img
                                                src="{{ $news->image ? asset($news->image) : asset('frontend/assets/images/news1.jpg') }}"
                                                class="img-fluid"
                                                alt="{{ $news->title }}"
                                            >
                                        </a>
                                    </div>

                                    <div class="card__post__body ">
                                        <div class="card__post__content">
                                            <div class="card__post__author-info mb-2">
                                                <ul class="list-inline">
                                                    <li class="list-inline-item">
                                                        <span class="text-primary">
                                                            {{ __('By') }} {{ $news->author?->name ?? __('Unknown author') }}
                                                        </span>
                                                    </li>
                                                    <li class="list-inline-item">
                                                        <span class="text-dark text-capitalize">
                                                            {{ $news->created_at?->format('F j, Y') }}
                                                        </span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card__post__title">
                                                <h6>
                                                    <a href="{{ route('news.details', ['slug' => $news->slug]) }}">
                                                        {{ truncate_text($news->title, 60) }}
                                                    </a>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="wrapp__list__article-responsive text-center py-3">
                        <p class="mb-0">{{ __('No breaking news available.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- End Breaking news carousel -->
