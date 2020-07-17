<?php
// +----------------------------------------------------------------------
// | [KyPHP System] Copyright (c) 2020 http://www.kuryun.com/
// +----------------------------------------------------------------------
// | [KyPHP] 并不是自由软件,你可免费使用,未经许可不能去掉KyPHP相关版权
// +----------------------------------------------------------------------
// | Author: fudaoji <461960962@qq.com>
// +----------------------------------------------------------------------


namespace app\admin\controller;

use app\common\controller\BaseCtl;
use think\captcha\Captcha;
use think\Validate;


class Auth extends BaseCtl
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 注册
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function register()
    {
        if (request()->isPost()) {
            $post_data = input('post.');

            Validate::extend([
                'checkCode'=> function ($value) {
                    $captcha = new Captcha();
                    return $captcha ->check($value, 2) ? true : '验证码错误';
                }
            ]);
            $rule = [
                'verify'    => 'checkCode',
                'username'   => 'require|length:3,50',
                'password'  => 'require|length:6,20',
                'repassword'  => 'confirm:password',
                '__token__' => 'require|token',
            ];
            $msg = [
                'username.require'  => '请填写账号',
                'username.length'   => '账号错误',
                'password.require' => '请填写密码',
                'password.length'  => '密码错误',
                'repassword.confirm' => '两次输入的密码不一致',
            ];
            $data = [
                'verify'   => $post_data['verify'],
                'username'   => $post_data['username'],
                'password'  => $post_data['password'],
                'repassword'  => $post_data['repassword'],
                '__token__' => $post_data['__token__'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), null, ['token' => request()->token()]);
                return false;
            }

            unset($data['__token__'], $data['repassword'], $data['verify']);
            $data['ip'] = request()->ip();
            $data['last_time'] = time();
            $data['password'] = ky_generate_password($data['password']);
            $data['group_id'] = intval(config('system.site.default_group_id')); //此处放置游客的权限组id

            $admin = model("admin")->addOne($data);
            if ($admin) {
                session('adminId', $admin['id']);
                $this->success('注册成功!', cookie('redirect_url') ? cookie('redirect_url') : url('system/index/index'));
            }else{
                $this->error('注册失败', '', ['token' => request()->token()]);
            }
        }

        if(session('adminId')){
            $this->redirect('system/index/index');
        }
        return $this->show();
    }

    /**
     * 登录
     * @return bool|\think\response\View
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function login()
    {
        if (request()->isPost()) {
            $post_data = input('post.');

            Validate::extend([
                'checkCode'=> function ($value) {
                    $captcha = new Captcha();
                    return $captcha ->check($value, 2) ? true : '验证码错误';
                }
            ]);
            $rule = [
                'verify'    => 'checkCode',
                'username'   => 'require|length:3,50',
                'password'  => 'require|length:6,20',
                '__token__' => 'require|token',
            ];
            $msg = [
                'username.require'  => '请填写账号',
                'username.length'   => '账号错误',
                'password.require' => '请填写密码',
                'password.length'  => '密码错误',
            ];
            $data = [
                'verify'   => $post_data['verify'],
                'username'   => $post_data['username'],
                'password'  => $post_data['password'],
                '__token__' => $post_data['__token__'],
            ];
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError(), null, ['token' => request()->token()]);
                return false;
            }

            $admin = model("admin")->getOneByMap(['username' => $post_data['username']]);
            if ($admin && $admin['status'] == 1) {
                if(password_verify($post_data['password'], $admin['password'])){
                    model("admin")->updateOne([
                        'id' => $admin['id'],
                        'ip' => request()->ip(),
                        'last_time' => time()
                    ]);
                    session('adminId', $admin['id']);
                    if(!empty($post_data['keeplogin'])){
                        cookie('record_username', $post_data['username']);
                    }
                    $this->success('登录成功!', cookie('redirect_url') ? cookie('redirect_url') : url('system/index/index'));
                }else{
                    $this->error('账号或密码错误', '', ['token' => request()->token()]);
                }
            }else{
                $this->error('用户不存在或已被禁用', '', ['token' => request()->token()]);
            }

        }

        if(session('adminId')){
            $this->redirect('system/index/index');
        }
        return $this->show(['username' => cookie('record_username')]);
    }

    public function logout()
    {
        session(null, config("app_prefix"));
        cookie(null, config("app_prefix"));
        $this->redirect('admin/auth/login');
    }

    public function verify()
    {
        $captcha = new Captcha(['fontSize' => 15]);
        return $captcha->entry(2);
    }
}