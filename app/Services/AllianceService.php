<?php

namespace App\Services;

use App\Exceptions\AllianceException;
use App\Models\Alliance;
use App\Repositories\AllianceRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AllianceService
{
    public function __construct(
        private readonly AllianceRepository $alliances,
    ) {
    }

    public function create(array $data): Alliance
    {
        return Alliance::create($data);
    }

    public function update(Alliance $alliance, array $data): Alliance
    {
        $alliance->update($data);

        return $alliance;
    }

    public function delete(Alliance $alliance): void
    {
        $logoPath = $alliance->logo_url;

        try {
            $alliance->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23503') {
                throw new AllianceException(
                    'No se puede eliminar la alianza porque tiene comercios o miembros vinculados.',
                    422
                );
            }
            throw $e;
        }

        if ($logoPath) {
            Storage::disk('public')->delete($logoPath);
        }
    }

    public function updateLogo(Alliance $alliance, UploadedFile $file): Alliance
    {
        if ($alliance->logo_url) {
            Storage::disk('public')->delete($alliance->logo_url);
        }

        $path = "alliances/alliance_{$alliance->id}";
        $filename = 'logo.' . $file->getClientOriginalExtension();
        $file->storeAs($path, $filename, 'public');

        $alliance->update(['logo_url' => "$path/$filename"]);

        return $alliance;
    }

    public function deleteLogo(Alliance $alliance): Alliance
    {
        if ($alliance->logo_url) {
            Storage::disk('public')->delete($alliance->logo_url);
        }

        $alliance->update(['logo_url' => null]);

        return $alliance;
    }
}
