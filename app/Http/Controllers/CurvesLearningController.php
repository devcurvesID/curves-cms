<?php

namespace App\Http\Controllers;

use App\Model\Curves\CurvesLearning\ClCategory;
use App\Model\Curves\CurvesLearning\ClMaterialContent;
use App\Model\Curves\CurvesLearning\ClMaterialHeader;
use App\Model\Curves\CurvesLearning\ClMaterialSupport;
use App\Model\Curves\CurvesLearning\ClMaterialQuestion;
use App\Http\Requests\CLQuestionRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Session;
use DB;

class CurvesLearningController extends Controller
{
    public function choose_menus(){
        return view('cf_lite.curves-learning.cl-choose-menu');
    }

    public function list_category(){
        $categories = ClCategory::orderBy('id', 'asc')->get();
        return view('cf_lite.curves-learning.category', compact('categories'));
    }

    public function store_category(Request $request){
        $this->validate($request,[
            'category_name' => 'required|unique:cl_categories,name'
        ]);

        $category = New ClCategory();
        $category->name = $request->category_name;
        $category->save();

        Session::flash('flash_message', trans('messages.category_success_add'));
        return redirect('cf-lite/curves-learning/category');
    }

    public function edit_category(Request $request){
        $this->validate($request,[
            'update_category_name' => 'required|unique:cl_categories,name,'.$request->category_id
        ]);

        $category = ClCategory::findOrFail($request->category_id);
        $name_temp = $category->name;

        if ($request->category_name != $name_temp):
            $category->name = $request->update_category_name;
            $category->save();
            Session::flash('flash_message', trans('messages.category_success_edit'));
        else:
            Session::flash('flash_message', trans('messages.category_error_name'));
        endif;

        return redirect('cf-lite/curves-learning/category');
    }

    public function delete_category($id){
        $category = ClCategory::findOrFail($id);
        $category->delete();

        Session::flash('flash_message', trans('messages.category_delete_message'));
        return redirect('cf-lite/curves-learning/category');
    }

    public function view_material_header()
    {
        $materials = ClMaterialHeader::all()->sortBy('id');
        $categories = ClCategory::all()->pluck('name','id');

        return view('cf_lite.curves-learning.material-headers-list', compact('materials','categories'));
    }

    public function add_material_header(Request $request)
    {
        $this->validate($request,[
            'material_header_name'=>'required|string',
            'material_header_category'=>'required|exists:cl_categories,id',
            'material_header_sequence'=>'required'
        ]);

        $newmaterial = new ClMaterialHeader;
        $newmaterial->name = $request->material_header_name;
        $newmaterial->category_id = $request->material_header_category;
        $newmaterial->sequences = $request->material_header_sequence;

        $newmaterial->save();

        return redirect('cf-lite/curves-learning/material-header');
    }

    public function edit_material_header(Request $request)
    {
        $this->validate($request,[
            'material_header_id_edit'=>'required|exists:cl_material_headers,id',
            'material_header_name_edit'=>'required|string',
            'material_header_category_edit'=>'required|exists:cl_categories,id',
            'material_header_sequence_edit'=>'required'
        ]);

        $material = ClMaterialHeader::findOrFail($request->material_header_id_edit);
        $material->name = $request->material_header_name_edit;
        $material->category_id = $request->material_header_category_edit;
        $material->sequences = $request->material_header_sequence_edit;

        $material->save();

        return redirect('cf-lite/curves-learning/material-header');
    }

    public function  delete_material_header($id)
    {
        $material = ClMaterialHeader::findOrFail($id);
        if($material->clmaterialheader_clmaterialcontent==null)
            $material->delete();
        else
            Session::flash('flash_message', trans('messages.cant_delete_because_existing_content'));
        return redirect('cf-lite/curves-learning/material-header');
    }

    public function  view_material_content($id)
    {
        $contents = ClMaterialContent::where('material_id','=',$id)->get();
        $header_id = $id;

        return view('cf_lite.curves-learning.material-contents-list', compact('contents','header_id'));
    }

    public function  add_material_content(Request $request, $id)
    {
        $this->validate($request,[
            'material_content_title'=>'required',
            'material_content_sequence'=>'required|integer',
            'material_content_content'=>'required'
        ]);

        $content = new ClMaterialContent;
        $content->title = $request->material_content_title;
        $content->material_id = $id;
        $content->sequence = $request->material_content_sequence;
        $content->content = $request->material_content_content;
        $content->status = 'NotApproved';
        $content->total_question = 0;

        $content->save();

        return redirect('cf-lite/curves-learning/material-content/'.$id);
    }

    public function edit_material_content($id)
    {
        $content = ClMaterialContent::findOrFail($id);
        $questions = ClMaterialQuestion::where('material_content_id','=',$id)->get();
        $supports = ClMaterialSupport::where('material_content_id','=',$id)->get();


        return view('cf_lite.curves-learning.material-content-edit',compact('content','questions','supports'));
    }

