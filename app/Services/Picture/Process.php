<?php

namespace App\Services\Picture;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Process
{
    /**
     * The key used in the multipart / formdata to specify the file
     * @var string
     */
    protected $formFileKey = 'file';
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {

        $this->request = $request;
    }

    /**
     * Process and store the uploaded csv file
     *
     * @return bool
     */
    public function store()
    {
        if ($this->isValidCsv()) {

            try {
                Excel::import(new PictureImport, $this->request->file($this->formFileKey));

                return true;

            } catch (\Exception $e) {

                return false;
            }

        }

        return false;
    }

    /**
     * Check if the uploaded file is a valid csv file
     * @return bool
     */
    protected function isValidCsv()
    {
        return $this->request->has($this->formFileKey) && $this->request->file($this->formFileKey)->getClientMimeType() == 'text/csv';
    }

    /**
     * Set the key that is used in multipart / formdata
     * @param string $key
     */
    public function setFormFileKey(string $key)
    {
        $this->formFileKey = $key;
    }

}
