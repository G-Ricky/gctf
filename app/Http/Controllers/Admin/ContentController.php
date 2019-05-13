<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function index()
    {
        $this->authorize('listContents');

        return view('admin.content.index');
    }

    public function list()
    {
        $this->authorize('listContents');

        $paginate = Content
            ::select()
            ->with('poster:id,username,nickname')
            ->with('modifier:id,username,nickname')
            ->orderBy('updated_at', 'DESC')
            ->paginate(20, ['*'], 'p')
            ->jsonSerialize();

        $contents = $paginate['data'];
        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $contents,
            'paginate' => $paginate,
        ];
    }

    public function add(Request $request)
    {
        $this->authorize('addContent');

        $data = $this->validate($request, [
            'title'   => 'nullable|string|max:60',
            'type'    => 'required|string|max:16',
            'content' => 'required|string|max:2000',
        ]);

        $data['title'] = $data['title'] ?? '';
        $data['modifier'] = $data['poster'] = Auth::id();

        $content = Content::create($data);

        return [
            'status'  => 200,
            'success' => !!$content,
        ];
    }

    public function edit(Request $request)
    {
        $this->authorize('editContent');

        $data = $this->validate($request, [
            'id'      => 'required|integer|min:1|exists:contents',
            'title'   => 'nullable|string|max:60',
            'type'    => 'required|string|max:16',
            'content' => 'nullable|string|max:2000',
        ]);

        $data['modifier'] = Auth::id();
        $data['title'] = $data['title'] ?? '';
        $data['content'] = $data['content'] ?? '';

        if(is_null($data['type'])) {
            unset($data['title']);
        }

        $affectedRows = Content::findOrFail($data['id'])->update($data);

        return [
            'status'  => 200,
            'success' => !!$affectedRows,
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteContent');

        $data = $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        $success = Content::findOrFail($data['id'])->delete();

        return [
            'status'  => 200,
            'success' => $success
        ];
    }
}
