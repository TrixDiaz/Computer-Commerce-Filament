<?php

namespace App\Filament\Resources\CmsResource\Pages;

use App\Filament\Resources\CmsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCms extends EditRecord
{
    protected static string $resource = CmsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
