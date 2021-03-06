<?php
namespace Home\Controller;
use Think\Controller;
//use Think\Upload;


class CourseTypeController extends Controller {
    // 列表
    private $courseType;
    public function __construct(){
        parent::__construct();
        $this->courseType =  M('course_type');
    }
    public function index(){
        
        $Page = new \Think\Page($count,25);

        $count = $this->courseType->where($where)->count();
        $data = $this->courseType->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        // $show = $Page -> show();

        $this->assign('list',$data);

        $this->assign("page",$show);
        $this->display();
    }

    public function add()
    {
    	 $this->display();
    }
    //保存信息
    public function save()
    {
       // 图片（文件）数据保存？
       // 路径从哪里获取？
       //1、把这个的文件保存到我们的项目目录中
        if ($_FILES['thumb']['size']) {
            
                $upload = new Upload();
                $upload->maxSize  = 3145728 ;
                $upload -> autoSub = false;
                $upload->exts  = array('jpg', 'gif', 'png', 'jpeg');
                $upload->rootPath="./Public/upload/news/";
                $up=$upload->upload();

               // 2、数据表里需要保存的是路径
                $data['course_icon']=str_replace('./Public/', "", $upload->rootPath).$up['thumb']['savename'];

                if(!$up) {
                    $this->error($upload->getError());
                }
        }

        // 3、把标题和内容保存到数据表
        $data['type_name']=I('type_name');

        $data['has_sub']=0;
        $data['father_id']=0;

        $this->courseType->add($data);

        $this->success('添加成功！',U('CourseType/index'));

    }
    public function edit()
    {
         $id=I('id');
        // 查询一条信息
        $info=$this->courseType->where("id=$id")->find();
        // echo $article_m->getLastSql();
       
        $this->assign('info',$info);
    	$this->display();
    }
    public function update()
    {
         if ($_FILES['thumb']['size']) {
            
                $upload = new Upload();
                $upload->maxSize  = 3145728 ;
                $upload -> autoSub = false;
                $upload->exts  = array('jpg', 'gif', 'png', 'jpeg');
                $upload->rootPath="./Public/upload/news/";
                $up=$upload->upload();

               // 2、数据表里需要保存的是路径
                $data['course_icon']=str_replace('./Public/', "", $upload->rootPath).$up['thumb']['savename'];

                if(!$up) {
                    $this->error($upload->getError());
                }
        }

        // 3、把标题和内容保存到数据表
        $data['type_name']=I('type_name');
        
        $id=I('id');
        $this->courseType->where("id=$id")->save($data);

        $this->success('修改成功！',U('CourseType/index'));
    }

    public function del(){
        $id = I('id');
        $where = "id={$id}";
        $this->courseType->where($where)->delete();
        echojson("success",true);
    }

    public function show(){
        $id = I('id');
        $data = $this->checkCourse($id);
        if (!$data) {
            echo "404";die;
        }
        $man = M('user')->where("id={$data['create_user']}")->find();
        $data['man'] = $man['user_name'];
        $data['usercourse']=$this->getUserCourse($id);
        $this->assign("data",$data);
        $this->display();

    }

    private function checkCourse($id){
        return $this->course->where("id={$id}")->find();
    }

    private function getUserCourse(){
        $u = M('user_course')->where("courser_id={$id}")->field('user_id')->select();

        foreach ($u as $key => $val) {
            $k[]=$val['user_id'];
        }
        if ($k) {
           $key = implode(',', $k);
           $sql = "select id,user_name from user where id in ({$ukey})";
           $Model = new \Think\Model() ;
           $u =$Model->query($sql);
           return $u;
        }else{
            return false;
        }
    }
}
