<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\Base\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    protected $banks;

    public function __construct(Bank $banks)
    {
        $this->banks = $banks;
    }

    public function index()
    {
        return view('base.bank.index');
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|string|max:256',
            'description' => 'required|string|max:1024',
        ]);

        $data = $request->all();
        if(array_key_exists('is_hidden', $data) and $data['is_hidden'] === 'on') {
            $data['is_hidden'] = true;
        }else{
            $data['is_hidden'] = false;
        }

        $data = array_only($data, ['name', 'description', 'is_hidden']);

        $success = $this->banks->create($data);
        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function edit()
    {

    }

    public function list()
    {

    }

    public function remove()
    {

    }
}
