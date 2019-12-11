<?php

namespace Sebastienheyd\BoilerplateMediaManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Image;
use Sebastienheyd\BoilerplateMediaManager\Models\Path;
use Validator;

class MediaManagerController extends Controller
{
    /**
     * EmailController constructor.
     */
    public function __construct()
    {
        $this->middleware('ability:admin,media_manager');
    }

    /**
     * Display the media manager.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');
        $path = $request->path;

        return view('boilerplate-media-manager::index', compact('path', 'type'));
    }

    /**
     * Display the media manager for MCE.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function mce(Request $request)
    {
        $type = $request->query('type', 'all');
        $path = $request->path;

        return view('boilerplate-media-manager::index-mce', compact('path', 'type'));
    }

    /**
     * Display files and directories list.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $mce = $request->input('mce', '0') == 1;
        $type = $request->input('type', 'all');
        $display = $request->input('display', 'list');

        $path = str_replace(route('mediamanager.mce', [], false), '', $request->input('path'));
        $path = str_replace(route('mediamanager.index', [], false), '', $path);

        if (empty($path)) {
            $path = '/';
        }

        $content = new Path($path, $mce);

        if (!$content->exists()) {
            return view('boilerplate-media-manager::error', compact('mce'));
        }

        $list = $content->ls($type);
        $parent = $content->parent();

        return view('boilerplate-media-manager::list', compact('content', 'list', 'parent', 'path', 'mce', 'display'));
    }

    /**
     * Add a new folder.
     *
     * @param Request $request
     *
     * @return string
     */
    public function newFolder(Request $request)
    {
        $path = new Path($request->input('path'));

        return (string) $path->newFolder($request->input('name'));
    }

    /**
     * Display file in browser.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $path = new Path($request->input('path'));
        $file = $request->input('fileName');
        $fPath = $request->input('path').'/'.$file;

        $infos = [
            'download' => '',
            'icon'     => $path->getIcon($file),
            'type'     => $path->detectFileType($file),
            'name'     => basename($file),
            'isDir'    => false,
            'size'     => $path->getFilesize($fPath),
            'url'      => '',
            'time'     => $path->getFileChangeTime($fPath),
        ];

        return view('boilerplate-media-manager::file', compact('filePath', 'infos'));
    }

    /**
     * Delete file(s) or a folder.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function delete(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'path'        => 'required',
            'files'       => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'error'  => implode(' / ', (array) $validation->errors()),
            ]);
        }
        $path = new Path($request->input('path'));

        try {
            foreach ($request->post('files') as $file) {
                $path->delete($file);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a file or a folder.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function rename(Request $request)
    {
        try {
            $path = new Path($request->input('path'));
            $path->rename($request->input('fileName'), $request->input('newName'));

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Upload file(s) to server.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function upload(Request $request)
    {
        $authorizedMimes = implode(',', config('boilerplate.mediamanager.authorized.mimes'));
        $authorizedSize = config('boilerplate.mediamanager.authorized.size');

        $validation = Validator::make($request->all(), [
            'path' => 'required',
            'file' => "required|mimes:$authorizedMimes|max:$authorizedSize",
        ], [
            'files.mimetypes' => 'File has not an authorized type',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'error'  => $validation->errors()->first('file'),
            ]);
        }

        $path = new Path($request->input('path'));

        try {
            $file = $request->file('file');

            if (!$file instanceof UploadedFile) {
                throw new \UnexpectedValueException('File is not instance of UploadedFile');
            }

            $fullPath = $path->upload($file);

            $ext = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'tif'];

            if (in_array(strtolower($file->getClientOriginalExtension()), $ext)) {
                $fInfo = pathinfo($fullPath);
                Image::make($fullPath)->fit(140)->save($fInfo['dirname'].'/thumb_'.$file->getClientOriginalName(), 75);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Paste file(s) into the given path.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function paste(Request $request)
    {
        $path = new Path($request->post('from'));

        $validation = Validator::make($request->all(), [
            'from'        => 'required',
            'files'       => 'required',
            'destination' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'error'  => implode(' / ', (array) $validation->errors()),
            ]);
        }

        try {
            foreach ($request->post('files') as $file) {
                $path->move($file, $request->post('destination'));
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Upload file to server from TinyMCE.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function uploadMce(Request $request)
    {
        $authorizedMimes = implode(',', ['jpg', 'jpeg', 'png', 'gif']);
        $authorizedSize = config('boilerplate.mediamanager.authorized.size');

        $validation = Validator::make($request->all(), [
            'file' => "required|mimes:$authorizedMimes|max:$authorizedSize",
        ]);

        if ($validation->fails()) {
            return response()->json(['status' => 'error', 'error' => $validation->errors()->first('file')]);
        }

        $uploadDir = config('boilerplate.mediamanager.tinymce_upload_dir', 'edition');
        $path = new Path($uploadDir);

        try {
            $file = $request->file('file');

            if (!$file instanceof UploadedFile) {
                throw new \UnexpectedValueException('File is not instance of UploadedFile');
            }

            $fileExt = strtolower($file->getClientOriginalExtension());
            $fileName = uniqid().'.'.$fileExt;

            $fullPath = $path->upload($file, $fileName);

            $ext = ['jpg', 'jpeg', 'gif', 'png'];
            if (in_array($fileExt, $ext)) {
                $fInfo = pathinfo($fullPath);
                Image::make($fullPath)->fit(140)->save($fInfo['dirname'].'/thumb_'.$fileName, 75);
            }

            return response()->json([
                'location' => '/storage/'.$uploadDir.'/'.$fileName,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }
}
