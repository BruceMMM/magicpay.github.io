<?php

namespace App\Http\Controllers\Frontend;

use App\User;
use App\Transcation;
use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdatePassword;
use App\Http\Requests\TransferValidate;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class PageController extends Controller
{
    public function home(){
        $user = Auth::user();
        return view('frontend.home',compact('user'));
    }
    public function profile(){
        return view('frontend.profile');
    }
    public function updatePassword()
    {
        return view('frontend.update-password');
    }
    public function updatePasswordstore(UpdatePassword $request)
    {
        $old_password = $request->old_password;
        $new_password = $request->new_password;
        $user = Auth::guard('web')->user();

        if (Hash::check($old_password, $user->password)) {
            $user->password = Hash::make($new_password);
            $user->update();

            $title ='Changed Password';
            $message = 'Your account password is successfully changed.';
            $sourceable_id = $user->id;
            $sourceable_type = User::class;
            $web_link = url('profile');
            $deep_link = [
                'target' => 'profile',
                'parameter' => null
            ];

            Notification::send($user, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type,$web_link,$deep_link));

            return redirect()->route('profile')->with('update','Successfully updated.');
        }
        return back()->withErrors(['old_password' => 'The old password is not correct.'])->withInput();
    }
    public function wallet()
    {
        $authUser = Auth::user();
        return view('frontend.wallet',compact('authUser'));
    }
    public function transfer()
    {
        return view('frontend/transfer');
    }
    public function transferConfirm(TransferValidate $request)
    {

        $from_account = Auth::user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;


        // $str = $to_phone.$amount.$description;
        // $hash_value2 = hash_hmac('sha256', $str, 'magicpay123!@#');
        // if($hash_value !== $hash_value2){
        //     return back()->withErrors(['amount' => 'The given data is invalid'])->withInput();
        // }

        if($request->amount < 1000){
            return back()->withErrors(['amount' => 'The amount must be at least 1000 MMk.'])->withInput();
        }
        if($from_account->phone == $request->to_phone){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }
        if(!$from_account->wallet || !$to_account->wallet){
        return back()->withErrors(['fail' => 'Something wronng. The given data is invalid.'])->withInput();
        }
        if($from_account->wallet->amount < $amount){
            return back()->withErrors(['amount' => 'The amount is not enought.'])->withInput();
        }

        return view('frontend/transfer-confirm',compact('from_account','to_account','amount','description'));
    }
    public function transferComplete(TransferValidate $request)
    {
        if($request->amount < 1000){
            return back()->withErrors(['amount' => 'The amount must be at least 1000 MMk.'])->withInput();
        }
        $from_account = Auth::user();
        if($from_account->phone == $request->to_phone){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail' => 'Something wronng. The given data is invalid.'])->withInput();
        }

        DB::beginTransaction();
        try{
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount',$amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();
            $from_account_transaction = new Transcation();
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();

            $to_account_transaction = new Transcation();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();

            // From Noti
            $title ='E-money Transfered!';
            $message = 'Your e-money transfered '. number_format($amount) .' MMK to '. $to_account->name .' ( '. $to_account->phone .' )';
            $sourceable_id = $from_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $from_account_transaction->trx_id
                ]
            ];

            Notification::send($from_account, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type,$web_link,$deep_link));

            // To Noti
            $title ='E-money Transfered!';
            $message = 'Your e-money transfered '. number_format($amount) .' MMK to '. $from_account->name .' ( '. $from_account->phone .' )';
            $sourceable_id = $to_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ]
            ];

            Notification::send($to_account, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type,$web_link,$deep_link));

            DB::commit();

            return redirect('/transaction/'.$from_account_transaction->trx_id)->with('transfer_success','Successfully transfered.');
        }catch(\Exception $err){
            DB::rollBack();

            return back()->withErrors(['fail' => 'Something wrong.' , $err->getMessage()])->withInput();
        }



    }

    public function transaction(Request $request)
    {
        $authUser = Auth::user();
        $transactions = Transcation::with('user','source')->orderBy('created_at', 'DESC')->where('user_id',$authUser->id);

        if($request->type){
            $transactions = $transactions->where('type', $request->type);
        }
        if($request->date){
            $transactions = $transactions->whereDate('created_at', $request->date);
        }

        $transactions = $transactions->paginate(5);
        return view('frontend.transaction',compact('transactions'));
    }
    public function transactionDetail($trx_id)
    {
        $authUser = Auth::user();
        $transaction = Transcation::with('user','source')->where('user_id',$authUser->id)->where('trx_id', $trx_id)->first();
        return view('frontend.transaction_detail',compact('transaction'));
    }

    public function toAccountVerify(Request $request)
    {
        if(Auth::user()->phone != $request->phone){
            $user = User::where('phone', $request->phone)->first();
            if($user){
                return response()->json([
                    'status' => 'success',
                    'data' => $user
                ]);
            }
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Invalid data',
        ]);
    }

    public function passwordCheck(Request $request)
    {
        if(!$request->password){
            return response()->json([
                'status' => 'fail',
                'message' => ' Please fill your password.'
            ]);
        }
        $authUser = Auth::user();
        if(Hash::check($request->password, $authUser->password)){
            return response()->json([
                'status' => 'success',
                'message' => ' The password is correct.'
            ]);
        }
        return response()->json([
            'status' => 'fail',
            'message' => ' The password is incorrect.'
        ]);
    }
    public function transferHash(Request $request)
    {
        $str = $request->to_phone.$request->amount.$request->description;
        $hash_value = hash_hmac('sha256', $str, 'magicpay123!@#');

        return response()->json([
            'status' => 'success',
            'data' => $hash_value,
        ]);
    }
    public function receiveQR()
    {
        $authUser = Auth::user();

        return view('frontend.receive-qr',compact('authUser'));
    }
    public function scanAndPay(){
        return view('frontend.scan-pay');
    }
    public function scanAndPayForm(Request $request){
        $from_account = Auth::user();
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return back()->withErrors(['fail'=> 'QR code is invalid'])->withInput();
        }

        return view('frontend.scan_and_pay_form', compact('from_account','to_account'));
    }

    public function scanAndPayConfirm(TransferValidate $request)
    {

        $from_account = Auth::user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;


        if($request->amount < 1000){
            return back()->withErrors(['amount' => 'The amount must be at least 1000 MMk.'])->withInput();
        }
        if($from_account->phone == $request->to_phone){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }
        if(!$from_account->wallet || !$to_account->wallet){
        return back()->withErrors(['fail' => 'Something wronng. The given data is invalid.'])->withInput();
        }
        if($from_account->wallet->amount < $amount){
            return back()->withErrors(['amount' => 'The amount is not enought.'])->withInput();
        }

        return view('frontend/scan_and_pay_confirm',compact('from_account','to_account','amount','description'));
    }
    public function scanAndPayComplete(TransferValidate $request)
    {
        if($request->amount < 1000){
            return back()->withErrors(['amount' => 'The amount must be at least 1000 MMk.'])->withInput();
        }
        $from_account = Auth::user();
        if($from_account->phone == $request->to_phone){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return back()->withErrors(['to_phone' => 'This acount is invalid.'])->withInput();
        }

        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        if(!$from_account->wallet || !$to_account->wallet){
            return back()->withErrors(['fail' => 'Something wronng. The given data is invalid.'])->withInput();
        }

        DB::beginTransaction();
        try{
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount',$amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount',$amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();
            $from_account_transaction = new Transcation();
            $from_account_transaction->ref_no = $ref_no;
            $from_account_transaction->trx_id = UUIDGenerate::trxId();
            $from_account_transaction->user_id = $from_account->id;
            $from_account_transaction->type = 2;
            $from_account_transaction->amount = $amount;
            $from_account_transaction->source_id = $to_account->id;
            $from_account_transaction->description = $description;
            $from_account_transaction->save();

            $to_account_transaction = new Transcation();
            $to_account_transaction->ref_no = $ref_no;
            $to_account_transaction->trx_id = UUIDGenerate::trxId();
            $to_account_transaction->user_id = $to_account->id;
            $to_account_transaction->type = 1;
            $to_account_transaction->amount = $amount;
            $to_account_transaction->source_id = $from_account->id;
            $to_account_transaction->description = $description;
            $to_account_transaction->save();

            // From Noti
            $title ='E-money Transfered!';
            $message = 'Your e-money transfered '. number_format($amount) .' MMK to '. $to_account->name .' ( '. $to_account->phone .' )';
            $sourceable_id = $from_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $from_account_transaction->trx_id
                ]
            ];

            Notification::send($from_account, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type,$web_link,$deep_link));

            // To Noti
            $title ='E-money Transfered!';
            $message = 'Your e-money transfered '. number_format($amount) .' MMK to '. $from_account->name .' ( '. $from_account->phone .' )';
            $sourceable_id = $to_account_transaction->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$from_account_transaction->trx_id);
            $deep_link = [
                'target' => 'transaction_detail',
                'parameter' => [
                    'trx_id' => $to_account_transaction->trx_id
                ]
            ];

            Notification::send($to_account, new GeneralNotification($title, $message, $sourceable_id, $sourceable_type,$web_link,$deep_link));

            DB::commit();

            return redirect('/transaction/'.$from_account_transaction->trx_id)->with('transfer_success','Successfully transfered.');
        }catch(\Exception $err){
            DB::rollBack();

            return back()->withErrors(['fail' => 'Something wrong.' , $err->getMessage()])->withInput();
        }



    }
}
