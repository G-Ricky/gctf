<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Bank;
use App\Models\Admin\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    public function index()
    {
        $this->authorize('listBanks');

        return view('admin.bank.index');
    }

    public function add(Request $request)
    {
        $this->authorize('addBank');

        $data = $this->validate($request, [
            'name'        => 'required|string|max:250',
            'description' => 'required|string|max:1000',
        ]);

        $success = Bank::create($data);

        return [
            'status'  => 200,
            'success' => !!$success
        ];
    }

    public function edit(Request $request)
    {
        $this->authorize('editBank');

        $data = $this->validate($request, [
            'id'          => 'required|integer',
            'name'        => 'required|string|max:250',
            'description' => 'required|string|max:1000'
        ]);

        $success = false;
        $message = __('global.success');
        DB::transaction(function() use($data, &$success, &$message) {
            $bank = Bank::findOrFail($data['id']);
            $success = (bool)$bank->update($data);
            $message = $success ? $message : __('global.fail');
        });

        return [
            'status'  => 200,
            'success' => $success,
            'message' => $message
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteBank');

        $data = $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $success = false;
        $message = __('global.success');
        DB::transaction(function() use ($data, &$message, &$success) {
            $bank = Bank::findOrFail($data['id']);
            $count = Challenge::where('bank', '=', $data['id'])->count();
            if($count > 0) {
                $message = '题库下的挑战数不为0，不能删除';
            } else {
                $success = $bank->delete();
                $message = $success ? $message : __('global.fail');
            }
        });

        return [
            'status'  => 200,
            'success' => $success,
            'message' => $message
        ];
    }

    public function list(Request $request)
    {
        $this->authorize('listBanks');

        $data['page'] = $request->query('page', 1);
        $data['pageSize'] = $request->query('pageSize', 20);

        $paginate = Bank::orderBy('created_at')
        ->withCount('challenges')
        ->paginate()
        ->jsonSerialize();

        $data = $paginate['data'];
        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $data,
            'paginate' => $paginate
        ];
    }
}
