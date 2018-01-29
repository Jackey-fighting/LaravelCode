<?php

namespace App\Http\Controllers;
use DB;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except'=>['query','insert','dbTransaction','allColumn','first','selectField',
                'dbChunk','innerJoin','leftJoin','limit','usersPaginate','tryCatch',
                'response',
            ]
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    //DB::select()方法来进行原生的查询
    public function query(){
        $users = DB::select('select * from users where id in(?,?)', [1,2]);
        dump($users);
        foreach ($users as $v) {
            echo 'name: '.$v->name.'<br/>';
            echo 'email: '.$v->email.'<br/>';
        }
        $created_at = DB::table("users")->max('created_at');
        dump($created_at);
    }
    //DB::insert()方法来进行原生数据的插入
    public function insert(){
        $res = DB::insert('insert into users(id,name,email,password,remember_token) values(?,?,?,?,?)', [2,'Jackey2','22@qq.com','111111','2222']);
        dump($res);
    }
    //Laravel 自带的数据库事务 DB::transaction(function(){})
    public function dbTransaction(){
       DB::transaction(function(){
            DB::table('users')->where(['id'=>1])->update(['name'=>'Jackey_1']);
        },5);//第二个参数是用户处理死锁时，重新试着访问5次
       dump('使用 DB::transaction(function(){}) 这个来事务进行自我的提交或回滚');
    }
    //也可以手动控制事务提交
    public function handleTransaction(){
        DB::beginTransaction();
        DB::rollBack();
        DB::commit();
    }
    //从数据表中获取所有的数据列
    public function allColumn(){
        $users = DB::table("users")->get();
        return view('test.index', compact('users'));
    }
    //获取表中的第一行数据
    public function first(){
        $user = DB::table("users")->first();
        dump($user);
    }
    //对于处理上千的数据的时候才用chunk()来进行分块管理
    public function dbChunk(){
        DB::table("users")->orderBy('id')->chunk(100, function($users){
            //代码处理

            return false;//这里返回false是停止后续块
        });
    }
    //在DB::table("users")->select()->get();中使用select()方法来选择获取的字段
    public function selectField(){
        $users = DB::table("users")->select('name', 'email as E_mail')->distinct()->get();
        return view('test.selectField', compact('users'));
    }
    //inner join 
    public function innerJoin(){
       $users = DB::table('users')
                ->join('migrations','users.id','=','migrations.id')
                ->join('oauth_personal_access_clients','users.id','=','oauth_personal_access_clients.id')
                ->select('users.*','migrations.migration','oauth_personal_access_clients.created_at')
                ->get();
        dump($users);
    }
    //leftJoin
    public function leftJoin(){
        $users = DB::table('users')
                ->leftJoin('migrations','users.id','=','migrations.id')
                ->select('users.*','migrations.migration')
                ->get();
        $crossJoin = DB::table('users')->crossJoin('migrations')->get();
        dump($users);
        foreach ($crossJoin as $cross) {
            echo $cross->id. ' ' .$cross->name. ' ' .$cross->migration.'<br/>';
        }
    //使用高级的join，使用闭包函数，来进行条件约束。
        $joinClause = DB::table('users')->join('migrations', function($join){
                $join->on('users.id', '=', 'migrations.id');
        })->get();
        dump($joinClause);
    }
    //limit
    public function limit(){
        $user = DB::table('users')->offset(1)->limit(1)->get();
        dump($user);
    }
    //当满足某个条件时使用when(),当满足第一个条件为true时促发
    public function isWhen(){
        $role = $reuqest->input('role');
        $users = DB::table('table')
         ->when($role, function($query) use ($role){
            return $query->where('role_id', $role);
         })
         ->get();
         return $users;
    }
    //paginate 分页
    public function usersPaginate(){
        $users = DB::table('users')->select('name', 'email')->paginate(1);
        //dump($users);exit;
        return view('test.usersPaginate', compact('users'));
    }
    //测试try catch
    public function tryCatch(){
        try{
            $result = DB::table('users')->find(10);
            //判断是否能获取到数据
            if (is_null($result)) {
                 throw new \Exception('很抱歉，数据找不到了', 404);
            }
        }catch(\Exception $e){
           echo $e->getCode().', '.$e->getLine();
        }
    }
    
    //获取单个值 或 列
    pulic function singleValue(){
        $emailValue = DB::table('users')->where(['id'=>1])->value('email');//获取email值
        $emailColumnValue = DB::table('users')->pluck('email');//获取email列值
    }
   
    //获取所有的model数据，并存到缓存10分钟
    public function userRemember(){
        User::remember(10, 'users.all')->get();
    }

}
