<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyAdmin;
use App\Models\CompanyBranch;
use App\Models\CompanyEvent;
use App\Models\CompanyGallery;
use App\Models\Message;
use App\Models\User;

class CompanyController extends Controller
{
    //
    public function cregister(Request $request)
        {

            $company_id2 = preg_replace('/\s+/', '.', $request->company_name);

		    $company_details = Company::where('company_id', strtolower($company_id2))->first();

            if(empty($company_details))
            {

                $company_id = strtolower($company_id2);

            }
            else
            {

                // //user ID already exists. Add 1 to it
                $id = $company_details->id;
                $id2 = $id + 4;
                $company_id = $company_details->company_id . '.' . $id2;
                //echo $company_details['company_name'];

            }

            $company = Company::create([
                    'company_id' => $company_id,
                    'company_name' => $request->company_name,
                    'company_id' => $company_id,
                    'company_email' => $request->company_email,
                    'phone' => $request->company_number,
                    'cac' => $request->cac,
                    'company_type' => $request->company_type,
                    'company_size' => $request->company_size,
                    'company_industry' => $request->company_industry,
                    'company_industry2' => $request->company_industry2,
                    'company_industry3' => $request->company_industry3,
                    //'logo' => $ghka,
                    'website' => $request->website,
                    'company_address' => $request->company_address,
                    'main_office_location_state' => $request->state,
                    'main_office_location_lga' => $request->lga,
                    'author' => auth()->user()->user_id,
            ]);

            // $token = JWTAuth::fromUser($user);

        return response()->json(compact('company'),201);

    }

    public function companies()
    {
        $companies = Company::where('author', auth()->user()->user_id)->orderBy('id', 'DESC')->get();
        return response()->json(compact('companies'));
    }

    public function companydetails($id)
    {
        $companydetails = Company::where('company_id', $id)->orderBy('id', 'DESC')->first();
        return response()->json(compact('companydetails'));
    }

    public function updatecompanyinfo(Request $request, $id)
	{
		//editbranchaction

		$data = [
	
			'founded_month' => $request->founded_month,
            'founded_year' => $request->founded_year,
			'field' => $request->field,
            
			];


			// $assoc = new CompanyModel();
			// $updated = $assoc->update($id, $data);

            $updated =  Company::where('company_id', $id)->update($data);

  
            return response()->json(compact('updated'));

	}

    public function makecompanyadmin(Request $request, $company_id)
	{
		
		
		$user_details = User::where('user_id', $request->sub_admin_id)->first();

		// var_dump($applicant_details);

        $company_admin = CompanyAdmin::create([
                
                's_no' => 'CA'. time(),
                'company_id' => $company_id,
                'sub_admin_id' => $request->sub_admin_id,
                'sub_admin_name' => $user_details->first_name. ' '. $user_details->last_name,
        
                ]);
					
				
        return response()->json(compact('company_admin'),201);
        //return response()->json($request->sub_admin_id);

	}


    public function removecompanyadmin($id)
    {

        $deleted = CompanyAdmin::where('s_no', $id)->delete();

        return response()->json(compact('deleted'));
       
    }

    public function updatecompanyabout(Request $request, $id)
	{
		
		$data = [

			'about' => $request->company_about,
			'phone' => $request->company_number,
			'cac' => $request->cac,
			'company_director' => $request->company_director,
			'website' => $request->website,
			'company_address' => $request->company_address,
			'main_office_location_state' => $request->about_state,
			'main_office_location_lga' => $request->about_lga,
		
            ];
            
	
            $updated =  Company::where('company_id', $id)->update($data);

  
            return response()->json(compact('updated'));

            //return response()->json($data);
		
	}

    public function createcompanybranch(Request $request, $id)
	{

        $company_branch = CompanyBranch::create([
       
			'company_id' => $id,
            'branch_id' => 'BR' . time(),
			'branch_name' => $request->branch_name,
			'branch_manager' => $request->branch_manager,
			'branch_address' => $request->branch_address,
			'branch_state' => $request->branch_state,
            'branch_lga' => $request->branch_lga,
            'branch_phone' => $request->branch_phone,
            ]);

            return response()->json(compact('company_branch'),201);
      
	}

    public function companybranches($id)
    {
        $companybranches = CompanyBranch::where('company_id', $id)->orderBy('id', 'DESC')->get();
        return response()->json(compact('companybranches'));
    }


