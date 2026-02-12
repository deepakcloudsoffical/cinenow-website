@php
    $trailerUrl = $data['trailer_url'] ?? '';
    $mainVideoUrl = $data['video_url_input'] ?? '';
    $trailerType = $data['trailer_url_type'] ?? 'URL';
    $mainVideoType = $data['video_upload_type'] ?? 'URL';

    $tracking = DB::table('movie_user_trackings')
        ->where('user_id', auth()->id())
        ->where('entertainment_id', $data['id'])
        ->first();

    
@endphp

<div id="video-section" class="position-relative video-player-wrapper">

    {{-- 🎥 Trailer Container --}}
    <div id="videoContainer" class="position-relative w-100" style="overflow:hidden;">
        <video id="trailerPlayer" preload="metadata" muted playsinline class="w-100"
            poster="{{ $data['thumbnail_image'] ?? ($data['thumbnail_image'] ?? '') }}">
            @if (!empty($trailerUrl))
                <source src="{{ $trailerUrl }}"
                    type="{{ $trailerType === 'HLS' ? 'application/x-mpegURL' : 'video/mp4' }}">
            @endif
        </video>

        <button id="muteToggleBtn" class="mute-btn" title="Toggle Sound">
            <i class="fa-solid fa-volume-mute"></i>
        </button>

        {{-- 🧾 Movie Overlay --}}
        <div class="movie-overlay">
            <div class="movie-overlay-content">
                <h1 class="movie-title">{{ $data['name'] }}</h1>
                <p class="movie-description">{!! $data['description'] !!}</p>

                <ul class="genres-list ps-0 mb-2 d-flex flex-wrap align-items-center gap-2">
                    @if (isset($data['genres']) && $data['genres']->isNotEmpty())
                        @foreach ($data['genres'] as $genre)
                            <li class="fw-semibold d-flex align-items-center">
                                {{ $genre->name ?? '--' }}
                                @if (!$loop->last)
                                    <span class="mx-1">•</span>
                                @endif
                            </li>
                        @endforeach
                    @endif
                </ul>

                <ul class="list-inline mt-2 mb-3 p-0 d-flex align-items-center flex-wrap gap-3 movie-metalist">
                    <li><span class="fw-medium">{{ \Carbon\Carbon::parse($data['release_date'])->format('Y') }}</span>
                    </li>
                    @if(!empty($data['language']))
                        <li><span class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-language"></i>
                            <span class="fw-medium">{{ ucfirst($data['language']) }}</span>
                        </span></li>
                    @endif
                    @if(!empty($data['duration']))
                    <li>
                        <span class="d-flex align-items-center gap-2">
                            <i class="fa-regular fa-clock"></i>
                
                            {{-- Arabic (RTL): show 02:26 --}}
                            @if(app()->getLocale() == 'ar')
                                <span class="fw-medium">{{ $data['duration'] }}</span>
                
                            {{-- Other languages: show 2h 26m --}}
                            @else
                                @php
                                    $parts = explode(':', $data['duration']);
                                    $hours = $parts[0] ?? 0;
                                    $minutes = $parts[1] ?? 0;
                                @endphp
                                <span class="fw-medium">{{ $hours }}h {{ $minutes }}m</span>
                            @endif
                
                        </span>
                    </li>
                @endif

                </ul>

                @php
                    // Defaults
                    $video_url = '';
                    $episode_id = '';
                    $episode_name = '';
                    $qualityOptions = [];
                    $subtitleInfoJson = json_encode([]);
                    $Isepisodepurhcase = false;

                    if ($data['type'] == 'movie') {
                        $video_url =
                            $data['video_upload_type'] === 'Local'
                                ? $data['video_url_input']
                                : $data['video_url_input'];
                        $videoLinks = $data['video_links'] ?? [];
                        if ($data['enable_quality'] == 1 && !empty($videoLinks)) {
                            foreach ($videoLinks as $link) {
                                $qualityOptions[$link->quality] = [
                                    'value' =>
                                        $link->type === 'Local' ? setBaseUrlWithFileName($link->url) : $link->url, // ✅ no encryption
                                    'type' => $link->type,
                                ];
                            }
                        }
                        $qualityOptionsJson = json_encode($qualityOptions);
                        if (isset($data['subtitle_info'])) {
                            $subtitleInfoJson = json_encode($data['subtitle_info']->toArray(request()));
                        }
                    } else {
                        $episodeData = $data['episodeData'] ?? [];
                        $episode_id = $episodeData['id'] ?? '';
                        $episode_name = $episodeData['name'] ?? '';
                        $video_url =
                            ($episodeData['video_upload_type'] ?? 'URL') === 'Local'
                                ? $episodeData['video_url_input'] ?? ''
                                : (isset($episodeData['video_url_input'])
                                    ? Crypt::encryptString($episodeData['video_url_input'])
                                    : '');
                        $videoLinks = $episodeData['video_links'] ?? [];
                        foreach ($videoLinks as $link) {
                            $qualityOptions[$link->quality] = [
                                'value' => $link->type === 'Local' ? setBaseUrlWithFileName($link->url) : $link->url, // ✅ no encryption
                                'type' => $link->type,
                            ];
                        }
                        $qualityOptionsJson = json_encode($qualityOptions);

                        if (isset($episodeData['subtitle_info'])) {
                            $subtitleInfoJson = json_encode($episodeData['subtitle_info']->toArray(request()));
                        }

                        if (isset($episodeData['access']) && $episodeData['access'] == 'pay-per-view') {
                            $Isepisodepurhcase = \Modules\Entertainment\Models\Entertainment::isPurchased(
                                $episode_id,
                                'episode',
                            );
                        }
                    }

                    // Trailer & Main Video Defaults
                    $trailerUrl = $data['trailer_url'] ?? '';
                    $mainVideoUrl = $data['video_url_input'] ?? '';
                    $trailerType = $data['trailer_url_type'] ?? 'URL';
                    $mainVideoType = $data['video_upload_type'] ?? 'URL';
                @endphp

                @php
                    use Carbon\Carbon;
                    $today = Carbon::today();
                    $releaseDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : null;
                    $isReleased = $releaseDate && $releaseDate->lte($today);

                @endphp

                <div class="play-button-wrapper">

    
    @if (!$isReleased)
        <button class="btn btn-primary play-now-btn" disabled
            style="background: gray; cursor: not-allowed;">
            <i class="fa-solid fa-clock me-2"></i> Coming Soon
        </button>

  
    @elseif(isset($episodeData) && ($episodeData['access'] ?? '') === 'pay-per-view' && !$Isepisodepurhcase)
        <a href="{{ route('pay-per-view.paymentform', ['id' => $episode_id, 'type' => 'episode']) }}"
           class="btn btn-primary play-now-btn">
            <i class="fa-solid fa-ticket me-2"></i> Get Ticket
        </a>

    @else
       
        @php
            $isMoviePPV = ($data['movie_access'] ?? '') === 'pay-per-view';
            $isMoviePurchased = \Modules\Entertainment\Models\Entertainment::isPurchased(
                $data['id'],
                'movie'
            );
            $isMoviePurchased = true;
            $finalPrice = $data['price'] - $data['price'] * ($data['discount'] / 100);

            // 🔑 tracking status (safe default)
            $movieStatus = $tracking->current_status ?? 'watch_now';
        @endphp

       
        @if ($isMoviePPV && !$isMoviePurchased)
            <a href="{{ route('pay-per-view.paymentform', ['id' => $data['id'], 'type' => 'movie']) }}"
               class="btn btn-primary btn-get-ticket">
                Get Ticket
                <span class="ms-2">{{ Currency::format($finalPrice, 2) }}</span>
            </a>

        @else
           
          
            @if ($movieStatus === 'expired')
                <a href="{{ route('pay-per-view.paymentform', ['id' => $data['id'], 'type' => 'movie']) }}"
                   class="btn btn-primary btn-get-ticket">
                    <i class="fa-solid fa-ticket me-2"></i> Get Ticket Again <span class="ms-2">{{ Currency::format($finalPrice, 2) }}</span>
                </a>

           
            @elseif ($movieStatus === 'watched_25')
                <a href="{{ route('pay-per-view.paymentform', ['id' => $data['id'], 'type' => 'movie']) }}"
                   class="btn btn-primary btn-get-ticket">
                    <i class="fa-solid fa-ticket me-2"></i> Get Ticket Again
                </a>

            
            @else
                <button class="btn btn-primary btn-watch-now" id="watchNowBtn"
                    data-type="{{ $data['video_upload_type'] ?? 'URL' }}"
                    data-entertainment-id="{{ $data['id'] }}"
                    data-entertainment-type="{{ $data['type'] }}"
                    data-video-url="{{ $video_url }}"
                    data-movie-access="{{ $data['movie_access'] ?? 'free' }}"
                    data-plan-id="{{ $data['plan_id'] ?? '' }}"
                    data-user-id="{{ auth()->id() }}"
                    data-purchase-type="{{ $data['purchase_type'] ?? '' }}"
                    data-profile-id="{{ getCurrentProfile(auth()->id(), request()) }}"
                    data-episode-id="{{ $episode_id }}"
                    data-first-episode-id="1"
                    data-quality-options="{{ $qualityOptionsJson }}"
                    data-subtitle-info="{{ $subtitleInfoJson }}"
                    data-contentid="{{ $data['type'] == 'movie' ? $data['id'] : $episode_id }}"
                    data-contenttype="{{ $data['type'] }}"
                    data-is-ppv="true"
                    data-is-purchased="true"
                    data-payment-url="{{ route('pay-per-view.paymentform', ['id' => $data['id'], 'type' => 'movie']) }}">
                    Watch Now
                </button>
            @endif
        @endif
    @endif

