@extends('frontend::layouts.master')

@section('content')

@if(isset($featured_movies) && !is_null($featured_movies) && !empty($featured_movies) && !in_array($access_type, ['pay-per-view', 'purchased']))

<div class="banner-section section-spacing-bottom px-0">
    <div class="slick-banner main-banner"
         data-speed="1000"
         data-autoplay="true"
         data-center="false"
         data-infinite="true"
         data-navigation="true"
         data-pagination="true"
         data-spacing="0">

        @forelse($featured_movies as $movie)
            @php
                $sliderImage   = $movie['file_url'] ?? null;
                $movieData     = !empty($movie['data']) ? $movie['data']->toArray(request()) : [];
                $fallbackImage = $movieData['thumbnail_image'] ?? $movieData['poster_image'] ?? null;

                // ✅ SAFE FALLBACK (VERY IMPORTANT)
                $imageUrl = $sliderImage
                    ?: $fallbackImage
                    ?: asset('img/default-banner.jpg');

                // ✅ SAFE URL HANDLING
                $finalImageUrl = \Illuminate\Support\Str::startsWith($imageUrl, ['http', '//'])
                    ? $imageUrl
                    : setBaseUrlWithFileName($imageUrl);
            @endphp

            <div class="slick-item banner-slide"
                 style="background-image: url('{{ $finalImageUrl }}') !important;">

                <div class="movie-content h-100">
                    <div class="container-fluid h-100">
                        <div class="row align-items-center h-100">
                            <div class="col-xxl-4 col-lg-6">
                                <div class="movie-info">

                                    @if(!empty($movieData['genres']))
                                        <div class="movie-tag mb-3">
                                            <ul class="list-inline p-0 d-flex flex-wrap gap-2">
                                                @foreach($movieData['genres'] as $genre)
                                                    <li><span class="tag">{{ $genre['name'] }}</span></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <h4 class="movie-title mb-2">
                                        {{ $movieData['name'] ?? '' }}
                                    </h4>

                                    <p class="mb-0 font-size-14 line-count-3">
                                        {!! $movieData['description'] ?? '' !!}
                                    </p>

                                    <ul class="movie-meta list-inline mt-4 p-0 d-flex flex-wrap gap-3">
                                        @if(!empty($movieData['release_date']))
                                            <li>
                                                <span class="d-flex align-items-center gap-2">
                                                    <i class="ph ph-calendar"></i>
                                                    <span class="fw-medium">
                                                        {{ \Carbon\Carbon::parse($movieData['release_date'])->format('Y') }}
                                                    </span>
                                                </span>
                                            </li>
                                        @endif

                                        @if(!empty($movieData['language']))
                                            <li>
                                                <span class="d-flex align-items-center gap-2">
                                                    <i class="ph ph-translate"></i>
                                                    <span class="fw-medium">
                                                        {{ ucfirst($movieData['language']) }}
                                                    </span>
                                                </span>
                                            </li>
                                        @endif

                                        @if(!empty($movieData['duration']))
                                            <li>
                                                <span class="d-flex align-items-center gap-2">
                                                    <i class="ph ph-clock"></i>
                                                    <span class="fw-medium">
                                                        {{ $movieData['duration'] }}
                                                    </span>
                                                </span>
                                            </li>
                                        @endif
                                    </ul>

                                    <div class="mt-4 d-flex gap-2">
                                        <a href="{{ route('movie-details', $movieData['id'] ?? 0) }}"
                                           class="btn btn-primary">
                                            <i class="ph-fill ph-play"></i> Watch Now
                                        </a>

                                        <a href="{{ route('movie-details', $movieData['id'] ?? 0) }}"
                                           class="btn btn-dark">
                                            <i class="ph ph-info"></i> More Info
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="slick-item banner-slide">
                <div class="movie-content text-center h-100 d-flex align-items-center justify-content-center">
                    <h2>No Featured Movies Available</h2>
                </div>
            </div>
        @endforelse

    </div>
</div>

