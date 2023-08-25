<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Education;
use App\Models\Portfolio;
use App\Models\Document;
use App\Models\Team;
use App\Models\User;
use App\Models\UserJob;
use App\Models\ProSkill;
use App\Models\OtherSkill;
use App\Models\OnlineLink;
use App\Models\Lga;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use JWTAuth;

class UserDataController extends Controller
{
    public function open()
    {
        $data = "This data is open and can be accessed without the client being authenticated";
        return response()->json(compact('data'), 200);
    }

    public function closed()
    {
        $data = "Only authorized users can see this";
        return response()->json(compact('data'), 200);
    }

    public function addexperience(Request $request)
    {
        $start_day = 01;
        $end_day = 01;
        $start_month = $request->start_month;
        $start_year = $request->start_year;
        $end_month = $request->end_month;
        $end_year = $request->end_year;

        $start_duration = $start_year . '-' . $start_month . '-' . $start_day;
        $end_duration = $end_year . '-' . $end_month . '-' . $end_day;
        $start_DT = date('Y-m-d', strtotime("$start_duration"));
        $end_DT = date('Y-m-d', strtotime("$end_duration"));


        $roles = $request->roles;

        $extracted_roles2 = json_decode($roles, true);

        $extracted_roles = implode(',', array_map(function ($entry) {
            return $entry['role_name'];
        }, $extracted_roles2));


        $experience_id = 'EXP' . time();

        $experience = Experience::create([
            'user_id' => auth()->user()->user_id,
            'experience_id' => $experience_id,
            'start' => $start_duration,
            'end' => $end_duration,
            'company_name' => $request->company_name,
            'position' => $request->position,
            'roles' => $extracted_roles,
        ]);


        return response()->json(compact('experience', 201));
    }

    public function updateexperience(Request $request, $id)
    {
        $start_day = 01;
        $end_day = 01;
        $start_month = $request->start_month;
        $start_year = $request->start_year;
        $end_month = $request->end_month;
        $end_year = $request->end_year;

        $start_duration = $start_year . '-' . $start_month . '-' . $start_day;
        $end_duration = $end_year . '-' . $end_month . '-' . $end_day;
        $start_DT = date('Y-m-d', strtotime("$start_duration"));
        $end_DT = date('Y-m-d', strtotime("$end_duration"));


        if (!empty($request->roles)) {

            $roles = $request->roles;

            $extracted_roles2 = json_decode($roles, true);

            $extracted_roles = implode(',', array_map(function ($entry) {
                return $entry['role_name'];
            }, $extracted_roles2));
        }

        if (!empty($request->roles)) {
            $data = [
                'roles' => $extracted_roles,
            ];
        }

        $data = [
            'start' => $start_DT,
            'end' => $end_DT,
            'company_name' => $request->company_name,
            'position' => $request->position,
        ];

        //$updated = Experience::where('experience_id', $id)->update($data);


        // return response()->json(compact('updated'));

        return response()->json($start_DT);
    }

    public function deleteexperience($id)
    {


        $deleted = Experience::where('experience_id', $id)->delete();

        return response()->json(compact('deleted'));
    }


    public function experience($id)
    {
        $experience = Experience::where('user_id', auth()->user()->user_id)->where('experience_id', $id)->orderBy('id', 'DESC')->first();
        return response()->json(compact('experience'));
    }


    public function experiences()
    {
        $experiences = Experience::where('user_id', auth()->user()->user_id)->orderBy('id', 'DESC')->get();
        return response()->json(compact('experiences'));
    }

    public function addeducation(Request $request)
    {
        $start_day = 01;
        $end_day = 01;
        $start_month = $request->start_month;
        $start_year = $request->start_year;
        $end_month = $request->end_month;
        $end_year = $request->end_year;

        $start_duration = $start_year . '-' . $start_month . '-' . $start_day;
        $end_duration = $end_year . '-' . $end_month . '-' . $end_day;
        $start_DT = date('Y-m-d', strtotime("$start_duration"));
        $end_DT = date('Y-m-d', strtotime("$end_duration"));


        $skills = $request->skills;

        $extracted_skills2 = json_decode($skills, true);

        $extracted_skills = implode(',', array_map(function ($entry) {
            return $entry['skill_name'];
        }, $extracted_skills2));



        $education_id = 'EDU' . time();

        $education = Education::create([
            'user_id' => auth()->user()->user_id,
            'education_id' => $education_id,
            'start' => $start_DT,
            'end' => $end_DT,
            'school' => $request->school,
            'course' => $request->course,
            'class_of_degree' => $request->class_of_degree,
            'skills' => $extracted_skills,
        ]);


        return response()->json(compact('education', 201));
        //return response()->json(compact('start_DT'));

    }


    public function updateeducation(Request $request, $id)
    {
        $start_day = 01;
        $end_day = 01;
        $start_month = $request->start_month;
        $start_year = $request->start_year;
        $end_month = $request->end_month;
        $end_year = $request->end_year;

        $start_duration = $start_year . '-' . $start_month . '-' . $start_day;
        $end_duration = $end_year . '-' . $end_month . '-' . $end_day;
        $start_DT = date('Y-m-d', strtotime("$start_duration"));
        $end_DT = date('Y-m-d', strtotime("$end_duration"));


        if (!empty($request->roles)) {

            $roles = $request->roles;

            $extracted_roles2 = json_decode($roles, true);

            $extracted_roles = implode(',', array_map(function ($entry) {
                return $entry['role_name'];
            }, $extracted_roles2));
        }

        if (!empty($request->roles)) {
            $data = [
                'roles' => $extracted_roles,
            ];
        }

        $data = [
            'start' => $start_DT,
            'end' => $end_DT,
            'company_name' => $request->company_name,
            'position' => $request->position,
        ];

        //$updated = Experience::where('experience_id', $id)->update($data);


        // return response()->json(compact('updated'));

        return response()->json($start_DT);
    }

