<?php

use App\Models\Gejala;
use App\Models\Penyakit;
use App\Models\Pestisida;
use App\Models\Pupuk;
use App\Models\User;
use App\Support\ProjectImage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:migrate-to-project', function () {
    $targets = [
        ['model' => User::class, 'column' => 'foto_profil', 'directory' => 'profil'],
        ['model' => Gejala::class, 'column' => 'gambar', 'directory' => 'gejala', 'enabled' => fn () => Gejala::supportsImages()],
        ['model' => Penyakit::class, 'column' => 'gambar', 'directory' => 'penyakit'],
        ['model' => Pupuk::class, 'column' => 'gambar', 'directory' => 'pupuk'],
        ['model' => Pestisida::class, 'column' => 'gambar', 'directory' => 'pestisida'],
    ];

    $migrated = 0;
    $skipped = 0;

    foreach ($targets as $target) {
        if (isset($target['enabled']) && $target['enabled']() === false) {
            $skipped++;

            continue;
        }

        $modelClass = $target['model'];
        $column = $target['column'];
        $directory = $target['directory'];

        $modelClass::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->chunkById(100, function ($rows) use ($column, $directory, &$migrated, &$skipped) {
                foreach ($rows as $row) {
                    $currentPath = ProjectImage::normalize($row->{$column});

                    if ($currentPath === null || str_starts_with($currentPath, 'uploads/')) {
                        $skipped++;

                        continue;
                    }

                    $legacyPath = ProjectImage::legacyStoragePath($currentPath);
                    if ($legacyPath === null || ! Storage::disk('public')->exists($legacyPath)) {
                        $skipped++;

                        continue;
                    }

                    $extension = pathinfo($legacyPath, PATHINFO_EXTENSION) ?: 'bin';
                    $newRelativeDirectory = trim('uploads/'.$directory, '/');
                    $newAbsoluteDirectory = public_path($newRelativeDirectory);

                    if (! is_dir($newAbsoluteDirectory)) {
                        mkdir($newAbsoluteDirectory, 0755, true);
                    }

                    $newFilename = (string) Str::uuid().'.'.strtolower($extension);
                    $newRelativePath = $newRelativeDirectory.'/'.$newFilename;
                    copy(Storage::disk('public')->path($legacyPath), public_path($newRelativePath));

                    $row->{$column} = $newRelativePath;
                    $row->save();
                    $migrated++;
                }
            });
    }

    $this->info("Migrasi selesai. Berhasil: {$migrated}, dilewati: {$skipped}");
})->purpose('Pindahkan path gambar lama ke public/uploads di dalam proyek');