@endif

<div class="list-page section-spacing-bottom px-0">
    <div class="movie-lists">

        <div class="container-fluid">


        <h4 class="mb-5" >@if($access_type == 'pay-per-view')
                    {{__('messages.pay_per_view')}}
                @elseif($access_type == 'purchased')
                    {{__('messages.lbl_unlock_videos')}}
                @else
                    {{__('frontend.movies')}}
                @endif</h4>

            <div class="row gy-4 row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6" id="entertainment-list">
            </div>
            <div class="card-style-slider shimmer-container">
                <div class="row gy-4 row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 mt-3">
                        @for ($i = 0; $i < 12; $i++)
                            <div class="shimmer-container col mb-3">
                                    @include('components.card_shimmer_movieList')
                            </div>
                        @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/entertainment.min.js') }}" defer></script>

<script>
    const noDataImageSrc = '{{ asset('img/NoData.png') }}';
    const envURL = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
    const shimmerContainer = document.querySelector('.shimmer-container');
    const EntertainmentList = document.getElementById('entertainment-list');
    const pageTitle = document.getElementById('page_title');
    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;
    const per_page = 12;
    const csrf_token='{{ csrf_token() }}'
    const language = "{{ $language ?? '' }}";
    const genreId = "{{ $genre_id ?? '' }}"; // Get genre_id from the Blade template
    const accessType = "{{ $access_type ?? '' }}";

    // Initialize the API URL
    let apiUrl = `${envURL}/api/v2/movie-list?page=${currentPage}&is_ajax=1&per_page=${per_page}`;

    // Add query parameters only if they exist
    if (language) {
        apiUrl += `&language=${language}`;
    }
    if (genreId) {
        apiUrl += `&genre_id=${genreId}`;
    }
    if (accessType) {
        apiUrl += `&access_type=${accessType}`;
    }

    const showNoDataImage = () => {
        shimmerContainer.innerHTML = '';
        const noDataImage = document.createElement('img');
        noDataImage.src = noDataImageSrc;
        noDataImage.alt = 'No Data Found';
        noDataImage.style.display = 'block';
        noDataImage.style.margin = '0 auto';
        shimmerContainer.appendChild(noDataImage);
    };

    const loadData = async () => {
        if (!hasMore || isLoading) return;

        isLoading = true;
        shimmerContainer.style.display = '';  // Show shimmer container
        try {
            const response = await fetch(`${apiUrl}&page=${currentPage}`);
            const data = await response.json();

            if (data?.html) {
                EntertainmentList.insertAdjacentHTML(currentPage === 1 ? 'afterbegin' : 'beforeend', data.html);
                hasMore = !!data.hasMore;
                if (hasMore) currentPage++;
                shimmerContainer.style.display = 'none';  // Hide shimmer container
                initializeWatchlistButtons();
            } else {
                showNoDataImage();
            }
        } catch (error) {
            console.error('Fetch error:', error);
            showNoDataImage();
        } finally {
            isLoading = false;
        }
    };

    const handleScroll = () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500 && hasMore) {
            loadData();
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        loadData();  // Load the first page of movies
        window.addEventListener('scroll', handleScroll);  // Attach scroll listener
        initializeWatchlistButtons()
    });

    function initializeWatchlistButtons() {

  const watchList = typeof isWatchList!== 'undefined' ? !!emptyWatchList : null;
  const watchListPresent = typeof emptyWatchList !== 'undefined' ? !!emptyWatchList : null;
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    $('.watch-list-btn').off('click').on('click', function () {

      var $this = $(this);
      var isInWatchlist = $this.data('in-watchlist');
      var entertainmentId = $this.data('entertainment-id');
      const baseUrl = document.querySelector('meta[name="baseUrl"]').getAttribute('content');
      var entertainmentType = $this.data('entertainment-type'); // Get the type
      let action = isInWatchlist == '1' ? 'delete' : 'save';
      var data = isInWatchlist
          ? { id: [entertainmentId], _token: csrf_token }
          : { entertainment_id: entertainmentId, type: entertainmentType, _token: csrfToken };

      // Perform the AJAX request
      $.ajax({
          url: action === 'save' ? `${baseUrl}/api/save-watchlist` : `${baseUrl}/api/delete-watchlist?is_ajax=1`,
          method: 'POST',
          data: data,
          success: function (response) {
            window.successSnackbar(response.message)
              $this.find('i').toggleClass('ph-check ph-plus');
              $this.toggleClass('btn-primary btn-dark');
              $this.data('in-watchlist', !isInWatchlist);
              var newInWatchlist = !isInWatchlist ? 'true' : 'false';
              var newTooltip = newInWatchlist === 'true' ? 'Remove Watchlist' : 'Add Watchlist';

              // Destroy the current tooltip
              $this.tooltip('dispose');

              // Update the tooltip attribute
              $this.attr('data-bs-title', newTooltip);

              // Reinitialize the tooltip
              $this.tooltip();
              if (action !== 'save' && watchList) {
                $this.closest('.iq-card').remove();
                if (EntertainmentList.children.length === 0) {
                  if (watchListPresent) {
                    emptyWatchList.style.display = '';
                    const noDataImage = document.createElement('img');
                    noDataImage.src = noDataImageSrc;
                    noDataImage.alt = 'No Data Found';
                    noDataImage.style.display = 'block';
                    noDataImage.style.margin = '0 auto';
                    emptyWatchList.appendChild(noDataImage);
                }
                }
                // shimmerContainer.style.display = 'none';

            }
          },
          error: function (xhr) {
              if (xhr.status === 401) {
                  window.location.href = `${baseUrl}/login`;
              } else {
                  console.error(xhr);
              }
          }
      });
  });
  // Initialize tooltips for all watchlist buttons on page load
//    $('[data-bs-toggle="tooltip"]').tooltip();

}
 // Initialize Banner Swiper

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const $banner = $('.slick-banner.main-banner');

    if ($banner.length && !$banner.hasClass('slick-initialized')) {
        $banner.slick({
            rtl: $('html').attr('dir') === 'rtl',
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 5000,
            speed: 1000,
            arrows: true,
            dots: true,
            infinite: true,
            pauseOnHover: false,
            adaptiveHeight: false
        });
    }

});
</script>