    public function edit_material_content_question($contentid, CLQuestionRequest $request)
    {
        $question = $request->question;
        $status = $request->status;
        $id = $request->id;
        $content = ClMaterialContent::findOrFail($contentid);
        $increment =0;
        foreach ($question as $i=>$value)
        {
            if($id[$i]==0)
            {
                $newquestion = new ClMaterialQuestion;
                $newquestion->question = $question[$i];
                $newquestion->material_content_id = $contentid;
                $increment++;

                $newquestion->save();
            }
            else if($status[$i]==1)
            {
                $newquestion = ClMaterialQuestion::findOrFail($id[$i]);
                $newquestion->question = $question[$i];

                $newquestion->save();
            }
        }
        $content->total_question += $increment;
        $content->save();

        return redirect('cf-lite/curves-learning/material-content-detail/'.$contentid);
    }

    public function delete_material_content_question(Request $request,$contentid)
    {
        $question = ClMaterialQuestion::findOrFail($request->deleteid);
        $content = ClMaterialContent::findOrFail($question->clmaterialquestion_clmaterialcontent->id);
        $content->total_question--;
        $content->save();
        $question->delete();

        return redirect('cf-lite/curves-learning/material-content-detail/'.$contentid);
    }

    public function upload_material_content_file(Request $request, $contentid)
    {
        $support = $request->cl_file;
        $status = $request->file_status;
        $id = $request->file_id;
        foreach ($support as $i=>$value)
        {
            if($id[$i]==0)
            {
                $newsupport = new ClMaterialSupport;
                $path = 'images/curves_learning';
                $newsupport->file_name = $this->uploadfile($path, $support[$i], $i);
                $newsupport->material_content_id = $contentid;

                $newsupport->save();
            }
            else if($support[$i]!=null)
            {
                $newsupport = ClMaterialSupport::findOrFail($id[$i]);
                $this->deletefile($newsupport);
                $path = 'images/curves_learning';
                $newsupport->file_name = $this->uploadfile($path, $support[$i], $i);

                $newsupport->save();
            }
//            else if($status[$i]==1)
//            {
//                $newsupport = ClMaterialSupport::findOrFail($id[$i]);
//                $this->deletefile($newsupport);
//                $path = 'curves_learning';
//                $newsupport->file_name = $this->uploadfile($path, $support[$i], $i);
//
//                $newsupport->save();
//            }
        }

        return redirect('cf-lite/curves-learning/material-content-detail/'.$contentid);
    }

    public function delete_material_content_file(Request $request, $contentid)
    {
        $file = ClMaterialSupport::findOrFail($request->deletefid);
        $this->deletefile($file);
        $file->delete();

        return redirect('cf-lite/curves-learning/material-content-detail/'.$contentid);
    }

    private function uploadfile($path, $file, $sequence)
    {
        $photo = $file;
        $ext = $photo->getClientOriginalExtension();

        if ($file->isValid()) {
            $photo_name = date('YmdHis').'-'.$sequence.".$ext";
            $upload_path = $path;
            $file->move($upload_path, $photo_name);
            return $photo_name;
        }

        return false;
    }

    private function deletefile(ClMaterialSupport $file)
    {
        //$user = User::findOrFail($id);
        $exist = Storage::disk('curves_learning')->exists($file->file_name);

        if (isset($file->file_name) && $exist) {
            $delete = Storage::disk('curves_learning')->delete($file->file_name);
            if ($delete) {
                return true;
            }
            return false;
        }
    }

    public function list_approval(){
        $contents = ClMaterialContent::where('status', '=', 'NotApproved')->orderBy('id', 'asc')->get();
        return view('cf_lite.curves-learning.list_approval', compact('contents'));
    }

    public function update_bulk_content_approved(Request $request){
        foreach ($request->content_check as $key => $value):
            $content = ClMaterialContent::findOrFail($value);
            $content->status = "Approved";
            $content->save();
        endforeach;
        Session::flash('flash_message', trans('messages.content_success_approved'));
        return redirect('cf-lite/curves-learning/material-header');
    }

    public function edit_material_content_detail(Request $request, $id)
    {
        $this->validate($request,[
            'material_content_title'=>'required',
            'material_content_content'=>'required'
        ]);

        $cl = ClMaterialContent::findOrFail($id);
        $cl->title = $request->material_content_title;
        $cl->content = $request->material_content_content;

        $cl->save();
         return redirect('cf-lite/curves-learning/material-content-detail/'.$id);
    }

    public function delete_material_content($id)
    {
        $content = ClMaterialContent::findOrFail($id);
        $questions = $content->clmaterialcontent_clmaterialquestion;
        $supports = $content->clmaterialcontent_clmaterialsuppot;
        $header_id = $content->clmaterialcontent_clmaterialheader->id;
        foreach ($questions as $question)
        {
            $deletequestion = ClMaterialQuestion::findOrFail($question->id);
            $deletequestion->delete();
        }
        foreach ($supports as $support)
        {
            $deletesupport = ClMaterialSupport::findOrFail($support->id);
            $this->deletefile($deletesupport);
            $deletesupport->delete();
        }

        $content->delete();
        return redirect('cf-lite/curves-learning/material-content/'.$header_id);
    }
}
