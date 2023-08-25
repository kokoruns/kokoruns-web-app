<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Association;
use App\Models\AssociationAdmin;
use App\Models\AssociationBranch;
use App\Models\AssociationEvent;
use App\Models\AssociationGallery;
use App\Models\Message;
use App\Models\User;

class AssociationController extends Controller
{
    //
    public function aregister(Request $request)
        {

            $association_id2 = preg_replace('/\s+/', '.', $request->association_name);

		    $association_details = association::where('association_id', strtolower($association_id2))->first();

            if(empty($association_details))
            {

                $association_id = strtolower($association_id2);

            }
            else
            {

                // //user ID already exists. Add 1 to it
                $id = $association_details->id;
                $id2 = $id + 4;
                $association_id = $association_details->association_id . '.' . $id2;
                //echo $association_details['association_name'];

            }

            $association = Association::create([
                    'association_id' => $association_id,
                    'association_name' => $request->association_name,
                    'association_id' => $association_id,
                    'association_email' => $request->association_email,
                    'phone' => $request->phone,
                    'about' => $request->about,
                    'cac' => $request->cac,
                    'association_type' => $request->association_type,
                    'association_size' => $request->association_size,
                    //'logo' => $ghka,
                    'website' => $request->website,
                    'association_director' => $request->association_director,
                    'association_contact_email' => $request->association_contact_email,
                    'association_address' => $request->association_address,
                    'main_office_location_state' => $request->main_office_location_state,
                    'main_office_location_lga' => $request->main_office_location_lga,
                    'state' => $request->state,
                    'lga' => $request->lga,
                    'author' => auth()->user()->user_id,
            ]);

            // $token = JWTAuth::fromUser($user);

        return response()->json(compact('association'),201);

    }

    public function associations()
    {
        $associations = Association::where('author', auth()->user()->user_id)->orderBy('id', 'DESC')->get();
        return response()->json(compact('associations'));
    }

    public function associationdetails($id)
    {
        $associationdetails = Association::where('association_id', $id)->orderBy('id', 'DESC')->first();
        return response()->json(compact('associationdetails'));
    }

    public function updateassociationinfo(Request $request, $id)
	{
		//editbranchaction

		$data = [
	
			'founded_month' => $request->founded_month,
            'founded_year' => $request->founded_year,
			'field' => $request->field,
            
			];


			// $assoc = new associationModel();
			// $updated = $assoc->update($id, $data);

            $updated =  Association::where('association_id', $id)->update($data);

  
            return response()->json(compact('updated'));

	}

    public function makeassociationadmin(Request $request, $association_id)
	{
		
		
		$user_details = User::where('user_id', $request->sub_admin_id)->first();

		// var_dump($applicant_details);

        $association_admin = AssociationAdmin::create([
                
                's_no' => 'CA'. time(),
                'association_id' => $association_id,
                'sub_admin_id' => $request->sub_admin_id,
                'sub_admin_name' => $user_details->first_name. ' '. $user_details->last_name,
        
                ]);
					
				
        return response()->json(compact('association_admin'),201);
        //return response()->json($request->sub_admin_id);

	}


    public function removeassociationadmin($id)
    {

        $deleted = AssociationAdmin::where('s_no', $id)->delete();

        return response()->json(compact('deleted'));
       
    }

    public function updateassociationabout(Request $request, $id)
	{
		
		$data = [

			'about' => $request->association_about,
			'phone' => $request->association_number,
			'cac' => $request->cac,
			'association_director' => $request->association_director,
			'website' => $request->website,
			'association_address' => $request->association_address,
			'main_office_location_state' => $request->about_state,
			'main_office_location_lga' => $request->about_lga,
		
            ];
            
	
            $updated =  Association::where('association_id', $id)->update($data);

  
            return response()->json(compact('updated'));

            //return response()->json($data);
		
	}

    public function createassociationbranch(Request $request, $id)
	{

        $association_branch = AssociationBranch::create([
       
			'association_id' => $id,
            'branch_id' => 'BR' . time(),
			'branch_name' => $request->branch_name,
			'branch_manager' => $request->branch_manager,
			'branch_address' => $request->branch_address,
			'branch_state' => $request->branch_state,
            'branch_lga' => $request->branch_lga,
            'branch_phone' => $request->branch_phone,
            ]);

            return response()->json(compact('association_branch'),201);
      
	}

    public function associationbranches($id)
    {
        $associationbranches = AssociationBranch::where('association_id', $id)->orderBy('id', 'DESC')->get();
        return response()->json(compact('associationbranches'));
    }


    public function updateassociationsocials(Request $request, $id)
	{
		//editbranchaction

		$data = [
	
			'linkedin' => $request->linkedin,
			'facebook' => $request->facebook,
			'instagram' => $request->instagram,
            
			];

			$updated =  Association::where('association_id', $id)->update($data);

  
            return response()->json(compact('updated'));
	}

    public function createassociationevent(Request $request, $id)
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

            $association_event = AssociationEvent::create([
			
            'association_id' => $id,
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

            
            return response()->json(compact('association_event'),201);

	}
 
    public function associationevents($id)
    {
        $associationevents = AssociationEvent::where('association_id', $id)->orderBy('id', 'DESC')->get();
        return response()->json(compact('associationevents'));
    }

    public function addassociationgallery(Request $request, $id)
    {
        if ($request->hasFile('gallery')) 
        {
            $gallery_image_name = time().uniqid().'.'.$request->gallery->extension();
            // $fileSize = $request->atm_card_file_name->getClientSize();
            $request->gallery->move(public_path('uploads/associationgalleries'), $gallery_image_name);

            $gallery = new AssociationGallery();
            $gallery->association_id = $id;
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


    public function associationgalleries($id)
    {
        $associationgalleries = AssociationGallery::where('association_id', $id)->get();
        return response()->json(compact('associationgalleries'));
    }


    public function createassociationebroadcast(Request $request, $id)
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


    public function associationebroadcasts($id)
    {
        $associationebroadcasts = Message::where('sender_id', $id)->get();
        return response()->json(compact('associationebroadcasts'));
    }




}