<style>
  /* ✅ Banner visibility fix */
.banner-slide {
    min-height: 58vh;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
}

/* Netflix / Prime-style overlay */
/* RESET any previous overlay */
.banner-slide::before,
.banner-slide::after {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
}

/* 🔽 BOTTOM FADE ONLY (no top darkening) */
.banner-slide::before {
    z-index: 1;
    background: linear-gradient(
        0deg,
        rgba(0,0,0,1.00) 0%,
        rgba(0,0,0,0.92) 18%,
        rgba(0,0,0,0.55) 40%,
        rgba(0,0,0,0.18) 58%,
        rgba(0,0,0,0.00) 72%
      );
}

/* ⬅ LEFT TEXT CONTRAST ONLY (center stays clean) */
.banner-slide::after {
    z-index: 1;
    background: linear-gradient(
        0deg,
        rgba(0,0,0,1.00) 0%,
        rgba(0,0,0,0.92) 18%,
        rgba(0,0,0,0.55) 40%,
        rgba(0,0,0,0.18) 58%,
        rgba(0,0,0,0.00) 72%
      );
}

/* Content always above overlays */
.movie-content {
    position: relative;
    z-index: 2;
}

.banner-slide {
    filter: brightness(1.10) contrast(1.05);
}



.movie-title,
.movie-description {
    text-shadow: 0 2px 10px rgba(0,0,0,0.45);
}


.banner-slide {
    height: 58vh !important;
    max-height: 750px;
    background-size: cover !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
    position: relative;
}

.slick-banner,
.slick-list,
.slick-track,
.slick-slide {
    height: 100% !important;
}


</style>
@endsection
