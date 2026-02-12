<?php

namespace Modules\Entertainment\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Genres\Transformers\GenresResource;
use Modules\Subscriptions\Transformers\PlanResource;
use Modules\Subscriptions\Models\Plan;
use Modules\Entertainment\Models\Entertainment;

class WatchlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        // Determine which relationship to use based on entertainment_type
        $isVideo = ($this->type ?? '') == 'video';
        $contentItem = $isVideo ? $this->video : $this->entertainment;

        $genre_data = [];
        $plans = [];

        if (!$isVideo && $contentItem) {
            // For entertainment items, get genres and plans
            $genres = optional($contentItem)->entertainmentGenerMappings;
            foreach($genres as $genre){
                $genre_data[] = $genre->genre;
            }

            $plan = optional($contentItem)->plan;
            if(!empty($plan)){
                $plans = Plan::where('level', '<=', $plan->level)->get();
            }
        }

        return [
            'id' => $this->id,
            'entertainment_id' => $this->entertainment_id,
            'user_id' => $this->user_id,
            'entertainment_type' => $this->type ?? '',
            'name' => optional($contentItem)->name,
            'description' => strip_tags(optional($contentItem)->description),
            'trailer_url_type' => optional($contentItem)->trailer_url_type,
            'type' => $isVideo ? 'video' : optional($contentItem)->type,
            'trailer_url' => isset($contentItem) && optional($contentItem)->trailer_url_type == 'Local'
                ? setBaseUrlWithFileName(optional($contentItem)->trailer_url)
                : (optional($contentItem)->trailer_url ?? null),
            'movie_access' => $isVideo ? optional($contentItem)->access : optional($contentItem)->movie_access,
            'plan_id' => optional($contentItem)->plan_id,
            'plan_level' => (optional(optional($contentItem)->plan)->level ?? 0),
            'language' => optional($contentItem)->language,
            'imdb_rating' => optional($contentItem)->IMDb_rating,
            'content_rating' => optional($contentItem)->content_rating,
            'duration' => optional($contentItem)->duration,
            'release_date' => optional($contentItem)->release_date,
            'is_restricted' => optional($contentItem)->is_restricted,
            'video_upload_type' => optional($contentItem)->video_upload_type,
            'video_url_input' => isset($contentItem) && optional($contentItem)->video_upload_type == 'Local'
                ? setBaseUrlWithFileName(optional($contentItem)->video_url_input)
                : (optional($contentItem)->video_url_input ?? null),
            'download_status' => optional($contentItem)->download_status,
            'enable_quality' => optional($contentItem)->enable_quality,
            'download_url' => optional($contentItem)->download_url,
            'poster_image' => setBaseUrlWithFileName(optional($contentItem)->poster_url ?? null),
            'thumbnail_image' => $isVideo
                ? setBaseUrlWithFileName(optional($contentItem)->poster_url ?? null)
                : setBaseUrlWithFileName(optional($contentItem)->thumbnail_url ?? null),
            'genres' => GenresResource::collection($genre_data),
            'plans' => PlanResource::collection($plans),
            'status' => optional($contentItem)->status,
            'is_watch_list' => true,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'poster_tv_image' => setBaseUrlWithFileName(optional($contentItem)->poster_tv_url ?? null),
            'is_purchased' => Entertainment::isPurchased($this->entertainment_id, $isVideo ? 'video' : optional($contentItem)->type, $this->user_id),
        ];
    }
}
