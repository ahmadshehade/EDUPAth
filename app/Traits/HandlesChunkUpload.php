<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

trait HandlesChunkUpload {


    /**
     * Summary of handleChunkUpload
     * @param Request $request
     * @param array $keys
     * @param string $tmpDir
     * @return array<array>
     */
    public function handleChunkUpload(Request $request, array $keys, string $tmpDir = 'tmp'): array {
        $uploadedPaths = [];

        foreach ($keys as $key) {
            if (!$request->hasFile($key)) continue;

            $uploadedPaths[$key] = [];
            $files = is_array($request->file($key)) ? $request->file($key) : [$request->file($key)];

            foreach ($files as $file) {
                try {
                    $receiver = new FileReceiver($key, $request, HandlerFactory::classFromRequest($request));

                    if ($receiver->isUploaded()) {
                        $save = $receiver->receive();
                        if ($save->isFinished()) {
                            $uploadedPaths[$key][] = $save->getFile();
                        }
                    } else {

                        $uploadedPaths[$key][] = $file;
                    }
                } catch (\Exception $e) {
                    Log::error("Chunk upload failed for key {$key}: " . $e->getMessage());
                }
            }
        }

        return $uploadedPaths;
    }
}
