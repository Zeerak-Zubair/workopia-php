<?php

namespace App\Controllers;
use Framework\Database;
use Framework\Validation;

class ListingController{

    protected $db;

    public function __construct(){
        //die('HomeController');
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index(){
        //inspectAndDie(Validation::email('john@example.com'));
        //inspectAndDie(Validation::match('zayyan','zayyan'));

        $listings = $this->db->query('SELECT * FROM workopia.listings')->fetchAll();
        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create(){
        loadView('listings/create');
    }

    /**
     * Display a post
     * @param array $params
     * @return void
     */
    public function show($params) {
        $id = $params['id'] ?? '';
        //inspect($id); 

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id',$params)->fetch();
        
        if(!$listing){
            ErrorController::notFound('Listing Not Found');
            return;
        }
        //inspect($listing);

        loadView('listings/show', [ 'listing' => $listing ] );

    }

    /**
     * Store data in DB
     * @param mixed $data
     * @return void
     */
    public function store(){

        $allowedFields = ['title','description','salary','tags','company','address',
        'city','state','phone','email','requirements','benefits'];

        $newListingData = array_intersect_key($_POST,array_flip($allowedFields));

        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];

        $errors = [];

        foreach($requiredFields as $field){
            if(empty($newListingData[$field]) && !Validation::string($newListingData[$field])){
                $errors[$field] = ucfirst($field) . 'is required';
            }
        }

        if(!empty($errors)){
            //Reload the view
            loadView('listings/create', [
                "errors" => $errors,
                "listing" => $newListingData
            ]);
        } else{
            // Submit the data
            //$query = 'INSERT INTO workopia.listings 
            //(user_id, title, description, salary, tags, company, address, city, 
            //state, phone, email, requirements, benefits) 
            //VALUES 
            //(:user_id, :title, :description, :salary, :tags, :company, :address, :city,
            //:state, :phone, :email, :requirements, :benefits)';

            //$this->db->query($query, $newListingData);

            $fields = [];

            foreach($newListingData as $field => $value){
                $fields[] = $field;
            }

            $fields = implode(',',$fields);

            //inspectAndDie($fields);

            $values = [];

            foreach($newListingData as $field => $value){
                if( $value==='' ){
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }

            $values = implode(',',$values);

            $query = "INSERT INTO workopia.listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);

            redirect('/listings');
        }
    }


    /**
     * Delete a post
     * @param array $params
     * @return void
     */
    public function destroy($params){
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id', $params)->fetch();

        if(!$listing){
            ErrorController::notFound('Listing Not Found');
            return;
        }

        $result = $this->db->query('DELETE FROM workopia.listings WHERE id = :id', $params)->execute();

        if($result){
            $_SESSION['success_message'] = 'Listing deleted successfully';
            redirect('/listings');
        }else{
            echo "Failed to delete the record.";
        }
    }

    /**
     * Show the edit listing form
     * @param array $params
     * @return void
     */
    public function edit($params) {
        $id = $params['id'] ?? '';
        //inspect($id); 

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id',$params)->fetch();
        
        if(!$listing){
            ErrorController::notFound('Listing Not Found');
            return;
        }
        //inspect($listing);

        loadView('listings/edit', [ 'listing' => $listing ] );

    }

}

?>