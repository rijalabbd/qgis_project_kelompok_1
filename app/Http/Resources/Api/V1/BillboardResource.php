<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BillboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $size = null;
        if ($this->description && str_starts_with($this->description, 'Ukuran:')) {
            preg_match('/Ukuran: ([^|]+)/', $this->description, $sizeMatch);
            $size = isset($sizeMatch[1]) ? mb_trim($sizeMatch[1]) : null;
        }

        $primaryPhoto = $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();

        return [
            'id' => $this->id,
            'title' => $this->name,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'price_per_month' => (int) ($this->activePricing?->price_per_month ?? 0),
            'is_available' => (bool) $this->is_active,
            'impressions_per_day' => (int) $this->impressions_per_day,
            'thumbnail_url' => $primaryPhoto?->photo_url ?? $this->thumbnail_url,
            'category' => $this->category?->name,
            'size' => $size,
            'address' => $this->address,
            'district' => $this->district,
            'city' => $this->city,
            'traffic_density' => $this->traffic_density,
            // Full data for detail
            'code' => $this->when($request->routeIs('*.show'), $this->code),
            'description' => $this->when($request->routeIs('*.show'), $this->description),
            'facing_direction' => $this->when($request->routeIs('*.show'), $this->facing_direction),
            'is_illuminated' => $this->when($request->routeIs('*.show'), $this->is_illuminated),
            'is_featured' => $this->when($request->routeIs('*.show'), $this->is_featured),
            'photos' => $this->when($request->routeIs('*.show'), $this->photos->map(fn ($photo): array => [
                'id' => $photo->id,
                'photo_url' => $photo->photo_url,
                'is_primary' => $photo->is_primary,
            ])),
            'created_at' => $this->when($request->routeIs('*.show'), $this->created_at?->toIso8601String()),
        ];
    }
}
