<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Setting\InvalidDataTypeException;
use App\Exceptions\Setting\InvalidJsonDataException;
use App\Exceptions\Setting\InvalidSerializedDataException;
use App\Exceptions\Setting\UnsupportedTypeException;
use App\Http\Controllers\Controller;
use App\Models\Admin\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'type'        => 'required|string|in:array,bool,boolean,date,float,int,integer,null,object,stdclass,string',
            'description' => 'nullable|string|max:250',
        ]);

        foreach ($data as $key => $value) {
            if(is_null($value)) {
                $data[$key] = '';
            }
        }

        $this->typeCheck($data['type'], $data['value']);

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
            'id'          => 'required|integer',
            'name'        => 'required|string|max:128',
            'value'       => 'nullable|string|max:2000',
            'type'        => 'required|string|in:array,bool,boolean,date,float,int,integer,null,object,stdclass,string',
            'description' => 'nullable|string|max:250',
        ]);

        foreach ($data as $key => $value) {
            if(is_null($value)) {
                unset($data[$key]);
            }
        }

        $data['value'] = $data['value'] ?? '';

        $success = false;
        $message = '';
        DB::transaction(function() use($data, &$success, &$message) {
            $setting = Setting::where('id', '=', $data['id'])->first();

            if(is_null($setting)) {
                $message = __('The setting does not exist!');
            } else {
                $this->typeCheck($data['type'], $data['value']);
                $success = $setting->update($data);
                $message = $success ? __('Success') : __('Fail');
            }
        });

        return [
            'status'  => 200,
            'success' => $success,
            'message' => $message
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteSetting');

        $data = $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $success = false;
        $message = '';

        DB::transaction(function() use ($data, &$success, &$message) {
            $setting = Setting::where('name', '=', $data['name'])->first();
            if(is_null($setting)) {
                $message = __('The setting does not exist!');
            } else {
                $success = $setting->delete();
                $message = $success ? __('Success') : __('Fail');
            }
        });

        return [
            'status'  => 200,
            'success' => $success,
            'message' => $message
        ];
    }

    private function typeCheck($type, $value) {
        switch($type) {
            case 'string':
            case 'null':
                break;
            case 'array':
            case 'stdclass':
                if(is_null($data = json_decode($value))) {
                    throw new InvalidJsonDataException(__('Invalid json data format!'));
                }
                if($type === 'array' && !is_array($data)) {
                    throw new InvalidDataTypeException(__('Given object is not array!'));
                }
                if($type === 'stdclass' && is_array($data)) {
                    throw new InvalidDataTypeException(__('Given object is not stdClass!'));
                }
                break;
            case 'object':
                if(@unserialize($value) === false) {
                    throw new InvalidSerializedDataException(__('Invalid serialized data format!'));
                }
                break;
            case 'bool':
            case 'boolean':
                if(!in_array($value, [
                    '0', '1', 'true', 'false', 'on', 'off'
                ], true)) {
                    throw new InvalidDataTypeException(__('Invalid boolean data format!'));
                }
                break;
            case 'int':
            case 'integer':
                if(!is_numeric($value) || strpos($value,".") !== false) {
                    throw new InvalidDataTypeException(__('Invalid integer data format!'));
                }
                break;
            case 'float':
                if(!is_numeric($value)) {
                    throw new InvalidDataTypeException(__('Invalid float data format!'));
                }
                break;
            case 'date':
                if(preg_match('/^\\d\\d(\\d\\d)?-\\d\\d-\\d\\d \\d\\d:\\d\\d:\\d\\d$/', $value) === 0) {
                    throw new InvalidDataTypeException(__('Invalid date format!'));
                }
                break;
            default:
                throw new UnsupportedTypeException(__('Unsupported data type!'));
        }
    }
}
