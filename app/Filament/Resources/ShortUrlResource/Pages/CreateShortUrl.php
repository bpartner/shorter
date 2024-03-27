<?php

namespace App\Filament\Resources\ShortUrlResource\Pages;

use App\Builders\ShortUrlBuilder;
use App\Filament\Resources\ShortUrlResource;
use AshAllenDesign\ShortURL\Classes\Builder;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CreateShortUrl extends CreateRecord
{
    protected static string $resource = ShortUrlResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl;
    }

    /**
     * @throws \AshAllenDesign\ShortURL\Exceptions\ShortURLException
     */
    protected function handleRecordCreation(array $data): Model
    {
        return (new ShortUrlBuilder())->destinationUrl($data['destination_url'])
                                      ->when(
                                          $data['url_key'],
                                          fn(Builder $builder): Builder => $builder->urlKey($data['url_key'])
                                      )
                                      ->singleUse($data['single_use'])
                                      ->forwardQueryParams($data['forward_query_params'])
                                      ->trackVisits($data['track_visits'])
                                      ->redirectStatusCode($data['redirect_status_code'])
                                      ->when(
                                          $data['activated_at'],
                                          fn(Builder $builder): Builder => $builder->activateAt(
                                              Carbon::parse($data['activated_at'])
                                          )
                                      )
                                      ->trackOperatingSystem($data['track_operating_system'])
                                      ->trackOperatingSystemVersion($data['track_operating_system_version'])
                                      ->trackBrowser($data['track_browser'])
                                      ->trackBrowserVersion($data['track_browser_version'])
                                      ->trackRefererURL($data['track_referer_url'])
                                      ->trackDeviceType($data['track_device_type'])
                                      ->userId(auth()->id())
                                      ->title($data['title'])
                                      ->create();
    }
}
