<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use OSS\Core\OssException;
use OSS\OssClient;

class OssService
{
    protected $ossClient;
    protected $bucket;

    public function __construct()
    {
        $this->ossClient = new OssClient(
            env('ALIYUN_OSS_ACCESS_ID'),
            env('ALIYUN_OSS_ACCESS_KEY'),
            env('ALIYUN_OSS_ENDPOINT')
        );

        $this->bucket = env('ALIYUN_OSS_BUCKET');
    }

    /**
     * Upload a file to Aliyun OSS with a random filename.
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @return string|null The uploaded file path or null on failure
     */
    public function upload(UploadedFile $file, string $folderPath = 'uploads'): ?string
    {
        $ext = $file->getClientOriginalExtension();
        $randomFilename = Str::uuid() . '.' . $ext;
        $ossPath = 'musicsync/' . $folderPath . '/' . $randomFilename;

        try {
            $this->ossClient->uploadFile($this->bucket, $ossPath, $file->getPathname());
            return $ossPath;
        } catch (OssException $e) {
            logger()->error('OSS upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from OSS by path.
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        try {
            $this->ossClient->deleteObject($this->bucket, 'musicsync/' . $path);
            return true;
        } catch (OssException $e) {
            logger()->error('OSS delete failed: ' . $e->getMessage());
            return false;
        }
    }

    //delete all files in a folder
    public function deleteFolder(string $folderPath): bool
    {
        try {
            $this->ossClient->deleteObjects($this->bucket, $this->ossClient->listObjects($this->bucket, $folderPath)->getObjectList());
            return true;
        } catch (OssException $e) {
            logger()->error('OSS delete folder failed: ' . $e->getMessage());
            return false;
        }
    }
}