    public function deleteeducation($id)
    {


        $deleted = Education::where('education_id', $id)->delete();

        return response()->json(compact('deleted'));
    }


    public function education($id)
    {
        $education = Education::where('user_id', auth()->user()->user_id)->where('education_id', $id)->orderBy('id', 'DESC')->first();
        return response()->json(compact('education'));
    }


    public function educations()
    {
        $educations = Education::where('user_id', auth()->user()->user_id)->get();
        if (empty($educations)) {
            return response()->json(null);
        } else {
            return response()->json(compact('educations'));
        }
    }

    public function addportfolio(Request $request)
    {
        if ($request->hasFile('portfolio')) {

            $file = $request->file('portfolio');
            $portfolio_image_name = time() . uniqid() . '.' . $request->portfolio->extension();
            $file->storeAs('userportfolios/images', $portfolio_image_name, 's3');

            $portfolio = new Portfolio();
            $portfolio->user_id = auth()->user()->user_id;
            $portfolio->portfolio_id = 'POR' . time();
            $portfolio->image = $portfolio_image_name;
            $portfolio->save();

            return response()->json([
                "success" => true,
                "message" => "Portfolio successfully uploaded",
            ]);
        }
    }

    public function portfolios()
    {
        $portfolios = Portfolio::where('user_id', auth()->user()->user_id)->get();
        return response()->json(compact('portfolios'));
    }



    public function adddocument(Request $request)
    {
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $document_name = time() . uniqid() . '.' . $request->document->extension();
            $file->storeAs('userportfolios/documents', $document_name, 's3');

            $doc = new Document();
            $doc->user_id = auth()->user()->user_id;
            $doc->doc_id = 'DOC' . time();
            $doc->doc = $document_name;
            $doc->save();

            return response()->json([
                "success" => true,
                "message" => "Document successfully uploaded",
            ]);
        }
    }

    public function documents()
    {
        $documents = Document::where('user_id', auth()->user()->user_id)->get();
        return response()->json(compact('documents'));
    }


    public function addproskill(Request $request)
    {

        $pro_skill_id = 'PRO' . time();

        $pro_skill = ProSkill::create([
            'user_id' => auth()->user()->user_id,
            'pro_skill_id' => $pro_skill_id,
            'pro_skill' => $request->pro_skill,

        ]);

        return response()->json(compact('pro_skill', 201));
    }

    public function proskills()
    {
        $proskills = ProSkill::where('user_id', auth()->user()->user_id)->get();
        return response()->json(compact('proskills'));
    }

    public function addotherskill(Request $request)
    {
        $other_skill_id = 'OTH' . time();
        $other_skill = OtherSkill::create([
            'user_id' => auth()->user()->user_id,
            'other_skill_id' => $other_skill_id,
            'other_skill' => $request->other_skill,

        ]);

        return response()->json(compact('other_skill', 201));
    }

    public function otherskills()
    {
        $otherskills = OtherSkill::where('user_id', auth()->user()->user_id)->get();
        return response()->json(compact('otherskills'));
    }



    public function addonlinelink(Request $request)
    {

        $online_link_id = 'ONL' . time();

        $online_link = OnlineLink::create([
            'user_id' => auth()->user()->user_id,
            'online_link_id' => $online_link_id,
            'link_title' => $request->link_title,
            'link_address' => $request->link_address,

        ]);

        return response()->json(compact('online_link', 201));
    }

    public function onlinelinks()
    {
        $onlinelinks = OnlineLink::where('user_id', auth()->user()->user_id)->get();
        return response()->json(compact('onlinelinks'));
    }


    public function createteam(Request $request)
    {
        $team_id = 'TM' . time();

        $team = Team::create([
            'team_id' => $team_id,
            'team_name' => $request->team_name,
            'team_description' => $request->team_description,
            'team_privacy' => $request->team_privacy,
            'admin' => auth()->user()->user_id,
        ]);

        return response()->json(compact('team', 201));
    }

    public function teams()
    {
        $teams = Team::where('admin', auth()->user()->user_id)->get();
        return response()->json(compact('teams'));
    }

    public function createuserjob(Request $request)
    {
        $user_job_id = 'UJ' . time();

        $user_job = UserJob::create([

            'job_title' => $request->job_title,
            'job_id' => $user_job_id,
            'job_description' => $request->job_description,
            'salary' => $request->salary,
            'location' => $request->lga . ',' . ' ' . $request->state,
            'employment_type' => $request->employment_type,
            'languages' => 'English',
            'skills' => 'Cooking',
            'user_id' => auth()->user()->user_id
        ]);

        return response()->json(compact('user_job', 201));
    }

    public function userjobs()
    {
        $user_jobs = UserJob::where('user_id', auth()->user()->user_id)->get();
        return response()->json(compact('user_jobs'));
    }

    public function states(Request $request)
    {
        $states = State::where('name', '!=', null);
        if ($request->query('term')) {
            $states = $states->where('name', 'LIKE', '%' . $request->query('term') . '%');
        }
        $states = $states->get();
        return response()->json($states);
    }

    public function lgas(Request $request)
    {

        $lgas = Lga::where('name', '!=', null);
        if ($request->query('term')) {
            $lgas = $lgas->where('name', 'LIKE', '%' . $request->query('term') . '%');
        }
        if ($request->query('state_id')) {
            $lgas = $lgas->where('state_id', $request->query('state_id'));
        }
        $lgas = $lgas->get();
        return response()->json($lgas);
    }
}