</div>

            </div>
        </div>
    </div>
    <div class="banner-bottom-fade"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/artplayer/dist/artplayer.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
       const watchNowBtn = document.querySelector('.btn-watch-now');
        const getTicketBtn = document.querySelector('.btn-get-ticket');

        const overlay = document.querySelector('.movie-overlay');
        const videoContainer = document.querySelector('.video-player-wrapper');
        const trailerPlayer = document.getElementById('trailerPlayer');
        const muteToggleBtn = document.getElementById('muteToggleBtn');

        const muteBtn = document.getElementById('muteToggleBtn');

        if (muteBtn) {
            // Detect RTL layout
            const isRTL = document.dir === 'rtl' || document.documentElement.getAttribute('dir') === 'rtl';

            if (isRTL) {
                // Move mute button to the left side
                muteBtn.style.right = 'unset';
                muteBtn.style.left = '25px';
            } else {
                // Default LTR position (right side)
                muteBtn.style.left = 'unset';
                muteBtn.style.right = '25px';
            }
        }

        // ✅ Mute/unmute trailer
        if (muteToggleBtn && trailerPlayer) {
            muteToggleBtn.addEventListener('click', function() {
                trailerPlayer.muted = !trailerPlayer.muted;
                const icon = muteToggleBtn.querySelector('i');
                icon.classList.toggle('fa-volume-up', !trailerPlayer.muted);
                icon.classList.toggle('fa-volume-mute', trailerPlayer.muted);
            });
        }

        if (trailerPlayer) {
            trailerPlayer.pause();
            trailerPlayer.removeAttribute('src'); // don’t load trailer yet
            trailerPlayer.load(); // reset to show poster only

            // Show the poster for 5 seconds first
            setTimeout(() => {
                // Now load the trailer after poster display
                if ('{{ $trailerType }}' === 'HLS' && Hls.isSupported()) {
                    const hls = new Hls();
                    hls.loadSource('{{ $trailerUrl }}');
                    hls.attachMedia(trailerPlayer);
                    trailerPlayer.muted = true;
                    trailerPlayer.play().catch(() => {});
                } else {
                    trailerPlayer.src = '{{ $trailerUrl }}';
                    trailerPlayer.muted = true;
                    trailerPlayer.play().catch(() => {});
                }

                // ✅ Let full trailer play (no pause limit)
                trailerPlayer.addEventListener('loadeddata', function() {
                    trailerPlayer.play().catch(() => {});
                });
            }, 5000); // wait 5 seconds showing poster
        }




        if (!watchNowBtn || !overlay || !videoContainer) return;

        function playMainVideo() {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            setTimeout(() => overlay.style.display = 'none', 400);

            videoContainer.innerHTML = `<div id="artplayer-app" style="width:100%;height:100%;"></div>`;

            const mainVideoType = watchNowBtn.getAttribute('data-type');
            const mainVideoUrl = watchNowBtn.getAttribute('data-video-url');
            const qualityOptions = JSON.parse(watchNowBtn.getAttribute('data-quality-options') || '{}');

            const qualities = Object.keys(qualityOptions).map(q => ({
                html: q,
                url: qualityOptions[q].value,
                type: qualityOptions[q].type,
            }));

            const defaultQuality = qualities[0] ? qualities[0].url : mainVideoUrl;
            const defaultType = qualities[0] ? qualities[0].type : mainVideoType;
            
            let subtitleInfoRaw = watchNowBtn.getAttribute('data-subtitle-info');
            let subtitleInfo = [];
            
            try {
                subtitleInfo = JSON.parse(subtitleInfoRaw && subtitleInfoRaw !== 'null' ? subtitleInfoRaw : '[]');
            } catch (e) {
                subtitleInfo = [];
            }
            
const subtitleTracks = Array.isArray(subtitleInfo)
    ? subtitleInfo
        .filter(sub => sub.subtitle_file && typeof sub.subtitle_file === "string")
        .map(sub => ({
            html: sub.language || sub.language_code || "Subtitle",
            url: sub.subtitle_file,
            type: "vtt",
        }))
    : [];


            
            const defaultSubtitle = subtitleTracks.length ? subtitleTracks[0] : undefined;
            
            console.log("Raw Subtitle Info:", subtitleInfo);
console.log("Parsed Subtitles:", subtitleTracks);

const hasSubtitles = subtitleTracks.length > 0;


            // ✅ Only HLS handled by hls.js — no blob URL issue
//             const art = new Artplayer({
//                 container: '#artplayer-app',
//                 autoplay: true,
//                 playbackRate: true,
//                 fullscreen: true,
//                 pip: true,
//                 hotkey: true,
//                 url: defaultQuality,
//                 controls: [{
//                     position: 'top',
//                     html: '',
//                 }, ],
//                 customType: {
//                     m3u8: function(video, url) {
//                         if (Hls.isSupported()) {
//                             const hls = new Hls();
//                             hls.loadSource(url);
//                             hls.attachMedia(video);

//                             video.hls = hls;

//                             // ✅ Handle quality switch dynamically
//                             art.on('qualityChange', (item) => {
//                                 if (item.url) {
//                                     hls.destroy();
//                                     const newHls = new Hls();
//                                     newHls.loadSource(item.url);
//                                     newHls.attachMedia(video);
//                                     video.hls = newHls;
//                                 }
//                             });
//                         } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
//                             video.src = url;
//                         }
//                     },
//                 },

//                 // ✅ Quality dropdown array
//                 quality: qualities.length ?
//                     qualities.map((q, i) => ({
//                         html: q.html,
//                         url: q.url,
//                         default: i === 0,
//                     })) : [],

// if (subtitleTracks.length) {
//     playerOptions.subtitle = {
//         url: defaultSubtitle.url,
//         type: "vtt",
//         switch: subtitleTracks,
//         default: true,
//         encoding: "utf-8",
//     };
// }

//             });

const playerOptions = {
    container: '#artplayer-app',
    url: defaultQuality,
    autoplay: true,
    hotkey: true,
    pip: true,
    fullscreen: true,
    playbackRate: false,
    setting: true,
    subtitleOffset: hasSubtitles,
    customType: {
        m3u8(video, url) {
            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(url);
                hls.attachMedia(video);
                video.hls = hls;

                art.on('qualityChange', (item) => {
                    if (item.url) {
                        hls.destroy();
                        const newHls = new Hls();
                        newHls.loadSource(item.url);
                        newHls.attachMedia(video);
                        video.hls = newHls;
                    }
                });
            } else {
                video.src = url;
            }
        },
    },
    quality: qualities.map((q, i) => ({
        html: q.html,
        url: q.url,
        default: i === 0,
    })),
};

