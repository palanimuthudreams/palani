<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends Admin_Core_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *        http://example.com/index.php/welcome
     *    - or -
     *        http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */

    public function __construct()
    {
        parent::__construct();

        //check auth
        if (!is_admin()) {
            redirect('login');
        }
    }


    /**
     * Photo Gallery
     */
    public function photo_gallery()
    {
        $data['title'] = trans("gallery");
        $data['images'] = $this->gallery_image_model->get_images();
        $data['categories'] = $this->gallery_category_model->get_categories();

        $this->load->view('blog/admin/_header', $data);
        $this->load->view('blog/admin/photo_gallery', $data);
        $this->load->view('blog/admin/_footer');
    }


    /**
     * Add Image Post
     */
    public function add_image_post()
    {
        //validate inputs
        $this->form_validation->set_rules('category_id',trans("category"), 'required');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('errors', validation_errors());
            $this->session->set_flashdata('form_data', $this->gallery_image_model->input_values());
            redirect($this->agent->referrer());
        } else {
            if ($this->gallery_image_model->add_image()) {
                $this->session->set_flashdata('success', trans("image") . " " . trans("msg_suc_added"));
                redirect($this->agent->referrer());
            } else {
                $this->session->set_flashdata('form_data', $this->gallery_image_model->input_values());
                $this->session->set_flashdata('error', trans("msg_error"));
                redirect($this->agent->referrer());
            }
        }
    }


    /**
     * Delete Image Post
     */
    public function delete_image_post()
    {
        $id = $this->input->post('id', true);

        if ($this->gallery_image_model->delete_image($id)) {
            redirect($this->agent->referrer());
        } else {
            redirect($this->agent->referrer());
        }
    }


}
