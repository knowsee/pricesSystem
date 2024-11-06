<?php
namespace app\controller;

use support\Request;
use support\Controller;
use app\model\{Files as FilesModel, Goods as GoodsModel};
use Intervention\Image\ImageManager;
class FilesController extends Controller
{
    public function index(Request $request)
    {
		if(empty($request->file())) {
			return json(['code' => 404, 'msg' => 'files not found']);
		}
		foreach ($request->file() as $key => $spl_file) {
			if($spl_file->isValid()) {
				if(in_array($spl_file->getUploadExtension(), ['jpg','jpeg','png'])) {
					$fileSize = $spl_file->getSize();
					$newPath = date('Y/m/').time().sha1(time().$spl_file->getUploadName().mt_rand(1000,9999)).'.'.$spl_file->getUploadExtension();
					$post = $request->all();
					if(isset($post['files_id']) && intval($post['files_id'])>0) {
						$info = FilesModel::query()->findOrFail(intval($post['files_id']));
						$spl_file->move(public_path().'/goods/'.$info['files_path']);
						$newPath = $info['files_path'];
						$info->file_size = filesize(public_path().'/goods/'.$newPath);
						$info->save();
					} else {
						$spl_file->move(public_path().'/goods/'.$newPath);
						$info = new FilesModel;
						$info->files_path = $newPath;
						$info->file_size = filesize(public_path().'/goods/'.$newPath);
						$info->save();
					}
					$manager = new ImageManager(['driver' => 'imagick']);
					$manager->make(public_path().'/goods/'.$newPath)->resize(null, 600, function ($constraint) {
						$constraint->aspectRatio();
						$constraint->upsize();
					})->save(public_path().'/goods/'.$newPath, 80);
					return $this->dataJson([
						'files' => 'https://img.goods.acghx.net/'.$newPath,
						'id' => $info->id
					]);
				} else {
					return json(['code' => 403, 'msg' => 'files not allow']);
				}
			}
        }
    }
    
}
