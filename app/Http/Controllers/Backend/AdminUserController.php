<?php

namespace App\Http\Controllers\Backend;

use App\AdminUser;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreAdminUser;
use App\Http\Requests\UpdateAdminUser;

class AdminUserController extends Controller
{
    public function index(){
        return view('backend.admin_user.index');
    }

    public function ssd(){
        $data = AdminUser::query();

        return Datatables::of($data)
        ->editColumn('user_agent',function($each){
            if($each->user_agent){
                $agent =new Agent();
                $agent->setUserAgent($each->user_agent);
                $device = $agent->device();
                $platform = $agent->platform();
                $browser = $agent->browser();

                return '<table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Device</td><td>'.$device.'</td>
                                </tr>
                                <tr>
                                    <td>Platform</td><td>'.$platform.'</td>
                                </tr>
                                <tr>
                                    <td>Browser</td><td>'.$browser.'</td>
                                </tr>
                            </tbody>
                        </table>';
            }
            return '-';
        })
        ->editColumn('created_at',function($each){
            return Carbon::parse($each->created_at)->format('d-M-Y (H:i:s)');
        })
        ->addColumn('action',function($each){
            $edit_icon = '<a href="'.route('admin.admin-user.edit',$each->id).'" class="text-warning"><i class="fas fa-edit"></i></a>';
            $delete_icon = '<a href="" class="text-danger delete" data-id="'.$each->id.'"><i class="fas fa-trash-alt"></i></a>';

            return '<div class="action-icon">'.$edit_icon . $delete_icon.'</div>';
        })
        ->rawColumns(['user_agent','action'])
        ->make(true);
    }

    public function create(){
        return view('backend.admin_user.create');
    }
    public function store(StoreAdminUser $request){
        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->route('admin.admin-user.index')->with('create','You have successfully created');
    }
    public function edit($id){
        $admin_user = AdminUser::findOrFail($id);
        return view('backend.admin_user.edit',compact('admin_user'));
    }
    public function update($id,UpdateAdminUser $request){
        $admin_user = AdminUser::findOrFail($id);
        $admin_user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? Hash::make($request->password) : $admin_user->password,
        ]);
        return redirect()->route('admin.admin-user.index')->with('update','You have successfully update');
    }
    public function destroy($id){
        $admin_user = AdminUser::findOrFail($id);
        $admin_user->delete();

        return 'success';
    }

}

