<?php

namespace App\Helper;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeHelper
{
    /**
     * Generate QR code image and save to storage. Returns the storage path.
     * Uses SVG format (no Imagick required - works with GD only).
     */
    public static function generateAndSave(string $content, string $directory = 'offers/qr/', int $size = 200): string
    {
        $filename = \Illuminate\Support\Str::uuid()->toString() . '.svg';
        $path = rtrim($directory, '/') . '/' . $filename;

        $imageContent = QrCode::format('svg')
            ->size($size)
            ->generate($content);

        Storage::disk('public')->put($path, (string) $imageContent);

        return $path;
    }

    /**
     * Get full URL for a stored QR code path.
     */
    public static function url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
