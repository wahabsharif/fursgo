<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class DevHotReload
{
    /**
     * @return array{livewire: string, layout: string}
     */
    public static function fingerprints(): array
    {
        $livewireParts = [];
        $layoutParts = [];

        foreach (static::livewireRoots() as $root) {
            if (!is_dir($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                $livewireParts[] = static::fileSignature($file);
            }
        }

        foreach (static::layoutRoots() as $root) {
            if (!is_dir($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                if (static::isLivewireView($root, $file)) {
                    continue;
                }

                $layoutParts[] = static::fileSignature($file);
            }
        }

        sort($livewireParts);
        sort($layoutParts);

        return [
            'livewire' => hash('xxh128', implode("\0", $livewireParts)),
            'layout' => hash('xxh128', implode("\0", $layoutParts)),
        ];
    }

    /**
     * @return list<string>
     */
    private static function livewireRoots(): array
    {
        return [
            resource_path('views/livewire'),
            app_path('Livewire'),
        ];
    }

    /**
     * @return list<string>
     */
    private static function layoutRoots(): array
    {
        return [
            resource_path('views'),
            public_path('css'),
        ];
    }

    private static function isLivewireView(string $root, SplFileInfo $file): bool
    {
        if (str_contains(str_replace('\\', '/', $root), '/views/livewire')) {
            return true;
        }

        $relative = str_replace('\\', '/', $file->getRelativePathname());

        return str_starts_with($relative, 'livewire/');
    }

    private static function fileSignature(SplFileInfo $file): string
    {
        $relative = str_replace('\\', '/', $file->getRelativePathname());

        return $relative . ':' . $file->getMTime() . ':' . $file->getSize();
    }
}
