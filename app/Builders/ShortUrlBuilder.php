<?php

declare(strict_types = 1);

namespace App\Builders;

use App\Models\ShortUrl;
use AshAllenDesign\ShortURL\Classes\Builder;
use AshAllenDesign\ShortURL\Classes\KeyGenerator;
use AshAllenDesign\ShortURL\Classes\Validation;
use AshAllenDesign\ShortURL\Exceptions\ShortURLException;


class ShortUrlBuilder extends Builder
{
    protected KeyGenerator $keyGenerator;
    protected int $userId;
    protected string $title;

    public function __construct(Validation $validation = null, KeyGenerator $keyGenerator = null)
    {
        if (! $validation) {
            $validation = new Validation();
        }

        $validation->validateConfig();

        $this->keyGenerator = $keyGenerator ?? new KeyGenerator();
    }

    public function userId(int $userID): self
    {
        $this->userId = $userID;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;

    }

    public function toArray(): array
    {
        $this->setOptions();

        return [
            'destination_url'                => $this->destinationUrl,
            'default_short_url'              => $this->buildDefaultShortUrl(),
            'url_key'                        => $this->urlKey,
            'single_use'                     => $this->singleUse,
            'forward_query_params'           => $this->forwardQueryParams,
            'track_visits'                   => $this->trackVisits,
            'redirect_status_code'           => $this->redirectStatusCode,
            'track_ip_address'               => $this->trackIPAddress,
            'track_operating_system'         => $this->trackOperatingSystem,
            'track_operating_system_version' => $this->trackOperatingSystemVersion,
            'track_browser'                  => $this->trackBrowser,
            'track_browser_version'          => $this->trackBrowserVersion,
            'track_referer_url'              => $this->trackRefererURL,
            'track_device_type'              => $this->trackDeviceType,
            'activated_at'                   => $this->activateAt,
            'deactivated_at'                 => $this->deactivateAt,
            'user_id'                        => $this->userId ?? auth()->id(),
            'title'                          => $this->title,
        ];
    }

    private function setOptions(): void
    {
        if ($this->secure === null) {
            $this->secure = config('short-url.enforce_https');
        }

        if ($this->secure) {
            $this->destinationUrl = str_replace('http://', 'https://', $this->destinationUrl);
        }

        if ($this->forwardQueryParams === null) {
            $this->forwardQueryParams = config('short-url.forward_query_params') ?? false;
        }

        if (! $this->urlKey) {
            $this->urlKey = $this->keyGenerator->generateKeyUsing($this->generateKeyUsing);
        }

        if (! $this->activateAt) {
            $this->activateAt = now();
        }

        $this->setTrackingOptions();
    }

    private function buildDefaultShortUrl(): string
    {
        $baseUrl = config('short-url.default_url') ?? config('app.url');
        $baseUrl .= '/';

        if ($this->prefix() !== null) {
            $baseUrl .= $this->prefix().'/';
        }

        return $baseUrl.$this->urlKey;
    }

    private function setTrackingOptions(): void
    {
        if ($this->trackVisits === null) {
            $this->trackVisits = config('short-url.tracking.default_enabled');
        }

        if ($this->trackIPAddress === null) {
            $this->trackIPAddress = config('short-url.tracking.fields.ip_address');
        }

        if ($this->trackOperatingSystem === null) {
            $this->trackOperatingSystem = config('short-url.tracking.fields.operating_system');
        }

        if ($this->trackOperatingSystemVersion === null) {
            $this->trackOperatingSystemVersion = config('short-url.tracking.fields.operating_system_version');
        }

        if ($this->trackBrowser === null) {
            $this->trackBrowser = config('short-url.tracking.fields.browser');
        }

        if ($this->trackBrowserVersion === null) {
            $this->trackBrowserVersion = config('short-url.tracking.fields.browser_version');
        }

        if ($this->trackRefererURL === null) {
            $this->trackRefererURL = config('short-url.tracking.fields.referer_url');
        }

        if ($this->trackDeviceType === null) {
            $this->trackDeviceType = config('short-url.tracking.fields.device_type');
        }
    }

    /**
     * @throws \AshAllenDesign\ShortURL\Exceptions\ShortURLException
     */
    public function create(): ShortUrl
    {
        if (! $this->destinationUrl) {
            throw new ShortURLException('No destination URL has been set.');
        }

        $data = $this->toArray();

        $this->checkKeyDoesNotExist();

        $shortURL = new ShortUrl($data);

        if ($this->beforeCreateCallback) {
            value($this->beforeCreateCallback, $shortURL);
        }

        $shortURL->save();

        $this->resetOptions();

        return $shortURL;
    }
}
