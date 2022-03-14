<?php

namespace Sebastienheyd\BoilerplateMediaManager\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Image;
use Sebastienheyd\BoilerplateMediaManager\Models\Breadcrumb;
use Sebastienheyd\BoilerplateMediaManager\Models\Path;
use UnexpectedValueException;
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
     * Delete file(s) or a folder.
     *
     * @param  Request  $request
     * @return JsonResponse|ResponseFactory
     */
    public function delete(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'path'  => 'required',
            'files' => 'required',
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
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the media manager.
     *
     * @param  Request  $request
     * @return View
     */
    public function index(Request $request)
    {
        // Store query string to build correct back link when path does not exists
        $queryString = '';
        if (preg_match("#\?(.*)$#", $request->fullUrl(), $m)) {
            parse_str($m[1], $v);
            unset($v['selected']);
            $queryString = http_build_query($v);
        }
        session()->put('queryString', $queryString);

        if ($request->get('mce')) {
            return $this->mce($request);
        }

        $type = $request->get('type', 'all');
        $path = $request->path;

        return view('boilerplate-media-manager::index', compact('path', 'type'));
    }

    /**
     * Display files and directories list.
     *
     * @param  Request  $request
     * @return Factory|View
     */
    public function list(Request $request)
    {
        $type = $request->input('type', 'all');
        $display = $request->input('display', 'list');

        $path = str_replace(route('mediamanager.index', [], false), '', $request->input('path'));

        if (empty($path)) {
            $path = '/';
        }

        $content = new Path($path);

        if (! $content->exists()) {
            return view('boilerplate-media-manager::error', ['query' => session()->get('queryString')]);
        }

        if ($request->input('clearcache', 'false') === 'true') {
            $content->clearCache();
        }

        $list = $content->ls($type);

        $breadcrumb = new Breadcrumb($path);
        $parent = $breadcrumb->parent();

        return view(
            'boilerplate-media-manager::list',
            compact('content', 'list', 'parent', 'path', 'display', 'breadcrumb')
        );
    }

    /**
     * Display the media manager for MCE.
     *
     * @param  Request  $request
     * @return View
     */
    public function mce(Request $request)
    {
        $path = $request->path;

        if ($selected = $request->input('selected')) {
            $baseUrl = config('boilerplate.mediamanager.base_url', '/');
            $pInfo = pathinfo($selected);
            $path = preg_replace('#^'.$baseUrl.'#', '', $pInfo['dirname']);
        }

        $data = [
            'type'        => $request->input('type', 'all'),
            'path'        => $path ?? '/',
            'field'       => $request->input('field'),
            'return_type' => $request->input('return_type'),
            'selected'    => $selected,
        ];

        return view('boilerplate-media-manager::index-mce', $data);
    }

    /**
     * Add a new folder.
     *
     * @param  Request  $request
     * @return JsonResponse|ResponseFactory
     */
    public function newFolder(Request $request)
    {
        $path = new Path($request->input('path'));
        $path->newFolder($request->input('name'));

        return response()->json(['status' => 'success']);
    }

    /**
     * Paste file(s) into the given path.
     *
     * @param  Request  $request
     * @return JsonResponse|ResponseFactory
     */
    public function paste(Request $request)
    {
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

        $path = new Path($request->post('from'));

        try {
            foreach ($request->post('files') as $file) {
                $path->move($file, $request->post('destination'));
            }

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a file or a folder.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function rename(Request $request)
    {
        if (! $request->isXmlHttpRequest()) {
            abort(403);
        }

        $validator = Validator::make($request->post(), [
            'path'     => 'required',
            'type'     => ['required', Rule::in(['folder', 'file'])],
            'fileName' => 'required',
            'newName'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()]);
        }

        try {
            $path = new Path($request->post('path'));
            $path->rename($request->input('fileName'), $request->input('newName'));

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Upload file(s) to server.
     *
     * @param  Request  $request
     * @return JsonResponse|ResponseFactory
     *
     * @throws Exception
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

            if (! $file instanceof UploadedFile) {
                throw new UnexpectedValueException('File is not instance of UploadedFile');
            }

            $fullPath = $path->upload($file);

            $ext = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'tif'];

            if (in_array(strtolower($file->getClientOriginalExtension()), $ext)) {
                $fInfo = pathinfo($fullPath);
                Image::make($fullPath)->fit(150)->save($fInfo['dirname'].'/thumb_'.$file->getClientOriginalName(), 75);
            }

            $path->clearCache();

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }
}
