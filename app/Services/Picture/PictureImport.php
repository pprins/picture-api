<?php

namespace App\Services\Picture;

use App\Models\Picture;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\MimeTypes;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PictureImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @var MimeTypes
     */
    public $mimetypes;

    public function __construct()
    {
        $this->mimetypes = new MimeTypes();
    }

    /**
     * @param Collection $csvRows
     */
    public function collection(Collection $csvRows): void
    {
        foreach ($csvRows as $line) {

            // Validate the csv row. Continue to the next if the validation fails
            $validator = validator($line->toArray(), $this->rules());
            if ($validator->fails()) {

                continue;
            }

            // Find or create a new unsaved image model
            $picture = Picture::firstOrNew([
                'pic_title' => $line['picture_title']
            ]);

            // if the picture is new, or the url has changed, or the picture exists but has no filename (failed fetching the contents)
            // then download the picture content
            if ($this->shouldCacheImage($line, $picture)) {

                // Cache the picture to our local disk
                // and save the model.
                if ($fileName = $this->cachePicture($line)) {

                    $picture->user_id = auth()->user()->id;
                    $picture->pic_filename = $fileName;

                    // fille the description and url attributes
                    $picture->pic_url = $line['picture_url'];
                    $picture->pic_description = $line['picture_description'];

                    $picture->save();

                }
            }
        }
    }

    /**
     * Check if we should cache the requested image. This is the case with a new image or
     * an update of an existing image.
     *
     * @param Collection $row
     * @param Picture $picture
     * @return bool
     */
    protected function shouldCacheImage(Collection $row, Picture $picture): bool
    {
        return $picture->hasUpdatedPictureUrl($row['picture_url']);
    }

    /**
     * Cache a remote picture to our local / s3 / service-x picure disk
     * fetch the correct extension based on file contents.
     * Return the filename of the cached picture.
     *
     * @param Collection $csvLine
     * @return string|null
     */
    protected function cachePicture(Collection $csvLine)
    {
        // Check if the url is reachable
        if ($this->isReachableUrl($csvLine['picture_url'])) {

            // Get the contents of the file
            $fileContents = file_get_contents($csvLine['picture_url']);

            // File info handle to get the correct extension based on the mime type
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $extension = Arr::first($this->mimetypes->getExtensions($finfo->buffer($fileContents)));

            // Get a unique filename based on the picture title plus extension
            $pictureFilename = md5($csvLine['picture_title']) . '.' . $extension;

            // Store the picture to our picture disk in a user specific directory
            Storage::disk('pictures')
                ->put(join('/', [auth()->user()->id, $pictureFilename]), $fileContents);

            // @todo We might add file deduplication based on a md5_file hash to save storage.

            return $pictureFilename;

        }

        return null;

    }

    /**
     * Check if the url is reachable by using a simple get_headers.
     * See: https://www.php.net/manual/en/function.get-headers.php#112652
     * @param $picture_url
     * @return bool
     */
    private function isReachableUrl($picture_url): bool
    {
        try {
            $headers = get_headers($picture_url);

            return intval(substr($headers[0], 9, 3)) < 400;

        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' url:' . $picture_url);
        }

        return false;
    }

    /**
     * When the import of a row fails
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // On import error silently skip it
    }

    /**
     * Validate CSV input fields
     * @return array
     */
    public function rules(): array
    {
        return [
            'picture_title' => 'required|min:1',
            'picture_url' => 'required|min:1',
        ];
    }

}