// 🟢 ADD SUBTITLE ONLY WHEN AVAILABLE
if (subtitleTracks.length > 0) {
    playerOptions.subtitle = {
    url: subtitleTracks[0].url,
    type: 'vtt',
    switch: subtitleTracks,
    default: true,
    encoding: 'utf-8'
};

}
console.log("Subtitle Enabled in Player:", hasSubtitles);

const art = new Artplayer(playerOptions);

let hasSavedOnEnd = false;

function saveContinueWatch() {
    if (!art || !art.video || !art.video.duration) return;
    if (!watchNowBtn || !watchNowBtn.dataset.entertainmentId) return;

    const watchedTime = Math.floor(art.video.currentTime);
    const totalTime = Math.floor(art.video.duration);
    if (watchedTime < 5) return;

    fetch('{{ route('frontend.continueWatch.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            entertainment_id: watchNowBtn.dataset.entertainmentId,
            entertainment_type: watchNowBtn.dataset.entertainmentType,
            profile_id: watchNowBtn.dataset.profileId,
            episode_id: watchNowBtn.dataset.episodeId || null,
            watched_time: watchedTime,
            total_time: totalTime,
        }),
    }).catch(() => {});
}

art.on('video:ended', () => {
    if (!hasSavedOnEnd) {
        saveContinueWatch();
        hasSavedOnEnd = true;
    }
});

