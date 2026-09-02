<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

trait UploadFileTrait
{
    protected function doUpload(Request $request, string $field, string $path, array $allowedTypes)
    {
        try {
            if (!$request->hasFile($field)) {
                Log::error("No file uploaded for field: $field");
                return 'error';
            }
            $file = $request->file($field);

            if (
                !$file->isValid() ||
                !in_array($file->extension(), $allowedTypes) ||
                $file->getSize() > 2048 * 1024
            ) {
                Log::error("Invalid file upload for field: $field", [
                    'extension' => $file->extension(),
                    'size' => $file->getSize(),
                    'allowedTypes' => $allowedTypes
                ]);
                return 'error';

            }

            $filename = time() . '.' . $file->extension();
            $file->move(public_path($path), $filename);

            return $filename;
        } catch (\Throwable $th) {
            Log::error($th);
            return 'error';
        }
    }

    protected function deleteFile(string $filePath)
    {
        $fullPath = public_path($filePath);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
            return true;
        }
        return 'error';
    }
}
