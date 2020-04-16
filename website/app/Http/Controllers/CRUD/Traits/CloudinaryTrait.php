<?php

namespace App\Http\Controllers\CRUD\Traits;

use App\Exceptions\ValidationException;
use App\Models\MediaLibrary;
use Cloudinary\Uploader;
use Config;
use Illuminate\Http\Request;
use Cloudder;
use Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait CloudinaryTrait
{
    /**
     * Unauthorized route
     * @param Request $request
     */
    public function store(Request $request)
    {
        throw new NotFoundHttpException();
    }

    /**
     * Upload a file to cloudinary
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function upload(Request $request)
    {
        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
        $data = $request->all();

        if (get_class($this->getRepository()->getModel()) == VideoLibrary::class) {
            $rules = ['file' => 'mimes:mp4|max:1000000|required'];
        } elseif (get_class($this->getRepository()->getModel()) == AudioLibrary::class) {
            $rules = ['file' => 'mimes:mpga,wav|max:1000000|required'];
        } else {
            $rules = ['file' => 'max:200000|required'];
        }

        $validation = Validator($data, $rules);
        if ($validation->fails()) {
            throw new ValidationException(
                $validation->messages(),
                json_encode($validation->messages()->getMessages())
            );
        }

        $itemId = null;
        $cloudinaryError = null;
        $storage = Storage::disk('temp');
        $file = &$data['file'];

        if ($file &&
            $storage->put($file->getClientOriginalName(), file_get_contents($file->getFileInfo()->getPathname()))
        ) {
            try {
                if (get_class($this->getRepository()->getModel()) == VideoLibrary::class) {
                    $timestamp = time();

                    if (filesize($storage->getDriver()->getAdapter()->getPathPrefix() . $file->getClientOriginalName()) <= 20000000) {
                        // Send file to cloudinary
                        $cloudinaryReturn = Cloudder::upload(
                            $storage->getDriver()->getAdapter()->getPathPrefix() . $file->getClientOriginalName(),
                            null,
                            [
                                'folder' => 'academy/videos',
                                'resource_type' => 'video',
                                'timestamp' => $timestamp,
                                'timeout' => 600,
                                'eager' => [
                                    'format' => 'm3u8',
                                    'transformation' => [
                                        ['streaming_profile' => 'hd']
                                    ]
                                ],
                                'eager_async' => false
                            ]
                        );
                        $cloudinaryResult = $cloudinaryReturn->getResult();

                        if ($cloudinaryResult) {
                            if (class_exists('ffmpeg_movie', true)) {
                                $videoInfo = new \ffmpeg_movie($file);
                                $videoDuration = round($videoInfo->getDuration(), 2, PHP_ROUND_HALF_UP);
                                unset($videoInfo);
                            } elseif (class_exists('getID3', true)) {
                                $getID3 = new \getID3();
                                $videoInfo = $getID3->analyze($file);
                                $videoDuration = round($videoInfo['playtime_seconds'], 2, PHP_ROUND_HALF_UP);
                                unset($getID3, $videoInfo);
                            } else {
                                $videoDuration = null;
                            }

                            // Set cloudinary file info to db
                            $fields = [
                                'public_id' => $cloudinaryResult['public_id'],
                                'url' => $cloudinaryResult['secure_url'],
                                'streaming_url' => $cloudinaryResult['eager'][0]['secure_url'],
                                'size' => filesize($file),
                                'duration' => $videoDuration,
                                'internal_title' => preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName()),
                                'active_fr' => true,
                                'active_en' => true
                            ];

                            $item = $this->getRepository()->create($fields);
                            $itemId = $item->id;
                        }
                    } else {
                        // Send large video to cloudinary
                        /** @var Uploader $uploader */
                        $uploader = Cloudder::getUploader();
                        $cloudinaryResult = $uploader->upload_large(
                            $storage->getDriver()->getAdapter()->getPathPrefix() . $file->getClientOriginalName(),
                            [
                                'folder' => 'academy/videos',
                                'resource_type' => 'video',
                                'chunk_size' => 5243000,
                                'timestamp' => $timestamp,
                                'async' => true,
                                'eager_async' => true
                            ]
                        );

                        if ($cloudinaryResult) {
                            if (class_exists('ffmpeg_movie', true)) {
                                $videoInfo = new \ffmpeg_movie($file);
                                $videoDuration = round($videoInfo->getDuration(), 2, PHP_ROUND_HALF_UP);
                                unset($videoInfo);
                            } elseif (class_exists('getID3', true)) {
                                $getID3 = new \getID3();
                                $videoInfo = $getID3->analyze($file);
                                $videoDuration = round($videoInfo['playtime_seconds'], 2, PHP_ROUND_HALF_UP);
                                unset($getID3, $videoInfo);
                            } else {
                                $videoDuration = null;
                            }

                            // Set cloudinary file info to db
                            $fields = [
                                'public_id' => $cloudinaryResult['public_id'],
                                'url' => 'https://res.cloudinary.com/' . Config::get('cloudder.cloudName') .
                                    '/video/upload/v' . $timestamp . '/' . $cloudinaryResult['public_id'],
                                'size' => filesize($file),
                                'duration' => $videoDuration,
                                'internal_title' => preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName()),
                                'active_fr' => true,
                                'active_en' => true
                            ];

                            $item = $this->getRepository()->create($fields);
                            $itemId = $item->id;
                        }
                    }
                } else {
                    // Send file to cloudinary
                    $cloudinaryReturn = Cloudder::unsignedUpload(
                        $storage->getDriver()->getAdapter()->getPathPrefix() . $file->getClientOriginalName(),
                        null,
                        Config::get('cloudder.presetName'),
                        ['resource_type' => 'auto', 'timeout' => 600]
                    );

                    if ($cloudinaryReturn) {
                        $cloudinaryResult = $cloudinaryReturn->getResult();

                        // Set cloudinary file info to db
                        $fields = [
                            'url' => $cloudinaryResult['secure_url'],
                            'width' => $cloudinaryResult['width'],
                            'height' => $cloudinaryResult['height'],
                            'public_id' => $cloudinaryResult['public_id'],
                            'format' => get_class($this->getRepository()->getModel()) == MediaLibrary::class ?
                                ($cloudinaryResult['format'] == 'pdf' ? 1 : 0) : 0,
                            'size' => $cloudinaryResult['bytes'],
                            'internal_title' => preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName()),
                            'duration' => (isset($cloudinaryResult['duration'])) ? $cloudinaryResult['duration'] : null,
                            'artist' => (array_key_exists('artist', $data)) ? $data['artist'] : '',
                            'active_fr' => true,
                            'active_en' => true
                        ];

                        $item = $this->getRepository()->create($fields);
                        $itemId = $item->id;
                    }
                }
            } catch (\Exception $e) {
                $cloudinaryError = $e->getMessage();
            }

            // Remove temp file
            $storage->delete($file->getClientOriginalName());

            if ($cloudinaryError) {
                return response()->json(
                    ['error' => $cloudinaryError],
                    422
                );
            } elseif ($itemId) {
                return response()->json(
                    $this->getResource()::make($this->getRepository()->getById($itemId)),
                    201
                );
            } else {
                throw new ValidationException(null, 'Unable to upload file to Cloudinary. Check your .env file');
            }
        }
    }

    /**
     * Override update the specified resource in storage.
     * @param Request $request
     * @param $itemId
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $itemId)
    {
        if ($request->has('url') || $request->has('width') || $request->has('height') || $request->has('public_id') ||
            $request->has('format') || $request->has('size') || $request->has('duration')
        ) {
            throw new ValidationException(null, 'fields: url, width, height or public_id can not be updated');
        }

        return parent::update($request, $itemId);
    }

    /**
     * Override remove the specified resource from storage.
     * @param $itemId
     * @return mixed
     */
    public function destroy($itemId)
    {
        /*$existingItem = $this->getRepository()->getById($itemId);

        if (get_class($this->getRepository()->getModel()) == VideoLibrary::class) {
            Cloudder::delete('academy/subtitles/video-' . $itemId . '-fr.vtt');
            Cloudder::delete('academy/subtitles/video-' . $itemId . '-en.vtt');
        }*/

        // Remove file from cloudinary
        //Cloudder::delete($existingItem->public_id);

        // Remove file from db
        return parent::destroy($itemId);
    }
}