let lastContinueWatchSaved = 0;

art.on('video:timeupdate', () => {
    if (art.video.currentTime - lastContinueWatchSaved >= 15) {
        lastContinueWatchSaved = art.video.currentTime;
        saveContinueWatch();
    }
});

window.addEventListener('beforeunload', () => {
    if (typeof art !== 'undefined' && art.video && !hasSavedOnEnd) {
        saveContinueWatch();
    }
});


            // ✅ When video ends → update watch count
            art.on('video:ended', () => {
                if (!hasSavedOnEnd) {
        saveContinueWatch();
        hasSavedOnEnd = true;
    }
                const entertainmentId = watchNowBtn.getAttribute('data-entertainment-id');
                fetch('{{ route('frontend.decrementWatchCount') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            entertainment_id: entertainmentId
                        }),
                    })
                    .then((res) => res.json())
                    .then((data) => console.log('Watch count updated:', data))
                    .catch((err) => console.error('Error updating watch count:', err));
            });

            // Watch progress tracking
            let lastReported = 0;
            const entertainmentId = watchNowBtn.getAttribute('data-entertainment-id');
            const userId = watchNowBtn.getAttribute('data-user-id');

            if (userId) {
                art.on('video:timeupdate', () => {
                    const percentage = (art.video.currentTime / art.video.duration) * 100;

                    // Only report every 5% change
                    if (percentage - lastReported >= 5) {
                        lastReported = percentage;

                        fetch('{{ route('frontend.updateWatchProgress') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    entertainment_id: entertainmentId,
                                    watched_percentage: Math.round(percentage)
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.status === 'get_ticket') {
                                    // 🧾 Replace Watch Now with Get Ticket
                                    const buttonWrapper = document.querySelector(
                                        '.play-button-wrapper');
                                    if (buttonWrapper) {
                                        buttonWrapper.innerHTML = `
                        <a href="${watchNowBtn.dataset.paymentUrl}" 
                           class="btn-watch-now">
                           Get Ticket
                        </a>`;
                                    }
                                }
                            })
                            .catch(err => console.error('Error updating progress:', err));
                    }
                });
            }
        }


        // ✅ WatchNow logic — keep your existing flow intact
        watchNowBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // const isRented = watchNowBtn.getAttribute('data-is-rented') === 'true';
            const isPPV = watchNowBtn.getAttribute('data-is-ppv') === 'true';
            const isPurchased = watchNowBtn.getAttribute('data-is-purchased') === 'true';
            const paymentUrl = watchNowBtn.getAttribute('data-payment-url');
            const entertainmentId = watchNowBtn.getAttribute('data-entertainment-id');

            // if (isRented) {
            //     window.location.href = paymentUrl;
            //     return;
            // }
         
            if (isPPV && !isPurchased) {
                window.location.href = paymentUrl;
                return;
            }

            fetch('{{ route('frontend.checkWatchCount') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        entertainment_id: entertainmentId
                    }),
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.status === 'unauthenticated') {
                        window.location.href = '{{ route('login') }}';
                    } else if (data.status === 'error') {
                        window.location.href = paymentUrl;
                    } else {
                        playMainVideo();
                    }
                })
                .catch((err) => console.error(err));
        });
    });
