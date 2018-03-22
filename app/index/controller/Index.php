<?php
namespace index\controller;

use index\model\User;
use index\model\Likes;
use index\model\Collect;
use framework\Verify;
use framework\Upload;
use index\model\Song;
use framework\Page;

class Index extends Controller
{
	//主页
	public function index()
	{
		if (isset($_SESSION['id'])) {
			$user = new User;
			$id = $_SESSION['id'];
			$data = $user->userList("id = $id");
			$this->assign('data', $data);
		} else {
			$this->assign('data', null);
		}
		$this->display();
	}
	//随便看看
	public function look()
	{
		if (isset($_SESSION['id'])) {
			$user = new User;
			$id = $_SESSION['id'];
			$data = $user->userList("id = $id");
			$this->assign('data', $data);
		} else {
			$this->assign('data', null);
		}
		$this->display('look.html');
	}
	//博客
	public function show()
	{
		$user = new User;
		$song = new Song;
		/*分页*/
			$totalarr   = $song->songCount('rid = 0');
			$total = (int)$totalarr[0]['count'];
			$pageobj = new Page($total);
			$result  = $pageobj->urlList();//四条url
			$page    = $pageobj->getPage();//1
			$count   = ceil($total / 5);//60                           
			$limit   = $pageobj->limit();//0,5
			$data_song  = $song->songAll('rid = 0',$limit);//博客所有详情
			/*分页的各个数据*/
			$this->assign('data_song', $data_song);
			$this->assign('result', $result);
			$this->assign('page', $page);
			$this->assign('count', $count);
		
		if (isset($_SESSION['id'])) {
			$user = new User;
			$id = $_SESSION['id'];
			$data = $user->userList("id = $id");
			$this->assign('data', $data);
		} else {
			$this->assign('data', null);
		}
		
		$this->display('show.html');
	}
	//发博客
	public function announce()
	{
		if (isset($_SESSION['id'])) {
			$user = new User;
			$id = $_SESSION['id'];
			$data = $user->userList("id = $id");
			$this->assign('data', $data);
		} else {
			$this->assign('data', null);
		}
		$this->display('announce.html');
	}
	//博主个人
	public function me()
	{
		$user = new User;
		$song    = new Song();
		if (isset($_SESSION['id'])) {
			$id = $_SESSION['id'];
			$data = $user->userList("id = $id");
			/*分页*/
			$totalarr   = $song->songCount('cid = 8 and rid = 0');
			$total = (int)$totalarr[0]['count'];
			$pageobj = new Page($total);
			$result  = $pageobj->urlList();//四条url
			$page    = $pageobj->getPage();//1
			$count   = ceil($total / 5);//60                           
			$limit   = $pageobj->limit();//0,5
			$data_song  = $song->songAll('cid = 8 and rid = 0',$limit);//博客所有详情
			/*分页的各个数据*/
			$this->assign('result', $result);
			$this->assign('page', $page);
			$this->assign('count', $count);

			$this->assign('data', $data);
			$this->assign('data_song', $data_song);
		} else {
			/*分页*/
			$totalarr   = $song->songCount('cid = 8 and rid = 0');
			$total = (int)$totalarr[0]['count'];
			$pageobj = new Page($total);
			$result  = $pageobj->urlList();//四条url
			$page    = $pageobj->getPage();//1
			$count   = ceil($total / 5);//60                           
			$limit   = $pageobj->limit();//0,5
			$data_song  = $song->songAll('cid = 8 and rid = 0',$limit);//博客所有详情
			/*分页的各个数据*/
			$this->assign('result', $result);
			$this->assign('page', $page);
			$this->assign('count', $count);
			$this->assign('data_song', $data_song);
		}
		$this->display('me.html');
	}
	//song详情
	public function details()
	{
		if (isset($_SESSION['id'])) {
			$user    = new User();
			$song    = new Song();
			$collect = new Collect();
			$likes   = new Likes();
			$id = $_SESSION['id'];
			$data = $user->userList("id = $id");//用户的所有数据
			$this->assign('data', $data);
			if (isset($_GET['tid'])) {
				$tid = $_GET['tid'];
				$_SESSION['tid'] = $tid;
			} else {
				$tid = $_SESSION['tid'];
			}
			$data_song  = $song->songAll("id = $tid");//博客所有详情
			if (!empty($data_song)) {
				$data_song[0]['time'] = date("Y-m-d H:i:s",$data_song[0]['time']);
				/*分页*/
				$totalarr   = $song->songCount("rid = $tid");
				$total = (int)$totalarr[0]['count'];
				$pageobj = new Page($total);
				$result  = $pageobj->urlList();//四条url
				$page    = $pageobj->getPage();//1
				$count   = ceil($total / 5);//60                           
				$limit   = $pageobj->limit();//0,5
				/*分页的各个数据*/
				$this->assign('result', $result);
				$this->assign('page', $page);
				$this->assign('count', $count);
				$data_songs = $song->songAll("rid = $tid",$limit);//评论所有详情
				foreach ($data_songs as $key => $value) {
					$uid = $value['cid'];
					$data_user = $user->userList("id = $uid");
					$data_songs[$key]['username'] = $data_user[0]['username'];//查出评论人的信息
					$data_songs[$key]['time'] = date("Y-m-d H:i:s",$value['time']);
				}
				/*点赞*/
				if (isset($_GET['likes'])) {
					$datac['likes'] = $_GET['likes'];
					$praise = $song->songCount("id = $tid",'praise')[0]['count'];
					if ($datac['likes'] =='1') {
						$song->songUpdate("id = $tid",['praise'=>$praise+1]);
					} else {
						$song->songUpdate("id = $tid",['praise'=>$praise-1]);
					}
					$like = $datac['likes'];
					$datas1 = $likes->likesList("bid = $tid and uid = $id");
					// var_dump($datas1);
					if (empty($datas1)) {
						$likes->likesInsert(['bid' => (int)$tid, 'uid' => (int)$id]);
					} else {
						$likes->likesUpdate("bid = $tid and uid = $id","likes = $like");
					}
				} else {
					$datas1 = $likes->likesList("bid = $tid and uid = $id");
					if (empty($datas1)) {
						$datac['likes'] = '0';
					} else {
						$datac['likes'] = $datas1[0]['likes'];
					}
				}
				/*收藏*/
				if (isset($_GET['collect'])) {
					$datac['collect'] = $_GET['collect'];
					$collects = $datac['collect'];
					$datas2  = $collect->collectList("bid = $tid and uid = $id");
					// var_dump($datas2);
					if (empty($datas2)) {
						$collect->collectInsert(['bid' => (int)$tid, 'uid' => (int)$id]);
					} else {
						$collect->collectUpdate("bid = $tid and uid = $id", "collect = $collects");
					}
				} else {
					$datas2 = $collect->collectList("bid = $tid and uid = $id");
					if (empty($datas2)) {
						$datac['collect'] = '0';
					} else {
						$datac['collect'] = $datas2[0]['collect'];
					}
				}

				$this->assign('data_song', $data_song);
				$this->assign('data_songs', $data_songs);
				$this->assign('datac', $datac);
				//var_dump($data_songs);
				$this->display('details.html');
			} else {
				$check = '所看博客已过期或已被删除';
				$this->assign('check', $check);
				$this->assign('is', '1');
				$this->display('jump.html');
			}
		} else {
			$check = '未登录...';
			$this->assign('is', '3');
			$this->assign('check', $check);
			$this->display('jump.html');
		}
	}
	//发表博客
	public function song()
	{
		if (isset($_SESSION['id'])) {
			$id = $_SESSION['id'];
			$data = $_POST;
			if (empty($data['title'])||empty($data['label'])||empty($data['song_name'])||empty($data['content'])||empty($data['icon'])) {
				$this->assign('is', '4');
				$check = '所填信息缺失，发表失败!';
			} else {
				$data['cid'] = (int)$id;
				//上传封面
				$upload = new Upload();
				$src = $upload->uploadFile('icon'); 
				$data['icon'] = $src;
				//插入信息
				$data['time'] = time();
				$song = new Song();
				$tid = $song->songInsert($data);
				if (is_int($tid)) {
					$check = '分享心情成功!';
					$_SESSION['tid'] = $tid;
					$this->assign('tid', $tid);
					$this->assign('is', '5');
				} else {
					$this->assign('is', '4');
					$check = '所填信息错误，发表失败!';
				}
			}
		} else {
			$check = '未登录...';
			$this->assign('is', '3');
	    }
		$this->assign('check', $check);
		$this->display('jump.html');
		//var_dump($_POST);
	}
	//登录注册界面
	public function reslog()
	{
		$this->display('reslog.html');
	}
	//验证登录注册
	public function check()
	{			
		$arr = $_POST;
		$user = new User;
		$this->assign('is', '0');
		//注册
		if (isset($_POST['register'])) {

			$id = $user->userInsert($arr);
			if (is_int($id)) {
				$_SESSION['id'] = $id;
				$this->assign('is', '1');
				$this->assign('check', '恭喜'. $_POST['username'] .'注册成功');
			} else {
				$check = $id;
				$this->assign('check', $check);	
			}
		} 
		//登录
		if (isset($_POST['login'])) {
			$data = $user->userId($arr);
			if (!empty($data)) {
				$_SESSION['id'] = $data[0]['id'];
				$id = $_SESSION['id'];
				$user->scoreAdd($id);
				//var_dump($id);
				$this->assign('is', '1');
				$this->assign('check', '欢迎'. $_POST['username'] .'登录成功');
			} else {
				$check = '登录信息错误';
				$this->assign('check', $check);
			}
		}
		$this->display('jump.html');
	}
	//修改密码
	public function reset()
	{
		if (isset($_POST['tj'])) {
			$arr = $_POST;
			$arr['password'] = md5($arr['opwd']);
			$user = new User;
			$data = $user->userList($arr);
			if (!empty($data)) {
					$check = $user->userUpdate($arr);
					if (is_bool($check)) {
						$this->assign('is', '0');
						$this->assign('check', '修改密码成功！');
						session_unset();
						session_destroy();
					}
					else {
						$this->assign('is', '2');
						$this->assign('check', $check);
					}
			} else {
				$check = '验证信息错误';
				$this->assign('is', '7');
				$this->assign('check', $check);
			}
			$this->display('jump.html');
		} else {
			$this->display('reset.html');
		}
	}	
	//忘记密码
	public function forget()
	{
		if (isset($_POST['tj'])) {
			$arr['username'] = $_POST['username'];
			$arr['email'] = $_POST['email'];
			$user = new User;
			$data = $user->userList($arr);
			if (!empty($data)) {
					$arra = $_POST;
					$check = $user->userUpdate($arra);
					if (is_bool($check)) {
						$this->assign('is', '0');
						$check = '设置密码成功';
						$this->assign('check', $check);
						session_unset();
						session_destroy();
					}
					else {
						$this->assign('is', '2');
						$this->assign('check',$check );
					}
			} else {
				$check = '填写信息错误';
				$this->assign('is', '8');
				$this->assign('check', $check);
			}
			$this->display('jump.html');
		} else {
			$this->display('forget.html');
		}
	}
	//收藏夹
	public function collects()
	{	
		if (isset($_SESSION['id'])) {
			$collect = new Collect();
			$song = new Song();
			$user = new User();
			$id = $_SESSION['id'];
			$data = $user->userList("id = $id");//用户的所有数据
			$id = $_SESSION['id'];
			$datac = $collect->collectList("uid = $id",'bid');
			foreach ($datac as $key => $value) {
				$bid =  $value['bid'];
				$datas[$key] = $song->songSelect("id = $bid");
			}
			$this->assign('datas', $datas);
			$this->assign('data', $data);
			$this->display('collects.html');
		} else {
			$check = '未登录...';
			$this->assign('is', '3');
			$this->assign('check', $check);
			$this->display('jump.html');
		}
		
	}
	//退出
	public function jump()
	{
		session_unset();
		session_destroy();
		$this->assign('is', '2');
		$this->assign('check', '退出成功');
		$this->display('jump.html');
	}
	//验证码方法
	public function verify()
	{
		$code =	Verify::ver();
		$_SESSION['yzm'] = $code;
		return $code;
	}
	//评论处理
	public function comment()
	{
		$song = new Song;
		if (isset($_POST['tj']))
		{
			$tid 		 = $_SESSION['tid'];
			$id 		 = $_SESSION['id'];
			$data        = $_POST;
			$data['cid'] = (int)$id;
			$data['rid'] = (int)$tid;
			$data['time']= time();
			$data['detail'] = 0;
			$tid   		 = $song->songInsert($data);
			$this->assign('is', '6');
			$check = '评论成功';
			$this->assign('check', $check);
		} else {
			$this->assign('is', '6');
			$check = '评论失败';
			$this->assign('check', $check);
		}
		$this->display('jump.html');
	}
	
}