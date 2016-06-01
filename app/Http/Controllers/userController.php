<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class userController extends Controller
{
    
 	public function usermanagement()
    {
        $users=User::getUser();
        return view('admin.usermanagement')->withUsers($users);  
        
    }
    public function authedit($id)
    {
        $user=User::find($id);
        if($user->auth==2)
        {
            $user->auth=1;
        }
        else
        {
            $user->auth=2;
        }
        if($user->save())
           echo 1;
        else
        {
            echo 0;
        }
        return;
    }
   


}
