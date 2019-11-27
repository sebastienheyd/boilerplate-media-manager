<?php

namespace Sebastienheyd\BoilerplateMediaManager\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Image;
use Sebastienheyd\BoilerplateMediaManager\Models\Path;

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
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
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
     * Delete a file or a folder.
     *
     * @param Request $request
     */
    public function delete(Request $request)
    {
        try {
            $path = new Path($request->input('path'));
            $path->delete($request->input('fileName'));

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a file or a folder.
     *
     * @param Request $request
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $authorizedMimes = implode(',', config('mediamanager.authorized.mimes'));
        $authorizedSize = config('mediamanager.authorized.size');

        $this->validate($request, [
            'path' => 'required',
            'file' => "required|mimes:$authorizedMimes|max:$authorizedSize",
        ], [
            'files.mimetypes' => 'File has not an authorized type',
        ]);

        $path = new Path($request->input('path'));

        try {
            $file = $request->file('file');
            $fullPath = $path->upload($file);

            $ext = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'tif'];

            if (in_array(strtolower($file->getClientOriginalExtension()), $ext)) {
                $fInfo = pathinfo($fullPath);
                Image::make($fullPath)->fit(140)->save($fInfo['dirname'].'/thumb_'.$file->getClientOriginalName(), 75);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error']);
        }
    }
}
