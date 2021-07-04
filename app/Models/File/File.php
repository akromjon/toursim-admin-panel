<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\File\Traits\FileRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\File\Traits\Methods;
use Illuminate\Http\UploadedFile;


class File extends Model
{
    use SoftDeletes, FileRelation, Methods;

    protected $table = "files";

    protected $guarded = [];

    const FILE_RESOURCE = '/uploads/files';

    const ALLOWED_FILES = [
        'pdf',
        'docs',
        'docx'
    ];

    const ALLOWED_IMAGES = [
        'jpg',
        'jpeg',
        'png',
        'svg'
    ];

    const FILE_TYPES = [
        'image',
        'document'
    ];

    const ALLOWED_SIZE = 2048;


    /**

     * @param  Illuminate\Http\Request
     * @return void
     */
    public function storeFile(object $request)
    {


        if (!($request instanceof UploadedFile)) {

            return false;
        }

        $path = '';

        $file_type = '';

        $size = $request->getSize();

        $extension = $request->extension();

        $file_resource = SELF::FILE_RESOURCE;

        if (in_array($extension, SELF::ALLOWED_IMAGES)) {

            $path = $request->storeAs(

                '/images/stored',

                \Carbon\Carbon::now()->format('d_m_Y_H_i_s') . ".{$extension}"
            );

            $path = $file_resource . "/{$path}";

            $file_type = SELF::FILE_TYPES[0];
        }


        if (in_array($extension, SELF::ALLOWED_FILES)) {

            $path = $request->storeAs(
                'documents/stored',
                \Carbon\Carbon::now()->format('d_m_Y_H_i_s') . ".{$extension}"
            );

            $path = $file_resource . "/{$path}";

            $file_type = SELF::FILE_TYPES[1];
        }

        $file = $this->update([
            'fileable_type' => get_class($this->fileable),
            'fileable_id' => $this->fileable->id,
            'file_type' => $file_type,
            'path' => $path,
            'size' => $size
        ]);

        return $file;
    }

    public function restoreFile(int $id)
    {

        if (!$file = $this->where('id', $id)->withTrashed()->first()) {

            return false;
        }

        $type = $file->type == SELF::FILE_TYPES[0] ? 'images' : 'documents';

        $restorable_path = "/uploads/files/{$type}/stored/" . basename($file->path);

        if (!Storage::disk('public_path')->exists($restorable_path)) {

            Storage::disk('public_path')->move($file->path,  $restorable_path);
        }


        $file->update([
            'path' => $restorable_path,
            'deleted_at' => null,
        ]);

        return $file;
    }

    public function deleteFile(int $id)
    {
        if (!$file = $this->find($id)) {

            return false;
        }

        $type = $file->type == SELF::FILE_TYPES[0] ? 'images' : 'documents';

        $deleteable_path = "/uploads/files/{$type}/deleted/" . basename($file->path);

        if (!Storage::disk('public_path')->exists($deleteable_path)) {

            Storage::disk('public_path')->move($file->path,  $deleteable_path);
        }


        $file->update([
            'path' =>  $deleteable_path,
        ]);


        $file->delete();

        return $file;
    }
}
