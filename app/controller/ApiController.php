<?php
namespace app\controller;

use support\Request;
use support\Controller;

use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use app\model\{Brand as BrandModel, Goods as GoodsModel, Prices as PricesModel, PricesLog as PricesLogModel, Shops as ShopsModel, CodeCheck as CodeCheckModel};

class ApiController extends Controller
{

    private $page = 1;
    private $limit = 25;

    private function getOffset() : int {
        return intval(($this->page-1)*$this->limit);
    }

    private function getPage(Request $request) {
        $get = $request->get();
        if(isset($get['page'])) {
            $this->page = $get['page'] < 1 ? 1 : intval($get['page']);
        }
        if(isset($get['limit'])) {
            $this->limit = $get['limit'] < 1 ? 25 : intval($get['limit']);
        }
    }
	
	public function goodsCheckInfo(Request $request) {
		$get = $request->get();
		if(empty($get['code'])) {
			return $this->dataJson(['info' => null]);
		}
		$info = CodeCheckModel::query()->where('code', trim($get['code']))->first();
		if(empty($info)) {
			return $this->dataJson(['info' => null]);
		}
		return $this->dataJson(['info' => $info]);
	}
	
	public function goodsSyncUpdate(Request $request)
    {
        $goodsList = GoodsModel::query()->orderBy('id', 'desc')->get();
        foreach($goodsList as $goods) {
			$country = str_replace('GS1 ', '', $goods['country']);
			$info = GoodsModel::query()->findOrFail($goods['id']);
			$info->country = $country;
			$info->save();
        }
        return $this->dataJson(['update' => true]);
    }

    public function goodsList(Request $request)
    {
        $this->getPage($request);
        $goodsList = GoodsModel::query()->orderBy('id', 'desc')->offset($this->getOffset())->limit($this->limit)->get();
        if(empty($goodsList)) {
            return $this->dataJson(['list' => null]);
        }
        $brandId = array();
        foreach($goodsList as $goods) {
            if(!in_array($goods['brand'], $brandId)) {
                $brandId[] = $goods['brand'];
            }
        }
        $brandList = BrandModel::query()->whereIn('id', $brandId)->get();
        $brandList = array_column($brandList->toArray(), null, 'id');
        foreach($goodsList as $key => $goods) {
            $brandGoods = $brandList[$goods['brand']];
            if($goods['files_path']) {
                $fileInfo = getimagesize(public_path().'/goods/'.$goods['files_path']);
                $goodsList[$key]['w'] = $fileInfo[0] ?? null;
                $goodsList[$key]['h'] = $fileInfo[1] ?? null;
            } else {
                $goodsList[$key]['w'] = null;
                $goodsList[$key]['h'] = null;
            }

			$goodsList[$key]['images'] = empty($goods['files_path']) ? null : 'https://img.goods.acghx.net/'.$goods['files_path'];
            $goodsList[$key]['brand'] = [
                'id' => $goods['brand'],
                'name' => $brandGoods['name_chi'],
                'english' => $brandGoods['name_en']
            ];
        }
        return $this->dataJson(['list' => $goodsList]);
    }

    public function shopList(Request $request) 
    {
        $this->getPage($request);
        $list = ShopsModel::query()->orderBy('id', 'desc')->offset($this->getOffset())->limit($this->limit)->get();
        $shopList = $list->toArray();
        if(empty($shopList)) {
            return json(['code' => 404, 'msg' => 'result not found']);
        }
        $resultList = array();
        foreach($shopList as $key => $val) {
            $resultList[] = [
                'id' => $val['id'],
                'name' => $val['name_chi'],
                'name_en' => $val['name_en'],
                'address' => $val['address_chi'],
                'address_en' => $val['address_en'],
                'ares' => $val['ares_chi'],
                'ares_en' => $val['ares_en']
            ];
        }
        return $this->dataJson($resultList);
    }

    public function brandList(Request $request) 
    {
        $this->getPage($request);
        $list = BrandModel::query()->orderBy('id', 'desc')->offset($this->getOffset())->limit($this->limit)->get();
        $brandList = $list->toArray();
        if(empty($brandList)) {
            return json(['code' => 404, 'msg' => 'result not found']);
        }
        $resultList = array();
        foreach($brandList as $key => $val) {
            $resultList[] = [
                'id' => $val['id'],
                'name' => $val['name_chi'],
                'name_en' => $val['name_en'],
                'country' => $val['country'],
                'desc' => $val['descText_chi'],
                'desc_en' => $val['descText_en']
            ];
        }
        return $this->dataJson($resultList);
    }

    public function brandSearch(Request $request)
    {
        $get = $request->get();
        $data = Validator::input($get, [
            'keyword' => Validator::stringType()->NotEmpty()->setName('Keyword')
        ]);
        $list = BrandModel::query()->where('name_chi', 'like', $data['keyword'].'%')->orWhere('name_en', 'like', $data['keyword'].'%')->get();
        $brandList = $list->toArray();
        if(empty($brandList)) {
            return json(['code' => 404, 'msg' => 'result not found']);
        }
        $resultList = array();
        foreach($brandList as $key => $val) {
            $resultList[] = [
                'id' => $val['id'],
                'name' => $val['name_chi'],
                'name_en' => $val['name_en'],
                'country' => $val['country'],
                'desc' => $val['descText_chi'],
                'desc_en' => $val['descText_en']
            ];
        }
        return $this->dataJson($resultList);
    }
    
    public function goodDelete(Request $request, int $id)
    {
        try {
            $info = GoodsModel::query()->findOrFail($id);
            $info->delete();
            return $this->dataJson(['id' => $info->id, 'action' => 'delete']);
        } catch (ModelNotFoundException $e) {
            return json(['code' => 404, 'msg' => $e->getMessage()]);
        }
    }

    public function brandDelete(Request $request, int $id)
    {
        try {
            $info = BrandModel::query()->findOrFail($id);
            $info->delete();
            return $this->dataJson(['id' => $info->id, 'action' => 'delete']);
        } catch (ModelNotFoundException $e) {
            return json(['code' => 404, 'msg' => $e->getMessage()]);
        }
    }

    public function shopDelete(Request $request, int $id)
    {
        try {
            $info = ShopsModel::query()->findOrFail($id);
            $info->delete();
            return $this->dataJson(['id' => $info->id, 'action' => 'delete']);
        } catch (ModelNotFoundException $e) {
            return json(['code' => 404, 'msg' => $e->getMessage()]);
        }
    }
    
}
