<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Transcation;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\TransferValidate;
use App\Http\Resources\ProfileResource;
use App\Notifications\GeneralNotification;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\TransactionDetailResource;
use App\Http\Resources\NotificationDetailResource;

class PageController extends Controller
{
    public function profile()
    {
        $user = auth()->user();

        $data = new ProfileResource($user);

        return success('success',$data);
    }

    public function transaction(Request $request){
        $authUser = auth()->user();
        $transactions = Transcation::with('user','source')->orderBy('created_at', 'DESC')->where('user_id',$authUser->id);

        if($request->data){
            $transactions = $transactions->whereDate('created_at', $request->date);
        }

        if($request->type){
            $transactions = $transactions->where('type', $request->type);
        }

        $transactions = $transactions->paginate(5);

        $data =TransactionResource::collection($transactions)->additional(['result' => 1, 'message' => 'success']);

        return ($data);
    }
    public function transactionDetail($trx_id){
        $authUser = auth()->user();
        $transaction = Transcation::with('user', 'source')->where('user_id', $authUser->id)->where('trx_id', $trx_id)->firstOrFail();
        $data =new TransactionDetailResource($transaction);
        return success('success', $data);
    }
    public function notification(){
        $authUser = auth()->user();
        $notifications = $authUser->notifications()->paginate(5);

        return NotificationResource::collection($notifications)->additional(['result' => 1 , 'message' => 'success']);
    }
    public function notificationDetail($id){
        $authUser = auth()->user();
        $notification = $authUser->notifications()->where('id',$id)->firstOrFail();
        $notification->markAsRead();

        $data = new NotificationDetailResource($notification);
        return success('success', $data);
    }
    public function toAccountVerify(Request $request){

        if($request->phone){
            $authUser =auth()->user();
            if($authUser->phone != $request->phone){
                $user = User::where('phone',$request->phone)->first();
                if($user){
                    return success('success',['name' => $user->name , 'phone' => $user->phone ]);
                }
            }
        }

        return fail('Invalid number.', null);
    }
    public function transferConfirm(TransferValidate $request)
    {

        $from_account = auth()->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac('sha256', $str, 'magicpay123!@#');
        if($hash_value !== $hash_value2){
            return fail('The given data is invalid123.', null);
        }

        if($request->amount < 1000){
            return fail('The amount must be at least 1000 MMk.', null);
        }
        if($from_account->phone == $request->to_phone){
            return fail('This acount is invalid.', null);
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return fail('This acount is invalid.', null);
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return fail('Something wrong. The given data is invalid.', null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('The amount is not enought.', null);
        }

        return success('success',[
            'from_account_name' => $from_account->name,
            'from_account_phone' => $from_account->phone,

            'to_account_name' => $to_account->name,
            'to_account_phone' => $to_account->phone,

            'amount' => $amount,
            'description' => $description,
            'hash_value' => $hash_value,
        ]);
    }
    public function transferComplete(TransferValidate $request)
    {
        if(!$request->password){
            return fail('Please fill your password.',null);

        }
        $authUser = auth()->user();
        if(!Hash::check($request->password, $authUser->password)){
            return fail('The password is incorred.',null);
        }

        $from_account = auth()->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac('sha256', $str, 'magicpay123!@#');
        // if($hash_value !== $hash_value2){
        //     return fail('The given data is invalid123.', null);
        // }

        if($request->amount < 1000){
            return fail('The amount must be at least 1000 MMk.', null);
        }
        if($from_account->phone == $request->to_phone){
            return fail('This acount is invalid.', null);
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return fail('This acount is invalid.', null);
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return fail('Something wrong. The given data is invalid.', null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('The amount is not enought.', null);
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

            return success('success' , ['trx_id' => $from_account_transaction->trx_id ]);

        }catch(\Exception $err){
            DB::rollBack();

            return fail('Something wrong.' , $err->getMessage() , null);
        }
    }
    public function scanAndPayForm(Request $request)
    {
        $from_account = auth()->user();
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return fail('QR code is invalid', null);
        }

        return success('success',[
            'from_name' => $from_account->name,
            'from_phone' => $from_account->phone,
            'to_name' => $to_account->name,
            'to_phone' => $to_account->phone,
        ]);
    }
    public function scanAndPayConfirm(TransferValidate $request)
    {

        $from_account = auth()->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        if($request->amount < 1000){
            return fail('The amount must be at least 1000 MMk.', null);
        }
        if($from_account->phone == $request->to_phone){
            return fail('This acount is invalid.', null);
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return fail('This acount is invalid.', null);
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return fail('Something wrong. The given data is invalid.', null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('The amount is not enought.', null);
        }


        return success('success',[
            'from_account_name' => $from_account->name,
            'from_account_phone' => $from_account->phone,

            'to_account_name' => $to_account->name,
            'to_account_phone' => $to_account->phone,

            'amount' => $amount,
            'description' => $description,
            'hash_value' => $hash_value,
        ]);
    }
    public function scanAndPayComplete(TransferValidate $request)
    {
        if(!$request->password){
            return fail('Please fill your password.',null);

        }
        $authUser = auth()->user();
        if(!Hash::check($request->password, $authUser->password)){
            return fail('The password is incorred.',null);
        }

        $from_account = auth()->user();
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        $hash_value = $request->hash_value;

        $str = $to_phone . $amount . $description;
        $hash_value2 = hash_hmac('sha256', $str, 'magicpay123!@#');
        if($hash_value !== $hash_value2){
            return fail('The given data is invalid123.', null);
        }

        if($request->amount < 1000){
            return fail('The amount must be at least 1000 MMk.', null);
        }
        if($from_account->phone == $request->to_phone){
            return fail('This acount is invalid.', null);
        }
        $to_account = User::where('phone', $request->to_phone)->first();
        if(!$to_account){
            return fail('This acount is invalid.', null);
        }
        if(!$from_account->wallet || !$to_account->wallet){
            return fail('Something wrong. The given data is invalid.', null);
        }
        if($from_account->wallet->amount < $amount){
            return fail('The amount is not enought.', null);
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

            return success('success' , ['trx_id' => $from_account_transaction->trx_id ]);

        }catch(\Exception $err){
            DB::rollBack();

            return fail('Something wrong.' , $err->getMessage() , null);
        }
    }

}