</script>

<style>
    .art-control-progress,
    .art-control-progress-inner {
        display: none !important;
    }

    .mute-btn {
        position: absolute;
        bottom: 25px;
        right: 25px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        border-radius: 50%;
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 15;
        transition: background 0.3s ease;
    }

    .mute-btn:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .mute-btn i {
        font-size: 18px;
    }

    .plyr--video {
        --plyr-color-main: #ffffff;
    }

    .plyr__controls {
        background: rgba(0, 0, 0, 0.35) !important;
        border-radius: 0 0 8px 8px;
    }

    .js-minimal-player {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .custom-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        display: flex;
        gap: 10px;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .video-player-wrapper:hover .custom-controls {
        opacity: 1;
    }

    .custom-controls button,
    .custom-controls select {
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 6px 10px;
        cursor: pointer;
        font-size: 14px;
    }

    .custom-controls button:hover,
    .custom-controls select:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .video-player-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .movie-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        /*background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 25%, rgba(0, 0, 0, 0.3) 75%);*/
        background: linear-gradient(to top, rgb(0 0 0 / 72%) 15%, rgb(32 32 32 / 11%) 50%) !important;
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
        padding: 60px;
        color: #fff;
        z-index: 5;
        transition: opacity 0.4s ease;
    }

    .movie-overlay-content {
        max-width: 600px;
    }

    .movie-title {
        font-size: 40px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .movie-description {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 25px;
    }

    .btn-watch-now {
        background: #91969e;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn-watch-now:hover {
        background: rgb(116 120 126);
    }

    @media (max-width: 768px) {
        .movie-overlay {
            padding: 25px;
            align-items: flex-end;
        }

        .movie-title {
            font-size: 26px;
        }

        .movie-description {
            font-size: 14px;
        }
    }

    .video-player-wrapper {
        position: relative;
        width: 100%;
        margin: 0 auto;
        height: 650px;
        overflow: hidden;
        max-height: 650px;
    }

    #videoContainer,
    #trailerPlayer,
    #mainPlayer,
    iframe {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
.banner-bottom-fade {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 15%; /* Only bottom 1/3 fades */
    pointer-events: none;
    z-index: 2 !important;

    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0) 0%,
        rgba(0,0,0,0.10) 10%,
        rgba(0,0,0,0.15) 30%,
        rgba(0,0,0,0.55) 50%,
        rgba(0,0,0,0.75) 80%,
        rgba(0,0,0,1) 100%
    );
}
</style>
