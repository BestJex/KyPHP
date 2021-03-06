<?php
/**
 * Created by PhpStorm.
 * Script Name: WxaSubmitAudit.php
 * Create: 2018/9/3 18:03
 * Description:将第三方提交的代码包提交审核
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/Mini_Programs/code/submit_audit.html
 * Author: Jason<dcq@kuryun.cn>
 */
namespace ky\MiniPlatform\Request;

use ky\MiniPlatform\RequestCheckUtil;

class WxaSubmitAudit
{
    private $url = "https://api.weixin.qq.com/wxa/submit_audit";
    private $itemList = array();
    private $getParams = array();
    private $postParams = array();
    private $versionDesc = '';

    /**
     * @return string
     */
    public function getVersionDesc()
    {
        return $this->versionDesc;
    }

    /**
     * @param string $versionDesc
     */
    public function setVersionDesc($versionDesc)
    {
        $this->versionDesc = $versionDesc;
        $this->postParams['version_desc'] = $this->versionDesc;
    }

    /**
     * 获取请求url
     * @author Jason<dcq@kuryun.cn>
     */
    public function getUrl(){
        return $this->url;
    }

    /**
     * 设置请求地址
     * @param string $url
     * @author Jason<dcq@kuryun.cn>
     */
    public function setUrl($url) {
        $this->url = $url;
    }

    /**
     * 设置itemList
     * @param array $itemList
     * @author Jason<dcq@kuryun.cn>
     */
    public function setItemList($itemList) {
        $this->itemList = $itemList;
        $this->postParams['item_list'] = $itemList;
    }

    /**
     * 获取itemList
     * @author Jason<dcq@kuryun.cn>
     */
    public function getItemList() {
        return $this->itemList;
    }

    /**
     * get请求参数
     * @author Jason<dcq@kuryun.cn>
     */
    public function getParams() {
        return $this->getParams;
    }

    /**
     * post请求参数
     * @author Jason<dcq@kuryun.cn>
     */
    public function postParams() {
        return $this->postParams;
    }

    /**
     * 参数验证
     * @author Jason<dcq@kuryun.cn>
     */
    public function check() {
        RequestCheckUtil::checkArray($this->itemList, "itemList");
    }
}