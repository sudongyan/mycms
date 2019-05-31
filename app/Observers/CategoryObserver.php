<?php
/**
 * CMS - CMS based on laravel
 *
 * @category  CMS
 * @package   Laravel
 */

namespace App\Observers;

use App\Models\Category;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

/**
 * 分类观察者
 *
 * Class CategoryObserver
 * @package App\Observers
 */
class CategoryObserver
{
    public function creating(Category $category)
    {
        $category->order = $category->order ?? 999;
    }

    public function updating(Category $category)
    {

    }

    public function saving(Category $category){
        $parent = Category::find($category->parent);
        if(isset($parent->path)){
            $category->path = $parent->path . '-'. $parent->id;
        }else{
            $category->path = $category->parent;
        }
    }

    public function deleting(Category $category){
        // 先删除子分类
        Category::where('parent',$category->id)->delete();
    }


    public function updated(Category $category){
        // 因要递归处理子级所以不能直接使用update
        $categorys = Category::where('parent',$category->id)->get();
        foreach($categorys as $categoryModel){
            $categoryModel->path = $category->path . '-'. $category->id;
            $categoryModel->save();
        }
    
        Category::clearCache($category->id, $category->type);
    }
}
