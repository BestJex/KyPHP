<?php
// +----------------------------------------------------------------------
// | [KyPHP System] Copyright (c) 2020 http://www.kuryun.com/
// +----------------------------------------------------------------------
// | [KyPHP] 并不是自由软件,你可免费使用,未经许可不能去掉KyPHP相关版权
// +----------------------------------------------------------------------
// | Author: fudaoji <fdj@kuryun.cn>
// +----------------------------------------------------------------------

/**
 * Created by PhpStorm.
 * Script Name: Setting.php
 * Create: 2020/6/11 下午10:16
 * Description: 公众号相关设置
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace app\mp\controller;

use app\admin\controller\FormBuilder;

class Setting extends Base
{
    private $tabList;
    /**
     * @var \app\common\model\Setting
     */
    private $settingM;
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->settingM = model('mpSetting');
        $this->tabList = [
            'wxpay' => [
                'title' => '微信支付',
                'href' => url('index', ['name' => 'wxpay'])
            ]
        ];
    }

    /**
     * 设置
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author: fudaoji<fdj@kuryun.cn>
     */
    public function index(){
        $current_name = input('name', 'wxpay');
        $setting = $this->settingM->getOneByMap(['mpid' => $this->mpId, 'name' => $current_name], true, 1);
        if(request()->isPost()){
            $post_data = input('post.');
            unset($post_data['__token__']);
            if(empty($setting)){
                $res = $this->settingM->addOne([
                    'mpid' => $this->mpId,
                    'name' => $current_name,
                    'title' => $this->tabList[$current_name]['title'],
                    'value' => json_encode($post_data)
                ]);
            }else{
                $res = $this->settingM->updateOne([
                    'id' => $setting['id'],
                    'value' => json_encode($post_data)
                ]);
            }
            if($res){
                $this->success('保存成功');
            }else{
                $this->error('保存失败，请刷新重试', '', ['token' => request()->token()]);
            }
        }

        if(empty($setting)){
            $data = [];
        }else{
            $data = json_decode($setting['value'], true);
        }
        $builder = new FormBuilder();
        switch ($current_name){
            case 'wxpay':
                $builder->setTemplate('common/form')
                    ->setTip('支付授权目录：' . request()->domain() . '/mp/payment/')
                    ->addFormItem('appid', 'text', 'AppId', 'AppId', [], 'required maxlength=150')
                    ->addFormItem('secret', 'text', 'Secret', 'Secret', [], 'required maxlength=150')
                    ->addFormItem('merchant_id', 'text', '商户ID', '商户ID', [], 'required maxlength=100')
                    ->addFormItem('key', 'text', '支付秘钥', '支付秘钥', [], 'required maxlength=32 minlength=32')
                    ->addFormItem('cert_path', 'textarea', '支付证书cert', '请在微信商户后台下载支付证书，用记事本打开apiclient_cert.pem，并复制里面的内容粘贴到这里。', [], 'maxlength=20000')
                    ->addFormItem('key_path', 'textarea', '支付证书key', '请在微信商户后台下载支付证书，使用记事本打开apiclient_key.pem，并复制里面的内容粘贴到这里。', [], ' maxlength=20000')
                    ->addFormItem('rsa_path', 'textarea', 'RSA公钥', '企业付款到银行卡需要RSA公钥匙');
                break;
        }
        $builder->setFormData($data);
        return $builder->show(['tab_nav' => ['tab_list' => $this->tabList, 'current_tab' => $current_name]]);

    }
}