    public function updatecompanysocials(Request $request, $id)
	{
		//editbranchaction

		$data = [
	
			'linkedin' => $request->linkedin,
			'facebook' => $request->facebook,
			'instagram' => $request->instagram,
            
			];

			$updated =  Company::where('company_id', $id)->update($data);

  
            return response()->json(compact('updated'));
	}

    public function createcompanyevent(Request $request, $id)
	{

            if(!empty($request->event_price))
            {
                $price1 = 0;
                $price2 = null;
            }
            else if(!empty($request->event_price_from2))
            {
                $price1 = $request->event_price_from2;
                $price2 = null;
            }
            else if(!empty($request->event_price_from3) && !empty($request->event_price_to))
            {
                $price1 = $request->event_price_from3;
                $price2 = $request->event_price_to;

            }


			if(!empty($request->hasFile('event_image')))
			{
				// $file1 = $request->getFile('event_image');
				// $filename1 = $file1->getRandomName();
                $filename1 = time().uniqid().'.'.$request->event_image->extension();
			}
			else
			{
				$filename1 = 'event.jpg';
			}

			if(!empty($request->hasFile('event_logo')))
			{
				// $logo = $request->getFile('event_logo');
				// $logoname = $logo->getRandomName();
                $logoname = time().uniqid().'.'.$request->event_image->extension();
			}
			else
			{
				$logoname = 'logo.jpg';
			}


			$start_DT = date('Y-m-d H:i:s', strtotime($request->event_start));
			$end_DT = date('Y-m-d H:i:s', strtotime($request->event_end));

            $company_event = CompanyEvent::create([
			
            'company_id' => $id,
			'event_id' => 'EV' . time(),
			'from' => $start_DT,
			'to' => $end_DT,
			'title' => $request->event_title,
			'event_link' => $request->event_link,
			'author' => auth()->user()->user_id,
			'description' => $request->event_description,
			'event_type' => $request->event_type,
			'event_industry' => $request->event_industry,
			'event_price1' => '$price1',
			'event_price2' => '$price2',
			'event_address' => $request->event_address,
			'event_state' => $request->event_state, 
			'event_lga' => $request->event_lga,
			'event_image1' => $filename1,
			'event_logo' => $logoname,

            ]);

            
            return response()->json(compact('company_event'),201);

	}
 
    public function companyevents($id)
    {
        $companyevents = CompanyEvent::where('company_id', $id)->orderBy('id', 'DESC')->get();
        return response()->json(compact('companyevents'));
    }

    public function addcompanygallery(Request $request, $id)
    {
        if ($request->hasFile('gallery')) 
        {
            $gallery_image_name = time().uniqid().'.'.$request->gallery->extension();
            // $fileSize = $request->atm_card_file_name->getClientSize();
            $request->gallery->move(public_path('uploads/companygalleries'), $gallery_image_name);

            $gallery = new CompanyGallery();
            $gallery->company_id = $id;
            $gallery->user_id = auth()->user()->user_id;
            $gallery->gallery_id = 'CGA' . time();
            $gallery->image = $gallery_image_name;
            $gallery->image_title = $request->image_title;
            $gallery->save();

            return response()->json([
                "success" => true,
                "message" => "Gallery successfully uploaded",
                "gallery_image_name" => $gallery_image_name,
            ]);
        }
    }


    public function companygalleries($id)
    {
        $companygalleries = CompanyGallery::where('company_id', $id)->get();
        return response()->json(compact('companygalleries'));
    }


    public function createcompanyebroadcast(Request $request, $id)
	{


		$applicant_ids = ['1621129452', '1621179317', '1621179324', '1621179329', '1621179333'];


		$receivers = array();
		foreach ($applicant_ids as $user_id) 
		{
			$receivers[] = User::where('user_id', $user_id)->where('active', 1)->pluck('first_name', 'last_name')->first();
		}
	
		$message_id = 'MSG' . time();

		for($i=0; $i<count($applicant_ids); $i++)
		{
			$data[]=array(
                'author' => auth()->user()->user_id,
				'sender_id' => $id,
				//'sender_name' => $request->sender_name,
				'subject'=> $request->subject,
				'message'=> $request->message,
                'message_id'=> $message_id,
				'receiver_id'=>$applicant_ids[$i],
				// 'receiver_name'=> $receivers[$i],
				'is_broadcast' => 1,
			);
		}

			$message = Message::insert($data);

            return response()->json(compact('message'),201);


	}


    public function companyebroadcasts($id)
    {
        $companyebroadcasts = Message::where('sender_id', $id)->get();
        return response()->json(compact('companyebroadcasts'));
    }




}
