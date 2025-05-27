<?php
namespace App\Traits;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
trait UploadImageTrait
{
    public function uploadImage(UploadedFile $image, string $folder): ?string
    {
        $imageName = uniqid() . '_' . $image->getClientOriginalName();
        $image->move(public_path("uploads/{$folder}"), $imageName);
        return asset("uploads/{$folder}/{$imageName}");
    }
    public function deleteImage(string $imageUrl, string $default = 'default.jpg'): void
    {
        $imageName = basename($imageUrl);
        if ($imageName !== $default) {
            $imagePath = public_path("uploads/{$this->getFolderFromUrl($imageUrl)}/" . $imageName);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
    }
    protected function getFolderFromUrl(string $url): string
    {
        $parsed = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', $parsed);
        return $segments[count($segments) - 2] ?? '';
    }
}
