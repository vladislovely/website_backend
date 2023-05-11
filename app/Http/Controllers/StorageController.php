<?php

namespace App\Http\Controllers;

use http\Exception\RuntimeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate(
            [
                'file' => ['required', 'image', 'max:2048']
            ]
        );

        if ($request->hasFile('file') && $request->file('file') instanceof UploadedFile) {
            $file     = $request->file('file');
            $name     = time() . '-' . $file->getClientOriginalName();
            $filePath = 'images/' . $name;

            $savedFile = Storage::put($filePath, file_get_contents($file->getRealPath()));

            if ($savedFile) {
                $url             = getenv('AWS_ENDPOINT') . getenv('AWS_BUCKET') . '/';
                $storageFilePath = Storage::path($filePath);

                \Log::info('File successful saved:', ['path' => $url . $storageFilePath]);

                return response()->json(['message' => 'File success uploaded', 'full_path' => $url . $storageFilePath, 'relative_path' => $storageFilePath]);
            }
            throw new RuntimeException('Файл не был загружен, попробуйте позднее');
        }

        throw new RuntimeException('Пожалуйста передайте файл и повторите действие еще раз');
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(
            [
                'path' => ['required', 'string']
            ]
        );

        if (Storage::delete($request->only('path'))) {
            return response()->json(['message' => 'Файл успешно удален']);
        }
        throw new RuntimeException('Что-то пошло не так, не смог удалить файл из s3, повторите позднее');
    }
}
