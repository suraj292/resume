<?php

namespace Webvio\FilamentSeo\Concerns;

trait HandlesFilamentSeo
{
    protected array $seoData = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record && $this->record->seo) {
            $data['seo'] = $this->record->seo->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $seoData = $data['seo'] ?? [];
        unset($data['seo']);

        if (!empty($seoData)) {
            $this->record->updateSeo($seoData);
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $seoData = $data['seo'] ?? [];
        unset($data['seo']);

        $this->seoData = $seoData;

        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->seoData)) {
            $this->record->updateSeo($this->seoData);
        }
    }
}
