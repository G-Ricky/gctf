<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $this->authorize('listSettings');

        return view('admin.setting.index');
    }

    public function list()
    {
        $this->authorize('listSettings');

        $paginate = Setting
            ::select()
            ->paginate(20, ['*'], 'p')
            ->jsonSerialize();

        $settings = $paginate['data'];
        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $settings,
            'paginate' => $paginate,
        ];
    }

    public function add(Request $request)
    {
        $this->authorize('addSetting');

        $data = $this->validate($request, [
            'name'        => 'required|string|max:128|unique:settings',
            'value'       => 'nullable|string|max:2000',
            'type'        => 'required|string|in:stdclass,array,object,bool,boolean,int,integer,float,string,null',
            'description' => 'nullable|string|max:250',
        ]);

        $setting = Setting::create($data);

        return [
            'status'  => 200,
            'success' => !!$setting,
        ];
    }

    public function edit(Request $request)
    {
        $this->authorize('editSetting');

        $data = $this->validate($request, [
            'name'        => 'required|string|max:128',
            'value'       => 'nullable|string|max:2000',
            'type'        => 'nullable|string|in:stdclass,array,object,bool,boolean,int,integer,float,string,null',
            'description' => 'nullable|string|max:250',
        ]);

        $affectedRows = Setting::where('name', '=', $data['name'])->update($data);

        return [
            'status'  => 200,
            'success' => !!$affectedRows,
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteSetting');

        $data = $this->validate($request, [
            'name' => 'required|string|max:128',
        ]);

        $success = Setting::where('name', '=', $data['name'])->delete();

        return [
            'status'  => 200,
            'success' => $success
        ];
    }
}
