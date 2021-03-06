<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mentor_list extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('role')==2){
			redirect('login');
		}
		$this->load->model('guru_model','guru');
	}

	public function index()
	{
		
		$this->data['module'] = 'gurus';        
		$this->load->vars($this->data);
		$this->load->view('template');
	}
	
	Public function get_gurus()
	{
		$list = $this->guru->get_datatables();
		$data = array();
		$no = $_POST['start'];
		$i = 1;
		foreach ($list as $g) {
			$row = array();             


     if(!empty($g->mentor_profile_img) && empty($g->mentor_picture) ||  !empty($g->mentor_profile_img) && !empty($g->mentor_picture)){

      $mentor_img = $g->mentor_profile_img;    
      $file_to_check = FCPATH . '/assets/images/' . $mentor_img;
      if (file_exists($file_to_check)) {
        $mentor_img = base_url() . 'assets/images/'.$mentor_img;
      }else{
        $mentor_img = base_url() . 'assets/images/default-avatar.png';
      }  
    }else if(empty($g->mentor_profile_img) && !empty($g->mentor_picture)){
      $mentor_img = $g->mentor_picture;      
    }else{
      $mentor_img = base_url() . 'assets/images/default-avatar.png';
    }



    $user_id = base64_encode($g->id);
    $row[] = '<a href="'.base_url().'mentor-profile/'.$g->mentor_user_name.'"><img src="'.$mentor_img.'" class="img-circle" height="40" width="40"> '.$g->mentor_name.'</a>';


          // $row[] = '<span class="spa_greytext" >'.date('d-m-Y',strtotime($g->created_date)).'</span>';
    $row[] =$g->calls;   
    // $row[] =($g->earned)?'<strong>$'.$g->earned.'</strong>':'<strong>$0.00</strong>';   
    $amount = $this->db->select('SUM(payment_gross) as amount')->get_where('payments',array('mentor_id'=>$g->id,'payment_status'=>1))->row_array();
    $row[] =  (!empty($amount)?'$'.number_format($amount['amount'],2):'0.00'); 

          if($g->delete_sts == 0){ // Active 
          	$row[] = '<span class="label label-success">Active</span>'; 
          } else if($g->delete_sts == 1){ // Inactive 
          	$row[] = '<span class="label label-danger">Inactive</span>';
          }                        
          $row[] = '<a class="btn btn-info" href="'.base_url().'mentor_list/mentor_details/'.$user_id.'" ><i class="fa fa-eye" aria-hidden="true"></i></a>';        

          $data[] = $row;
        }

        $output = array(
         "draw" => $_POST['draw'],
         "recordsTotal" => $this->guru->count_all(),
         "recordsFiltered" => $this->guru->count_filtered(),
         "data" => $data,
       );
                //output to json format
        echo json_encode($output);
      }

      Public function mentor_details()
      {	
        $this->data['guru'] =  $this->guru->get_guru_details();
        $this->data['earned'] =  $this->guru->get_total_earned(); 
        $this->data['requested'] =  $this->guru->get_total_requested(); 
        $this->data['paid'] =  $this->guru->get_total_paid();       
        $this->data['module'] = 'guru_details';        
        $this->load->vars($this->data);
        $this->load->view('template');
      }

  // Get Individual Gurus Payment Details 

      Public function get_gurus_payment()
      {
        $list = $this->guru->get_datatables_g();
        $data = array();
        $no = $_POST['start'];
        $i = 1;
        foreach ($list as $g) {
         $row = array(); 



      if($g->profile_img!=''){ // Getting profile image from applicant table for applicant 
        $applicant_img = $g->profile_img;
        $file_to_check = FCPATH . '/assets/images/' . $applicant_img;
        if (file_exists($file_to_check)) {
           $applicant_img = base_url() . 'assets/images/'.$applicant_img;
        }else{
          $applicant_img = base_url() . 'assets/images/default-avatar.png';
        }                

          }else if($g->picture_url!=''){ // Getting profile image from social table for applicant 
            $applicant_img = $g->picture_url;
          }else{
            $applicant_img = base_url() . 'assets/images/default-avatar.png';
          }  


        $user_id = base64_encode($g->id);          
        $row[] = '<a  href="'.base_url().'mentee-profile/'.$g->username.'" ><img src="'.$applicant_img.'" class="img-circle" height="40" width="40"> '.$g->first_name.' '.$g->last_name.'</a>';

        $row[] = date('d-m-Y',strtotime($g->invite_date));
        $row[] = date('h:i A',strtotime($g->invite_time)).' -  '.date('h:i A',strtotime($g->invite_end_time));
        $row[] ='$'.$g->payment_gross;

        if($g->payment_status == 1){ // Success 
          $row[] = '<span class="label label-success">Completed</span>'; 
          } else if($g->payment_status == 2){ // Cancel 
           $row[] = '<span class="label label-danger">Cancelled</span>';
          } else{ // Pending 
            $row[] = '<span class="label label-info">Pending</span>';
          }     

          $data[] = $row;
        }

        $output = array(
         "draw" => $_POST['draw'],
         "recordsTotal" => $this->guru->count_all_g(),
         "recordsFiltered" => $this->guru->count_filtered_g(),
         "data" => $data,
       );
                //output to json format
        echo json_encode($output);
      }





    }

