<?php

namespace Sebastienheyd\BoilerplateMediaManager\Controllers;

use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;
use Sebastienheyd\BoilerplateMediaManager\Models\Breadcrumb;
use Sebastienheyd\BoilerplateMediaManager\Models\Path;
use UnexpectedValueException;
use Validator;

class MediaManagerController
{
    /**
     * Delete file(s) or a folder.
     *
     * @param  Request  $request
     * @return JsonResponse|ResponseFactory
     */
    public function delete(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'path' => 'required',
            'files' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => implode(' / ', (array) $validation->errors()),
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

        // Multi-sort: read JSON array, fallback to single sort/order for compatibility
        if ($request->has('sorts')) {
            $sortCriteria = json_decode($request->input('sorts'), true);
            if (! is_array($sortCriteria) || empty($sortCriteria)) {
                $sortCriteria = [['field' => 'name', 'order' => 'asc']];
            }
        } else {
            $sortCriteria = [['field' => $request->input('sort', 'name'), 'order' => $request->input('order', 'asc')]];
        }

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

        $list = $this->sortList($content->ls($type), $sortCriteria);

        // Build indexed sorts map for the view: ['name' => ['order' => 'asc', 'priority' => 1], ...]
        $sorts = [];
        foreach ($sortCriteria as $i => $criteria) {
            $sorts[$criteria['field']] = ['order' => $criteria['order'], 'priority' => $i + 1];
        }

        $breadcrumb = new Breadcrumb($path);
        $parent = $breadcrumb->parent();

        return view(
            'boilerplate-media-manager::list',
            compact('content', 'list', 'parent', 'path', 'display', 'breadcrumb', 'sorts')
        );
    }

    /**
     * Sort a list of files and directories.
     *
     * @param  array  $list
     * @param  array  $sortCriteria
     * @return array
     */
    private function sortList($list, $sortCriteria)
    {
        $allowedFields = ['name' => 'name', 'size' => 'bytes', 'date' => 'ts', 'type' => 'type'];
        $stringFields = ['name', 'type'];

        // Resolve field names and filter invalid criteria
        $resolved = [];
        foreach ($sortCriteria as $criteria) {
            $field = $allowedFields[$criteria['field']] ?? null;
            if ($field !== null) {
                $resolved[] = ['field' => $field, 'order' => $criteria['order'] ?? 'asc'];
            }
        }

        if (empty($resolved)) {
            $resolved = [['field' => 'name', 'order' => 'asc']];
        }

        $dirs = array_filter($list, fn ($item) => $item['isDir']);
        $files = array_filter($list, fn ($item) => ! $item['isDir']);

        $sorter = function ($a, $b) use ($resolved, $stringFields) {
            foreach ($resolved as $criteria) {
                $field = $criteria['field'];
                $valA = $a[$field] ?? 0;
                $valB = $b[$field] ?? 0;

                if (in_array($field, $stringFields)) {
                    $cmp = strnatcasecmp($valA, $valB);
                } else {
                    $cmp = $valA <=> $valB;
                }

                if ($criteria['order'] === 'desc') {
                    $cmp = -$cmp;
                }

                if ($cmp !== 0) {
                    return $cmp;
                }
            }

            return 0;
        };

        usort($dirs, $sorter);
        usort($files, $sorter);

        return array_merge($dirs, $files);
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
            'type' => $request->input('type', 'all'),
            'path' => $path ?? '/',
            'field' => $request->input('field'),
            'return_type' => $request->input('return_type'),
            'selected' => $selected,
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
            'from' => 'required',
            'files' => 'required',
            'destination' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => implode(' / ', (array) $validation->errors()),
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
            'path' => 'required',
            'type' => ['required', Rule::in(['folder', 'file'])],
            'fileName' => 'required',
            'newName' => 'required',
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
     * Search for files by name.
     *
     * @param  Request  $request
     * @return Factory|View|JsonResponse
     */
    public function search(Request $request)
    {
        $term = $request->input('term', '');

        if (mb_strlen($term) < 3) {
            return response()->json(['status' => 'error', 'message' => 'Search term too short']);
        }

        $type = $request->input('type', 'all');
        $display = $request->input('display', 'list');

        if ($request->has('sorts')) {
            $sortCriteria = json_decode($request->input('sorts'), true);

            // Validate that sortCriteria is a non-empty array with valid structure
            if (! is_array($sortCriteria) || empty($sortCriteria)) {
                $sortCriteria = [['field' => 'name', 'order' => 'asc']];
            } else {
                // Validate each criterion has required fields
                $validCriteria = [];
                foreach ($sortCriteria as $criterion) {
                    $isValid = is_array($criterion)
                        && isset($criterion['field']) && is_string($criterion['field'])
                        && isset($criterion['order']) && is_string($criterion['order']);

                    if ($isValid) {
                        $validCriteria[] = $criterion;
                    }
                }

                // Fallback if no valid criteria found
                $sortCriteria = empty($validCriteria)
                    ? [['field' => 'name', 'order' => 'asc']]
                    : $validCriteria;
            }
        } else {
            $sortCriteria = [['field' => 'name', 'order' => 'asc']];
        }

        $path = new Path('/');
        $results = $this->sortList($path->search($term, $type), $sortCriteria);

        $sorts = [];
        foreach ($sortCriteria as $i => $criteria) {
            $sorts[$criteria['field']] = ['order' => $criteria['order'], 'priority' => $i + 1];
        }

        return view('boilerplate-media-manager::search-results', compact('results', 'term', 'display', 'sorts'));
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
                'error' => $validation->errors()->first('file'),
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
                Image::read($fullPath)
                    ->cover(150, 150)
                    ->save($fInfo['dirname'].'/thumb_'.$file->getClientOriginalName(), 75);
            }

            $path->clearCache();

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'error' => $e->getMessage()]);
        }
    }
}
