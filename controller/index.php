<?php //首页模板入口
if($libs==''){exit('<h3>非法请求</h3>');}//禁止直接访问此接口!

//如果数据库不存在则转入错误提示或注册页面
if(!file_exists('./data/'.$u.'.db3' )){
    $Redirect = 0; //1,账号不存且未隐藏接口时自动载入注册页面! 非1.载入错误提示页面,如果允许注册则显示注册连接!
    if ( $Redirect == 1 && $login =='login'){
        header('location:index.php?c='.$login.'&u='.$u); //库不存在,载入注册页面
    }else{
        if ( $login !='login'){
            $msg = "<h3>未找到账号数据！<br />注册接口已隐藏！<br />请联系管理员！</h3>";
        }else if ($reg ==1 or $reg ==2){//如果允许注册则显示注册连接!
            $msg = "<h3>未找到账号数据，请<a href = 'index.php?c=".$login."&u=".$u."'>注册账号</a>！</h3>";
        }else if ($reg == 0 ){
            $msg = "<h3>未找到账号数据！<br />管理员不允许注册账号！<br />请联系管理员！</h3>";
        }else {
            $msg = "<h3>未找到账号数据！<br />注册配置错误！<br />请联系管理员！</h3>";
        }
        require('./templates/admin/403.php');
    }
    exit;
}
$version =get_version();
//如果已经登录，获取所有分类和链接
$is_login=is_login();
if($is_login){
    //查询分类目录
    $categorys = $db->select('on_categorys','*',[
        "ORDER" =>  ["weight" => "DESC"]
    ]);
    //根据category id查询链接
    function get_links($fid) {
        global $db;
        $fid = intval($fid);
        $links = $db->select('on_links','*',[ 
                'fid'   =>  $fid,
                'ORDER' =>  ["weight" => "DESC"]
            ]);
        return $links;
    }
    //右键菜单标识
    $onenav['right_menu'] = 'admin_menu();';
}
//如果没有登录，只获取公有链接
else{
    //查询分类目录
    $categorys = $db->select('on_categorys','*',[
        "property"  =>  0,
        "ORDER" =>  ["weight" => "DESC"]
    ]);
    //根据category id查询链接
    function get_links($fid) {
        global $db;
        $fid = intval($fid);
        $links = $db->select('on_links','*',[ 
            'fid' =>  $fid,
            'property'  =>  0,
            'ORDER' =>  ["weight" => "DESC"]
        ]);
        return $links;
    }
    //右键菜单标识
    $onenav['right_menu'] = 'user_menu();';
}

//将URL转换为base64编码
function base64($url){
    $urls = parse_url($url);

    //获取请求协议
    $scheme = empty( $urls['scheme'] ) ? 'http://' : $urls['scheme'].'://';
    //获取主机名
    $host = $urls['host'];
    //获取端口
    $port = empty( $urls['port'] ) ? '' : ':'.$urls['port'];

    $new_url = $scheme.$host.$port;
    return base64_encode($new_url);
}
//允许只用参数载入指定主题,如需禁止屏蔽或删掉此段!
$Style= $_GET['Style']==''? '0':$_GET['Style'] ;
$Theme='./templates/'.$_GET['Theme'];
$templates = $Theme.'/index.php';
//如果主题文件存在则使用载入它!
if(file_exists(dirname(dirname(__FILE__)).'/'.$Theme.'/index.php')){require($templates);exit;}


// 载入前台首页模板,如果文件不存在则恢复默认主题!
$Style=getconfig('Style');
$Theme='./templates/'.getconfig('Theme');
$templates = $Theme.'/index.php';
if(!file_exists($templates)){
    Writeconfig('Theme','default');
    Writeconfig('Style','0');
    $Theme='./templates/default';
    $Style='0';
    $templates = './templates/default/index.php';
}
require($templates);
?>